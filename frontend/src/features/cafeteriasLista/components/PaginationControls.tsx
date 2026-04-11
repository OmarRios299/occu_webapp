import { Box, Button, Typography, Pagination } from '@mui/material'
import ChevronLeftIcon from '@mui/icons-material/ChevronLeft'
import ChevronRightIcon from '@mui/icons-material/ChevronRight'

type PaginationControlsProps = {
  page: number
  totalPages: number
  totalItems: number
  pageSize: number
  onPageChange: (page: number) => void
}

export function PaginationControls({
  page,
  totalPages,
  totalItems,
  pageSize,
  onPageChange,
}: PaginationControlsProps) {
  const startItem = totalItems === 0 ? 0 : (page - 1) * pageSize + 1
  const endItem = Math.min(page * pageSize, totalItems)

  return (
    <Box
      sx={{
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center',
        flexWrap: 'wrap',
        gap: 2,
        mt: 4,
      }}
    >
      {/* Información de página */}
      <Typography variant="body2" color="text.secondary">
        Página {page} de {totalPages > 0 ? totalPages : 1}
        {totalItems > 0 && (
          <>
            {' '}
            ({startItem}-{endItem} de {totalItems})
          </>
        )}
      </Typography>

      {/* Controles de paginación */}
      <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
        <Button
          variant="outlined"
          startIcon={<ChevronLeftIcon />}
          onClick={() => onPageChange(page - 1)}
          disabled={page <= 1}
        >
          Anterior
        </Button>

        <Pagination
          count={totalPages}
          page={page}
          onChange={(_, value) => onPageChange(value)}
          color="primary"
          hidePrevButton
          hideNextButton
          sx={{
            '& .MuiPaginationItem-root': {
              minWidth: 40,
            },
          }}
        />

        <Button
          variant="outlined"
          endIcon={<ChevronRightIcon />}
          onClick={() => onPageChange(page + 1)}
          disabled={page >= totalPages}
        >
          Siguiente
        </Button>
      </Box>
    </Box>
  )
}
