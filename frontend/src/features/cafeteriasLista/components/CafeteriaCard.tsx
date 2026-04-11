import { Card, CardContent, CardMedia, Box, Typography, Chip, useTheme } from '@mui/material'
import { designTokens } from '@/theme/tokens'
import type { CafeteriaListItem } from '../types'

type CafeteriaCardProps = {
  cafeteria: CafeteriaListItem
  onClick?: () => void
}

export function CafeteriaCard({ cafeteria, onClick }: CafeteriaCardProps) {
  const theme = useTheme()

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
          image={cafeteria.imagen || '/placeholder-cafeteria.jpg'}
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
        {/* Badge en esquina superior izquierda */}
        <Box
          sx={{
            position: 'absolute',
            top: 8,
            left: 8,
          }}
        >
          <Chip
            label={cafeteria.isOpenNow ? 'Abierto' : 'Cerrado'}
            sx={{
              backgroundColor: cafeteria.isOpenNow ? '#4caf50' : '#f44336',
              color: 'white',
              fontWeight: 600,
              fontSize: '12px',
              height: '24px',
              borderRadius: '12px',
              boxShadow: '0 2px 4px rgba(0, 0, 0, 0.2)',
            }}
          />
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
          {cafeteria.direccion}
        </Typography>

        {/* Horario */}
        <Typography
          variant="body2"
          sx={{
            color: designTokens.colors.gris,
            fontSize: '13px',
            lineHeight: 1.5,
          }}
        >
          {cafeteria.todayScheduleLabel}
        </Typography>
      </CardContent>
    </Card>
  )
}
