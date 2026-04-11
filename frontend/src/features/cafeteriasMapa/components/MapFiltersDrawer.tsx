import {
  Drawer,
  Box,
  Typography,
  IconButton,
  RadioGroup,
  FormControlLabel,
  Radio,
  Select,
  MenuItem,
  FormControl,
  InputLabel,
  Switch,
  FormGroup,
  Checkbox,
  Button,
  Divider,
  useTheme,
  useMediaQuery,
} from '@mui/material'
import CloseIcon from '@mui/icons-material/Close'
import { designTokens } from '@/theme/tokens'
import type { CafeteriasMapaFilters } from '../types'
import type { Servicio, Ciudad } from '../../cafeteriasLista/types'

type MapFiltersDrawerProps = {
  open: boolean
  onClose: () => void
  horario: 'todos' | 'abierto'
  ciudadId: number | null
  servicioIds: number[]
  servicios: Servicio[]
  ciudades: Ciudad[]
  serviciosEnabled: boolean
  onHorarioChange: (horario: 'todos' | 'abierto') => void
  onCiudadChange: (ciudadId: number | null) => void
  onServiciosToggle: (enabled: boolean) => void
  onServicioToggle: (servicioId: number) => void
  onClear: () => void
  onApply: () => void
}

export function MapFiltersDrawer({
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
}: MapFiltersDrawerProps) {
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
          width: isMobile ? '100%' : 400,
          pt: isMobile ? 0 : designTokens.spacing.navbarHeight,
          pb: isMobile ? `calc(${designTokens.spacing.bottomNavHeight} + env(safe-area-inset-bottom))` : 0,
        },
      }}
    >
      <Box sx={{ p: 3, height: '100%', display: 'flex', flexDirection: 'column' }}>
        {/* Header */}
        <Box sx={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', mb: 3 }}>
          <Typography variant="h6" fontWeight={600} sx={{ color: designTokens.colors.texto }}>
            Filtros
          </Typography>
          <IconButton onClick={onClose} size="small">
            <CloseIcon />
          </IconButton>
        </Box>

        <Divider sx={{ mb: 3 }} />

        {/* Contenido scrolleable */}
        <Box sx={{ flex: 1, overflowY: 'auto' }}>
          {/* Horario */}
          <Box sx={{ mb: 3 }}>
            <Typography variant="subtitle2" fontWeight={600} sx={{ mb: 1.5, color: designTokens.colors.texto }}>
              Horario
            </Typography>
            <RadioGroup value={horario} onChange={(e) => onHorarioChange(e.target.value as 'todos' | 'abierto')}>
              <FormControlLabel value="todos" control={<Radio />} label="Todos" />
              <FormControlLabel value="abierto" control={<Radio />} label="Solo abiertas" />
            </RadioGroup>
          </Box>

          <Divider sx={{ my: 3 }} />

          {/* Ciudad */}
          <Box sx={{ mb: 3 }}>
            <FormControl fullWidth>
              <InputLabel>Ciudad</InputLabel>
              <Select
                value={ciudadId || ''}
                label="Ciudad"
                onChange={(e) => onCiudadChange(e.target.value ? (e.target.value as number) : null)}
              >
                <MenuItem value="">Todas</MenuItem>
                {ciudades.map((ciudad) => (
                  <MenuItem key={ciudad.id} value={ciudad.id}>
                    {ciudad.nombre}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
          </Box>

          <Divider sx={{ my: 3 }} />

          {/* Servicios */}
          <Box sx={{ mb: 3 }}>
            <Box sx={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', mb: 1.5 }}>
              <Typography variant="subtitle2" fontWeight={600} sx={{ color: designTokens.colors.texto }}>
                Servicios
              </Typography>
              <Switch checked={serviciosEnabled} onChange={(e) => onServiciosToggle(e.target.checked)} />
            </Box>
            {serviciosEnabled && (
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
            )}
          </Box>
        </Box>

        {/* Footer con botones */}
        <Box sx={{ pt: 2, borderTop: `1px solid ${designTokens.colors.grisClaro}` }}>
          <Box sx={{ display: 'flex', gap: 2 }}>
            <Button variant="outlined" onClick={onClear} fullWidth>
              Limpiar
            </Button>
            <Button
              variant="contained"
              onClick={() => {
                onApply()
                onClose()
              }}
              fullWidth
              sx={{
                backgroundColor: designTokens.colors.principal,
                '&:hover': {
                  backgroundColor: designTokens.colors.sidebarDark,
                },
              }}
            >
              Aplicar
            </Button>
          </Box>
        </Box>
      </Box>
    </Drawer>
  )
}
