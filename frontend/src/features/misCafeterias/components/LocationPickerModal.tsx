import { useState, useEffect, useRef } from 'react'
import {
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  Button,
  Box,
  Alert,
  CircularProgress,
} from '@mui/material'
import { MapContainer, TileLayer, Marker, useMap, Polygon } from 'react-leaflet'
import L from 'leaflet'
import { designTokens } from '@/theme/tokens'
import { fixLeafletIcons } from '@/utils/leaflet/leafletIconFix'

// Ejecutar fix de íconos
fixLeafletIcons()

type LocationData = {
  latitud: number
  longitud: number
  direccion: string
}

type LocationPickerModalProps = {
  open: boolean
  onClose: () => void
  onSave: (location: LocationData) => void
  initialLocation?: { latitud: number; longitud: number; direccion: string } | null
  ciudadId?: number | null
  ciudadCoordenadas?: Array<{ lat: number; lng: number }> | null
}

// Componente para ajustar el mapa cuando cambian las coordenadas
function MapUpdater({
  center,
  zoom,
  polygon,
  selectedLocation,
}: {
  center: [number, number]
  zoom: number
  polygon: Array<{ lat: number; lng: number }> | null
  selectedLocation: { latitud: number; longitud: number } | null
}) {
  const map = useMap()

  useEffect(() => {
    // Pequeño delay para asegurar que el mapa esté completamente inicializado
    const timer = setTimeout(() => {
      if (selectedLocation) {
        // Si hay ubicación seleccionada, centrar ahí
        map.setView([selectedLocation.latitud, selectedLocation.longitud], 15)
      } else if (polygon && polygon.length > 0) {
        // Si hay polígono, ajustar a sus bounds
        const latlngs = polygon.map((coord) => [coord.lat, coord.lng] as [number, number])
        const bounds = L.latLngBounds(latlngs)
        map.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 })
      } else {
        // Default center
        map.setView(center, zoom)
      }
    }, 100)

    return () => clearTimeout(timer)
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [map]) // Solo cuando el mapa está listo

  return null
}

// Función para verificar si un punto está dentro de un polígono (ray casting)
function isPointInPolygon(
  point: { lat: number; lng: number },
  polygon: Array<{ lat: number; lng: number }>
): boolean {
  if (!polygon || polygon.length < 3) return true // Si no hay polígono, permitir cualquier punto

  let x = point.lat
  let y = point.lng
  let inside = false

  for (let i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
    const xi = polygon[i].lat
    const yi = polygon[i].lng
    const xj = polygon[j].lat
    const yj = polygon[j].lng

    const intersect = yi > y !== yj > y && x < ((xj - xi) * (y - yi)) / (yj - yi) + xi
    if (intersect) inside = !inside
  }

  return inside
}

