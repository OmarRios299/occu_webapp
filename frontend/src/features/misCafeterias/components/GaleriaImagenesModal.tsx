import React, { useState, useEffect, useCallback, useRef } from 'react'
import {
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  Button,
  Box,
  Grid,
  IconButton,
  Typography,
  CircularProgress,
  Alert,
  Switch,
  FormControlLabel,
  Paper,
} from '@mui/material'
import CloudUploadIcon from '@mui/icons-material/CloudUpload'
import DeleteIcon from '@mui/icons-material/Delete'
import CloseIcon from '@mui/icons-material/Close'
import { designTokens } from '@/theme/tokens'
import type { CafeteriaImagen } from '../types'
import type { MisCafeteriasRepository } from '../services/MisCafeteriasRepository'

type GaleriaImagenesModalProps = {
  open: boolean
  onClose: () => void
  cafeteriaId: number
  repository: MisCafeteriasRepository
  token: string
}

type PreviewFile = {
  file: File
  preview: string
  uploading: boolean
  progress: number
  error?: string
  success?: boolean
}

export function GaleriaImagenesModal({
  open,
  onClose,
  cafeteriaId,
  repository,
  token,
}: GaleriaImagenesModalProps) {
  const [imagenes, setImagenes] = useState<CafeteriaImagen[]>([])
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [selectedFiles, setSelectedFiles] = useState<PreviewFile[]>([])
  const [isDragging, setIsDragging] = useState(false)
  const fileInputRef = useRef<HTMLInputElement>(null)
  const dropZoneRef = useRef<HTMLDivElement>(null)

  // Cargar imágenes al abrir el modal
  const loadImagenes = useCallback(async () => {
    if (!open || !cafeteriaId) return

    setLoading(true)
    setError(null)
    try {
      const data = await repository.getGaleriaImagenes(cafeteriaId, token)
      setImagenes(data)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Error al cargar imágenes')
    } finally {
      setLoading(false)
    }
  }, [open, cafeteriaId, repository, token])

  useEffect(() => {
    loadImagenes()
  }, [loadImagenes])

  // Manejar selección de archivos
  const handleFileSelect = (files: FileList | null) => {
    if (!files || files.length === 0) return

    const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif', 'image/bmp']
    const nuevosArchivos: PreviewFile[] = []

    Array.from(files).forEach((file) => {
      if (!tiposPermitidos.includes(file.type)) {
        return
      }

      if (file.size > 5 * 1024 * 1024) {
        return
      }

      const reader = new FileReader()
      reader.onloadend = () => {
        nuevosArchivos.push({
          file,
          preview: reader.result as string,
          uploading: false,
          progress: 0,
        })
        if (nuevosArchivos.length === Array.from(files).length) {
          setSelectedFiles((prev) => [...prev, ...nuevosArchivos])
        }
      }
      reader.readAsDataURL(file)
    })
  }

  // Drag & Drop handlers
  const handleDragOver = (e: React.DragEvent) => {
    e.preventDefault()
    e.stopPropagation()
    setIsDragging(true)
  }

  const handleDragLeave = (e: React.DragEvent) => {
    e.preventDefault()
    e.stopPropagation()
    setIsDragging(false)
  }

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault()
    e.stopPropagation()
    setIsDragging(false)

    const files = e.dataTransfer.files
    handleFileSelect(files)
  }

  // Subir todas las imágenes seleccionadas
  const handleUploadAll = async () => {
    if (selectedFiles.length === 0) return

    const filesToUpload = selectedFiles.filter((f) => !f.uploading && !f.success && !f.error)

    for (let i = 0; i < filesToUpload.length; i++) {
      const previewFile = filesToUpload[i]
      const index = selectedFiles.findIndex((f) => f === previewFile)

      setSelectedFiles((prev) => {
        const updated = [...prev]
        updated[index] = { ...updated[index], uploading: true, progress: 0 }
        return updated
      })

      try {
        const result = await repository.uploadGaleriaImagen(cafeteriaId, previewFile.file, token)
        
        setSelectedFiles((prev) => {
          const updated = [...prev]
          updated[index] = { ...updated[index], uploading: false, success: true, progress: 100 }
          return updated
        })

        // Agregar a la lista de imágenes
        setImagenes((prev) => [
          {
            id: result.id,
            id_cafeteria: cafeteriaId,
            imagen: result.url,
            descripcion: '',
            estado: 0,
          },
          ...prev,
        ])
      } catch (err) {
        setSelectedFiles((prev) => {
          const updated = [...prev]
          updated[index] = {
            ...updated[index],
            uploading: false,
            error: err instanceof Error ? err.message : 'Error al subir',
            progress: 0,
          }
          return updated
        })
      }
    }

    // Limpiar archivos subidos exitosamente después de un delay
    setTimeout(() => {
      setSelectedFiles((prev) => prev.filter((f) => !f.success))
    }, 2000)
  }

  // Eliminar imagen
  const handleDelete = async (imagenId: number) => {
    if (!confirm('¿Estás seguro de eliminar esta imagen?')) return

    try {
      await repository.deleteGaleriaImagen(cafeteriaId, imagenId, token)
      setImagenes((prev) => prev.filter((img) => img.id !== imagenId))
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Error al eliminar imagen')
    }
  }

  // Cambiar estado
  const handleToggleEstado = async (imagenId: number, nuevoEstado: 0 | 1) => {
    try {
      await repository.updateGaleriaImagenEstado(cafeteriaId, imagenId, nuevoEstado, token)
      setImagenes((prev) =>
        prev.map((img) => (img.id === imagenId ? { ...img, estado: nuevoEstado } : img))
      )
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Error al actualizar estado')
    }
  }

  // Limpiar al cerrar
  useEffect(() => {
    if (!open) {
      setSelectedFiles([])
      setError(null)
    }
  }, [open])

  return (
    <Dialog open={open} onClose={onClose} maxWidth="lg" fullWidth>
      <DialogTitle>
        <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
          <Typography variant="h6">Galería de Imágenes</Typography>
          <IconButton onClick={onClose} size="small">
            <CloseIcon />
          </IconButton>
        </Box>
      </DialogTitle>
      <DialogContent>
        {error && (
          <Alert severity="error" sx={{ mb: 2 }} onClose={() => setError(null)}>
            {error}
          </Alert>
        )}

        {/* Área de Drag & Drop */}
        <Paper
          ref={dropZoneRef}
          onDragOver={handleDragOver}
          onDragLeave={handleDragLeave}
          onDrop={handleDrop}
          onClick={() => fileInputRef.current?.click()}
          sx={{
            border: '3px dashed',
            borderColor: isDragging ? designTokens.colors.primary : designTokens.colors.border,
            borderRadius: 2,
            p: 4,
            textAlign: 'center',
            backgroundColor: isDragging ? designTokens.colors.bgBody : 'transparent',
            cursor: 'pointer',
            transition: 'all 0.3s',
            mb: 3,
          }}
        >
          <input
            ref={fileInputRef}
            type="file"
            multiple
            accept="image/*"
            style={{ display: 'none' }}
            onChange={(e) => handleFileSelect(e.target.files)}
          />
          <CloudUploadIcon sx={{ fontSize: 64, color: designTokens.colors.primary, mb: 2 }} />
          <Typography variant="h6" sx={{ mb: 1 }}>
            Arrastra y suelta tus imágenes aquí
          </Typography>
          <Typography variant="body2" color="textSecondary" sx={{ mb: 2 }}>
            O haz clic para seleccionar archivos
          </Typography>
          <Typography variant="caption" color="textSecondary">
            Formatos: JPG, PNG, WebP, GIF, BMP. Máximo 5MB por imagen
          </Typography>
        </Paper>

        {/* Preview de imágenes seleccionadas */}
        {selectedFiles.length > 0 && (
          <Box sx={{ mb: 3 }}>
            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
              <Typography variant="subtitle1" sx={{ fontWeight: 600 }}>
                Imágenes Seleccionadas ({selectedFiles.length})
              </Typography>
              <Button
                variant="contained"
                startIcon={<CloudUploadIcon />}
                onClick={handleUploadAll}
                disabled={selectedFiles.every((f) => f.uploading || f.success)}
              >
                Subir Todas
              </Button>
            </Box>
            <Grid container spacing={2}>
              {selectedFiles.map((previewFile, index) => (
                <Grid item xs={6} sm={4} md={3} key={index}>
                  <Paper
                    sx={{
                      position: 'relative',
                      overflow: 'hidden',
                      borderRadius: 1,
                    }}
                  >
                    <Box
                      component="img"
                      src={previewFile.preview}
                      alt="Preview"
                      sx={{
                        width: '100%',
                        height: 150,
                        objectFit: 'cover',
                        display: 'block',
                      }}
                    />
                    {previewFile.uploading && (
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
                          backgroundColor: 'rgba(0,0,0,0.5)',
                        }}
                      >
                        <CircularProgress size={40} />
                      </Box>
                    )}
                    {previewFile.success && (
                      <Box
                        sx={{
                          position: 'absolute',
                          top: 8,
                          right: 8,
                          backgroundColor: 'success.main',
                          color: 'white',
                          borderRadius: '50%',
                          width: 32,
                          height: 32,
                          display: 'flex',
                          alignItems: 'center',
                          justifyContent: 'center',
                        }}
                      >
                        ✓
                      </Box>
                    )}
                    {previewFile.error && (
                      <Box
                        sx={{
                          position: 'absolute',
                          bottom: 0,
                          left: 0,
                          right: 0,
                          backgroundColor: 'error.main',
                          color: 'white',
                          p: 0.5,
                          fontSize: '0.75rem',
                        }}
                      >
                        {previewFile.error}
                      </Box>
                    )}
                  </Paper>
                </Grid>
              ))}
            </Grid>
          </Box>
        )}

        {/* Grid de imágenes existentes */}
        <Box>
          <Typography variant="subtitle1" sx={{ fontWeight: 600, mb: 2 }}>
            Imágenes Existentes ({imagenes.length})
          </Typography>
          {loading ? (
            <Box sx={{ display: 'flex', justifyContent: 'center', py: 4 }}>
              <CircularProgress />
            </Box>
          ) : imagenes.length === 0 ? (
            <Alert severity="info">No hay imágenes en la galería</Alert>
          ) : (
            <Grid container spacing={2}>
              {imagenes.map((imagen) => (
                <Grid item xs={6} sm={4} md={3} key={imagen.id}>
                  <Paper
                    sx={{
                      position: 'relative',
                      overflow: 'hidden',
                      borderRadius: 1,
                    }}
                  >
                    <Box
                      component="img"
                      src={imagen.imagen}
                      alt="Galería"
                      sx={{
                        width: '100%',
                        height: 150,
                        objectFit: 'cover',
                        display: 'block',
                        opacity: imagen.estado === 1 ? 0.5 : 1,
                      }}
                    />
                    <Box
                      sx={{
                        position: 'absolute',
                        top: 8,
                        right: 8,
                        display: 'flex',
                        gap: 1,
                      }}
                    >
                      <IconButton
                        size="small"
                        onClick={() => handleDelete(imagen.id)}
                        sx={{
                          backgroundColor: 'error.main',
                          color: 'white',
                          '&:hover': { backgroundColor: 'error.dark' },
                        }}
                      >
                        <DeleteIcon fontSize="small" />
                      </IconButton>
                    </Box>
                    <Box sx={{ p: 1 }}>
                      <FormControlLabel
                        control={
                          <Switch
                            checked={imagen.estado === 0}
                            onChange={(e) =>
                              handleToggleEstado(imagen.id, e.target.checked ? 0 : 1)
                            }
                            size="small"
                          />
                        }
                        label={imagen.estado === 0 ? 'Activa' : 'Inactiva'}
                        sx={{ m: 0 }}
                      />
                    </Box>
                  </Paper>
                </Grid>
              ))}
            </Grid>
          )}
        </Box>
      </DialogContent>
      <DialogActions>
        <Button onClick={onClose}>Cerrar</Button>
      </DialogActions>
    </Dialog>
  )
}
