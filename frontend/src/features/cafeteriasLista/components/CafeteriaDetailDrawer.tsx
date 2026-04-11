import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import {
  Drawer,
  Box,
  Typography,
  IconButton,
  Chip,
  CircularProgress,
  Alert,
  useTheme,
  useMediaQuery,
  Dialog,
  DialogContent,
  DialogTitle,
  Button,
} from '@mui/material'
import CloseIcon from '@mui/icons-material/Close'
import LocationOnIcon from '@mui/icons-material/LocationOn'
import PhoneIcon from '@mui/icons-material/Phone'
import EmailIcon from '@mui/icons-material/Email'
import AccessTimeIcon from '@mui/icons-material/AccessTime'
import CheckCircleIcon from '@mui/icons-material/CheckCircle'
import CancelIcon from '@mui/icons-material/Cancel'
import MenuBookIcon from '@mui/icons-material/MenuBook'
import ShareIcon from '@mui/icons-material/Share'
import { designTokens } from '@/theme/tokens'
import type { CafeteriaDetail, Comentario, Paginated } from '../types'
import { ComentariosSection } from './ComentariosSection'

type CafeteriaDetailDrawerProps = {
  open: boolean
  onClose: () => void
  cafeteriaId: number | null
  onLoadDetail: (id: number) => Promise<CafeteriaDetail>
  onLoadComentarios: (cafeteriaId: number, page: number, pageSize: number) => Promise<Paginated<Comentario>>
  onCreateComentario: (cafeteriaId: number, comentario: string, token: string) => Promise<void>
}

