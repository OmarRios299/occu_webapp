import {
  Drawer,
  Box,
  Typography,
  RadioGroup,
  FormControlLabel,
  Radio,
  FormControl,
  FormLabel,
  Select,
  MenuItem,
  FormGroup,
  Checkbox,
  Button,
  Divider,
  Switch,
  useTheme,
  useMediaQuery,
} from '@mui/material'
import { designTokens } from '@/theme/tokens'
import type { Servicio, Ciudad } from '../types'

type FiltersDrawerProps = {
  open: boolean
  onClose: () => void
  horario: 'todos' | 'abierto'
  ciudadId: number | null
  servicioIds: number[]
  servicios: Servicio[]
  ciudades: Ciudad[]
  serviciosEnabled: boolean
  onHorarioChange: (horario: 'todos' | 'abierto') => void
  onCiudadChange: (id: number | null) => void
  onServiciosToggle: (enabled: boolean) => void
  onServicioToggle: (id: number) => void
  onClear: () => void
  onApply: () => void
}

export function FiltersDrawer({
  open,
  onClose,
  horario,
  ciudadId,
  servicioIds,
  servicios,
  ciudades,
  serviciosEnabled,
  onHorarioChange,
  onCiudadChange,
  onServiciosToggle,
  onServicioToggle,
  onClear,
  onApply,
}: FiltersDrawerProps) {
  const theme = useTheme()
  const isMobile = useMediaQuery(theme.breakpoints.down('lg'))
  const anchor = isMobile ? 'bottom' : 'right'

  return (
    <Drawer
      anchor={anchor}
      open={open}
      onClose={onClose}
      PaperProps={{
        sx: {
          width: { xs: '100%', lg: 420 },
          maxHeight: { xs: '90vh', lg: '100vh' },
          borderTopLeftRadius: { xs: designTokens.spacing.borderRadius.large, lg: 0 },
          borderTopRightRadius: { xs: designTokens.spacing.borderRadius.large, lg: 0 },
          pt: { xs: 0, lg: designTokens.spacing.navbarHeight },
          pb: { xs: `calc(${designTokens.spacing.bottomNavHeight} + env(safe-area-inset-bottom))`, lg: 0 },
        },
      }}
    >
      <Box sx={{ p: 3, display: 'flex', flexDirection: 'column', height: '100%' }}>
        {/* Header */}
        <Box sx={{ mb: 3 }}>
          <Typography variant="h6" fontWeight={700} sx={{ color: designTokens.colors.principal }}>
            Filtros de Búsqueda
          </Typography>
        </Box>

        {/* Contenido scrollable */}
        <Box sx={{ flexGrow: 1, overflowY: 'auto', mb: 2 }}>
          {/* Horario */}
          <FormControl component="fieldset" fullWidth sx={{ mb: 3 }}>
            <FormLabel component="legend" sx={{ mb: 1, fontWeight: 600 }}>
              Horario
            </FormLabel>
            <RadioGroup value={horario} onChange={(e) => onHorarioChange(e.target.value as 'todos' | 'abierto')}>
              <FormControlLabel value="todos" control={<Radio />} label="Todos los horarios" />
              <FormControlLabel value="abierto" control={<Radio />} label="Abiertas ahora" />
            </RadioGroup>
          </FormControl>

          <Divider sx={{ my: 3 }} />

          {/* Ciudad */}
          <FormControl fullWidth sx={{ mb: 3 }}>
            <FormLabel sx={{ mb: 1, fontWeight: 600 }}>Ciudad</FormLabel>
            <Select
              value={ciudadId || ''}
              onChange={(e) => onCiudadChange(e.target.value ? Number(e.target.value) : null)}
              displayEmpty
            >
              <MenuItem value="">Todas las ciudades</MenuItem>
              {ciudades.map((ciudad) => (
                <MenuItem key={ciudad.id} value={ciudad.id}>
                  {ciudad.nombre}
                </MenuItem>
              ))}
            </Select>
          </FormControl>

          <Divider sx={{ my: 3 }} />

          {/* Servicios */}
          <FormControl component="fieldset" fullWidth sx={{ mb: 3 }}>
            <FormLabel component="legend" sx={{ mb: 1, fontWeight: 600 }}>
              Servicios
            </FormLabel>
            <FormControlLabel
              control={
                <Switch checked={serviciosEnabled} onChange={(e) => onServiciosToggle(e.target.checked)} />
              }
              label="Buscar por servicios"
            />

            {serviciosEnabled && (
              <Box sx={{ mt: 2, pl: 1 }}>
                <FormGroup>
                  {servicios.map((servicio) => (
                    <FormControlLabel
                      key={servicio.id}
                      control={
                        <Checkbox
                          checked={servicioIds.includes(servicio.id)}
                          onChange={() => onServicioToggle(servicio.id)}
                        />
                      }
                      label={servicio.nombre}
                    />
                  ))}
                </FormGroup>
              </Box>
            )}
          </FormControl>
        </Box>

        {/* Footer con botones */}
        <Box sx={{ display: 'flex', gap: 2, pt: 2, borderTop: 1, borderColor: 'divider' }}>
          <Button variant="outlined" fullWidth onClick={onClear}>
            Limpiar
          </Button>
          <Button
            variant="contained"
            fullWidth
            onClick={() => {
              onApply()
              onClose()
            }}
            sx={{
              backgroundColor: designTokens.colors.principal,
              '&:hover': {
                backgroundColor: designTokens.colors.sidebarDark,
              },
            }}
          >
            Aplicar Filtros
          </Button>
        </Box>
      </Box>
    </Drawer>
  )
}
