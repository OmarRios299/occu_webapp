import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import {
  Container,
  Box,
  Typography,
  Paper,
  CircularProgress,
  Alert,
  Button,
  IconButton,
  Divider,
  Card,
  CardContent,
  CardMedia,
  Chip,
  Grid,
} from '@mui/material'
import ArrowBackIcon from '@mui/icons-material/ArrowBack'
import ShoppingCartIcon from '@mui/icons-material/ShoppingCart'
import { designTokens } from '@/theme/tokens'
import { useAuth } from '@/store/auth/AuthProvider'
import { RestCarritoRepository } from '../services/RestCarritoRepository'
import type { Carrito, CarritoItem } from '../types'

export function CarritoPage() {
  const navigate = useNavigate()
  const { session } = useAuth()
  const [carrito, setCarrito] = useState<Carrito | null>(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  const repository = new RestCarritoRepository(() => session?.accessToken ?? null)

  useEffect(() => {
    if (!session?.accessToken) {
      setLoading(false)
      setCarrito(null)
      setError('Debes iniciar sesión para ver tu carrito')
      return
    }
    setLoading(true)
    setError(null)
    repository
      .obtenerCarrito()
      .then((data) => {
        setCarrito(data)
      })
      .catch((err) => {
        setError(err instanceof Error ? err.message : 'Error al cargar el carrito')
      })
      .finally(() => {
        setLoading(false)
      })
  }, [session?.accessToken])

  const renderPersonalizaciones = (item: CarritoItem) => {
    if (item.personalizaciones.length === 0) {
      return null
    }

    return (
      <Box sx={{ mt: 1, pl: 2 }}>
        <Typography variant="caption" color="text.secondary" sx={{ display: 'block', mb: 0.5 }}>
          Personalizaciones:
        </Typography>
        {item.personalizaciones.map((pers) => (
          <Box key={pers.id_ingrediente} sx={{ display: 'flex', alignItems: 'center', gap: 1, mb: 0.5 }}>
            <Typography variant="body2" sx={{ fontSize: '0.875rem' }}>
              • {pers.nombre}
              {pers.cantidad > 1 && ` (x${pers.cantidad})`}
            </Typography>
            {pers.monto_total > 0 && (
              <Typography variant="caption" color="text.secondary">
                +${pers.monto_total.toFixed(2)}
              </Typography>
            )}
          </Box>
        ))}
      </Box>
    )
  }

  return (
    <Box sx={{ minHeight: '100vh', backgroundColor: designTokens.colors.bgBody }}>
      {/* Header */}
      <Box
        sx={{
          backgroundColor: designTokens.colors.principal,
          color: 'white',
          py: 2,
          position: 'sticky',
          top: 0,
          zIndex: 1000,
        }}
      >
        <Container maxWidth="lg">
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 2 }}>
            <IconButton onClick={() => navigate(-1)} sx={{ color: 'white' }}>
              <ArrowBackIcon />
            </IconButton>
            <ShoppingCartIcon />
            <Typography variant="h5" fontWeight={700}>
              Carrito
            </Typography>
          </Box>
        </Container>
      </Box>

      {/* Contenido */}
      <Container maxWidth="lg" sx={{ py: 4 }}>
        {loading && (
          <Box sx={{ display: 'flex', justifyContent: 'center', py: 8 }}>
            <CircularProgress />
          </Box>
        )}

        {error && (
          <Alert severity="error" sx={{ mb: 3 }}>
            {error}
          </Alert>
        )}

        {!loading && !error && !carrito && (
          <Paper sx={{ p: 4, textAlign: 'center' }}>
            <ShoppingCartIcon sx={{ fontSize: 64, color: 'text.secondary', mb: 2 }} />
            <Typography variant="h6" color="text.secondary" gutterBottom>
              Tu carrito está vacío
            </Typography>
            <Typography variant="body2" color="text.secondary" sx={{ mb: 3 }}>
              Agrega productos desde el menú de una cafetería
            </Typography>
            <Button variant="contained" onClick={() => navigate('/cafeterias_lista')}>
              Ver cafeterías
            </Button>
          </Paper>
        )}

        {!loading && !error && carrito && (
          <>
            {/* Información de la cafetería */}
            <Paper sx={{ p: 2, mb: 3, display: 'flex', alignItems: 'center', gap: 2 }}>
              {carrito.cafeteria_imagen && (
                <img
                  src={carrito.cafeteria_imagen}
                  alt={carrito.cafeteria_nombre}
                  style={{
                    width: 60,
                    height: 60,
                    objectFit: 'cover',
                    borderRadius: 8,
                  }}
                />
              )}
              <Box sx={{ flex: 1 }}>
                <Typography variant="h6" fontWeight={600}>
                  {carrito.cafeteria_nombre}
                </Typography>
                <Typography variant="caption" color="text.secondary">
                  {carrito.items.length} {carrito.items.length === 1 ? 'producto' : 'productos'}
                </Typography>
              </Box>
            </Paper>

            {/* Items del carrito */}
            <Grid container spacing={2}>
              <Grid item xs={12} md={8}>
                {carrito.items.map((item) => (
                  <Card key={item.item_id} sx={{ mb: 2 }}>
                    <CardContent>
                      <Box sx={{ display: 'flex', gap: 2 }}>
                        {item.producto_imagen && (
                          <CardMedia
                            component="img"
                            sx={{
                              width: 100,
                              height: 100,
                              objectFit: 'cover',
                              borderRadius: 1,
                            }}
                            image={item.producto_imagen}
                            alt={item.producto_nombre}
                          />
                        )}
                        <Box sx={{ flex: 1 }}>
                          <Typography variant="h6" fontWeight={600} gutterBottom>
                            {item.producto_nombre}
                          </Typography>
                          {item.tamano_nombre && (
                            <Chip
                              label={`${item.tamano_nombre} ${item.tamano_medida} ${item.tamano_unidad_medida}`}
                              size="small"
                              sx={{ mb: 1 }}
                            />
                          )}
                          <Typography variant="body2" color="text.secondary">
                            Cantidad: {item.cantidad}
                          </Typography>
                          {renderPersonalizaciones(item)}
                          <Typography variant="h6" color={designTokens.colors.principal} sx={{ mt: 1 }}>
                            ${item.subtotal.toFixed(2)}
                          </Typography>
                        </Box>
                      </Box>
                    </CardContent>
                  </Card>
                ))}
              </Grid>

              {/* Resumen */}
              <Grid item xs={12} md={4}>
                <Paper sx={{ p: 3, position: 'sticky', top: 100 }}>
                  <Typography variant="h6" fontWeight={600} gutterBottom>
                    Resumen
                  </Typography>
                  <Divider sx={{ my: 2 }} />
                  <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 1 }}>
                    <Typography variant="body2" color="text.secondary">
                      Subtotal ({carrito.items.length} {carrito.items.length === 1 ? 'producto' : 'productos'})
                    </Typography>
                    <Typography variant="body2" fontWeight={600}>
                      ${carrito.total.toFixed(2)}
                    </Typography>
                  </Box>
                  <Divider sx={{ my: 2 }} />
                  <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 3 }}>
                    <Typography variant="h6" fontWeight={700}>
                      Total
                    </Typography>
                    <Typography variant="h6" fontWeight={700} color={designTokens.colors.principal}>
                      ${carrito.total.toFixed(2)}
                    </Typography>
                  </Box>
                  <Button
                    variant="contained"
                    fullWidth
                    size="large"
                    disabled
                    sx={{
                      backgroundColor: designTokens.colors.principal,
                      '&:hover': {
                        backgroundColor: designTokens.colors.sidebarDark,
                      },
                    }}
                  >
                    Proceder al pago
                  </Button>
                  <Typography variant="caption" color="text.secondary" sx={{ display: 'block', mt: 1, textAlign: 'center' }}>
                    (Funcionalidad de pago pendiente)
                  </Typography>
                </Paper>
              </Grid>
            </Grid>
          </>
        )}
      </Container>
    </Box>
  )
}
