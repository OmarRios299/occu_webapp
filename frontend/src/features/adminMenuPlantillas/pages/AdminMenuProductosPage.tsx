import { useState, useMemo, useCallback, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuth } from '@/store/auth/AuthProvider'
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
} from '@mui/material'
import SearchIcon from '@mui/icons-material/Search'
import EditIcon from '@mui/icons-material/Edit'
import { designTokens } from '@/theme/tokens'
import { RestAdminMenuRepository } from '../services/RestAdminMenuRepository'
import type { ProductoListItem } from '../types'

const PAGE_SIZE = 20

export function AdminMenuProductosPage() {
  const navigate = useNavigate()
  const { session } = useAuth()
  const repository = useMemo(() => new RestAdminMenuRepository(), [])

  const [productos, setProductos] = useState<{
    items: ProductoListItem[]
    page: number
    totalPages: number
    totalItems: number
  } | null>(null)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [searchQuery, setSearchQuery] = useState('')
  const [currentPage, setCurrentPage] = useState(1)

  const loadProductos = useCallback(
    async (page: number, q: string) => {
      if (!session) {
        setError('No hay sesión activa')
        return
      }

      setLoading(true)
      setError(null)

      try {
        const result = await repository.getProductos(
          page,
          PAGE_SIZE,
          session.accessToken,
          q || undefined
        )
        setProductos(result)
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Error al cargar productos')
      } finally {
        setLoading(false)
      }
    },
    [repository, session]
  )

  const handleSearch = useCallback(() => {
    setCurrentPage(1)
    loadProductos(1, searchQuery)
  }, [searchQuery, loadProductos])

  const handlePageChange = useCallback(
    (_event: React.ChangeEvent<unknown>, page: number) => {
      setCurrentPage(page)
      loadProductos(page, searchQuery)
    },
    [searchQuery, loadProductos]
  )

  // Cargar inicial
  useEffect(() => {
    if (session) {
      loadProductos(1, '')
    }
  }, [session, loadProductos])

  return (
    <Box sx={{ minHeight: '100vh', backgroundColor: designTokens.colors.bgBody }}>
      <Container maxWidth="lg" sx={{ py: 4 }}>
        <Box sx={{ mb: 4 }}>
          <Typography variant="h4" sx={{ mb: 2, color: designTokens.colors.texto }}>
            Plantillas de Menú OCCU
          </Typography>
          <Typography variant="body2" sx={{ color: designTokens.colors.gris }}>
            Administra las reglas de personalización para cada producto
          </Typography>
        </Box>

        {/* Barra de búsqueda */}
        <Box sx={{ mb: 3, display: 'flex', gap: 2 }}>
          <TextField
            fullWidth
            placeholder="Buscar producto..."
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
          <Button
            variant="contained"
            onClick={handleSearch}
            sx={{
              backgroundColor: designTokens.colors.principal,
              textTransform: 'none',
              px: 3,
              '&:hover': {
                backgroundColor: designTokens.colors.sidebarDark,
              },
            }}
          >
            Buscar
          </Button>
        </Box>

        {/* Error */}
        {error && (
          <Alert severity="error" sx={{ mb: 3 }}>
            {error}
          </Alert>
        )}

        {/* Loading */}
        {loading && !productos && (
          <Box sx={{ display: 'flex', justifyContent: 'center', py: 8 }}>
            <CircularProgress />
          </Box>
        )}

        {/* Tabla */}
        {productos && (
          <>
            <TableContainer component={Paper} sx={{ mb: 3 }}>
              <Table>
                <TableHead>
                  <TableRow sx={{ backgroundColor: designTokens.colors.plantillaClaro }}>
                    <TableCell sx={{ fontWeight: 600 }}>ID</TableCell>
                    <TableCell sx={{ fontWeight: 600 }}>Nombre</TableCell>
                    <TableCell sx={{ fontWeight: 600 }}>Categoría</TableCell>
                    <TableCell sx={{ fontWeight: 600 }}>Subcategoría</TableCell>
                    <TableCell sx={{ fontWeight: 600 }} align="right">
                      Acciones
                    </TableCell>
                  </TableRow>
                </TableHead>
                <TableBody>
                  {productos.items.length === 0 ? (
                    <TableRow>
                      <TableCell colSpan={5} align="center" sx={{ py: 4 }}>
                        <Typography color="text.secondary">
                          No se encontraron productos
                        </Typography>
                      </TableCell>
                    </TableRow>
                  ) : (
                    productos.items.map((producto) => (
                      <TableRow key={producto.id} hover>
                        <TableCell>{producto.id}</TableCell>
                        <TableCell sx={{ fontWeight: 500 }}>{producto.nombre}</TableCell>
                        <TableCell>{producto.categoria || '-'}</TableCell>
                        <TableCell>{producto.subcategoria || '-'}</TableCell>
                        <TableCell align="right">
                          <IconButton
                            onClick={() => navigate(`/admin/menu/productos/${producto.id}/reglas`)}
                            sx={{
                              color: designTokens.colors.principal,
                              '&:hover': {
                                backgroundColor: `${designTokens.colors.principal}20`,
                              },
                            }}
                          >
                            <EditIcon />
                          </IconButton>
                        </TableCell>
                      </TableRow>
                    ))
                  )}
                </TableBody>
              </Table>
            </TableContainer>

            {/* Paginación */}
            {productos.totalPages > 1 && (
              <Box sx={{ display: 'flex', justifyContent: 'center' }}>
                <Pagination
                  count={productos.totalPages}
                  page={currentPage}
                  onChange={handlePageChange}
                  color="primary"
                  sx={{
                    '& .MuiPaginationItem-root': {
                      color: designTokens.colors.texto,
                    },
                  }}
                />
              </Box>
            )}
          </>
        )}
      </Container>
    </Box>
  )
}

export default AdminMenuProductosPage
