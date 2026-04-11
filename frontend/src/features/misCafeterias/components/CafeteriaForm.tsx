import React, { useState, useEffect, useMemo } from 'react'
import { useNavigate } from 'react-router-dom'
import {
  Box,
  TextField,
  Button,
  FormControl,
  InputLabel,
  Select,
  MenuItem,
  FormControlLabel,
  Switch,
  Typography,
  Alert,
  CircularProgress,
  Grid,
  Paper,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  IconButton,
  Accordion,
  AccordionSummary,
  AccordionDetails,
  useTheme,
  useMediaQuery,
  Divider,
} from '@mui/material'
import LocationOnIcon from '@mui/icons-material/LocationOn'
import CloudUploadIcon from '@mui/icons-material/CloudUpload'
import DeleteIcon from '@mui/icons-material/Delete'
import ExpandMoreIcon from '@mui/icons-material/ExpandMore'
import InfoIcon from '@mui/icons-material/Info'
import MapIcon from '@mui/icons-material/Map'
import ScheduleIcon from '@mui/icons-material/Schedule'
import ContactPhoneIcon from '@mui/icons-material/ContactPhone'
import DescriptionIcon from '@mui/icons-material/Description'
import ImageIcon from '@mui/icons-material/Image'
import { designTokens } from '@/theme/tokens'
import { LocationPickerModal } from './LocationPickerModal'

export type CafeteriaFormData = {
  nombre: string
  correo_electronico?: string
  telefono: string
  direccion: string
  id_ciudad: number | null
  latitud: string
  longitud: string
  horario_apertura?: string
  horario_cierre?: string
  horario_diferente: 'SI' | 'NO' // 'SI' = horario detallado, 'NO' = horario simple
  horarios_detallados?: Record<string, { apertura: string; cierre: string; cerrado: 'SI' | 'NO' }>
  descripcion?: string
  imagen?: File | null
}

type Ciudad = {
  id: number
  nombre: string
}

type CafeteriaFormProps = {
  cafeteriaId?: number | null
  initialData?: Partial<CafeteriaFormData>
  ciudades: Ciudad[]
  onLoadCiudadCoordenadas: (ciudadId: number) => Promise<Array<{ lat: number; lng: number }> | null>
  onSubmit: (data: CafeteriaFormData) => Promise<void>
  onUploadImage?: (file: File) => Promise<{ ruta: string; url: string }>
  loading?: boolean
  imagenUrl?: string // URL de la imagen actual (si existe)
}

