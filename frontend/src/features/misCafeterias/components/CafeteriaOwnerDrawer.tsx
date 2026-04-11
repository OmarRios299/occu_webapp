import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import {
  Drawer,
  Box,
  Typography,
  IconButton,
  Chip,
  Divider,
  CircularProgress,
  Alert,
  useTheme,
  useMediaQuery,
  Button,
  Switch,
  FormControlLabel,
  Dialog,
  DialogTitle,
  DialogContent,
  DialogContentText,
  DialogActions,
} from '@mui/material'
import CloseIcon from '@mui/icons-material/Close'
import LocationOnIcon from '@mui/icons-material/LocationOn'
import PhoneIcon from '@mui/icons-material/Phone'
import EmailIcon from '@mui/icons-material/Email'
import AccessTimeIcon from '@mui/icons-material/AccessTime'
import EditIcon from '@mui/icons-material/Edit'
import MenuBookIcon from '@mui/icons-material/MenuBook'
import ReceiptIcon from '@mui/icons-material/Receipt'
import QrCodeIcon from '@mui/icons-material/QrCode'
import DeleteIcon from '@mui/icons-material/Delete'
import PhotoLibraryIcon from '@mui/icons-material/PhotoLibrary'
import { designTokens } from '@/theme/tokens'
import type { CafeteriaOwnerDetail } from '../types'
import { ServiciosModal } from './ServiciosModal'
import { GaleriaImagenesModal } from './GaleriaImagenesModal'
import type { MisCafeteriasRepository } from '../services/MisCafeteriasRepository'

type CafeteriaOwnerDrawerProps = {
  open: boolean
  onClose: () => void
  cafeteria: CafeteriaOwnerDetail | null
  loading: boolean
  mutating: boolean
  onToggleEstado: (id: number, nuevoEstado: 0 | 1) => Promise<void>
  onDelete: (id: number) => Promise<void>
  onLoadServicios: (cafeteriaId: number) => Promise<Array<{ id: number; nombre: string; imagen?: string; servicio_registrado?: number }>>
  onSaveServicios: (cafeteriaId: number, servicioIds: number[]) => Promise<void>
  repository: MisCafeteriasRepository
  token: string
}

