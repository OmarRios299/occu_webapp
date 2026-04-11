import { useState, useEffect, useMemo } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import {
  Container,
  Box,
  Typography,
  Paper,
  CircularProgress,
  Alert,
  Button,
  IconButton,
  Grid,
  Card,
  CardMedia,
  CardContent,
  Divider,
} from '@mui/material'
import ArrowBackIcon from '@mui/icons-material/ArrowBack'
import { designTokens } from '@/theme/tokens'
import { RestCafeteriaMenuRepository } from '../services/RestCafeteriaMenuRepository'
import type { CategoriaMenu, ProductoMenu } from '../types'
import { ProductoPersonalizacionDialog } from '../components/ProductoPersonalizacionDialog'

export function CafeteriaMenuPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const [menu, setMenu] = useState<CategoriaMenu[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [selectedProducto, setSelectedProducto] = useState<{ producto: ProductoMenu; categoriaId: number } | null>(null)
  const [personalizacionOpen, setPersonalizacionOpen] = useState(false)

  const repository = useMemo(() => new RestCafeteriaMenuRepository(), [])
  const cafeteriaId = id ? parseInt(id, 10) : null

  useEffect(() => {
    if (!cafeteriaId || isNaN(cafeteriaId)) {
      setError('ID de cafetería inválido')
      setLoading(false)
      return
    }

    setLoading(true)
    setError(null)
    setMenu([])
    repository
      .getMenu(cafeteriaId)
      .then((data) => {
        setMenu(data || [])
      })
      .catch((err) => {
        setError(err instanceof Error ? err.message : 'Error al cargar el menú')
        setMenu([])
      })
      .finally(() => {
        setLoading(false)
      })
  }, [cafeteriaId, repository])

  const handleProductoClick = (producto: ProductoMenu, categoriaId: number) => {
    setSelectedProducto({ producto, categoriaId })
    setPersonalizacionOpen(true)
  }

  const handleClosePersonalizacion = () => {
    setPersonalizacionOpen(false)
    setSelectedProducto(null)
  }

  if (!cafeteriaId || isNaN(cafeteriaId)) {
    return (
      <Container maxWidth="lg" sx={{ py: 4 }}>
        <Alert severity="error">ID de cafetería inválido</Alert>
      </Container>
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
            <Typography variant="h5" fontWeight={700}>
              Menú
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

        {!loading && !error && menu.length === 0 && (
          <Paper sx={{ p: 4, textAlign: 'center' }}>
            <Typography variant="h6" color="text.secondary" gutterBottom>
              Esta cafetería aún no tiene menú publicado
            </Typography>
            <Button variant="outlined" onClick={() => navigate(-1)} sx={{ mt: 2 }}>
              Volver
            </Button>
          </Paper>
        )}

        {!loading && !error && menu.length > 0 && (
          <>
            {menu.map((categoria) => (
              <Box key={categoria.id} sx={{ mb: 4 }}>
                <Typography
                  variant="h4"
                  fontWeight={700}
                  sx={{
                    mb: 3,
                    color: designTokens.colors.cuarto,
                    borderBottom: `3px solid ${designTokens.colors.principal}`,
                    pb: 1,
                  }}
                >
                  {categoria.nombre}
                </Typography>

                {categoria.subcategorias.map((subcategoria) => (
                  <Box key={subcategoria.id} sx={{ mb: 4 }}>
                    <Typography
                      variant="h6"
                      fontWeight={600}
                      sx={{
                        mb: 2,
                        color: designTokens.colors.texto,
                        pl: 1,
                      }}
                    >
                      {subcategoria.nombre}
                    </Typography>

                    <Grid container spacing={2}>
                      {subcategoria.productos.map((producto) => (
                        <Grid item xs={12} sm={6} md={4} key={producto.id}>
                          <Card
                            sx={{
                              height: '100%',
                              display: 'flex',
                              flexDirection: 'column',
                              cursor: 'pointer',
                              transition: 'transform 0.2s, box-shadow 0.2s',
                              '&:hover': {
                                transform: 'translateY(-4px)',
                                boxShadow: 4,
                              },
                            }}
                            onClick={() => handleProductoClick(producto, categoria.id)}
                          >
                            {producto.imagen && (
                              <CardMedia
                                component="img"
                                height="200"
                                image={producto.imagen}
                                alt={producto.nombre}
                                sx={{ objectFit: 'cover' }}
                              />
                            )}
                            <CardContent sx={{ flexGrow: 1 }}>
                              <Typography variant="h6" fontWeight={600} gutterBottom>
                                {producto.nombre}
                              </Typography>
                              <Typography variant="h6" color={designTokens.colors.principal} fontWeight={700}>
                                ${Number(producto.precio_base).toFixed(2)}
                              </Typography>
                            </CardContent>
                          </Card>
                        </Grid>
                      ))}
                    </Grid>
                  </Box>
                ))}
              </Box>
            ))}
          </>
        )}
      </Container>

      {/* Dialog de personalización */}
      {selectedProducto && (
        <ProductoPersonalizacionDialog
          open={personalizacionOpen}
          onClose={handleClosePersonalizacion}
          cafeteriaId={cafeteriaId}
          productoId={selectedProducto.producto.id}
        />
      )}
    </Box>
  )
}
