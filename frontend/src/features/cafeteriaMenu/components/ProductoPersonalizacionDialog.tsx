import { useState, useEffect, useMemo } from 'react'
import { useNavigate } from 'react-router-dom'
import {
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  Button,
  Box,
  Typography,
  Radio,
  RadioGroup,
  FormControlLabel,
  FormControl,
  FormLabel,
  Checkbox,
  IconButton,
  Chip,
  Divider,
  Alert,
  CircularProgress,
  Paper,
  Snackbar,
} from '@mui/material'
import CloseIcon from '@mui/icons-material/Close'
import AddIcon from '@mui/icons-material/Add'
import RemoveIcon from '@mui/icons-material/Remove'
import { designTokens } from '@/theme/tokens'
import { RestCafeteriaMenuRepository } from '../services/RestCafeteriaMenuRepository'
import { RestCarritoRepository } from '@/features/carrito/services/RestCarritoRepository'
import { useAuth } from '@/store/auth/AuthProvider'
import type { ProductoDetalle, ReglaCategoria, IngredienteRegla, PersonalizacionProducto, SeleccionIngrediente } from '../types'

type ProductoPersonalizacionDialogProps = {
  open: boolean
  onClose: () => void
  cafeteriaId: number
  productoId: number
}

export function ProductoPersonalizacionDialog({
  open,
  onClose,
  cafeteriaId,
  productoId,
}: ProductoPersonalizacionDialogProps) {
  const [producto, setProducto] = useState<ProductoDetalle | null>(null)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [tamanoSeleccionado, setTamanoSeleccionado] = useState<number | null>(null)
  const [selecciones, setSelecciones] = useState<Map<number, number>>(new Map()) // ingredienteId -> cantidad
  const [agregandoAlCarrito, setAgregandoAlCarrito] = useState(false)
  const [snackbarOpen, setSnackbarOpen] = useState(false)
  const [snackbarMessage, setSnackbarMessage] = useState('')

  const navigate = useNavigate()
  const { session } = useAuth()
  const repository = new RestCafeteriaMenuRepository()
  const carritoRepository = new RestCarritoRepository(() => session?.accessToken ?? null)

  useEffect(() => {
    if (open && cafeteriaId && productoId) {
      setLoading(true)
      setError(null)
      setTamanoSeleccionado(null)
      setSelecciones(new Map())
      repository
        .getProducto(cafeteriaId, productoId)
        .then((data) => {
          setProducto(data)
          // Si hay tamaños, seleccionar el primero por defecto
          if (data.tamanos.length > 0) {
            setTamanoSeleccionado(data.tamanos[0].id)
          }
        })
        .catch((err) => {
          setError(err instanceof Error ? err.message : 'Error al cargar el producto')
        })
        .finally(() => {
          setLoading(false)
        })
    } else {
      setProducto(null)
    }
  }, [open, cafeteriaId, productoId])

  // Calcular precio base (del tamaño seleccionado o base)
  const precioBase = useMemo(() => {
    if (!producto) return 0
    if (tamanoSeleccionado && producto.tamanos.length > 0) {
      const tamano = producto.tamanos.find((t) => t.id === tamanoSeleccionado)
      return tamano ? tamano.precio : producto.precio_base
    }
    return producto.precio_base
  }, [producto, tamanoSeleccionado])

  // Calcular total de ingredientes
  const totalIngredientes = useMemo(() => {
    if (!producto) return 0
    let total = 0

    producto.reglas_categorias.forEach((reglaCat) => {
      reglaCat.ingredientes.forEach((ing) => {
        const cantidad = selecciones.get(ing.id) || 0
        if (cantidad > 0) {
          // Calcular precio según tipo
          if (ing.tipo_precio === 'fijo') {
            // Precio fijo: se cobra una vez independientemente de cantidad
            total += ing.precio_unitario
          } else {
            // Por porción: cobrar solo por porciones adicionales
            const porcionesAdicionales = Math.max(0, cantidad - ing.cantidad_incluida)
            total += porcionesAdicionales * ing.precio_unitario
          }
        }
      })
    })

    return total
  }, [producto, selecciones])

  const totalFinal = precioBase + totalIngredientes

  const handleTamanoChange = (tamanoId: number) => {
    setTamanoSeleccionado(tamanoId)
  }

  const handleIngredienteToggle = (ingrediente: IngredienteRegla, reglaCat: ReglaCategoria) => {
    if (ingrediente.agotado) return

    const currentCantidad = selecciones.get(ingrediente.id) || 0
    const newCantidad = currentCantidad > 0 ? 0 : Math.max(ingrediente.cantidad_minima, 1)

    if (reglaCat.tipo_seleccion === 'unica') {
      // Deseleccionar otros ingredientes de la misma categoría
      const newSelecciones = new Map(selecciones)
      reglaCat.ingredientes.forEach((ing) => {
        if (ing.id !== ingrediente.id) {
          newSelecciones.delete(ing.id)
        }
      })
      if (newCantidad > 0) {
        newSelecciones.set(ingrediente.id, newCantidad)
      }
      setSelecciones(newSelecciones)
    } else {
      // Multiple: solo toggle este ingrediente
      const newSelecciones = new Map(selecciones)
      if (newCantidad > 0) {
        newSelecciones.set(ingrediente.id, newCantidad)
      } else {
        newSelecciones.delete(ingrediente.id)
      }
      setSelecciones(newSelecciones)
    }
  }

  const handleCantidadChange = (ingrediente: IngredienteRegla, delta: number) => {
    if (ingrediente.agotado) return

    const currentCantidad = selecciones.get(ingrediente.id) || 0
    const newCantidad = Math.max(
      ingrediente.cantidad_minima,
      Math.min(ingrediente.cantidad_maxima, currentCantidad + delta * ingrediente.paso_cantidad)
    )

    const newSelecciones = new Map(selecciones)
    if (newCantidad > 0) {
      newSelecciones.set(ingrediente.id, newCantidad)
    } else {
      newSelecciones.delete(ingrediente.id)
    }
    setSelecciones(newSelecciones)
  }

  const validarSelecciones = (): { valido: boolean; mensaje?: string } => {
    if (!producto) return { valido: false, mensaje: 'Producto no cargado' }

    // Validar tamaños (si hay tamaños, debe seleccionarse uno)
    if (producto.tamanos.length > 0 && !tamanoSeleccionado) {
      return { valido: false, mensaje: 'Debes seleccionar un tamaño' }
    }

    // Validar reglas por categoría
    for (const reglaCat of producto.reglas_categorias) {
      const seleccionadosEnCategoria = reglaCat.ingredientes.filter((ing) => {
        const cantidad = selecciones.get(ing.id) || 0
        return cantidad > 0
      }).length

      if (seleccionadosEnCategoria < reglaCat.seleccion_minima) {
        return {
          valido: false,
          mensaje: `Debes seleccionar al menos ${reglaCat.seleccion_minima} ${reglaCat.seleccion_minima === 1 ? 'opción' : 'opciones'} en ${reglaCat.nombre}`,
        }
      }

      if (reglaCat.seleccion_maxima !== null && seleccionadosEnCategoria > reglaCat.seleccion_maxima) {
        return {
          valido: false,
          mensaje: `Puedes seleccionar máximo ${reglaCat.seleccion_maxima} ${reglaCat.seleccion_maxima === 1 ? 'opción' : 'opciones'} en ${reglaCat.nombre}`,
        }
      }
    }

    return { valido: true }
  }

  const handleAgregarAlCarrito = async () => {
    const validacion = validarSelecciones()
    if (!validacion.valido) {
      setError(validacion.mensaje || 'Selección inválida')
      return
    }

    if (!session?.token) {
      setError('Debes iniciar sesión para agregar productos al carrito')
      return
    }

    setAgregandoAlCarrito(true)
    setError(null)

    try {
      // Convertir selecciones a formato Record
      const seleccionesRecord: Record<number, number> = {}
      selecciones.forEach((cantidad, ingredienteId) => {
        if (cantidad > 0) {
          seleccionesRecord[ingredienteId] = cantidad
        }
      })

      await carritoRepository.agregarItem({
        cafeteria_id: cafeteriaId,
        producto_id: productoId,
        tamano_id: tamanoSeleccionado,
        selecciones: seleccionesRecord,
        cantidad: 1,
      })

      setSnackbarMessage('Producto agregado al carrito')
      setSnackbarOpen(true)
      
      // Cerrar después de un breve delay
      setTimeout(() => {
        onClose()
      }, 1000)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Error al agregar al carrito')
    } finally {
      setAgregandoAlCarrito(false)
    }
  }

  return (
    <Dialog
      open={open}
      onClose={onClose}
      maxWidth="md"
      fullWidth
      PaperProps={{
        sx: {
          maxHeight: '90vh',
        },
      }}
    >
      <DialogTitle sx={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', pb: 1 }}>
        <Typography variant="h6" fontWeight={700}>
          {producto?.nombre || 'Cargando...'}
        </Typography>
        <IconButton onClick={onClose} size="small">
          <CloseIcon />
        </IconButton>
      </DialogTitle>

      <DialogContent dividers>
        {loading && (
          <Box sx={{ display: 'flex', justifyContent: 'center', py: 4 }}>
            <CircularProgress />
          </Box>
        )}

        {error && (
          <Alert severity="error" sx={{ mb: 2 }}>
            {error}
          </Alert>
        )}

        {producto && (
          <>
            {/* Imagen del producto */}
            {producto.imagen && (
              <Box sx={{ mb: 3, textAlign: 'center' }}>
                <img
                  src={producto.imagen}
                  alt={producto.nombre}
                  style={{
                    maxWidth: '100%',
                    maxHeight: 200,
                    objectFit: 'contain',
                    borderRadius: 8,
                  }}
                />
              </Box>
            )}

            {/* Selección de tamaño */}
            {producto.tamanos.length > 0 && (
              <Box sx={{ mb: 3 }}>
                <FormControl component="fieldset" fullWidth>
                  <FormLabel component="legend" sx={{ mb: 1, fontWeight: 600 }}>
                    Tamaño
                  </FormLabel>
                  <RadioGroup
                    value={tamanoSeleccionado || ''}
                    onChange={(e) => handleTamanoChange(parseInt(e.target.value, 10))}
                  >
                    {producto.tamanos.map((tamano) => (
                      <FormControlLabel
                        key={tamano.id}
                        value={tamano.id}
                        control={<Radio />}
                        label={
                          <Box sx={{ display: 'flex', justifyContent: 'space-between', width: '100%', pr: 2 }}>
                            <Typography>
                              {tamano.nombre} {tamano.medida} {tamano.unidad_medida}
                            </Typography>
                            <Typography fontWeight={600} color={designTokens.colors.principal}>
                              ${tamano.precio.toFixed(2)}
                            </Typography>
                          </Box>
                        }
                      />
                    ))}
                  </RadioGroup>
                </FormControl>
              </Box>
            )}

            <Divider sx={{ my: 3 }} />

            {/* Reglas por categoría */}
            {producto.reglas_categorias.map((reglaCat) => {
              const seleccionadosEnCategoria = reglaCat.ingredientes.filter((ing) => {
                const cantidad = selecciones.get(ing.id) || 0
                return cantidad > 0
              }).length

              return (
                <Box key={reglaCat.id} sx={{ mb: 3 }}>
                  <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mb: 2 }}>
                    <Typography variant="h6" fontWeight={600}>
                      {reglaCat.nombre}
                    </Typography>
                    {reglaCat.tipo_seleccion === 'unica' && (
                      <Chip label="Selección única" size="small" color="primary" />
                    )}
                    {reglaCat.tipo_seleccion === 'multiple' && (
                      <Chip label="Múltiple" size="small" color="secondary" />
                    )}
                    <Typography variant="caption" color="text.secondary">
                      ({seleccionadosEnCategoria}
                      {reglaCat.seleccion_maxima !== null ? `/${reglaCat.seleccion_maxima}` : ''} seleccionados)
                    </Typography>
                  </Box>

                  {reglaCat.tipo_seleccion === 'unica' && (
                    <RadioGroup
                      value={
                        reglaCat.ingredientes.find((ing) => selecciones.get(ing.id) || 0 > 0)?.id || ''
                      }
                      onChange={(e) => {
                        const ingId = parseInt(e.target.value, 10)
                        const ingrediente = reglaCat.ingredientes.find((ing) => ing.id === ingId)
                        if (ingrediente) {
                          handleIngredienteToggle(ingrediente, reglaCat)
                        }
                      }}
                    >
                      {reglaCat.ingredientes.map((ing) => {
                        const cantidad = selecciones.get(ing.id) || 0
                        const isSelected = cantidad > 0

                        return (
                          <Paper
                            key={ing.id}
                            sx={{
                              p: 2,
                              mb: 1,
                              border: isSelected ? `2px solid ${designTokens.colors.principal}` : '1px solid #e0e0e0',
                              backgroundColor: ing.agotado ? '#f5f5f5' : 'white',
                              opacity: ing.agotado ? 0.6 : 1,
                              cursor: ing.agotado ? 'not-allowed' : 'pointer',
                            }}
                          >
                            <FormControlLabel
                              value={ing.id}
                              control={<Radio disabled={ing.agotado} />}
                              disabled={ing.agotado}
                              label={
                                <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', width: '100%' }}>
                                  <Box>
                                    <Typography fontWeight={isSelected ? 600 : 400}>
                                      {ing.nombre}
                                      {ing.es_recomendado && (
                                        <Chip
                                          label="Recomendado"
                                          size="small"
                                          color="success"
                                          sx={{ ml: 1 }}
                                        />
                                      )}
                                      {ing.agotado && (
                                        <Chip label="Agotado" size="small" color="error" sx={{ ml: 1 }} />
                                      )}
                                    </Typography>
                                    {ing.cantidad_incluida > 0 && (
                                      <Typography variant="caption" color="text.secondary">
                                        {ing.cantidad_incluida} incluido(s)
                                      </Typography>
                                    )}
                                  </Box>
                                  {ing.precio_unitario > 0 && (
                                    <Typography fontWeight={600} color={designTokens.colors.principal}>
                                      +${ing.precio_unitario.toFixed(2)}
                                    </Typography>
                                  )}
                                </Box>
                              }
                            />
                          </Paper>
                        )
                      })}
                    </RadioGroup>
                  )}

                  {reglaCat.tipo_seleccion === 'multiple' && (
                    <Box>
                      {reglaCat.ingredientes.map((ing) => {
                        const cantidad = selecciones.get(ing.id) || 0
                        const isSelected = cantidad > 0

                        return (
                          <Paper
                            key={ing.id}
                            sx={{
                              p: 2,
                              mb: 1,
                              border: isSelected ? `2px solid ${designTokens.colors.principal}` : '1px solid #e0e0e0',
                              backgroundColor: ing.agotado ? '#f5f5f5' : 'white',
                              opacity: ing.agotado ? 0.6 : 1,
                            }}
                          >
                            <Box sx={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                              <Box sx={{ flex: 1 }}>
                                <FormControlLabel
                                  control={
                                    <Checkbox
                                      checked={isSelected}
                                      disabled={ing.agotado}
                                      onChange={() => handleIngredienteToggle(ing, reglaCat)}
                                    />
                                  }
                                  label={
                                    <Box>
                                      <Typography fontWeight={isSelected ? 600 : 400}>
                                        {ing.nombre}
                                        {ing.es_recomendado && (
                                          <Chip
                                            label="Recomendado"
                                            size="small"
                                            color="success"
                                            sx={{ ml: 1 }}
                                          />
                                        )}
                                        {ing.agotado && (
                                          <Chip label="Agotado" size="small" color="error" sx={{ ml: 1 }} />
                                        )}
                                      </Typography>
                                      {ing.cantidad_incluida > 0 && (
                                        <Typography variant="caption" color="text.secondary">
                                          {ing.cantidad_incluida} incluido(s)
                                        </Typography>
                                      )}
                                    </Box>
                                  }
                                />
                              </Box>

                              {ing.permite_cantidad && isSelected && !ing.agotado && (
                                <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                                  <IconButton
                                    size="small"
                                    onClick={() => handleCantidadChange(ing, -1)}
                                    disabled={cantidad <= ing.cantidad_minima}
                                  >
                                    <RemoveIcon />
                                  </IconButton>
                                  <Typography minWidth={30} textAlign="center">
                                    {cantidad}
                                  </Typography>
                                  <IconButton
                                    size="small"
                                    onClick={() => handleCantidadChange(ing, 1)}
                                    disabled={cantidad >= ing.cantidad_maxima}
                                  >
                                    <AddIcon />
                                  </IconButton>
                                </Box>
                              )}

                              {ing.precio_unitario > 0 && (
                                <Typography fontWeight={600} color={designTokens.colors.principal} sx={{ ml: 2 }}>
                                  +${ing.precio_unitario.toFixed(2)}
                                  {ing.permite_cantidad && isSelected && cantidad > 1 && ing.tipo_precio === 'por_porcion' && (
                                    <Typography component="span" variant="caption" display="block">
                                      ×{cantidad}
                                    </Typography>
                                  )}
                                </Typography>
                              )}
                            </Box>
                          </Paper>
                        )
                      })}
                    </Box>
                  )}
                </Box>
              )
            })}
          </>
        )}
      </DialogContent>

      <DialogActions sx={{ p: 2, justifyContent: 'space-between', borderTop: '1px solid #e0e0e0' }}>
        <Box>
          <Typography variant="caption" color="text.secondary">
            Total
          </Typography>
          <Typography variant="h5" fontWeight={700} color={designTokens.colors.principal}>
            ${totalFinal.toFixed(2)}
          </Typography>
        </Box>
        <Box sx={{ display: 'flex', gap: 2 }}>
          <Button onClick={onClose} variant="outlined">
            Cancelar
          </Button>
          <Button
            onClick={handleAgregarAlCarrito}
            variant="contained"
            disabled={loading || !producto || agregandoAlCarrito}
            sx={{
              backgroundColor: designTokens.colors.principal,
              '&:hover': {
                backgroundColor: designTokens.colors.sidebarDark,
              },
            }}
          >
            {agregandoAlCarrito ? 'Agregando...' : 'Agregar al carrito'}
          </Button>
        </Box>
      </DialogActions>

      <Snackbar
        open={snackbarOpen}
        autoHideDuration={3000}
        onClose={() => setSnackbarOpen(false)}
        message={snackbarMessage}
        anchorOrigin={{ vertical: 'bottom', horizontal: 'center' }}
      />
    </Dialog>
  )
}
