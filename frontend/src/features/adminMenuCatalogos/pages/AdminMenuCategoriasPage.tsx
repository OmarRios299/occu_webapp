import { useState, useMemo, useCallback, useEffect } from 'react'
import {
  Container,
  Box,
  Typography,
  TextField,
  Button,
  Alert,
  CircularProgress,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Paper,
  IconButton,
  Pagination,
  InputAdornment,
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  Select,
  MenuItem,
  FormControl,
  InputLabel,
  Chip,
} from '@mui/material'
import SearchIcon from '@mui/icons-material/Search'
import EditIcon from '@mui/icons-material/Edit'
import DeleteIcon from '@mui/icons-material/Delete'
import AddIcon from '@mui/icons-material/Add'
import { useAuth } from '@/store/auth/AuthProvider'
import { designTokens } from '@/theme/tokens'
import { RestAdminMenuCatalogosRepository } from '../services/RestAdminMenuCatalogosRepository'
import type { Categoria, CategoriaCreatePayload } from '../types'

const PAGE_SIZE = 20

export function AdminMenuCategoriasPage() {
  const { session } = useAuth()
  const repository = useMemo(() => new RestAdminMenuCatalogosRepository(), [])

  const [categorias, setCategorias] = useState<{
    items: Categoria[]
    page: number
    totalPages: number
    totalItems: number
  } | null>(null)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [success, setSuccess] = useState<string | null>(null)
  const [searchQuery, setSearchQuery] = useState('')
  const [currentPage, setCurrentPage] = useState(1)
  const [openModal, setOpenModal] = useState(false)
  const [editingId, setEditingId] = useState<number | null>(null)
  const [formData, setFormData] = useState<CategoriaCreatePayload>({
    nombre: '',
    imagen: '',
    estado: 0,
    es_bebida: 'No',
  })

  const loadCategorias = useCallback(
    async (page: number, q: string) => {
      if (!session) {
        setError('No hay sesión activa')
        return
      }

      setLoading(true)
      setError(null)

      try {
        const result = await repository.getCategorias(page, PAGE_SIZE, session.accessToken, q || undefined)
        setCategorias(result)
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Error al cargar categorías')
      } finally {
        setLoading(false)
      }
    },
    [repository, session]
  )

  const handleSearch = useCallback(() => {
    setCurrentPage(1)
    loadCategorias(1, searchQuery)
  }, [searchQuery, loadCategorias])

  const handlePageChange = useCallback(
    (_event: React.ChangeEvent<unknown>, page: number) => {
      setCurrentPage(page)
      loadCategorias(page, searchQuery)
    },
    [searchQuery, loadCategorias]
  )

  const handleOpenCreate = useCallback(() => {
    setEditingId(null)
    setFormData({ nombre: '', imagen: '', estado: 0, es_bebida: 'No' })
    setOpenModal(true)
  }, [])

  const handleOpenEdit = useCallback(
    async (id: number) => {
      if (!session) return
      try {
        const categoria = await repository.getCategoriaById(id, session.accessToken)
        setEditingId(id)
        setFormData({
          nombre: categoria.nombre,
          imagen: categoria.imagen,
          estado: categoria.estado,
          es_bebida: categoria.es_bebida,
        })
        setOpenModal(true)
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Error al cargar categoría')
      }
    },
    [repository, session]
  )

  const handleSave = useCallback(async () => {
    if (!session) return
    if (!formData.nombre.trim()) {
      setError('El nombre es requerido')
      return
    }

    setLoading(true)
    setError(null)
    setSuccess(null)

    try {
      if (editingId === null) {
        await repository.createCategoria(formData, session.accessToken)
        setSuccess('Categoría creada exitosamente')
      } else {
        await repository.updateCategoria(editingId, formData, session.accessToken)
        setSuccess('Categoría actualizada exitosamente')
      }
      setOpenModal(false)
      loadCategorias(currentPage, searchQuery)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Error al guardar categoría')
    } finally {
      setLoading(false)
    }
  }, [formData, editingId, repository, session, currentPage, searchQuery, loadCategorias])

  const handleDelete = useCallback(
    async (id: number) => {
      if (!session) return
      if (!confirm('¿Estás seguro de eliminar esta categoría?')) return

      setLoading(true)
      setError(null)

      try {
        await repository.deleteCategoria(id, session.accessToken)
        setSuccess('Categoría eliminada exitosamente')
        loadCategorias(currentPage, searchQuery)
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Error al eliminar categoría')
      } finally {
        setLoading(false)
      }
    },
    [repository, session, currentPage, searchQuery, loadCategorias]
  )

  useEffect(() => {
    if (session) {
      loadCategorias(1, '')
    }
  }, [session, loadCategorias])

  return (
    <Box sx={{ minHeight: '100vh', backgroundColor: designTokens.colors.bgBody }}>
      <Container maxWidth="lg" sx={{ py: 4 }}>
        <Box sx={{ mb: 4, display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
          <Box>
            <Typography variant="h4" sx={{ mb: 2, color: designTokens.colors.texto }}>
              Categorías de Menú
            </Typography>
            <Typography variant="body2" sx={{ color: designTokens.colors.gris }}>
              Administra las categorías del catálogo de menú OCCU
            </Typography>
          </Box>
          <Button
            variant="contained"
            startIcon={<AddIcon />}
            onClick={handleOpenCreate}
            sx={{
              backgroundColor: designTokens.colors.principal,
              textTransform: 'none',
              '&:hover': {
                backgroundColor: designTokens.colors.sidebarDark,
              },
            }}
          >
            Nueva Categoría
          </Button>
        </Box>

        {/* Barra de búsqueda */}
        <Box sx={{ mb: 3, display: 'flex', gap: 2 }}>
          <TextField
            fullWidth
            placeholder="Buscar categoría..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            onKeyPress={(e) => {
              if (e.key === 'Enter') {
                handleSearch()
              }
            }}
            InputProps={{
              startAdornment: (
                <InputAdornment position="start">
                  <SearchIcon />
                </InputAdornment>
              ),
            }}
            sx={{
              backgroundColor: 'white',
              '& .MuiOutlinedInput-root': {
                borderRadius: designTokens.spacing.borderRadius.medium,
              },
            }}
          />
          <Button variant="contained" onClick={handleSearch}>
            Buscar
          </Button>
        </Box>

        {/* Mensajes */}
        {error && (
          <Alert severity="error" sx={{ mb: 3 }} onClose={() => setError(null)}>
            {error}
          </Alert>
        )}
        {success && (
          <Alert severity="success" sx={{ mb: 3 }} onClose={() => setSuccess(null)}>
            {success}
          </Alert>
        )}

        {/* Loading */}
        {loading && !categorias && (
          <Box sx={{ display: 'flex', justifyContent: 'center', py: 8 }}>
            <CircularProgress />
          </Box>
        )}

        {/* Tabla */}
        {categorias && (
          <>
            <TableContainer component={Paper} sx={{ mb: 3 }}>
              <Table>
                <TableHead>
                  <TableRow sx={{ backgroundColor: designTokens.colors.plantillaClaro }}>
                    <TableCell sx={{ fontWeight: 600 }}>ID</TableCell>
                    <TableCell sx={{ fontWeight: 600 }}>Nombre</TableCell>
                    <TableCell sx={{ fontWeight: 600 }}>Es Bebida</TableCell>
                    <TableCell sx={{ fontWeight: 600 }}>Estado</TableCell>
                    <TableCell sx={{ fontWeight: 600 }} align="right">
                      Acciones
                    </TableCell>
                  </TableRow>
                </TableHead>
                <TableBody>
                  {categorias.items.length === 0 ? (
                    <TableRow>
                      <TableCell colSpan={5} align="center" sx={{ py: 4 }}>
                        <Typography color="text.secondary">No se encontraron categorías</Typography>
                      </TableCell>
                    </TableRow>
                  ) : (
                    categorias.items.map((categoria) => (
                      <TableRow key={categoria.id} hover>
                        <TableCell>{categoria.id}</TableCell>
                        <TableCell sx={{ fontWeight: 500 }}>{categoria.nombre}</TableCell>
                        <TableCell>
                          <Chip
                            label={categoria.es_bebida === 'Si' ? 'Sí' : 'No'}
                            size="small"
                            color={categoria.es_bebida === 'Si' ? 'primary' : 'default'}
                          />
                        </TableCell>
                        <TableCell>
                          <Chip
                            label={categoria.estado === 0 ? 'Activo' : 'Inactivo'}
                            size="small"
                            color={categoria.estado === 0 ? 'success' : 'default'}
                          />
                        </TableCell>
                        <TableCell align="right">
                          <IconButton
                            onClick={() => handleOpenEdit(categoria.id)}
                            sx={{ color: designTokens.colors.principal }}
                          >
                            <EditIcon />
                          </IconButton>
                          <IconButton
                            onClick={() => handleDelete(categoria.id)}
                            sx={{ color: 'error.main' }}
                          >
                            <DeleteIcon />
                          </IconButton>
                        </TableCell>
                      </TableRow>
                    ))
                  )}
                </TableBody>
              </Table>
            </TableContainer>

            {/* Paginación */}
            {categorias.totalPages > 1 && (
              <Box sx={{ display: 'flex', justifyContent: 'center' }}>
                <Pagination
                  count={categorias.totalPages}
                  page={currentPage}
                  onChange={handlePageChange}
                  color="primary"
                />
              </Box>
            )}
          </>
        )}

        {/* Modal Crear/Editar */}
        <Dialog open={openModal} onClose={() => setOpenModal(false)} maxWidth="sm" fullWidth>
          <DialogTitle>{editingId === null ? 'Nueva Categoría' : 'Editar Categoría'}</DialogTitle>
          <DialogContent>
            <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2, pt: 1 }}>
              <TextField
                label="Nombre"
                fullWidth
                required
                value={formData.nombre}
                onChange={(e) => setFormData({ ...formData, nombre: e.target.value })}
              />
              <TextField
                label="Imagen (URL)"
                fullWidth
                value={formData.imagen}
                onChange={(e) => setFormData({ ...formData, imagen: e.target.value })}
              />
              <FormControl fullWidth>
                <InputLabel>Es Bebida</InputLabel>
                <Select
                  value={formData.es_bebida}
                  label="Es Bebida"
                  onChange={(e) => setFormData({ ...formData, es_bebida: e.target.value })}
                >
                  <MenuItem value="Si">Sí</MenuItem>
                  <MenuItem value="No">No</MenuItem>
                </Select>
              </FormControl>
              <FormControl fullWidth>
                <InputLabel>Estado</InputLabel>
                <Select
                  value={formData.estado}
                  label="Estado"
                  onChange={(e) => setFormData({ ...formData, estado: Number(e.target.value) })}
                >
                  <MenuItem value={0}>Activo</MenuItem>
                  <MenuItem value={1}>Inactivo</MenuItem>
                </Select>
              </FormControl>
            </Box>
          </DialogContent>
          <DialogActions>
            <Button onClick={() => setOpenModal(false)}>Cancelar</Button>
            <Button onClick={handleSave} variant="contained" disabled={loading}>
              {loading ? <CircularProgress size={20} /> : 'Guardar'}
            </Button>
          </DialogActions>
        </Dialog>
      </Container>
    </Box>
  )
}

export default AdminMenuCategoriasPage
