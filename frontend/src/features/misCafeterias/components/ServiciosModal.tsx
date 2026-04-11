import { useState, useEffect } from 'react'
import {
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  Button,
  Box,
  Checkbox,
  FormControlLabel,
  Grid,
  CircularProgress,
  Alert,
  Typography,
} from '@mui/material'
import { designTokens } from '@/theme/tokens'

export type Servicio = {
  id: number
  nombre: string
  imagen?: string
  servicio_registrado?: number // 1 = registrado, 0 = no registrado
}

type ServiciosModalProps = {
  open: boolean
  onClose: () => void
  cafeteriaId: number | null
  onLoadServicios: (cafeteriaId: number) => Promise<Servicio[]>
  onSaveServicios: (cafeteriaId: number, servicioIds: number[]) => Promise<void>
}

export function ServiciosModal({
  open,
  onClose,
  cafeteriaId,
  onLoadServicios,
  onSaveServicios,
}: ServiciosModalProps) {
  const [servicios, setServicios] = useState<Servicio[]>([])
  const [selectedIds, setSelectedIds] = useState<Set<number>>(new Set())
  const [loading, setLoading] = useState(false)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState<string | null>(null)

  // Cargar servicios al abrir el modal
  useEffect(() => {
    if (open && cafeteriaId) {
      setLoading(true)
      setError(null)
      onLoadServicios(cafeteriaId)
        .then((data) => {
          setServicios(data)
          // Preseleccionar servicios registrados
          const preseleccionados = new Set<number>()
          data.forEach((servicio) => {
            if (servicio.servicio_registrado === 1) {
              preseleccionados.add(servicio.id)
            }
          })
          setSelectedIds(preseleccionados)
        })
        .catch((err) => {
          setError(err instanceof Error ? err.message : 'Error al cargar servicios')
        })
        .finally(() => {
          setLoading(false)
        })
    } else {
      setServicios([])
      setSelectedIds(new Set())
    }
  }, [open, cafeteriaId, onLoadServicios])

  const handleToggle = (servicioId: number) => {
    setSelectedIds((prev) => {
      const newSet = new Set(prev)
      if (newSet.has(servicioId)) {
        newSet.delete(servicioId)
      } else {
        newSet.add(servicioId)
      }
      return newSet
    })
  }

  const handleSave = async () => {
    if (!cafeteriaId) return

    setSaving(true)
    setError(null)

    try {
      const servicioIds = Array.from(selectedIds)
      await onSaveServicios(cafeteriaId, servicioIds)
      onClose()
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Error al guardar servicios')
    } finally {
      setSaving(false)
    }
  }

  return (
    <Dialog open={open} onClose={onClose} maxWidth="md" fullWidth>
      <DialogTitle>Gestionar Servicios</DialogTitle>
      <DialogContent>
        {loading && (
          <Box sx={{ display: 'flex', justifyContent: 'center', py: 4 }}>
            <CircularProgress />
          </Box>
        )}

        {error && (
          <Alert severity="error" sx={{ mb: 2 }}>
            {error}
          </Alert>
        )}

        {!loading && servicios.length === 0 && (
          <Typography color="text.secondary" sx={{ textAlign: 'center', py: 4 }}>
            No hay servicios disponibles
          </Typography>
        )}

        {!loading && servicios.length > 0 && (
          <Grid container spacing={2} sx={{ mt: 1 }}>
            {servicios.map((servicio) => {
              const imagenUrl =
                servicio.imagen && servicio.imagen.startsWith('http')
                  ? servicio.imagen
                  : servicio.imagen
                    ? `${import.meta.env.VITE_API_BASE_URL || ''}/${servicio.imagen.replace(/^\//, '')}`
                    : null

              return (
                <Grid item xs={6} sm={4} md={3} key={servicio.id}>
                  <Box
                    sx={{
                      border: `2px solid ${
                        selectedIds.has(servicio.id)
                          ? designTokens.colors.principal
                          : designTokens.colors.grisClaro
                      }`,
                      borderRadius: 2,
                      overflow: 'hidden',
                      cursor: 'pointer',
                      transition: 'all 0.2s',
                      '&:hover': {
                        borderColor: designTokens.colors.principal,
                        transform: 'translateY(-2px)',
                        boxShadow: 2,
                      },
                    }}
                    onClick={() => handleToggle(servicio.id)}
                  >
                    {imagenUrl && (
                      <Box
                        sx={{
                          width: '100%',
                          paddingTop: '75%',
                          position: 'relative',
                          backgroundColor: designTokens.colors.grisClaro,
                        }}
                      >
                        <img
                          src={imagenUrl}
                          alt={servicio.nombre}
                          style={{
                            position: 'absolute',
                            top: 0,
                            left: 0,
                            width: '100%',
                            height: '100%',
                            objectFit: 'cover',
                          }}
                        />
                      </Box>
                    )}
                    <Box sx={{ p: 1.5, textAlign: 'center' }}>
                      <FormControlLabel
                        control={
                          <Checkbox
                            checked={selectedIds.has(servicio.id)}
                            onChange={() => handleToggle(servicio.id)}
                            size="small"
                          />
                        }
                        label={
                          <Typography variant="body2" fontWeight={500}>
                            {servicio.nombre}
                          </Typography>
                        }
                        sx={{ m: 0 }}
                      />
                    </Box>
                  </Box>
                </Grid>
              )
            })}
          </Grid>
        )}
      </DialogContent>
      <DialogActions>
        <Button onClick={onClose} disabled={saving}>
          Cancelar
        </Button>
        <Button onClick={handleSave} variant="contained" disabled={saving || loading}>
          {saving ? <CircularProgress size={20} /> : 'Guardar'}
        </Button>
      </DialogActions>
    </Dialog>
  )
}