export function CafeteriaOwnerDrawer({
  open,
  onClose,
  cafeteria,
  loading,
  mutating,
  onToggleEstado,
  onDelete,
  onLoadServicios,
  onSaveServicios,
  repository,
  token,
}: CafeteriaOwnerDrawerProps) {
  const theme = useTheme()
  const isMobile = useMediaQuery(theme.breakpoints.down('lg'))
  const anchor = isMobile ? 'bottom' : 'right'
  const navigate = useNavigate()

  const [deleteDialogOpen, setDeleteDialogOpen] = useState(false)
  const [deleteLoading, setDeleteLoading] = useState(false)
  const [toggleLoading, setToggleLoading] = useState(false)
  const [serviciosModalOpen, setServiciosModalOpen] = useState(false)
  const [galeriaModalOpen, setGaleriaModalOpen] = useState(false)

  // El backend ya devuelve URLs completas, pero mantenemos fallback
  const imagenUrl = cafeteria?.imagen || '/placeholder-cafeteria.jpg'

  const handleToggleEstado = async () => {
    if (!cafeteria) return

    setToggleLoading(true)
    try {
      const nuevoEstado: 0 | 1 = cafeteria.estado === 0 ? 1 : 0
      await onToggleEstado(cafeteria.id, nuevoEstado)
    } catch (err) {
      // El error ya se maneja en el controller
    } finally {
      setToggleLoading(false)
    }
  }

  const handleDeleteClick = () => {
    setDeleteDialogOpen(true)
  }

  const handleDeleteConfirm = async () => {
    if (!cafeteria) return

    setDeleteLoading(true)
    try {
      await onDelete(cafeteria.id)
      setDeleteDialogOpen(false)
      onClose()
    } catch (err) {
      // El error ya se maneja en el controller
    } finally {
      setDeleteLoading(false)
    }
  }

  return (
    <>
      <Drawer
        anchor={anchor}
        open={open}
        onClose={onClose}
        PaperProps={{
          sx: {
            width: { xs: '100%', lg: 500 },
            maxHeight: { xs: '90vh', lg: '100vh' },
            borderTopLeftRadius: { xs: designTokens.spacing.borderRadius.large, lg: 0 },
            borderTopRightRadius: { xs: designTokens.spacing.borderRadius.large, lg: 0 },
            pt: { xs: 0, lg: designTokens.spacing.navbarHeight },
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
              {cafeteria?.nombre || 'Cargando...'}
            </Typography>
            <IconButton onClick={onClose} sx={{ color: 'white' }}>
              <CloseIcon />
            </IconButton>
          </Box>

          {/* Contenido scrollable */}
          <Box sx={{ flexGrow: 1, overflowY: 'auto', p: 3 }}>
            {loading && (
              <Box sx={{ display: 'flex', justifyContent: 'center', py: 8 }}>
                <CircularProgress />
              </Box>
            )}

            {cafeteria && (
              <>
                {/* Imagen */}
                {imagenUrl && (
                  <Box
                    sx={{
                      width: '100%',
                      borderRadius: 2,
                      overflow: 'hidden',
                      mb: 3,
                      backgroundColor: designTokens.colors.grisClaro,
                    }}
                  >
                    <img
                      src={imagenUrl}
                      alt={cafeteria.nombre}
                      style={{
                        width: '100%',
                        height: 'auto',
                        maxHeight: 200,
                        objectFit: 'cover',
                        display: 'block',
                      }}
                    />
                  </Box>
                )}

                {/* Información */}
                <Box sx={{ mb: 3 }}>
                  <Typography variant="h6" fontWeight={600} sx={{ mb: 2, color: designTokens.colors.texto }}>
                    Información
                  </Typography>

                  {cafeteria.direccion && (
                    <Box sx={{ display: 'flex', alignItems: 'flex-start', gap: 1, mb: 2 }}>
                      <LocationOnIcon sx={{ color: designTokens.colors.principal, mt: 0.5 }} />
                      <Typography variant="body2" sx={{ flex: 1 }}>
                        {cafeteria.direccion}
                        {cafeteria.ciudad && `, ${cafeteria.ciudad}`}
                        {cafeteria.entidad_federativa && `, ${cafeteria.entidad_federativa}`}
                      </Typography>
                    </Box>
                  )}

                  {(cafeteria.horario_apertura || cafeteria.horario_cierre) && (
                    <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mb: 2 }}>
                      <AccessTimeIcon sx={{ color: designTokens.colors.principal }} />
                      <Typography variant="body2">
                        {cafeteria.horario_apertura || '--'} - {cafeteria.horario_cierre || '--'}
                      </Typography>
                    </Box>
                  )}

                  {cafeteria.telefono && (
                    <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mb: 2 }}>
                      <PhoneIcon sx={{ color: designTokens.colors.principal }} />
                      <Typography variant="body2" component="a" href={`tel:${cafeteria.telefono}`}>
                        {cafeteria.telefono}
                      </Typography>
                    </Box>
                  )}

                  {cafeteria.correo && (
                    <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mb: 2 }}>
                      <EmailIcon sx={{ color: designTokens.colors.principal }} />
                      <Typography variant="body2" component="a" href={`mailto:${cafeteria.correo}`}>
                        {cafeteria.correo}
                      </Typography>
                    </Box>
                  )}

                  {cafeteria.descripcion && (
                    <Box sx={{ mt: 2 }}>
                      <Typography variant="body2" color="text.secondary">
                        {cafeteria.descripcion}
                      </Typography>
                    </Box>
                  )}
                </Box>

                <Divider sx={{ my: 3 }} />

                {/* Acciones */}
                <Box sx={{ mb: 3 }}>
                  <Typography variant="h6" fontWeight={600} sx={{ mb: 2, color: designTokens.colors.texto }}>
                    Acciones
                  </Typography>

                  <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
                    {/* Editar */}
                    <Button
                      variant="outlined"
                      startIcon={<EditIcon />}
                      onClick={() => {
                        navigate(`/cafeterias/${cafeteria.id}/editar`)
                        onClose()
                      }}
                      sx={{
                        borderColor: designTokens.colors.principal,
                        color: designTokens.colors.principal,
                        textTransform: 'none',
                        justifyContent: 'flex-start',
                      }}
                    >
                      Editar Cafetería
                    </Button>

                    {/* Gestionar Servicios */}
                    <Button
                      variant="outlined"
                      startIcon={<MenuBookIcon />}
                      onClick={() => {
                        setServiciosModalOpen(true)
                      }}
                      sx={{
                        borderColor: designTokens.colors.principal,
                        color: designTokens.colors.principal,
                        textTransform: 'none',
                        justifyContent: 'flex-start',
                      }}
                    >
                      Gestionar Servicios
                    </Button>

                    {/* Gestionar Menú */}
                    <Button
                      variant="outlined"
                      startIcon={<MenuBookIcon />}
                      onClick={() => {
                        navigate('/menu')
                        onClose()
                      }}
                      sx={{
                        borderColor: designTokens.colors.principal,
                        color: designTokens.colors.principal,
                        textTransform: 'none',
                        justifyContent: 'flex-start',
                      }}
                    >
                      Gestionar Menú
                    </Button>

                    {/* Ver Pedidos */}
                    <Button
                      variant="outlined"
                      startIcon={<ReceiptIcon />}
                      onClick={() => {
                        // Placeholder
                        onClose()
                      }}
                      sx={{
                        borderColor: designTokens.colors.principal,
                        color: designTokens.colors.principal,
                        textTransform: 'none',
                        justifyContent: 'flex-start',
                      }}
                    >
                      Ver Pedidos
                    </Button>

                    {/* Ver Código QR */}
                    <Button
                      variant="outlined"
                      startIcon={<QrCodeIcon />}
                      onClick={() => {
                        // Placeholder
                        onClose()
                      }}
                      sx={{
                        borderColor: designTokens.colors.principal,
                        color: designTokens.colors.principal,
                        textTransform: 'none',
                        justifyContent: 'flex-start',
                      }}
                    >
                      Ver Código QR
                    </Button>

                    {/* Gestionar Galería */}
                    <Button
                      variant="outlined"
                      startIcon={<PhotoLibraryIcon />}
                      onClick={() => setGaleriaModalOpen(true)}
                      disabled={mutating || !cafeteria}
                      sx={{
                        borderColor: designTokens.colors.principal,
                        color: designTokens.colors.principal,
                        textTransform: 'none',
                        justifyContent: 'flex-start',
                      }}
                    >
                      Gestionar Galería
                    </Button>

                    {/* Switch Estado */}
                    <FormControlLabel
                      control={
                        <Switch
                          checked={cafeteria.estado === 0}
                          onChange={handleToggleEstado}
                          disabled={mutating || toggleLoading}
                          color="primary"
                        />
                      }
                      label="Cafetería Activa"
                      sx={{
                        mt: 1,
                        '& .MuiFormControlLabel-label': {
                          fontWeight: 500,
                        },
                      }}
                    />

                    {/* Eliminar */}
                    <Button
                      variant="outlined"
                      color="error"
                      startIcon={<DeleteIcon />}
                      onClick={handleDeleteClick}
                      disabled={mutating || deleteLoading}
                      sx={{
                        textTransform: 'none',
                        justifyContent: 'flex-start',
                        mt: 1,
                      }}
                    >
                      Eliminar Cafetería
                    </Button>
                  </Box>
                </Box>
              </>
            )}
          </Box>
        </Box>
      </Drawer>

      {/* Dialog de confirmación de eliminación */}
      <Dialog open={deleteDialogOpen} onClose={() => !deleteLoading && setDeleteDialogOpen(false)}>
        <DialogTitle>¿Eliminar cafetería?</DialogTitle>
        <DialogContent>
          <DialogContentText>
            ¿Estás seguro que deseas eliminar la cafetería "{cafeteria?.nombre}"? Esta acción no se puede
            deshacer.
          </DialogContentText>
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setDeleteDialogOpen(false)} disabled={deleteLoading}>
            Cancelar
          </Button>
          <Button onClick={handleDeleteConfirm} color="error" disabled={deleteLoading}>
            {deleteLoading ? <CircularProgress size={20} /> : 'Eliminar'}
          </Button>
        </DialogActions>
      </Dialog>

      {/* Modal de servicios */}
      <ServiciosModal
        open={serviciosModalOpen}
        onClose={() => setServiciosModalOpen(false)}
        cafeteriaId={cafeteria?.id || null}
        onLoadServicios={onLoadServicios}
        onSaveServicios={onSaveServicios}
      />

      {/* Modal de galería */}
      {cafeteria && (
        <GaleriaImagenesModal
          open={galeriaModalOpen}
          onClose={() => setGaleriaModalOpen(false)}
          cafeteriaId={cafeteria.id}
          repository={repository}
          token={token}
        />
      )}
    </>
  )
}
