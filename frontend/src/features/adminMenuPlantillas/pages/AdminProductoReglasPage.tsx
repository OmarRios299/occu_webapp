import { useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import {
  Container,
  Box,
  Typography,
  Button,
  Alert,
  CircularProgress,
  Tabs,
  Tab,
  Paper,
  IconButton,
  Dialog,
  DialogTitle,
  DialogContent,
  DialogContentText,
  DialogActions,
} from '@mui/material'
import ArrowBackIcon from '@mui/icons-material/ArrowBack'
import SaveIcon from '@mui/icons-material/Save'
import RefreshIcon from '@mui/icons-material/Refresh'
import { designTokens } from '@/theme/tokens'
import { useAdminProductoReglasController } from '../hooks/useAdminProductoReglasController'
import { ReglasCategoriasTable } from '../components/ReglasCategoriasTable'
import { ReglasIngredientesList } from '../components/ReglasIngredientesList'

type TabValue = 'categorias' | 'ingredientes'

export function AdminProductoReglasPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const [activeTab, setActiveTab] = useState<TabValue>('categorias')
  const [confirmDeleteOpen, setConfirmDeleteOpen] = useState(false)
  const [categoriaToDelete, setCategoriaToDelete] = useState<number | null>(null)

  const { state, actions } = useAdminProductoReglasController()

  const handleEliminarCategoria = (categoriaId: number) => {
    setCategoriaToDelete(categoriaId)
    setConfirmDeleteOpen(true)
  }

  const confirmEliminarCategoria = () => {
    if (categoriaToDelete !== null) {
      actions.eliminarCategoria(categoriaToDelete)
      setConfirmDeleteOpen(false)
      setCategoriaToDelete(null)
    }
  }

  const categoriaIds =
    state.reglas?.categorias.map((c) => c.id_ingrediente_categoria) || []

  return (
    <Box sx={{ minHeight: '100vh', backgroundColor: designTokens.colors.bgBody }}>
      <Container maxWidth="lg" sx={{ py: 4 }}>
        {/* Header */}
        <Box sx={{ mb: 4, display: 'flex', alignItems: 'center', gap: 2 }}>
          <IconButton
            onClick={() => navigate('/admin/menu/productos')}
            sx={{
              color: designTokens.colors.texto,
              '&:hover': {
                backgroundColor: `${designTokens.colors.principal}20`,
              },
            }}
          >
            <ArrowBackIcon />
          </IconButton>
          <Box sx={{ flex: 1 }}>
            <Typography variant="h4" sx={{ color: designTokens.colors.texto }}>
              Plantilla de Reglas - Producto #{id}
            </Typography>
            <Typography variant="body2" sx={{ color: designTokens.colors.gris }}>
              Configura las reglas de personalización para este producto
            </Typography>
          </Box>
          <Box sx={{ display: 'flex', gap: 2 }}>
            <Button
              variant="outlined"
              startIcon={<RefreshIcon />}
              onClick={actions.recargar}
              disabled={state.loading || state.saving}
              sx={{
                borderColor: designTokens.colors.principal,
                color: designTokens.colors.texto,
                textTransform: 'none',
                '&:hover': {
                  borderColor: designTokens.colors.sidebarDark,
                  backgroundColor: `${designTokens.colors.principal}10`,
                },
              }}
            >
              Recargar
            </Button>
            <Button
              variant="contained"
              startIcon={<SaveIcon />}
              onClick={actions.guardar}
              disabled={state.loading || state.saving || !state.reglas}
              sx={{
                backgroundColor: designTokens.colors.principal,
                textTransform: 'none',
                '&:hover': {
                  backgroundColor: designTokens.colors.sidebarDark,
                },
              }}
            >
              {state.saving ? 'Guardando...' : 'Guardar Cambios'}
            </Button>
          </Box>
        </Box>

        {/* Mensajes */}
        {state.error && (
          <Alert
            severity="error"
            onClose={actions.limpiarMensajes}
            sx={{ mb: 3, whiteSpace: 'pre-line' }}
          >
            {state.error}
          </Alert>
        )}

        {state.successMessage && (
          <Alert
            severity="success"
            onClose={actions.limpiarMensajes}
            sx={{ mb: 3 }}
          >
            {state.successMessage}
          </Alert>
        )}

        {/* Loading */}
        {state.loading && !state.reglas && (
          <Box sx={{ display: 'flex', justifyContent: 'center', py: 8 }}>
            <CircularProgress />
          </Box>
        )}

        {/* Contenido */}
        {state.reglas && (
          <>
            {/* Tabs */}
            <Paper sx={{ mb: 3 }}>
              <Tabs
                value={activeTab}
                onChange={(_, newValue) => setActiveTab(newValue)}
                sx={{
                  borderBottom: `1px solid ${designTokens.colors.grisClaro}`,
                  '& .MuiTab-root': {
                    textTransform: 'none',
                    fontWeight: 500,
                  },
                }}
              >
                <Tab label="Categorías" value="categorias" />
                <Tab label="Ingredientes" value="ingredientes" />
              </Tabs>
            </Paper>

            {/* Tab Panel: Categorías */}
            {activeTab === 'categorias' && (
              <ReglasCategoriasTable
                categorias={state.reglas.categorias}
                catalogoCategorias={state.reglas.catalogo_categorias}
                onAgregar={actions.agregarCategoria}
                onEliminar={handleEliminarCategoria}
                onActualizar={(categoriaId, updates) =>
                  actions.actualizarCategoria(categoriaId, updates)
                }
              />
            )}

            {/* Tab Panel: Ingredientes */}
            {activeTab === 'ingredientes' && (
              <>
                {categoriaIds.length === 0 ? (
                  <Alert severity="info" sx={{ mb: 3 }}>
                    Primero debes agregar al menos una categoría aplicable en la
                    pestaña "Categorías".
                  </Alert>
                ) : (
                  <ReglasIngredientesList
                    ingredientes={state.reglas.ingredientes}
                    catalogoIngredientes={state.reglas.catalogo_ingredientes}
                    categoriaIds={categoriaIds}
                    onToggle={actions.toggleIngrediente}
                    onActualizar={(ingredienteId, updates) =>
                      actions.actualizarIngrediente(ingredienteId, updates)
                    }
                  />
                )}
              </>
            )}
          </>
        )}

        {/* Dialog de confirmación para eliminar categoría */}
        <Dialog open={confirmDeleteOpen} onClose={() => setConfirmDeleteOpen(false)}>
          <DialogTitle>Confirmar eliminación</DialogTitle>
          <DialogContent>
            <DialogContentText>
              ¿Estás seguro de que deseas eliminar esta categoría? También se
              eliminarán todas las reglas de ingredientes asociadas.
            </DialogContentText>
          </DialogContent>
          <DialogActions>
            <Button onClick={() => setConfirmDeleteOpen(false)}>Cancelar</Button>
            <Button
              onClick={confirmEliminarCategoria}
              variant="contained"
              sx={{
                backgroundColor: designTokens.colors.rojo,
                '&:hover': {
                  backgroundColor: designTokens.colors.rojoOscuro,
                },
              }}
            >
              Eliminar
            </Button>
          </DialogActions>
        </Dialog>
      </Container>
    </Box>
  )
}