export function LocationPickerModal({
  open,
  onClose,
  onSave,
  initialLocation,
  ciudadId,
  ciudadCoordenadas,
}: LocationPickerModalProps) {
  const [selectedLocation, setSelectedLocation] = useState<LocationData | null>(
    initialLocation || null
  )
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [mapReady, setMapReady] = useState(false)
  const markerRef = useRef<L.Marker | null>(null)

  // Resetear cuando se abre el modal
  useEffect(() => {
    if (open) {
      setSelectedLocation(initialLocation || null)
      setError(null)
      setMapReady(false)
      // Pequeño delay para asegurar que el mapa se renderice
      const timer = setTimeout(() => setMapReady(true), 100)
      return () => clearTimeout(timer)
    }
  }, [open, initialLocation])

  // Calcular centro inicial
  const getInitialCenter = (): [number, number] => {
    if (selectedLocation) {
      return [selectedLocation.latitud, selectedLocation.longitud]
    }
    if (ciudadCoordenadas && ciudadCoordenadas.length > 0) {
      const centroid = ciudadCoordenadas.reduce(
        (acc, coord) => ({
          lat: acc.lat + coord.lat,
          lng: acc.lng + coord.lng,
        }),
        { lat: 0, lng: 0 }
      )
      return [centroid.lat / ciudadCoordenadas.length, centroid.lng / ciudadCoordenadas.length]
    }
    return [32.624538, -115.452263] // Default: Mexicali
  }

  const handleMapClick = async (e: L.LeafletMouseEvent) => {
    const lat = e.latlng.lat
    const lng = e.latlng.lng

    // Validar que esté dentro del polígono si existe
    if (ciudadCoordenadas && ciudadCoordenadas.length > 0) {
      if (!isPointInPolygon({ lat, lng }, ciudadCoordenadas)) {
        setError('La ubicación seleccionada está fuera del área permitida de la ciudad')
        return
      }
    }

    setError(null)
    setLoading(true)

    try {
      // Reverse geocoding con Nominatim
      const response = await fetch(
        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`
      )
      const data = await response.json()

      let direccion = ''
      if (data && data.address) {
        const addr = data.address
        if (addr.road) direccion += addr.road
        if (addr.house_number) direccion += ' ' + addr.house_number
        if (addr.suburb) direccion += ', ' + addr.suburb
        if (addr.city || addr.town || addr.village) {
          direccion += ', ' + (addr.city || addr.town || addr.village)
        }
        if (addr.state) direccion += ', ' + addr.state
        if (addr.country) direccion += ', ' + addr.country

        if (!direccion || direccion.trim() === '') {
          direccion = data.display_name || `Lat: ${lat}, Lng: ${lng}`
        }
      } else {
        direccion = `Lat: ${lat}, Lng: ${lng}`
      }

      setSelectedLocation({ latitud: lat, longitud: lng, direccion })
    } catch (err) {
      console.error('Error en reverse geocoding:', err)
      setSelectedLocation({
        latitud: lat,
        longitud: lng,
        direccion: `Lat: ${lat}, Lng: ${lng}`,
      })
    } finally {
      setLoading(false)
    }
  }

  const handleSave = () => {
    if (!selectedLocation) {
      setError('Por favor selecciona una ubicación en el mapa')
      return
    }

    onSave(selectedLocation)
    onClose()
  }

  const initialCenter = getInitialCenter()
  const initialZoom = selectedLocation ? 15 : ciudadCoordenadas ? 13 : 13

  return (
    <Dialog open={open} onClose={onClose} maxWidth="md" fullWidth>
      <DialogTitle>Seleccionar Ubicación</DialogTitle>
      <DialogContent>
        {error && (
          <Alert severity="warning" sx={{ mb: 2 }}>
            {error}
          </Alert>
        )}

        {selectedLocation && (
          <Box sx={{ mb: 2, p: 2, bgcolor: designTokens.colors.bgBody, borderRadius: 1 }}>
            <strong>Ubicación seleccionada:</strong>
            <br />
            {selectedLocation.direccion}
            <br />
            <small>
              Lat: {selectedLocation.latitud.toFixed(6)}, Lng: {selectedLocation.longitud.toFixed(6)}
            </small>
          </Box>
        )}

        <Box sx={{ height: 400, width: '100%', position: 'relative' }}>
          {mapReady && (
            <MapContainer
              center={initialCenter}
              zoom={initialZoom}
              style={{ height: '100%', width: '100%' }}
              whenCreated={(map) => {
                map.on('click', handleMapClick)
              }}
            >
              <TileLayer
                url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                attribution="© OpenStreetMap contributors"
              />

              {mapReady && (
                <MapUpdater
                  center={initialCenter}
                  zoom={initialZoom}
                  polygon={ciudadCoordenadas || null}
                  selectedLocation={selectedLocation}
                />
              )}

              {/* Polígono de ciudad (si existe) */}
              {ciudadCoordenadas && ciudadCoordenadas.length > 0 && (
                <Polygon
                  positions={ciudadCoordenadas.map((c) => [c.lat, c.lng] as [number, number])}
                  pathOptions={{ color: designTokens.colors.principal, fillOpacity: 0.1 }}
                />
              )}

              {/* Marcador de ubicación seleccionada */}
              {selectedLocation && (
                <Marker position={[selectedLocation.latitud, selectedLocation.longitud]} />
              )}
            </MapContainer>
          )}

          {loading && (
            <Box
              sx={{
                position: 'absolute',
                top: '50%',
                left: '50%',
                transform: 'translate(-50%, -50%)',
                zIndex: 1000,
              }}
            >
              <CircularProgress />
            </Box>
          )}
        </Box>

        <Box sx={{ mt: 2, fontSize: '0.875rem', color: 'text.secondary' }}>
          Haz clic en el mapa para seleccionar la ubicación de la cafetería
        </Box>
      </DialogContent>
      <DialogActions>
        <Button onClick={onClose}>Cancelar</Button>
        <Button onClick={handleSave} variant="contained" disabled={!selectedLocation || loading}>
          Guardar Ubicación
        </Button>
      </DialogActions>
    </Dialog>
  )
}
