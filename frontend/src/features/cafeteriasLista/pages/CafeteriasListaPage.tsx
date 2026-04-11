import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import {
  Container,
  Box,
  AppBar,
  Toolbar,
  IconButton,
  Button,
  Alert,
  CircularProgress,
  useTheme,
  useMediaQuery,
} from '@mui/material'
import FilterListIcon from '@mui/icons-material/FilterList'
import MapIcon from '@mui/icons-material/Map'
import { designTokens } from '@/theme/tokens'
import { useCafeteriasListaController } from '../hooks/useCafeteriasListaController'
import { SearchBar } from '../components/SearchBar'
import { FiltersDrawer } from '../components/FiltersDrawer'
import { CafeteriasGrid } from '../components/CafeteriasGrid'
import { PaginationControls } from '../components/PaginationControls'
import { CafeteriaDetailDrawer } from '../components/CafeteriaDetailDrawer'
import { RestCafeteriasListaRepository } from '../services/RestCafeteriasListaRepository'

export function CafeteriasListaPage() {
  const theme = useTheme()
  const isDesktop = useMediaQuery(theme.breakpoints.up('lg'))
  const navigate = useNavigate()
  const [filtersDrawerOpen, setFiltersDrawerOpen] = useState(false)
  const [detailDrawerOpen, setDetailDrawerOpen] = useState(false)
  const [selectedCafeteriaId, setSelectedCafeteriaId] = useState<number | null>(null)

  const { state, actions, filters } = useCafeteriasListaController()
  const repository = new RestCafeteriasListaRepository()

  const serviciosEnabled = (filters.servicioIds?.length || 0) > 0

  const handleCafeteriaClick = (cafeteriaId: number) => {
    setSelectedCafeteriaId(cafeteriaId)
    setDetailDrawerOpen(true)
  }

  return (
    <Box sx={{ minHeight: '100vh', backgroundColor: designTokens.colors.bgBody }}>
      {/* Barra de acciones */}
      <Box
        sx={{
          position: 'sticky',
          top: isDesktop ? designTokens.spacing.navbarHeight : designTokens.spacing.navbarHeight,
          zIndex: theme.zIndex.appBar - 1,
          backgroundColor: designTokens.colors.bgBody,
          borderBottom: `1px solid ${designTokens.colors.grisClaro}`,
          py: 2,
        }}
      >
        <Container maxWidth="lg">
          <Box
            sx={{
              display: 'flex',
              alignItems: 'center',
              gap: 2,
              flexWrap: 'wrap',
            }}
          >
            {/* Botón Filtros */}
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

            {/* Botón Mapa */}
            <Button
              variant="outlined"
              startIcon={<MapIcon />}
              onClick={() => navigate('/cafeterias_mapa')}
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
              Mapa
            </Button>

            {/* Barra de búsqueda (colapsable) */}
            <Box sx={{ flex: 1, minWidth: 200 }}>
              <SearchBar value={filters.q || ''} onChange={actions.setSearchQuery} />
            </Box>
          </Box>
        </Container>
      </Box>

      {/* Contenido principal */}
      <Container maxWidth="lg" sx={{ py: 3 }}>
        {/* Estado de error */}
        {state.error && (
          <Alert
            severity="error"
            action={
              <Button color="inherit" size="small" onClick={actions.retry}>
                Reintentar
              </Button>
            }
            sx={{ mb: 3 }}
          >
            {state.error}
          </Alert>
        )}

        {/* Loading inicial */}
        {state.loading && !state.cafeterias && (
          <Box sx={{ display: 'flex', justifyContent: 'center', py: 8 }}>
            <CircularProgress />
          </Box>
        )}

        {/* Grid de cafeterías */}
        {state.cafeterias && (
          <>
            <CafeteriasGrid
              cafeterias={state.cafeterias.items}
              loading={state.loading}
              onCafeteriaClick={(cafeteria) => handleCafeteriaClick(cafeteria.id)}
            />

            {/* Paginación */}
            {state.cafeterias.totalPages > 1 && (
              <PaginationControls
                page={state.cafeterias.page}
                totalPages={state.cafeterias.totalPages}
                totalItems={state.cafeterias.totalItems}
                pageSize={state.cafeterias.pageSize}
                onPageChange={actions.setPage}
              />
            )}
          </>
        )}
      </Container>

      {/* Drawer de filtros */}
      <FiltersDrawer
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
        onClose={() => {
          setDetailDrawerOpen(false)
          setSelectedCafeteriaId(null)
        }}
        cafeteriaId={selectedCafeteriaId}
        onLoadDetail={(id) => repository.getCafeteriaDetail(id)}
        onLoadComentarios={(id, page, pageSize) => repository.getComentarios(id, page, pageSize)}
        onCreateComentario={(id, comentario, token) => repository.createComentario(id, comentario, token)}
      />
    </Box>
  )
}