export function CafeteriaForm({
  cafeteriaId,
  initialData,
  ciudades,
  onLoadCiudadCoordenadas,
  onSubmit,
  onUploadImage,
  loading = false,
  imagenUrl,
}: CafeteriaFormProps) {
  const navigate = useNavigate()
  
  // Inicializar formData solo una vez o cuando cambia initialData significativamente
  const [formData, setFormData] = useState<CafeteriaFormData>(() => {
    const baseData = {
      nombre: '',
      telefono: '',
      direccion: '',
      id_ciudad: null,
      latitud: '',
      longitud: '',
      horario_diferente: 'NO' as const,
      ...initialData,
    }
    
    // Si horario_diferente es 'SI' pero no hay horarios_detallados, inicializarlos
    if (baseData.horario_diferente === 'SI' && !baseData.horarios_detallados) {
      const diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']
      const horariosIniciales: Record<string, { apertura: string; cierre: string; cerrado: 'SI' | 'NO' }> = {}
      diasSemana.forEach((dia, index) => {
        horariosIniciales[dia] = {
          apertura: index === 0 ? '09:00' : '',
          cierre: index === 0 ? '18:00' : '',
          cerrado: index === 0 ? 'NO' : 'SI',
        }
      })
      baseData.horarios_detallados = horariosIniciales
    }
    
    return baseData
  })

  // Actualizar formData cuando initialData cambia (solo en edición, una vez al montar)
  useEffect(() => {
    if (initialData && cafeteriaId && initialData.nombre) {
      // Solo actualizar si el formulario está vacío (primera carga)
      setFormData((prev) => {
        if (!prev.nombre && initialData.nombre) {
          const newData = {
            nombre: '',
            telefono: '',
            direccion: '',
            id_ciudad: null,
            latitud: '',
            longitud: '',
            horario_diferente: 'NO' as const,
            ...initialData,
          }
          
          // Si horario_diferente es 'SI' pero no hay horarios_detallados, inicializarlos
          if (newData.horario_diferente === 'SI' && !newData.horarios_detallados) {
            const diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']
            const horariosIniciales: Record<string, { apertura: string; cierre: string; cerrado: 'SI' | 'NO' }> = {}
            diasSemana.forEach((dia, index) => {
              horariosIniciales[dia] = {
                apertura: index === 0 ? '09:00' : '',
                cierre: index === 0 ? '18:00' : '',
                cerrado: index === 0 ? 'NO' : 'SI',
              }
            })
            newData.horarios_detallados = horariosIniciales
          }
          
          return newData
        }
        return prev
      })
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [cafeteriaId]) // Solo cuando cambia el ID de cafetería

  const [locationModalOpen, setLocationModalOpen] = useState(false)
  const [ciudadCoordenadas, setCiudadCoordenadas] = useState<Array<{ lat: number; lng: number }> | null>(null)
  const [errors, setErrors] = useState<Record<string, string>>({})
  const [submitting, setSubmitting] = useState(false)
  const [imagePreview, setImagePreview] = useState<string | null>(imagenUrl || null)
  const [uploadingImage, setUploadingImage] = useState(false)
  const fileInputRef = React.useRef<HTMLInputElement>(null)

  // Cargar coordenadas cuando cambia la ciudad
  useEffect(() => {
    if (formData.id_ciudad) {
      let cancelled = false
      onLoadCiudadCoordenadas(formData.id_ciudad)
        .then((coords) => {
          if (!cancelled) {
            setCiudadCoordenadas(coords)
          }
        })
        .catch((err) => {
          if (!cancelled) {
            console.error('Error cargando coordenadas:', err)
            setCiudadCoordenadas(null)
          }
        })
      
      return () => {
        cancelled = true
      }
    } else {
      setCiudadCoordenadas(null)
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [formData.id_ciudad]) // Removemos onLoadCiudadCoordenadas de las dependencias

  const handleChange = (field: keyof CafeteriaFormData, value: any) => {
    setFormData((prev) => {
      const newData = { ...prev, [field]: value }
      
      // Si se cambia horario_diferente a 'SI' y no hay horarios_detallados, inicializarlos
      if (field === 'horario_diferente' && value === 'SI' && !newData.horarios_detallados) {
        const diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']
        const horariosIniciales: Record<string, { apertura: string; cierre: string; cerrado: 'SI' | 'NO' }> = {}
        diasSemana.forEach((dia, index) => {
          // Por defecto, solo Lunes está habilitado
          horariosIniciales[dia] = {
            apertura: index === 0 ? '09:00' : '',
            cierre: index === 0 ? '18:00' : '',
            cerrado: index === 0 ? 'NO' : 'SI',
          }
        })
        newData.horarios_detallados = horariosIniciales
      }
      
      return newData
    })
    // Limpiar error del campo cuando se modifica
    if (errors[field]) {
      setErrors((prev) => {
        const newErrors = { ...prev }
        delete newErrors[field]
        return newErrors
      })
    }
  }

  const handleHorarioDetalladoChange = (
    dia: string,
    field: 'apertura' | 'cierre' | 'cerrado',
    value: string | boolean
  ) => {
    setFormData((prev) => {
      const horarios = { ...prev.horarios_detallados } || {}
      if (!horarios[dia]) {
        horarios[dia] = { apertura: '', cierre: '', cerrado: 'SI' }
      }
      
      if (field === 'cerrado') {
        horarios[dia].cerrado = value ? 'NO' : 'SI'
        // Si se desactiva, limpiar horas
        if (value === false) {
          horarios[dia].apertura = ''
          horarios[dia].cierre = ''
        }
      } else {
        horarios[dia][field] = value as string
        // Si se establece una hora, marcar como no cerrado
        if (horarios[dia].cerrado === 'SI') {
          horarios[dia].cerrado = 'NO'
        }
      }
      
      return { ...prev, horarios_detallados: horarios }
    })
    
    // Limpiar error de horarios si existe
    if (errors.horarios_detallados) {
      setErrors((prev) => {
        const newErrors = { ...prev }
        delete newErrors.horarios_detallados
        return newErrors
      })
    }
  }

  const handleLocationSave = (location: { latitud: number; longitud: number; direccion: string }) => {
    setFormData((prev) => ({
      ...prev,
      latitud: location.latitud.toString(),
      longitud: location.longitud.toString(),
      direccion: location.direccion,
    }))
  }

  const handleImageSelect = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0]
    if (!file) return

    // Validar tipo de archivo
    const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif', 'image/bmp', 'image/svg+xml']
    if (!tiposPermitidos.includes(file.type)) {
      setErrors((prev) => ({ ...prev, imagen: 'Tipo de archivo no válido. Solo se permiten imágenes.' }))
      return
    }

    // Validar tamaño (máximo 5MB)
    if (file.size > 5 * 1024 * 1024) {
      setErrors((prev) => ({ ...prev, imagen: 'El archivo es demasiado grande. Máximo 5MB.' }))
      return
    }

    // Crear preview
    const reader = new FileReader()
    reader.onloadend = () => {
      setImagePreview(reader.result as string)
    }
    reader.readAsDataURL(file)

    // Subir imagen si hay handler
    if (onUploadImage) {
      setUploadingImage(true)
      try {
        const result = await onUploadImage(file)
        // Guardar la ruta en formData para enviarla al backend
        setFormData((prev) => ({ ...prev, imagen: file }))
        setImagePreview(result.url)
        setErrors((prev) => {
          const newErrors = { ...prev }
          delete newErrors.imagen
          return newErrors
        })
      } catch (err) {
        setErrors((prev) => ({
          ...prev,
          imagen: err instanceof Error ? err.message : 'Error al subir la imagen',
        }))
        setImagePreview(null)
      } finally {
        setUploadingImage(false)
      }
    } else {
      // Si no hay handler, solo guardar el archivo
      setFormData((prev) => ({ ...prev, imagen: file }))
    }
  }

  const handleImageRemove = () => {
    setImagePreview(null)
    setFormData((prev) => ({ ...prev, imagen: null }))
    if (fileInputRef.current) {
      fileInputRef.current.value = ''
    }
  }

  const validate = (): boolean => {
    const newErrors: Record<string, string> = {}

    if (!formData.nombre.trim()) {
      newErrors.nombre = 'El nombre es requerido'
    }

    if (!formData.telefono.trim()) {
      newErrors.telefono = 'El teléfono es requerido'
    }

    if (!formData.direccion.trim()) {
      newErrors.direccion = 'La dirección es requerida'
    }

    if (!formData.id_ciudad) {
      newErrors.id_ciudad = 'Debes seleccionar una ciudad'
    }

    if (!formData.latitud || !formData.longitud) {
      newErrors.ubicacion = 'Debes seleccionar una ubicación en el mapa'
    }

    if (formData.horario_diferente === 'NO') {
      if (!formData.horario_apertura || !formData.horario_cierre) {
        newErrors.horario = 'Debes especificar horario de apertura y cierre'
      }
    } else {
      // Validar horarios detallados
      if (!formData.horarios_detallados) {
        newErrors.horarios_detallados = 'Debes configurar los horarios detallados'
      } else {
        const diasConHorario = Object.values(formData.horarios_detallados).filter(
          (h) => h.cerrado === 'NO' && h.apertura && h.cierre
        )
        if (diasConHorario.length === 0) {
          newErrors.horarios_detallados = 'Debes configurar al menos un día con horario'
        }
      }
    }

    setErrors(newErrors)
    return Object.keys(newErrors).length === 0
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()

    if (!validate()) {
      return
    }

    // Si hay una imagen seleccionada pero no se ha subido aún, subirla primero
    if (formData.imagen instanceof File && onUploadImage) {
      setSubmitting(true)
      try {
        const result = await onUploadImage(formData.imagen)
        // Actualizar formData con la ruta de la imagen subida
        // Nota: El backend esperará la ruta en el campo 'imagen', no el File
        const formDataToSubmit = {
          ...formData,
          imagen: result.ruta as any, // Se enviará como string en el body JSON
        }
        await onSubmit(formDataToSubmit)
        navigate('/cafeterias')
      } catch (err) {
        const errorMessage = err instanceof Error ? err.message : 'Error al guardar cafetería'
        setErrors({ submit: errorMessage })
      } finally {
        setSubmitting(false)
      }
    } else {
      setSubmitting(true)
      try {
        await onSubmit(formData)
        navigate('/cafeterias')
      } catch (err) {
        const errorMessage = err instanceof Error ? err.message : 'Error al guardar cafetería'
        setErrors({ submit: errorMessage })
      } finally {
        setSubmitting(false)
      }
    }
  }

  const theme = useTheme()
  const isMobile = useMediaQuery(theme.breakpoints.down('md'))

  // Componente de sección reutilizable
  const FormSection = ({
    title,
    subtitle,
    icon: Icon,
    iconColor,
    children,
    defaultExpanded = true,
  }: {
    title: string
    subtitle?: string
    icon: React.ElementType
    iconColor: string
    children: React.ReactNode
    defaultExpanded?: boolean
  }) => {
    const sectionContent = (
      <Box sx={{ pt: 2 }}>
        <Grid container spacing={3}>
          {children}
        </Grid>
      </Box>
    )

    if (isMobile) {
      return (
        <Accordion defaultExpanded={defaultExpanded} sx={{ mb: 2, boxShadow: 2 }}>
          <AccordionSummary
            expandIcon={<ExpandMoreIcon />}
            sx={{
              backgroundColor: designTokens.colors.bgBody,
              '&:hover': { backgroundColor: designTokens.colors.grisClaro },
            }}
          >
            <Box sx={{ display: 'flex', alignItems: 'center', gap: 2, width: '100%' }}>
              <Box
                sx={{
                  width: 40,
                  height: 40,
                  borderRadius: 2,
                  backgroundColor: iconColor,
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  color: 'white',
                }}
              >
                <Icon />
              </Box>
              <Box sx={{ flex: 1 }}>
                <Typography variant="h6" fontWeight={600} sx={{ color: designTokens.colors.cuarto }}>
                  {title}
                </Typography>
                {subtitle && (
                  <Typography variant="caption" sx={{ color: designTokens.colors.gris }}>
                    {subtitle}
                  </Typography>
                )}
              </Box>
            </Box>
          </AccordionSummary>
          <AccordionDetails sx={{ p: 3 }}>{sectionContent}</AccordionDetails>
        </Accordion>
      )
    }

    return (
      <Paper
        elevation={2}
        sx={{
          mb: 3,
          borderRadius: designTokens.spacing.borderRadius.large,
          overflow: 'hidden',
        }}
      >
        <Box
          sx={{
            backgroundColor: designTokens.colors.bgBody,
            p: 2.5,
            borderBottom: `2px solid ${designTokens.colors.principal}`,
          }}
        >
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 2 }}>
            <Box
              sx={{
                width: 48,
                height: 48,
                borderRadius: 2,
                backgroundColor: iconColor,
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                color: 'white',
                flexShrink: 0,
              }}
            >
              <Icon sx={{ fontSize: 28 }} />
            </Box>
            <Box>
              <Typography variant="h6" fontWeight={700} sx={{ color: designTokens.colors.cuarto }}>
                {title}
              </Typography>
              {subtitle && (
                <Typography variant="body2" sx={{ color: designTokens.colors.gris, mt: 0.5 }}>
                  {subtitle}
                </Typography>
              )}
            </Box>
          </Box>
        </Box>
        <Box sx={{ p: 3 }}>{sectionContent}</Box>
      </Paper>
    )
  }

  return (
    <Box
      component="form"
      onSubmit={handleSubmit}
      sx={{
        maxWidth: { xs: '100%', md: 900 },
        mx: 'auto',
        px: { xs: 2, md: 3 },
        py: { xs: 2, md: 4 },
        pb: { xs: 12, md: 4 }, // Espacio para barra sticky en móvil
      }}
    >
      {/* Título principal */}
      <Typography
        variant="h4"
        fontWeight={700}
        sx={{
          mb: 3,
          color: designTokens.colors.cuarto,
          fontSize: { xs: '1.75rem', md: '2rem' },
        }}
      >
        {cafeteriaId ? 'Editar Cafetería' : 'Nueva Cafetería'}
      </Typography>

      {errors.submit && (
        <Alert severity="error" sx={{ mb: 3, borderRadius: 2 }}>
          {errors.submit}
        </Alert>
      )}

      {/* Sección 1: Información Básica */}
      <FormSection
        title="Información Básica"
        subtitle="Datos principales de tu cafetería"
        icon={InfoIcon}
        iconColor={designTokens.colors.principal}
        defaultExpanded={true}
      >
        {/* Nombre */}
        <Grid item xs={12} md={8}>
          <TextField
            fullWidth
            label="Nombre de la Cafetería"
            required
            value={formData.nombre}
            onChange={(e) => handleChange('nombre', e.target.value)}
            error={!!errors.nombre}
            helperText={errors.nombre || 'Ej: Café del Centro, Starbucks, etc.'}
            sx={{
              '& .MuiOutlinedInput-root': {
                backgroundColor: designTokens.colors.plantillaClaro,
                '&:hover': {
                  backgroundColor: designTokens.colors.blanco,
                },
                '&.Mui-focused': {
                  backgroundColor: designTokens.colors.blanco,
                },
              },
            }}
          />
        </Grid>

        {/* Ciudad */}
        <Grid item xs={12} md={4}>
          <FormControl
            fullWidth
            required
            error={!!errors.id_ciudad}
            sx={{
              '& .MuiOutlinedInput-root': {
                backgroundColor: designTokens.colors.plantillaClaro,
                '&:hover': {
                  backgroundColor: designTokens.colors.blanco,
                },
                '&.Mui-focused': {
                  backgroundColor: designTokens.colors.blanco,
                },
              },
            }}
          >
            <InputLabel>Ciudad</InputLabel>
            <Select
              value={formData.id_ciudad || ''}
              onChange={(e) => handleChange('id_ciudad', e.target.value ? Number(e.target.value) : null)}
              label="Ciudad"
            >
              {ciudades.map((ciudad) => (
                <MenuItem key={ciudad.id} value={ciudad.id}>
                  {ciudad.nombre}
                </MenuItem>
              ))}
            </Select>
            {errors.id_ciudad && (
              <Typography variant="caption" color="error" sx={{ mt: 0.5, ml: 1.75 }}>
                {errors.id_ciudad}
              </Typography>
            )}
          </FormControl>
        </Grid>

        {/* Imagen Principal */}
        <Grid item xs={12}>
          <Typography variant="subtitle2" sx={{ mb: 1.5, fontWeight: 600, color: designTokens.colors.cuarto }}>
            Logo de la Cafetería <Typography component="span" sx={{ color: designTokens.colors.gris, fontWeight: 400 }}>(opcional)</Typography>
          </Typography>
          <Box
            sx={{
              border: '2px dashed',
              borderColor: imagePreview ? 'transparent' : designTokens.colors.grisClaro,
              borderRadius: designTokens.spacing.borderRadius.medium,
              p: 3,
              backgroundColor: imagePreview ? 'transparent' : designTokens.colors.bgBody,
              position: 'relative',
              minHeight: { xs: 180, md: 220 },
              display: 'flex',
              flexDirection: 'column',
              alignItems: 'center',
              justifyContent: 'center',
              transition: 'all 0.3s',
              '&:hover': {
                borderColor: designTokens.colors.principal,
                backgroundColor: imagePreview ? 'transparent' : designTokens.colors.plantillaClaro,
              },
            }}
          >
            {imagePreview ? (
              <>
                <Box
                  component="img"
                  src={imagePreview}
                  alt="Preview"
                  sx={{
                    maxWidth: '100%',
                    maxHeight: { xs: 200, md: 250 },
                    borderRadius: 1,
                    objectFit: 'contain',
                    mb: 2,
                  }}
                />
                <Box sx={{ display: 'flex', gap: 1, flexWrap: 'wrap', justifyContent: 'center' }}>
                  <Button
                    variant="outlined"
                    startIcon={<CloudUploadIcon />}
                    onClick={() => fileInputRef.current?.click()}
                    disabled={uploadingImage}
                    sx={{ textTransform: 'none' }}
                  >
                    Cambiar
                  </Button>
                  <IconButton
                    color="error"
                    onClick={handleImageRemove}
                    disabled={uploadingImage}
                    sx={{ border: `1px solid ${designTokens.colors.rojo}` }}
                  >
                    <DeleteIcon />
                  </IconButton>
                </Box>
              </>
            ) : (
              <>
                <ImageIcon sx={{ fontSize: 64, color: designTokens.colors.principal, mb: 1.5 }} />
                <Typography variant="body1" sx={{ mb: 1, fontWeight: 500, color: designTokens.colors.cuarto }}>
                  Selecciona una imagen para tu cafetería
                </Typography>
                <Button
                  variant="contained"
                  component="label"
                  startIcon={<CloudUploadIcon />}
                  disabled={uploadingImage}
                  sx={{
                    backgroundColor: designTokens.colors.principal,
                    textTransform: 'none',
                    '&:hover': {
                      backgroundColor: designTokens.colors.sidebarDark,
                    },
                  }}
                >
                  {uploadingImage ? 'Subiendo...' : 'Seleccionar Imagen'}
                  <input ref={fileInputRef} type="file" hidden accept="image/*" onChange={handleImageSelect} />
                </Button>
                <Typography variant="caption" sx={{ mt: 1.5, color: designTokens.colors.gris, textAlign: 'center' }}>
                  Formatos: JPG, PNG, WebP, GIF, BMP, SVG. Máximo 5MB
                </Typography>
              </>
            )}
            {uploadingImage && (
              <Box
                sx={{
                  position: 'absolute',
                  top: 0,
                  left: 0,
                  right: 0,
                  bottom: 0,
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  backgroundColor: 'rgba(255, 255, 255, 0.9)',
                  borderRadius: designTokens.spacing.borderRadius.medium,
                }}
              >
                <CircularProgress />
              </Box>
            )}
          </Box>
          {errors.imagen && (
            <Typography variant="caption" color="error" sx={{ mt: 0.5, ml: 1, display: 'block' }}>
              {errors.imagen}
            </Typography>
          )}
        </Grid>
      </FormSection>

      {/* Sección 2: Ubicación */}
      <FormSection
        title="Ubicación"
        subtitle="Selecciona la ubicación exacta de tu cafetería en el mapa"
        icon={MapIcon}
        iconColor={designTokens.colors.verde}
        defaultExpanded={!isMobile}
      >
        <Grid item xs={12}>
          <Button
            fullWidth
            variant="contained"
            startIcon={<LocationOnIcon />}
            onClick={() => {
              if (!formData.id_ciudad) {
                setErrors({ id_ciudad: 'Primero selecciona una ciudad' })
                return
              }
              setLocationModalOpen(true)
            }}
            disabled={!formData.id_ciudad}
            sx={{
              py: 1.5,
              mb: 2,
              backgroundColor: designTokens.colors.verde,
              textTransform: 'none',
              fontSize: '1rem',
              fontWeight: 600,
              '&:hover': {
                backgroundColor: designTokens.colors.verdeOscuro,
              },
            }}
          >
            Seleccionar Ubicación en el Mapa
          </Button>
        </Grid>

        <Grid item xs={12}>
          <TextField
            fullWidth
            label="Dirección"
            required
            value={formData.direccion}
            onChange={(e) => handleChange('direccion', e.target.value)}
            error={!!errors.direccion}
            helperText={errors.direccion || 'Primero selecciona una ubicación en el mapa'}
            placeholder="Primero selecciona una ubicación en el mapa"
            sx={{
              '& .MuiOutlinedInput-root': {
                backgroundColor: designTokens.colors.plantillaClaro,
                '&:hover': {
                  backgroundColor: designTokens.colors.blanco,
                },
                '&.Mui-focused': {
                  backgroundColor: designTokens.colors.blanco,
                },
              },
            }}
          />
          {errors.ubicacion && (
            <Typography variant="caption" color="error" sx={{ mt: 0.5, ml: 1.75, display: 'block' }}>
              {errors.ubicacion}
            </Typography>
          )}
          {formData.latitud && formData.longitud && (
            <Typography variant="caption" sx={{ mt: 0.5, ml: 1.75, display: 'block', color: designTokens.colors.gris }}>
              Coordenadas: Lat {formData.latitud}, Lng {formData.longitud}
            </Typography>
          )}
        </Grid>
      </FormSection>

      {/* Sección 3: Horarios */}
      <FormSection
        title="Horarios y Contacto"
        subtitle="Define los horarios de atención y medios de contacto"
        icon={ScheduleIcon}
        iconColor={designTokens.colors.naranja}
        defaultExpanded={!isMobile}
      >
        {/* Switch horario simple/detallado */}
        <Grid item xs={12}>
          <Paper
            sx={{
              p: 2,
              backgroundColor: designTokens.colors.bgBody,
              borderRadius: designTokens.spacing.borderRadius.medium,
            }}
          >
            <Box sx={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', flexWrap: 'wrap', gap: 2 }}>
              <Box>
                <Typography variant="subtitle1" sx={{ fontWeight: 600, color: designTokens.colors.cuarto }}>
                  Mismo horario todos los días
                </Typography>
                <Typography variant="body2" sx={{ color: designTokens.colors.gris, mt: 0.5 }}>
                  Activa esta opción si tu cafetería tiene el mismo horario todos los días
                </Typography>
              </Box>
              <FormControlLabel
                control={
                  <Switch
                    checked={formData.horario_diferente === 'NO'}
                    onChange={(e) => handleChange('horario_diferente', e.target.checked ? 'NO' : 'SI')}
                    sx={{
                      '& .MuiSwitch-switchBase.Mui-checked': {
                        color: designTokens.colors.principal,
                      },
                      '& .MuiSwitch-switchBase.Mui-checked + .MuiSwitch-track': {
                        backgroundColor: designTokens.colors.principal,
                      },
                    }}
                  />
                }
                label=""
                sx={{ m: 0 }}
              />
            </Box>
          </Paper>
        </Grid>

        {/* Horario Simple */}
        {formData.horario_diferente === 'NO' && (
          <>
            <Grid item xs={12} md={6}>
              <TextField
                fullWidth
                label="Horario de Apertura"
                type="time"
                value={formData.horario_apertura || ''}
                onChange={(e) => handleChange('horario_apertura', e.target.value)}
                InputLabelProps={{ shrink: true }}
                error={!!errors.horario}
                sx={{
                  '& .MuiOutlinedInput-root': {
                    backgroundColor: designTokens.colors.plantillaClaro,
                    '&:hover': {
                      backgroundColor: designTokens.colors.blanco,
                    },
                    '&.Mui-focused': {
                      backgroundColor: designTokens.colors.blanco,
                    },
                  },
                }}
              />
            </Grid>
            <Grid item xs={12} md={6}>
              <TextField
                fullWidth
                label="Horario de Cierre"
                type="time"
                value={formData.horario_cierre || ''}
                onChange={(e) => handleChange('horario_cierre', e.target.value)}
                InputLabelProps={{ shrink: true }}
                error={!!errors.horario}
                sx={{
                  '& .MuiOutlinedInput-root': {
                    backgroundColor: designTokens.colors.plantillaClaro,
                    '&:hover': {
                      backgroundColor: designTokens.colors.blanco,
                    },
                    '&.Mui-focused': {
                      backgroundColor: designTokens.colors.blanco,
                    },
                  },
                }}
              />
            </Grid>
            {errors.horario && (
              <Grid item xs={12}>
                <Typography variant="caption" color="error" sx={{ ml: 1.75 }}>
                  {errors.horario}
                </Typography>
              </Grid>
            )}
          </>
        )}

        {/* Horario Detallado */}
        {formData.horario_diferente === 'SI' && (
          <Grid item xs={12}>
            <TableContainer
              component={Paper}
              sx={{
                mt: 1,
                borderRadius: designTokens.spacing.borderRadius.medium,
                overflowX: 'auto',
              }}
            >
              <Table>
                <TableHead>
                  <TableRow sx={{ backgroundColor: designTokens.colors.bgBody }}>
                    <TableCell sx={{ fontWeight: 600, color: designTokens.colors.cuarto }}>
                      Día
                    </TableCell>
                    <TableCell align="center" sx={{ fontWeight: 600, color: designTokens.colors.cuarto }}>
                      Desbloquear
                    </TableCell>
                    <TableCell sx={{ fontWeight: 600, color: designTokens.colors.cuarto }}>
                      Hora de Apertura
                    </TableCell>
                    <TableCell sx={{ fontWeight: 600, color: designTokens.colors.cuarto }}>
                      Hora de Cierre
                    </TableCell>
                  </TableRow>
                </TableHead>
                <TableBody>
                  {['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'].map((dia) => {
                    const horario = formData.horarios_detallados?.[dia] || {
                      apertura: '',
                      cierre: '',
                      cerrado: 'SI' as const,
                    }
                    const habilitado = horario.cerrado === 'NO'

                    return (
                      <TableRow key={dia} hover>
                        <TableCell>
                          <Typography variant="body2" sx={{ fontWeight: 500 }}>
                            {dia}
                          </Typography>
                        </TableCell>
                        <TableCell align="center">
                          <Switch
                            checked={habilitado}
                            onChange={(e) => handleHorarioDetalladoChange(dia, 'cerrado', e.target.checked)}
                            size="small"
                            sx={{
                              '& .MuiSwitch-switchBase.Mui-checked': {
                                color: designTokens.colors.principal,
                              },
                              '& .MuiSwitch-switchBase.Mui-checked + .MuiSwitch-track': {
                                backgroundColor: designTokens.colors.principal,
                              },
                            }}
                          />
                        </TableCell>
                        <TableCell>
                          <TextField
                            type="time"
                            size="small"
                            fullWidth
                            value={horario.apertura || ''}
                            onChange={(e) => handleHorarioDetalladoChange(dia, 'apertura', e.target.value)}
                            disabled={!habilitado}
                            InputLabelProps={{ shrink: true }}
                          />
                        </TableCell>
                        <TableCell>
                          <TextField
                            type="time"
                            size="small"
                            fullWidth
                            value={horario.cierre || ''}
                            onChange={(e) => handleHorarioDetalladoChange(dia, 'cierre', e.target.value)}
                            disabled={!habilitado}
                            InputLabelProps={{ shrink: true }}
                          />
                        </TableCell>
                      </TableRow>
                    )
                  })}
                </TableBody>
              </Table>
            </TableContainer>
            {errors.horarios_detallados && (
              <Typography variant="caption" color="error" sx={{ mt: 1, ml: 1, display: 'block' }}>
                {errors.horarios_detallados}
              </Typography>
            )}
          </Grid>
        )}

        <Divider sx={{ my: 2, width: '100%' }} />

        {/* Contacto */}
        <Grid item xs={12} md={6}>
          <TextField
            fullWidth
            label="Teléfono"
            required
            value={formData.telefono}
            onChange={(e) => handleChange('telefono', e.target.value)}
            error={!!errors.telefono}
            helperText={errors.telefono}
            placeholder="Ej: 5551234567"
            sx={{
              '& .MuiOutlinedInput-root': {
                backgroundColor: designTokens.colors.plantillaClaro,
                '&:hover': {
                  backgroundColor: designTokens.colors.blanco,
                },
                '&.Mui-focused': {
                  backgroundColor: designTokens.colors.blanco,
                },
              },
            }}
          />
        </Grid>

        <Grid item xs={12} md={6}>
          <TextField
            fullWidth
            label="Correo Electrónico"
            type="email"
            value={formData.correo_electronico || ''}
            onChange={(e) => handleChange('correo_electronico', e.target.value || undefined)}
            placeholder="contacto@tucafeteria.com"
            helperText="(opcional)"
            sx={{
              '& .MuiOutlinedInput-root': {
                backgroundColor: designTokens.colors.plantillaClaro,
                '&:hover': {
                  backgroundColor: designTokens.colors.blanco,
                },
                '&.Mui-focused': {
                  backgroundColor: designTokens.colors.blanco,
                },
              },
            }}
          />
        </Grid>
      </FormSection>

      {/* Sección 4: Descripción */}
      <FormSection
        title="Información Adicional"
        subtitle="Cuéntanos más sobre tu cafetería"
        icon={DescriptionIcon}
        iconColor={designTokens.colors.morado}
        defaultExpanded={!isMobile}
      >
        <Grid item xs={12}>
          <TextField
            fullWidth
            label="Descripción"
            multiline
            rows={4}
            value={formData.descripcion || ''}
            onChange={(e) => handleChange('descripcion', e.target.value || undefined)}
            placeholder="Una breve descripción de la cafetería y su ambiente acogedor. ¿Qué hace especial a tu cafetería?"
            sx={{
              '& .MuiOutlinedInput-root': {
                backgroundColor: designTokens.colors.plantillaClaro,
                '&:hover': {
                  backgroundColor: designTokens.colors.blanco,
                },
                '&.Mui-focused': {
                  backgroundColor: designTokens.colors.blanco,
                },
              },
            }}
          />
        </Grid>
      </FormSection>

      {/* Barra de acciones sticky (móvil) */}
      {isMobile && (
        <Paper
          elevation={8}
          sx={{
            position: 'fixed',
            bottom: 0,
            left: 0,
            right: 0,
            p: 2,
            backgroundColor: designTokens.colors.blanco,
            borderTop: `2px solid ${designTokens.colors.principal}`,
            zIndex: 1000,
            display: 'flex',
            gap: 2,
          }}
        >
          <Button
            fullWidth
            variant="outlined"
            onClick={() => navigate('/cafeterias')}
            disabled={submitting}
            sx={{
              textTransform: 'none',
              fontWeight: 600,
              borderColor: designTokens.colors.principal,
              color: designTokens.colors.principal,
              '&:hover': {
                borderColor: designTokens.colors.sidebarDark,
                backgroundColor: designTokens.colors.plantillaClaro,
              },
            }}
          >
            Cancelar
          </Button>
          <Button
            fullWidth
            type="submit"
            variant="contained"
            disabled={submitting || loading}
            sx={{
              backgroundColor: designTokens.colors.principal,
              textTransform: 'none',
              fontWeight: 600,
              '&:hover': {
                backgroundColor: designTokens.colors.sidebarDark,
              },
            }}
          >
            {submitting ? <CircularProgress size={20} color="inherit" /> : 'Guardar'}
          </Button>
        </Paper>
      )}

      {/* Botones de acción (desktop) */}
      {!isMobile && (
        <Paper
          elevation={2}
          sx={{
            p: 3,
            borderRadius: designTokens.spacing.borderRadius.large,
            display: 'flex',
            gap: 2,
            justifyContent: 'flex-end',
          }}
        >
          <Button
            variant="outlined"
            onClick={() => navigate('/cafeterias')}
            disabled={submitting}
            sx={{
              textTransform: 'none',
              fontWeight: 600,
              px: 4,
              borderColor: designTokens.colors.principal,
              color: designTokens.colors.principal,
              '&:hover': {
                borderColor: designTokens.colors.sidebarDark,
                backgroundColor: designTokens.colors.plantillaClaro,
              },
            }}
          >
            Cancelar
          </Button>
          <Button
            type="submit"
            variant="contained"
            disabled={submitting || loading}
            sx={{
              backgroundColor: designTokens.colors.principal,
              textTransform: 'none',
              fontWeight: 600,
              px: 4,
              '&:hover': {
                backgroundColor: designTokens.colors.sidebarDark,
              },
            }}
          >
            {submitting ? <CircularProgress size={20} color="inherit" /> : 'Guardar'}
          </Button>
        </Paper>
      )}

      {/* Modal de ubicación */}
      <LocationPickerModal
        open={locationModalOpen}
        onClose={() => setLocationModalOpen(false)}
        onSave={handleLocationSave}
        initialLocation={
          formData.latitud && formData.longitud
            ? {
                latitud: parseFloat(formData.latitud),
                longitud: parseFloat(formData.longitud),
                direccion: formData.direccion,
              }
            : null
        }
        ciudadId={formData.id_ciudad || undefined}
        ciudadCoordenadas={ciudadCoordenadas}
      />
    </Box>
  )
}
