import { useEffect, useRef } from 'react'
import { MapContainer, TileLayer, Marker, Popup, useMap } from 'react-leaflet'
import L from 'leaflet'
import { Box } from '@mui/material'
import type { CafeteriaMapItem } from '../types'
import { fixLeafletIcons } from '@/utils/leaflet/leafletIconFix'

// Ejecutar fix de íconos una vez
fixLeafletIcons()

// Crear íconos personalizados para abierto/cerrado
const createCustomIcon = (isOpen: boolean): L.Icon => {
  const color = isOpen ? '#4caf50' : '#f44336'
  return L.icon({
    iconUrl: `data:image/svg+xml;base64,${btoa(`
      <svg xmlns="http://www.w3.org/2000/svg" width="25" height="41" viewBox="0 0 25 41">
        <path fill="${color}" d="M12.5 0C5.6 0 0 5.6 0 12.5c0 8.3 12.5 28.5 12.5 28.5S25 20.8 25 12.5C25 5.6 19.4 0 12.5 0z"/>
        <circle fill="white" cx="12.5" cy="12.5" r="6"/>
      </svg>
    `)}`,
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [0, -41],
  })
}

type FitBoundsProps = {
  bounds: L.LatLngBounds | null
}

function FitBounds({ bounds }: FitBoundsProps) {
  const map = useMap()

  useEffect(() => {
    if (bounds && bounds.isValid()) {
      map.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 })
    }
  }, [bounds, map])

  return null
}

type CafeteriasMapProps = {
  cafeterias: CafeteriaMapItem[]
  selectedCafeteriaId: number | null
  onMarkerClick: (cafeteriaId: number) => void
}

export function CafeteriasMap({ cafeterias, selectedCafeteriaId, onMarkerClick }: CafeteriasMapProps) {
  const mapRef = useRef<L.Map | null>(null)

  // Filtrar cafeterías con coordenadas válidas
  const cafeteriasWithCoords = cafeterias.filter(
    (c) => c.latitud !== null && c.longitud !== null && !isNaN(c.latitud) && !isNaN(c.longitud)
  )

  // Calcular bounds para fitBounds
  const bounds = cafeteriasWithCoords.length > 0
    ? L.latLngBounds(
        cafeteriasWithCoords.map((c) => [c.latitud!, c.longitud!] as [number, number])
      )
    : null

  // Centro por defecto (Tijuana)
  const defaultCenter: [number, number] = [32.5149, -117.0382]
  const defaultZoom = 12

  return (
    <Box sx={{ width: '100%', height: '100%', position: 'relative' }}>
      <MapContainer
        center={defaultCenter}
        zoom={defaultZoom}
        style={{ height: '100%', width: '100%' }}
        whenCreated={(map) => {
          mapRef.current = map
        }}
      >
        <TileLayer
          attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
          url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
        />

        {/* Fit bounds cuando hay cafeterías */}
        {bounds && <FitBounds bounds={bounds} />}

        {/* Marcadores */}
        {cafeteriasWithCoords.map((cafeteria) => (
          <Marker
            key={cafeteria.id}
            position={[cafeteria.latitud!, cafeteria.longitud!]}
            icon={createCustomIcon(cafeteria.isOpenNow)}
            eventHandlers={{
              click: () => {
                onMarkerClick(cafeteria.id)
              },
            }}
          >
            <Popup>
              <Box sx={{ minWidth: 150 }}>
                <Box sx={{ fontWeight: 600, mb: 0.5 }}>{cafeteria.nombre}</Box>
                <Box sx={{ fontSize: '12px', color: 'text.secondary', mb: 0.5 }}>
                  {cafeteria.direccion}
                </Box>
                <Box sx={{ fontSize: '12px', color: 'text.secondary' }}>
                  {cafeteria.todayScheduleLabel}
                </Box>
              </Box>
            </Popup>
          </Marker>
        ))}
      </MapContainer>
    </Box>
  )
}
