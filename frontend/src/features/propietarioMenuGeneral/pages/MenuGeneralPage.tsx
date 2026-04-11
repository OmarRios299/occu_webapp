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
  Chip,
  Select,
  MenuItem,
  FormControl,
  InputLabel,
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
} from '@mui/material'
import SearchIcon from '@mui/icons-material/Search'
import EditIcon from '@mui/icons-material/Edit'
import SettingsIcon from '@mui/icons-material/Settings'
import { designTokens } from '@/theme/tokens'
import { RestPropietarioMenuGeneralRepository } from '../services/RestPropietarioMenuGeneralRepository'
import type { ProductoMenuGeneral } from '../types'

const PAGE_SIZE = 20

export function MenuGeneralPage() {
  const navigate = useNavigate()
  const { session } = useAuth()
  const repository = useMemo(() => new RestPropietarioMenuGeneralRepository(), [])

  const [productos, setProductos] = useState<{
    items: ProductoMenuGeneral[]
    page: number
    totalPages: number
    totalItems: number
  } | null>(null)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [success, setSuccess] = useState<string | null>(null)
  const [searchQuery, setSearchQuery] = useState('')
  const [currentPage, setCurrentPage] = useState(1)
  const [estadoFiltro, setEstadoFiltro] = useState<number | ''>('')
  const [openPreciosModal, setOpenPreciosModal] = useState(false)
  const [productoEditando, setProductoEditando] = useState<ProductoMenuGeneral | null>(null)
  const [precioBase, setPrecioBase] = useState(0)
  const [preciosTamanos, setPreciosTamanos] = useState<Record<number, number>>({})

  const loadProductos = useCallback(
    async (page: number, q: string, estado?: number) => {
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
          q || undefined,
          undefined,
          estado
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
    loadProductos(1, searchQuery, estadoFiltro !== '' ? Number(estadoFiltro) : undefined)
  }, [searchQuery, estadoFiltro, loadProductos])

  const handlePageChange = useCallback(
    (_event: React.ChangeEvent<unknown>, page: number) => {
      setCurrentPage(page)
      loadProductos(page, searchQuery, estadoFiltro !== '' ? Number(estadoFiltro) : undefined)
    },
    [searchQuery, estadoFiltro, loadProductos]
  )

  const handleEstadoFiltroChange = useCallback(
    (estado: number | '') => {
      setEstadoFiltro(estado)
      setCurrentPage(1)
      loadProductos(1, searchQuery, estado !== '' ? Number(estado) : undefined)
    },
    [searchQuery, loadProductos]
  )

  const handleToggleEstado = useCallback(
    async (producto: ProductoMenuGeneral) => {
      if (!session) return

      const nuevoEstado = producto.estado_menu === 1 ? 0 : 1

      setLoading(true)
      setError(null)
      setSuccess(null)

      try {
        await repository.updateProductos(
          {
            productos: [
              {
                id_producto: producto.id,
                estado: nuevoEstado,
                precio_base: Number(producto.precio_base || 0),
              },
            ],
          },
          session.accessToken
        )
        setSuccess(`Producto ${nuevoEstado === 1 ? 'activado' : 'desactivado'} exitosamente`)
        loadProductos(currentPage, searchQuery, estadoFiltro !== '' ? Number(estadoFiltro) : undefined)
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Error al actualizar producto')
      } finally {
        setLoading(false)
      }
    },
    [repository, session, currentPage, searchQuery, estadoFiltro, loadProductos]
  )

  const handleOpenPreciosModal = useCallback(
    async (producto: ProductoMenuGeneral) => {
      if (!session) return

      try {
        const productoCompleto = await repository.getProductoById(producto.id, session.accessToken)
        setProductoEditando(productoCompleto)
        setPrecioBase(Number(productoCompleto.precio_base || 0))
        
        const precios: Record<number, number> = {}
        productoCompleto.tamanos?.forEach((t) => {
          precios[t.id_tamano] = Number(t.precio || 0)
        })
        setPreciosTamanos(precios)
        setOpenPreciosModal(true)
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Error al cargar producto')
      }
    },
    [repository, session]
  )

  const handleGuardarPrecios = useCallback(async () => {
    if (!session || !productoEditando) return

    setLoading(true)
    setError(null)
    setSuccess(null)

    try {
      const tamanos = Object.entries(preciosTamanos).map(([idTamano, precio]) => ({
        id_tamano: Number(idTamano),
        precio: Number(precio),
        estado: 0,
      }))

      await repository.updateProductos(
        {
          productos: [
            {
              id_producto: productoEditando.id,
              estado: productoEditando.estado_menu,
              precio_base: precioBase,
              tamanos,
            },
          ],
        },
        session.accessToken
      )
      setSuccess('Precios actualizados exitosamente')
      setOpenPreciosModal(false)
      loadProductos(currentPage, searchQuery, estadoFiltro !== '' ? Number(estadoFiltro) : undefined)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Error al guardar precios')
    } finally {
      setLoading(false)
    }
  }, [repository, session, productoEditando, precioBase, preciosTamanos, currentPage, searchQuery, estadoFiltro, loadProductos])

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
            Menú General
          </Typography>
          <Typography variant="body2" sx={{ color: designTokens.colors.gris }}>
            Administra tu menú base. Los cambios no se reflejan en las cafeterías hasta que publiques.
          </Typography>
        </Box>

        {/* Filtros */}
        <Box sx={{ mb: 3, display: 'flex', gap: 2, flexWrap: 'wrap' }}>
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
              flex: 1,
              minWidth: 200,
              backgroundColor: 'white',
              '& .MuiOutlinedInput-root': {
                borderRadius: designTokens.spacing.borderRadius.medium,
              },
            }}
          />
          <FormControl sx={{ minWidth: 150 }}>
            <InputLabel>Estado</InputLabel>
            <Select
              value={estadoFiltro}
              onChange={(e) => handleEstadoFiltroChange(e.target.value as number | '')}
              label="Estado"
            >
              <MenuItem value="">Todos</MenuItem>
              <MenuItem value={1}>Activos</MenuItem>
              <MenuItem value={0}>Inactivos</MenuItem>
            </Select>
          </FormControl>
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
                    <TableCell sx={{ fontWeight: 600 }}>Producto</TableCell>
                    <TableCell sx={{ fontWeight: 600 }}>Categoría</TableCell>
                    <TableCell sx={{ fontWeight: 600 }}>Subcategoría</TableCell>
                    <TableCell sx={{ fontWeight: 600 }}>Estado</TableCell>
                    <TableCell sx={{ fontWeight: 600 }}>Precio Base</TableCell>
                    <TableCell sx={{ fontWeight: 600 }} align="right">
                      Acciones
                    </TableCell>
                  </TableRow>
                </TableHead>
                <TableBody>
                  {productos.items.length === 0 ? (
                    <TableRow>
                      <TableCell colSpan={6} align="center" sx={{ py: 4 }}>
                        <Typography color="text.secondary">No se encontraron productos</Typography>
                      </TableCell>
                    </TableRow>
                  ) : (
                    productos.items.map((producto) => (
                      <TableRow key={producto.id} hover>
                        <TableCell sx={{ fontWeight: 500 }}>{producto.nombre}</TableCell>
                        <TableCell>{producto.categoria_nombre || '-'}</TableCell>
                        <TableCell>{producto.subcategoria_nombre || '-'}</TableCell>
                        <TableCell>
                          <Chip
                            label={producto.estado_menu === 1 ? 'Activo' : 'Inactivo'}
                            size="small"
                            color={producto.estado_menu === 1 ? 'success' : 'default'}
                            onClick={() => handleToggleEstado(producto)}
                            sx={{ cursor: 'pointer' }}
                          />
                        </TableCell>
                        <TableCell>${Number(producto.precio_base || 0).toFixed(2)}</TableCell>
                        <TableCell align="right">
                          <IconButton
                            onClick={() => handleOpenPreciosModal(producto)}
                            sx={{ color: designTokens.colors.principal }}
                            title="Editar precios"
                          >
                            <EditIcon />
                          </IconButton>
                          <IconButton
                            onClick={() => navigate(`/menu/productos/${producto.id}/reglas`)}
                            sx={{ color: designTokens.colors.principal }}
                            title="Editar reglas"
                          >
                            <SettingsIcon />
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
                />
              </Box>
            )}
          </>
        )}

        {/* Modal Precios */}
        <Dialog open={openPreciosModal} onClose={() => setOpenPreciosModal(false)} maxWidth="sm" fullWidth>
          <DialogTitle>Editar Precios - {productoEditando?.nombre}</DialogTitle>
          <DialogContent>
            <Box sx={{ display: 'flex', flexDirection: 'column', gap: 3, pt: 2 }}>
              <TextField
                label="Precio Base"
                type="number"
                value={precioBase}
                onChange={(e) => setPrecioBase(parseFloat(e.target.value) || 0)}
                InputProps={{
                  startAdornment: <InputAdornment position="start">$</InputAdornment>,
                }}
                inputProps={{ min: 0, step: 0.01 }}
                fullWidth
              />
              {productoEditando?.tamanos && productoEditando.tamanos.length > 0 && (
                <Box>
                  <Typography variant="subtitle2" sx={{ mb: 2 }}>
                    Precios por Tamaño
                  </Typography>
                  {productoEditando.tamanos.map((tamano) => (
                    <TextField
                      key={tamano.id_tamano}
                      label={`${tamano.tamano_nombre} ${tamano.unidad_medida}`}
                      type="number"
                      value={preciosTamanos[tamano.id_tamano] ?? 0}
                      onChange={(e) =>
                        setPreciosTamanos({
                          ...preciosTamanos,
                          [tamano.id_tamano]: parseFloat(e.target.value) || 0,
                        })
                      }
                      InputProps={{
                        startAdornment: <InputAdornment position="start">$</InputAdornment>,
                      }}
                      inputProps={{ min: 0, step: 0.01 }}
                      fullWidth
                      sx={{ mb: 2 }}
                    />
                  ))}
                </Box>
              )}
            </Box>
          </DialogContent>
          <DialogActions>
            <Button onClick={() => setOpenPreciosModal(false)}>Cancelar</Button>
            <Button onClick={handleGuardarPrecios} variant="contained" disabled={loading}>
              {loading ? <CircularProgress size={20} /> : 'Guardar'}
            </Button>
          </DialogActions>
        </Dialog>
      </Container>
    </Box>
  )
}

export default MenuGeneralPage
