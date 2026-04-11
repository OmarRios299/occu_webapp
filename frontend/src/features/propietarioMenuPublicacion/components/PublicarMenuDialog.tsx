import { useState, useMemo, useCallback } from 'react'
import {
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  Button,
  FormControl,
  FormLabel,
  RadioGroup,
  FormControlLabel,
  Radio,
  Checkbox,
  FormGroup,
  Alert,
  CircularProgress,
  Typography,
  Box,
} from '@mui/material'
import { designTokens } from '@/theme/tokens'
import { RestPropietarioMenuPublicacionRepository } from '../services/RestPropietarioMenuPublicacionRepository'
import type { ModoPublicacion } from '../types'

interface Cafeteria {
  id: number
  nombre: string
}

interface PublicarMenuDialogProps {
  open: boolean
  onClose: () => void
  cafeterias: Cafeteria[]
  onSuccess: () => void
  token: string
}

export function PublicarMenuDialog({
  open,
  onClose,
  cafeterias,
  onSuccess,
  token,
}: PublicarMenuDialogProps) {
  const repository = useMemo(() => new RestPropietarioMenuPublicacionRepository(), [])
  const [modo, setModo] = useState<ModoPublicacion>('todo')
  const [cafeteriasSeleccionadas, setCafeteriasSeleccionadas] = useState<number[]>([])
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)

  const handleToggleCafeteria = useCallback((cafeteriaId: number) => {
    setCafeteriasSeleccionadas((prev) =>
      prev.includes(cafeteriaId) ? prev.filter((id) => id !== cafeteriaId) : [...prev, cafeteriaId]
    )
  }, [])

  const handleSelectAll = useCallback(() => {
    if (cafeteriasSeleccionadas.length === cafeterias.length) {
      setCafeteriasSeleccionadas([])
    } else {
      setCafeteriasSeleccionadas(cafeterias.map((c) => c.id))
    }
  }, [cafeterias, cafeteriasSeleccionadas.length])

  const handlePublicar = useCallback(async () => {
    if (cafeteriasSeleccionadas.length === 0) {
      setError('Debes seleccionar al menos una cafetería')
      return
    }

    setLoading(true)
    setError(null)

    try {
      await repository.publicarMenu(
        {
          cafeteria_ids: cafeteriasSeleccionadas,
          modo,
        },
        token
      )
      onSuccess()
      onClose()
      // Resetear estado
      setCafeteriasSeleccionadas([])
      setModo('todo')
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Error al publicar menú')
    } finally {
      setLoading(false)
    }
  }, [repository, cafeteriasSeleccionadas, modo, token, onSuccess, onClose])

  const handleClose = useCallback(() => {
    if (!loading) {
      setCafeteriasSeleccionadas([])
      setModo('todo')
      setError(null)
      onClose()
    }
  }, [loading, onClose])

  return (
    <Dialog open={open} onClose={handleClose} maxWidth="sm" fullWidth>
      <DialogTitle>Publicar Menú a Cafeterías</DialogTitle>
      <DialogContent>
        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 3, pt: 2 }}>
          {/* Modo de publicación */}
          <FormControl component="fieldset">
            <FormLabel component="legend">Modo de publicación</FormLabel>
            <RadioGroup value={modo} onChange={(e) => setModo(e.target.value as ModoPublicacion)}>
              <FormControlLabel
                value="todo"
                control={<Radio />}
                label="Todo (productos, precios y reglas)"
              />
              <FormControlLabel
                value="solo_precios"
                control={<Radio />}
                label="Solo precios (sin cambiar activaciones ni reglas)"
              />
            </RadioGroup>
          </FormControl>

          {/* Selección de cafeterías */}
          <FormControl component="fieldset">
            <FormLabel component="legend">Cafeterías</FormLabel>
            <Box sx={{ mb: 1 }}>
              <Button size="small" onClick={handleSelectAll}>
                {cafeteriasSeleccionadas.length === cafeterias.length ? 'Deseleccionar todas' : 'Seleccionar todas'}
              </Button>
            </Box>
            <FormGroup>
              {cafeterias.map((cafeteria) => (
                <FormControlLabel
                  key={cafeteria.id}
                  control={
                    <Checkbox
                      checked={cafeteriasSeleccionadas.includes(cafeteria.id)}
                      onChange={() => handleToggleCafeteria(cafeteria.id)}
                    />
                  }
                  label={cafeteria.nombre}
                />
              ))}
            </FormGroup>
          </FormControl>

          {error && (
            <Alert severity="error" onClose={() => setError(null)}>
              {error}
            </Alert>
          )}
        </Box>
      </DialogContent>
      <DialogActions>
        <Button onClick={handleClose} disabled={loading}>
          Cancelar
        </Button>
        <Button
          onClick={handlePublicar}
          variant="contained"
          disabled={loading || cafeteriasSeleccionadas.length === 0}
          sx={{
            backgroundColor: designTokens.colors.principal,
            '&:hover': {
              backgroundColor: designTokens.colors.sidebarDark,
            },
          }}
        >
          {loading ? <CircularProgress size={20} /> : 'Publicar'}
        </Button>
      </DialogActions>
    </Dialog>
  )
}
