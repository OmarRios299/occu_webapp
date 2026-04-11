import { Grid, Box, Skeleton, Typography } from '@mui/material'
import { CafeteriaCard } from './CafeteriaCard'
import type { CafeteriaListItem } from '../types'

type CafeteriasGridProps = {
  cafeterias: CafeteriaListItem[]
  loading?: boolean
  onCafeteriaClick?: (cafeteria: CafeteriaListItem) => void
}

export function CafeteriasGrid({ cafeterias, loading, onCafeteriaClick }: CafeteriasGridProps) {
  if (loading) {
    return (
      <Grid container spacing={3}>
        {Array.from({ length: 9 }).map((_, index) => (
          <Grid item xs={12} sm={6} md={4} key={index}>
            <Skeleton variant="rectangular" height={300} sx={{ borderRadius: 2 }} />
          </Grid>
        ))}
      </Grid>
    )
  }

  if (cafeterias.length === 0) {
    return (
      <Box
        sx={{
          textAlign: 'center',
          py: 8,
          color: 'text.secondary',
        }}
      >
        <Typography variant="h6" gutterBottom>
          No se encontraron cafeterías
        </Typography>
        <Typography variant="body2">
          Intenta ajustar los filtros de búsqueda
        </Typography>
      </Box>
    )
  }

  return (
    <Grid container spacing={2.5}>
      {cafeterias.map((cafeteria) => (
        <Grid item xs={12} sm={6} md={4} key={cafeteria.id}>
          <CafeteriaCard
            cafeteria={cafeteria}
            onClick={() => onCafeteriaClick?.(cafeteria)}
          />
        </Grid>
      ))}
    </Grid>
  )
}
