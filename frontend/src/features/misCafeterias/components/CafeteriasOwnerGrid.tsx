import { Box, Grid, CircularProgress, Alert, Typography } from '@mui/material'
import { designTokens } from '@/theme/tokens'
import type { CafeteriaOwner } from '../types'
import type { EstadosActualizacion } from '@/features/propietarioMenuPublicacion/types'
import { CafeteriaOwnerCard } from './CafeteriaOwnerCard'

type CafeteriasOwnerGridProps = {
  cafeterias: CafeteriaOwner[]
  loading?: boolean
  onCafeteriaClick: (cafeteria: CafeteriaOwner) => void
  estadosActualizacion?: EstadosActualizacion
}

export function CafeteriasOwnerGrid({ cafeterias, loading, onCafeteriaClick, estadosActualizacion = {} }: CafeteriasOwnerGridProps) {
  if (loading) {
    return (
      <Box sx={{ display: 'flex', justifyContent: 'center', py: 8 }}>
        <CircularProgress />
      </Box>
    )
  }

  if (cafeterias.length === 0) {
    return (
      <Box
        sx={{
          textAlign: 'center',
          py: 8,
          px: 2,
        }}
      >
        <Typography
          variant="h6"
          sx={{
            color: designTokens.colors.gris,
            mb: 2,
          }}
        >
          No tienes cafeterías registradas
        </Typography>
        <Typography
          variant="body2"
          sx={{
            color: designTokens.colors.gris,
          }}
        >
          Agrega tu primera cafetería para comenzar
        </Typography>
      </Box>
    )
  }

  return (
    <Grid container spacing={3}>
      {cafeterias.map((cafeteria) => (
        <Grid item xs={12} sm={6} md={4} key={cafeteria.id}>
          <CafeteriaOwnerCard
            cafeteria={cafeteria}
            onClick={() => onCafeteriaClick(cafeteria)}
            estadoActualizacion={estadosActualizacion[cafeteria.id]}
          />
        </Grid>
      ))}
    </Grid>
  )
}
