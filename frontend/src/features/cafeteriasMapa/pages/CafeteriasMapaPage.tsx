import { useState } from 'react'
import {
  Box,
  AppBar,
  Toolbar,
  IconButton,
  Button,
  Alert,
  CircularProgress,
  useTheme,
  useMediaQuery,
  Fab,
} from '@mui/material'
import SearchIcon from '@mui/icons-material/Search'
import FilterListIcon from '@mui/icons-material/FilterList'
import { designTokens } from '@/theme/tokens'
import { useCafeteriasMapaController } from '../hooks/useCafeteriasMapaController'
import { CafeteriasMap } from '../components/CafeteriasMap'
import { MapFiltersDrawer } from '../components/MapFiltersDrawer'
import { CafeteriaDetailDrawer } from '../../cafeteriasLista/components/CafeteriaDetailDrawer'
import { RestCafeteriasListaRepository } from '../../cafeteriasLista/services/RestCafeteriasListaRepository'
import 'leaflet/dist/leaflet.css'

export function CafeteriasMapaPage() {
  const theme = useTheme()
  const isDesktop = useMediaQuery(theme.breakpoints.up('lg'))
  const [filtersDrawerOpen, setFiltersDrawerOpen] = useState(false)
  const [detailDrawerOpen, setDetailDrawerOpen] = useState(false)

  const { state, actions, filters, selectedCafeteriaId } = useCafeteriasMapaController()
  const repository = new RestCafeteriasListaRepository()

  const serviciosEnabled = (filters.servicioIds?.length || 0) > 0

  const handleMarkerClick = (cafeteriaId: number) => {
    actions.setSelectedCafeteriaId(cafeteriaId)
    setDetailDrawerOpen(true)
  }

  const handleCloseDetail = () => {
    setDetailDrawerOpen(false)
    actions.setSelectedCafeteriaId(null)
  }

  return (
    <Box
      sx={{
        width: '100%',
        height: {
          xs: `calc(100vh - ${designTokens.spacing.navbarHeight} - ${designTokens.spacing.bottomNavHeight} - env(safe-area-inset-bottom))`,
          lg: `calc(100vh - ${designTokens.spacing.navbarHeight})`,
        },
        position: 'relative',
        overflow: 'hidden',
      }}
    >
      {/* Barra de acciones superior */}
      <AppBar
        position="absolute"
        sx={{
          top: 0,
          left: 0,
          right: 0,
          zIndex: 1000,
          backgroundColor: 'rgba(255, 255, 255, 0.95)',
          backdropFilter: 'blur(10px)',
          boxShadow: '0 2px 8px rgba(0,0,0,0.1)',
        }}
      >
        <Toolbar sx={{ justifyContent: 'space-between', gap: 2, minHeight: '64px !important' }}>
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 2 }}>
            <Button
              variant="outlined"
              startIcon={<SearchIcon />}
              onClick={() => {
                const searchInput = document.querySelector('[data-search-bar]') as HTMLInputElement
                if (searchInput) searchInput.focus()
              }}
              sx={{
                borderColor: designTokens.colors.principal,
                color: designTokens.colors.texto,
                backgroundColor: 'white',
                textTransform: 'none',
                fontWeight: 500,
                '&:hover': {
                  borderColor: designTokens.colors.sidebarDark,
                  backgroundColor: `${designTokens.colors.principal}10`,
                },
              }}
            >
              Buscar
            </Button>

            <Button
              variant="outlined"
              startIcon={<FilterListIcon />}
              onClick={() => setFiltersDrawerOpen(true)}
              sx={{
                borderColor: designTokens.colors.principal,
                color: designTokens.colors.texto,
                backgroundColor: 'white',
                textTransform: 'none',
                fontWeight: 500,
                '&:hover': {
                  borderColor: designTokens.colors.sidebarDark,
                  backgroundColor: `${designTokens.colors.principal}10`,
                },
              }}
            >
              Filtros
            </Button>
          </Box>
        </Toolbar>
      </AppBar>

      {/* Mapa */}
      <Box
        sx={{
          width: '100%',
          height: '100%',
          pt: '64px', // Espacio para la barra superior
        }}
      >
        {state.loading && !state.cafeterias && (
          <Box
            sx={{
              position: 'absolute',
              top: '50%',
              left: '50%',
              transform: 'translate(-50%, -50%)',
              zIndex: 1001,
            }}
          >
            <CircularProgress />
          </Box>
        )}

        {state.error && (
          <Box
            sx={{
              position: 'absolute',
              top: 80,
              left: 16,
              right: 16,
              zIndex: 1001,
              maxWidth: 400,
            }}
          >
            <Alert
              severity="error"
              action={
                <Button color="inherit" size="small" onClick={actions.retry}>
                  Reintentar
                </Button>
              }
            >
              {state.error}
            </Alert>
          </Box>
        )}

        {state.cafeterias && (
          <CafeteriasMap
            cafeterias={state.cafeterias.items}
            selectedCafeteriaId={selectedCafeteriaId}
            onMarkerClick={handleMarkerClick}
          />
        )}
      </Box>

      {/* Botón flotante de filtros en móvil */}
      {!isDesktop && (
        <Fab
          color="primary"
          aria-label="filtros"
          sx={{
            position: 'absolute',
            bottom: { xs: `calc(24px + ${designTokens.spacing.bottomNavHeight} + env(safe-area-inset-bottom))`, lg: 24 },
            right: 24,
            backgroundColor: designTokens.colors.principal,
            '&:hover': {
              backgroundColor: designTokens.colors.sidebarDark,
            },
          }}
          onClick={() => setFiltersDrawerOpen(true)}
        >
          <FilterListIcon />
        </Fab>
      )}

      {/* Drawer de filtros */}
      <MapFiltersDrawer
        open={filtersDrawerOpen}
        onClose={() => setFiltersDrawerOpen(false)}
        horario={filters.horario || 'todos'}
        ciudadId={filters.ciudadId || null}
        servicioIds={filters.servicioIds || []}
        servicios={state.servicios}
        ciudades={state.ciudades}
        serviciosEnabled={serviciosEnabled}
        onHorarioChange={actions.setHorario}
        onCiudadChange={actions.setCiudadId}
        onServiciosToggle={(enabled) => {
          if (!enabled) {
            actions.setServicioIds([])
          }
        }}
        onServicioToggle={actions.toggleServicio}
        onClear={actions.clearFilters}
        onApply={actions.applyFilters}
      />

      {/* Drawer de detalle de cafetería */}
      <CafeteriaDetailDrawer
        open={detailDrawerOpen}
        onClose={handleCloseDetail}
        cafeteriaId={selectedCafeteriaId}
        onLoadDetail={(id) => repository.getCafeteriaDetail(id)}
        onLoadComentarios={(id, page, pageSize) => repository.getComentarios(id, page, pageSize)}
        onCreateComentario={(id, comentario, token) => repository.createComentario(id, comentario, token)}
      />
    </Box>
  )
}
