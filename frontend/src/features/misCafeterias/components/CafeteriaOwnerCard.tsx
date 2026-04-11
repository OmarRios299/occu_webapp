import { Card, CardContent, CardMedia, Box, Typography, Chip, useTheme } from '@mui/material'
import { designTokens } from '@/theme/tokens'
import type { CafeteriaOwner } from '../types'
import type { EstadoActualizacionCafeteria } from '@/features/propietarioMenuPublicacion/types'

type CafeteriaOwnerCardProps = {
  cafeteria: CafeteriaOwner
  onClick?: () => void
  estadoActualizacion?: EstadoActualizacionCafeteria
}

export function CafeteriaOwnerCard({ cafeteria, onClick, estadoActualizacion }: CafeteriaOwnerCardProps) {
  const theme = useTheme()
  const isActiva = cafeteria.estado === 0
  const desactualizado = estadoActualizacion?.desactualizado ?? false

  // El backend ya devuelve URLs completas, pero mantenemos fallback
  const imagenUrl = cafeteria.imagen || '/placeholder-cafeteria.jpg'

  return (
    <Card
      onClick={onClick}
      sx={{
        height: '100%',
        display: 'flex',
        flexDirection: 'column',
        cursor: onClick ? 'pointer' : 'default',
        transition: 'transform 0.2s, box-shadow 0.2s',
        borderRadius: designTokens.spacing.borderRadius.medium,
        overflow: 'hidden',
        backgroundColor: 'white',
        boxShadow: '0 2px 8px rgba(0, 0, 0, 0.1)',
        '&:hover': onClick
          ? {
              transform: 'translateY(-2px)',
              boxShadow: '0 4px 12px rgba(0, 0, 0, 0.15)',
            }
          : {},
      }}
    >
      {/* Imagen con badge de estado */}
      <Box sx={{ position: 'relative', width: '100%', paddingTop: '75%' }}>
        <CardMedia
          component="img"
          image={imagenUrl}
          alt={cafeteria.nombre}
          sx={{
            position: 'absolute',
            top: 0,
            left: 0,
            width: '100%',
            height: '100%',
            objectFit: 'cover',
            backgroundColor: designTokens.colors.grisClaro,
          }}
        />
        {/* Badges de estado */}
        <Box
          sx={{
            position: 'absolute',
            top: 8,
            right: 8,
            display: 'flex',
            flexDirection: 'column',
            gap: 0.5,
          }}
        >
          <Chip
            label={isActiva ? 'Activa' : 'Inactiva'}
            sx={{
              backgroundColor: isActiva ? '#4caf50' : '#f44336',
              color: 'white',
              fontWeight: 600,
              fontSize: '12px',
              height: '24px',
              borderRadius: '12px',
              boxShadow: '0 2px 4px rgba(0, 0, 0, 0.2)',
            }}
          />
          {desactualizado && (
            <Chip
              label="Menú desactualizado"
              sx={{
                backgroundColor: '#ff9800',
                color: 'white',
                fontWeight: 600,
                fontSize: '11px',
                height: '22px',
                borderRadius: '11px',
                boxShadow: '0 2px 4px rgba(0, 0, 0, 0.2)',
              }}
            />
          )}
        </Box>
      </Box>

      {/* Contenido */}
      <CardContent
        sx={{
          flexGrow: 1,
          display: 'flex',
          flexDirection: 'column',
          gap: 0.5,
          padding: '16px !important',
        }}
      >
        {/* Nombre */}
        <Typography
          variant="h6"
          component="h3"
          fontWeight={700}
          sx={{
            color: designTokens.colors.texto,
            fontSize: '18px',
            mb: 0.5,
            lineHeight: 1.3,
          }}
        >
          {cafeteria.nombre}
        </Typography>

        {/* Dirección */}
        <Typography
          variant="body2"
          sx={{
            color: designTokens.colors.gris,
            fontSize: '13px',
            lineHeight: 1.5,
            mb: 0.5,
          }}
        >
          {cafeteria.direccion || 'Sin dirección'}
        </Typography>

        {/* Teléfono */}
        <Typography
          variant="body2"
          sx={{
            color: designTokens.colors.gris,
            fontSize: '13px',
            lineHeight: 1.5,
            mb: 0.5,
          }}
        >
          {cafeteria.telefono || 'Sin teléfono'}
        </Typography>

        {/* Horario */}
        {(cafeteria.horario_apertura || cafeteria.horario_cierre) && (
          <Typography
            variant="body2"
            sx={{
              color: designTokens.colors.gris,
              fontSize: '13px',
              lineHeight: 1.5,
              mt: 0.5,
              pt: 0.5,
              borderTop: `1px solid ${designTokens.colors.grisClaro}`,
            }}
          >
            {cafeteria.horario_apertura || '--'} - {cafeteria.horario_cierre || '--'}
          </Typography>
        )}
      </CardContent>
    </Card>
  )
}
