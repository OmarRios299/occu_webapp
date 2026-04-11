import { useState, useEffect, useMemo, useCallback } from 'react'
import { useNavigate } from 'react-router-dom'
import {
  Container,
  Box,
  Typography,
  Button,
  Alert,
  CircularProgress,
  useTheme,
} from '@mui/material'
import AddIcon from '@mui/icons-material/Add'
import PublishIcon from '@mui/icons-material/Publish'
import { designTokens } from '@/theme/tokens'
import { useMisCafeteriasController } from '../hooks/useMisCafeteriasController'
import { CafeteriasOwnerGrid } from '../components/CafeteriasOwnerGrid'
import { CafeteriaOwnerDrawer } from '../components/CafeteriaOwnerDrawer'
import { RestMisCafeteriasRepository } from '../services/RestMisCafeteriasRepository'
import { RestPropietarioMenuPublicacionRepository } from '@/features/propietarioMenuPublicacion/services/RestPropietarioMenuPublicacionRepository'
import { PublicarMenuDialog } from '@/features/propietarioMenuPublicacion/components/PublicarMenuDialog'
import { useAuth } from '@/store/auth/AuthProvider'
import type { CafeteriaOwner } from '../types'
import type { EstadosActualizacion } from '@/features/propietarioMenuPublicacion/types'

export function MisCafeteriasPage() {
  const theme = useTheme()
  const navigate = useNavigate()
  const { session } = useAuth()
  const repository = useMemo(() => new RestMisCafeteriasRepository(), [])
  const publicacionRepo = useMemo(() => new RestPropietarioMenuPublicacionRepository(), [])
  const { state, actions } = useMisCafeteriasController()
  const [drawerOpen, setDrawerOpen] = useState(false)
  const [selectedCafeteriaId, setSelectedCafeteriaId] = useState<number | null>(null)
  const [publicarDialogOpen, setPublicarDialogOpen] = useState(false)
  const [estadosActualizacion, setEstadosActualizacion] = useState<EstadosActualizacion>({})
  const [loadingEstados, setLoadingEstados] = useState(false)

  const handleCafeteriaClick = async (cafeteria: CafeteriaOwner) => {
    setSelectedCafeteriaId(cafeteria.id)
    setDrawerOpen(true)
    await actions.loadCafeteriaDetail(cafeteria.id)
  }

  const handleCloseDrawer = () => {
    setDrawerOpen(false)
    setSelectedCafeteriaId(null)
    actions.clearSelected()
  }

  const loadEstadosActualizacion = useCallback(async () => {
    if (!session) return

    setLoadingEstados(true)
    try {
      const estados = await publicacionRepo.getEstadosActualizacion(session.accessToken)
      setEstadosActualizacion(estados)
    } catch (err) {
      console.error('Error al cargar estados de actualización:', err)
    } finally {
      setLoadingEstados(false)
    }
  }, [publicacionRepo, session])

  const handlePublicarSuccess = useCallback(() => {
    loadEstadosActualizacion()
    actions.retry() // Recargar cafeterías
  }, [loadEstadosActualizacion, actions])

  useEffect(() => {
    if (session && state.cafeterias.length > 0) {
      loadEstadosActualizacion()
    }
  }, [session, state.cafeterias.length, loadEstadosActualizacion])

  return (
    <Box sx={{ minHeight: '100vh', backgroundColor: designTokens.colors.bgBody }}>
      <Container maxWidth="lg" sx={{ py: 4 }}>
        {/* Header */}
        <Box
          sx={{
            display: 'flex',
            justifyContent: 'space-between',
            alignItems: 'center',
            mb: 4,
            flexWrap: 'wrap',
            gap: 2,
          }}
        >
          <Typography
            variant="h4"
            fontWeight={700}
            sx={{
              color: designTokens.colors.texto,
            }}
          >
            Mis Cafeterías
          </Typography>
          <Box sx={{ display: 'flex', gap: 2 }}>
            <Button
              variant="outlined"
              startIcon={<PublishIcon />}
              onClick={() => setPublicarDialogOpen(true)}
              disabled={state.cafeterias.length === 0}
              sx={{
                borderColor: designTokens.colors.principal,
                color: designTokens.colors.texto,
                textTransform: 'none',
                fontWeight: 600,
                '&:hover': {
                  borderColor: designTokens.colors.sidebarDark,
                  backgroundColor: `${designTokens.colors.principal}10`,
                },
              }}
            >
              Publicar Menú
            </Button>
            <Button
              variant="contained"
              startIcon={<AddIcon />}
              onClick={() => navigate('/cafeterias/agregar')}
              sx={{
                backgroundColor: designTokens.colors.principal,
                textTransform: 'none',
                fontWeight: 600,
                '&:hover': {
                  backgroundColor: designTokens.colors.sidebarDark,
                },
              }}
            >
              Agregar Cafetería
            </Button>
          </Box>
        </Box>

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
        {state.loading && state.cafeterias.length === 0 && (
          <Box sx={{ display: 'flex', justifyContent: 'center', py: 8 }}>
            <CircularProgress />
          </Box>
        )}

        {/* Grid de cafeterías */}
        {!state.loading && (
          <CafeteriasOwnerGrid
            cafeterias={state.cafeterias}
            loading={state.loading}
            onCafeteriaClick={handleCafeteriaClick}
            estadosActualizacion={estadosActualizacion}
          />
        )}

        {/* Drawer de detalle */}
        <CafeteriaOwnerDrawer
          open={drawerOpen}
          onClose={handleCloseDrawer}
          cafeteria={state.selectedCafeteria}
          loading={state.loading && selectedCafeteriaId !== null}
          mutating={state.mutating}
          onToggleEstado={actions.toggleEstado}
          onDelete={actions.deleteCafeteria}
          onLoadServicios={actions.loadServicios}
          onSaveServicios={actions.saveServicios}
          repository={repository}
          token={session?.accessToken || ''}
        />

        {/* Dialog de publicación */}
        {session && (
          <PublicarMenuDialog
            open={publicarDialogOpen}
            onClose={() => setPublicarDialogOpen(false)}
            cafeterias={state.cafeterias}
            onSuccess={handlePublicarSuccess}
            token={session.accessToken}
          />
        )}
      </Container>
    </Box>
  )
}