export function CafeteriaDetailDrawer({
  open,
  onClose,
  cafeteriaId,
  onLoadDetail,
  onLoadComentarios,
  onCreateComentario,
}: CafeteriaDetailDrawerProps) {
  const navigate = useNavigate()
  const theme = useTheme()
  const isMobile = useMediaQuery(theme.breakpoints.down('lg'))
  const anchor = isMobile ? 'bottom' : 'right'
  const [detail, setDetail] = useState<CafeteriaDetail | null>(null)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [selectedImage, setSelectedImage] = useState<string | null>(null)
  const [selectedImageIndex, setSelectedImageIndex] = useState<number>(0)

  useEffect(() => {
    if (open && cafeteriaId) {
      setLoading(true)
      setError(null)
      setDetail(null)
      onLoadDetail(cafeteriaId)
        .then((data) => {
          setDetail(data)
        })
        .catch((err) => {
          setError(err instanceof Error ? err.message : 'Error al cargar detalles')
        })
        .finally(() => {
          setLoading(false)
        })
    } else {
      setDetail(null)
      setError(null)
    }
  }, [open, cafeteriaId, onLoadDetail])

  if (!cafeteriaId) return null

  return (
    <>
      <Drawer
        variant="temporary"
        anchor={anchor}
        open={open}
        onClose={onClose}
        ModalProps={{
          disableScrollLock: false,
          slotProps: {
            root: { sx: { zIndex: 1400 } },
          } as { root?: { sx?: { zIndex: number } } },
        }}
        PaperProps={{
          sx: {
            width: { xs: '100%', lg: 500 },
            height: '100vh',
            maxHeight: '90vh',
            boxSizing: 'border-box',
            borderTopLeftRadius: { xs: designTokens.spacing.borderRadius.large, lg: 0 },
            borderTopRightRadius: { xs: designTokens.spacing.borderRadius.large, lg: 0 },
            pb: { xs: `calc(${designTokens.spacing.bottomNavHeight} + env(safe-area-inset-bottom))`, lg: 0 },
          },
        }}
      >
        <Box sx={{ display: 'flex', flexDirection: 'column', height: '100%' }}>
          {/* Header */}
          <Box
            sx={{
              p: 2,
              backgroundColor: designTokens.colors.principal,
              color: 'white',
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'space-between',
            }}
          >
            <Typography variant="h6" fontWeight={700}>
              {detail?.nombre || 'Cargando...'}
            </Typography>
            <IconButton onClick={onClose} sx={{ color: 'white' }}>
              <CloseIcon />
            </IconButton>
          </Box>

          {/* Contenido scrollable: fondo un poco oscuro para resaltar secciones en blanco */}
          <Box
            sx={{
              flexGrow: 1,
              overflowY: 'auto',
              p: 2,
              backgroundColor: designTokens.colors.grisClaro,
              minHeight: '100%',
            }}
          >
            {loading && (
              <Box sx={{ display: 'flex', justifyContent: 'center', py: 8 }}>
                <CircularProgress />
              </Box>
            )}

            {error && (
              <Alert severity="error" sx={{ mb: 2 }}>
                {error}
              </Alert>
            )}

            {detail && (
              <>
                {/* Galería estilo legacy (layouts 1–5+, tipo Google Maps/Airbnb) */}
                {(() => {
                  const allImages = detail.imagen ? [detail.imagen, ...detail.imagenes] : detail.imagenes
                  if (allImages.length === 0) return null

                  const n = allImages.length
                  let layout: '1' | '2' | '3' | '4' | '5plus' = '1'
                  let visibleCount = n
                  if (n === 2) layout = '2'
                  else if (n === 3) layout = '3'
                  else if (n === 4) layout = '4'
                  else if (n >= 5) {
                    layout = '5plus'
                    visibleCount = 5
                  }

                  const gridSx: Record<string, unknown> =
                    layout === '1'
                      ? { gridTemplateColumns: '1fr', gridTemplateRows: '1fr' }
                      : layout === '2'
                        ? { gridTemplateColumns: '1fr 1fr', gridTemplateRows: '1fr' }
                        : layout === '3' || layout === '4'
                          ? { gridTemplateColumns: '2fr 1fr', gridTemplateRows: '1fr 1fr' }
                          : { gridTemplateColumns: '2fr 1fr 1fr', gridTemplateRows: '1fr 1fr' }

                  return (
                    <Box sx={{ position: 'relative', mb: 2 }}>
                      {allImages.length > 1 && (
                        <Box
                          sx={{
                            position: 'absolute',
                            top: 12,
                            left: 12,
                            backgroundColor: 'rgba(0,0,0,0.75)',
                            backdropFilter: 'blur(12px)',
                            color: 'white',
                            px: 1.5,
                            py: 0.75,
                            borderRadius: '24px',
                            fontSize: '0.875rem',
                            fontWeight: 600,
                            zIndex: 10,
                          }}
                        >
                          {allImages.length} fotos
                        </Box>
                      )}
                      <Box
                        sx={{
                          display: 'grid',
                          gap: 1,
                          height: { xs: 260, sm: 320 },
                          borderRadius: 2,
                          overflow: 'hidden',
                          cursor: 'pointer',
                          ...gridSx,
                        }}
                      >
                        {allImages.slice(0, visibleCount).map((img, index) => {
                          const isLast = index === visibleCount - 1 && n > visibleCount
                          const remaining = n - visibleCount
                          return (
                            <Box
                              key={index}
                              sx={{
                                position: 'relative',
                                overflow: 'hidden',
                                bgcolor: '#f0f0f0',
                                gridRow: layout !== '1' && layout !== '2' && index === 0 ? '1 / 3' : undefined,
                                '&:hover img': { transform: 'scale(1.05)' },
                                '& img': {
                                  width: '100%',
                                  height: '100%',
                                  objectFit: 'cover',
                                  transition: 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)',
                                },
                              }}
                              onClick={() => {
                                setSelectedImageIndex(index)
                                setSelectedImage(img)
                              }}
                            >
                              <img
                                src={img}
                                alt={`${detail.nombre} - Imagen ${index + 1}`}
                                loading={index === 0 ? 'eager' : 'lazy'}
                              />
                              {isLast && remaining > 0 && (
                                <Box
                                  sx={{
                                    position: 'absolute',
                                    inset: 0,
                                    bgcolor: 'rgba(0,0,0,0.7)',
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    color: 'white',
                                    fontSize: '1.25rem',
                                    fontWeight: 700,
                                    '&:hover': { bgcolor: 'rgba(0,0,0,0.8)' },
                                  }}
                                  onClick={(e) => {
                                    e.stopPropagation()
                                    const nextImg = allImages[visibleCount]
                                    if (nextImg) {
                                      setSelectedImageIndex(visibleCount)
                                      setSelectedImage(nextImg)
                                    }
                                  }}
                                >
                                  +{remaining} más
                                </Box>
                              )}
                            </Box>
                          )
                        })}
                      </Box>
                    </Box>
                  )
                })()}

                {/* Estado y horario — sección en blanco */}
                <Box
                  sx={{
                    display: 'flex',
                    alignItems: 'center',
                    gap: 2,
                    mb: 2,
                    p: 2,
                    borderRadius: 2,
                    backgroundColor: designTokens.colors.blanco,
                    boxShadow: '0 1px 4px rgba(0,0,0,0.08)',
                  }}
                >
                  <Chip
                    icon={detail.isOpenNow ? <CheckCircleIcon /> : <CancelIcon />}
                    label={detail.isOpenNow ? 'Abierto' : 'Cerrado'}
                    color={detail.isOpenNow ? 'success' : 'error'}
                    sx={{ fontWeight: 600 }}
                  />
                  <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, color: 'text.secondary' }}>
                    <AccessTimeIcon fontSize="small" />
                    <Typography variant="body2">{detail.todayScheduleLabel}</Typography>
                  </Box>
                </Box>

                {/* Información de contacto — sección en blanco */}
                <Box
                  sx={{
                    mb: 2,
                    p: 2,
                    borderRadius: 2,
                    backgroundColor: designTokens.colors.blanco,
                    boxShadow: '0 1px 4px rgba(0,0,0,0.08)',
                  }}
                >
                  <Typography variant="h6" fontWeight={600} sx={{ mb: 2, color: designTokens.colors.texto }}>
                    Información
                  </Typography>

                  {detail.direccion && (
                    <Box sx={{ display: 'flex', alignItems: 'flex-start', gap: 1, mb: 2 }}>
                      <LocationOnIcon sx={{ color: designTokens.colors.principal, mt: 0.5 }} />
                      <Typography variant="body2" sx={{ flex: 1 }}>
                        {detail.direccion}
                        {detail.ciudad && `, ${detail.ciudad}`}
                        {detail.entidad_federativa && `, ${detail.entidad_federativa}`}
                        {detail.pais && `, ${detail.pais}`}
                      </Typography>
                    </Box>
                  )}

                  {detail.telefono && (
                    <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mb: 2 }}>
                      <PhoneIcon sx={{ color: designTokens.colors.principal }} />
                      <Typography variant="body2" component="a" href={`tel:${detail.telefono}`}>
                        {detail.telefono}
                      </Typography>
                    </Box>
                  )}

                  {detail.correo && (
                    <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mb: 2 }}>
                      <EmailIcon sx={{ color: designTokens.colors.principal }} />
                      <Typography variant="body2" component="a" href={`mailto:${detail.correo}`}>
                        {detail.correo}
                      </Typography>
                    </Box>
                  )}

                  {detail.descripcion && (
                    <Box sx={{ mt: 2 }}>
                      <Typography variant="body2" color="text.secondary">
                        {detail.descripcion}
                      </Typography>
                    </Box>
                  )}

                  {/* Botones de acción (mismo estilo: outlined principal) */}
                  <Box sx={{ display: 'flex', gap: 2, mt: 3, flexWrap: 'wrap' }}>
                    <Button
                      variant="outlined"
                      startIcon={<MenuBookIcon />}
                      onClick={(e) => {
                        e.preventDefault()
                        e.stopPropagation()
                        if (detail?.id) {
                          navigate(`/cafeterias/${detail.id}/menu`)
                          onClose()
                        }
                      }}
                      sx={{
                        borderColor: designTokens.colors.principal,
                        color: designTokens.colors.principal,
                        textTransform: 'none',
                        '&:hover': {
                          borderColor: designTokens.colors.sidebarDark,
                          backgroundColor: `${designTokens.colors.principal}10`,
                        },
                      }}
                    >
                      Ver Menú
                    </Button>
                    <Button
                      variant="outlined"
                      startIcon={<ShareIcon />}
                      onClick={() => {
                        if (navigator.share) {
                          navigator.share({
                            title: detail.nombre,
                            text: detail.descripcion || `Visita ${detail.nombre}`,
                            url: window.location.href,
                          })
                        } else {
                          navigator.clipboard.writeText(window.location.href)
                        }
                      }}
                      sx={{
                        borderColor: designTokens.colors.principal,
                        color: designTokens.colors.principal,
                        textTransform: 'none',
                        '&:hover': {
                          borderColor: designTokens.colors.sidebarDark,
                          backgroundColor: `${designTokens.colors.principal}10`,
                        },
                      }}
                    >
                      Compartir
                    </Button>
                  </Box>

                  {/* Botón de ubicación (si tiene coordenadas) */}
                  {detail.latitud && detail.longitud && (
                    <Box sx={{ mt: 3 }}>
                      <Button
                        variant="outlined"
                        startIcon={<LocationOnIcon />}
                        onClick={() => {
                          window.open(
                            `https://www.google.com/maps/search/?api=1&query=${detail.latitud},${detail.longitud}`,
                            '_blank'
                          )
                        }}
                        sx={{
                          borderColor: designTokens.colors.principal,
                          color: designTokens.colors.principal,
                          textTransform: 'none',
                          '&:hover': {
                            borderColor: designTokens.colors.sidebarDark,
                            backgroundColor: `${designTokens.colors.principal}10`,
                          },
                        }}
                      >
                        Ver ubicación
                      </Button>
                    </Box>
                  )}
                </Box>

                {/* Servicios: solo iconos, sin fondo gris ni títulos */}
                {detail.servicios && detail.servicios.length > 0 && (
                  <Box
                    sx={{
                      mb: 2,
                      p: 2,
                      borderRadius: 2,
                      backgroundColor: designTokens.colors.blanco,
                      boxShadow: '0 1px 4px rgba(0,0,0,0.08)',
                    }}
                  >
                    <Typography variant="h6" fontWeight={600} sx={{ mb: 2, color: designTokens.colors.texto }}>
                      Servicios
                    </Typography>
                    <Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 2 }}>
                      {detail.servicios.map((servicio) => (
                        <Box
                          key={servicio.id}
                          sx={{ display: 'flex', alignItems: 'center', gap: 1 }}
                          title={servicio.nombre}
                        >
                          {servicio.imagen ? (
                            <img
                              src={servicio.imagen}
                              alt=""
                              style={{ width: 40, height: 40, objectFit: 'contain' }}
                            />
                          ) : (
                            <Box
                              sx={{
                                width: 40,
                                height: 40,
                                borderRadius: 1,
                                bgcolor: designTokens.colors.grisClaro,
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                              }}
                            >
                              <Typography variant="caption" color="text.secondary">
                                {servicio.nombre.slice(0, 1)}
                              </Typography>
                            </Box>
                          )}
                        </Box>
                      ))}
                    </Box>
                  </Box>
                )}

                {/* Sección de comentarios — sección en blanco */}
                <Box
                  sx={{
                    p: 2,
                    borderRadius: 2,
                    backgroundColor: designTokens.colors.blanco,
                    boxShadow: '0 1px 4px rgba(0,0,0,0.08)',
                  }}
                >
                  <ComentariosSection
                  cafeteriaId={detail.id}
                  onLoadComentarios={onLoadComentarios}
                  onCreateComentario={onCreateComentario}
                  />
                </Box>
              </>
            )}
          </Box>
        </Box>
      </Drawer>

      {/* Dialog para imagen ampliada */}
      <Dialog
        open={Boolean(selectedImage)}
        onClose={() => setSelectedImage(null)}
        maxWidth="lg"
        fullWidth
        PaperProps={{
          sx: {
            backgroundColor: 'transparent',
            boxShadow: 'none',
          },
        }}
      >
        <DialogTitle sx={{ textAlign: 'right', pb: 0 }}>
          <IconButton onClick={() => setSelectedImage(null)} sx={{ color: 'white' }}>
            <CloseIcon />
          </IconButton>
        </DialogTitle>
        <DialogContent sx={{ p: 0, textAlign: 'center', position: 'relative' }}>
          {selectedImage && detail && (
            <>
              <img
                src={selectedImage}
                alt="Ampliación"
                style={{
                  maxWidth: '100%',
                  maxHeight: '90vh',
                  objectFit: 'contain',
                  borderRadius: 8,
                }}
              />
              {/* Navegación entre imágenes */}
              {(() => {
                const allImages = detail.imagen ? [detail.imagen, ...detail.imagenes] : detail.imagenes
                const hasPrev = selectedImageIndex > 0
                const hasNext = selectedImageIndex < allImages.length - 1

                return (
                  <>
                    {hasPrev && (
                      <IconButton
                        onClick={(e) => {
                          e.stopPropagation()
                          const prevImage = allImages[selectedImageIndex - 1]
                          if (prevImage) {
                            setSelectedImageIndex(selectedImageIndex - 1)
                            setSelectedImage(prevImage)
                          }
                        }}
                        sx={{
                          position: 'absolute',
                          left: 16,
                          top: '50%',
                          transform: 'translateY(-50%)',
                          backgroundColor: 'rgba(0, 0, 0, 0.5)',
                          color: 'white',
                          '&:hover': { backgroundColor: 'rgba(0, 0, 0, 0.7)' },
                        }}
                      >
                        ←
                      </IconButton>
                    )}
                    {hasNext && (
                      <IconButton
                        onClick={(e) => {
                          e.stopPropagation()
                          const nextImage = allImages[selectedImageIndex + 1]
                          if (nextImage) {
                            setSelectedImageIndex(selectedImageIndex + 1)
                            setSelectedImage(nextImage)
                          }
                        }}
                        sx={{
                          position: 'absolute',
                          right: 16,
                          top: '50%',
                          transform: 'translateY(-50%)',
                          backgroundColor: 'rgba(0, 0, 0, 0.5)',
                          color: 'white',
                          '&:hover': { backgroundColor: 'rgba(0, 0, 0, 0.7)' },
                        }}
                      >
                        →
                      </IconButton>
                    )}
                  </>
                )
              })()}
            </>
          )}
        </DialogContent>
      </Dialog>
    </>
  )
}
