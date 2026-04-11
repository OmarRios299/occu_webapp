import { useState, useEffect } from 'react'
import {
  Box,
  Typography,
  TextField,
  Button,
  List,
  ListItem,
  Avatar,
  Divider,
  CircularProgress,
  Alert,
  Pagination,
} from '@mui/material'
import SendIcon from '@mui/icons-material/Send'
import PersonIcon from '@mui/icons-material/Person'
import { designTokens } from '@/theme/tokens'
import { useAuth } from '@/store/auth/AuthProvider'
import type { Comentario, Paginated } from '../types'

type ComentariosSectionProps = {
  cafeteriaId: number
  onLoadComentarios: (cafeteriaId: number, page: number, pageSize: number) => Promise<Paginated<Comentario>>
  onCreateComentario: (cafeteriaId: number, comentario: string, token: string) => Promise<void>
}

export function ComentariosSection({
  cafeteriaId,
  onLoadComentarios,
  onCreateComentario,
}: ComentariosSectionProps) {
  const { session } = useAuth()
  const [comentarios, setComentarios] = useState<Paginated<Comentario> | null>(null)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [newComment, setNewComment] = useState('')
  const [submitting, setSubmitting] = useState(false)
  const [page, setPage] = useState(1)
  const pageSize = 5

  const loadComentarios = async (pageNum: number) => {
    setLoading(true)
    setError(null)
    try {
      const result = await onLoadComentarios(cafeteriaId, pageNum, pageSize)
      setComentarios(result)
      setPage(pageNum)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Error al cargar comentarios')
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    if (cafeteriaId) {
      loadComentarios(1)
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [cafeteriaId])

  const handleSubmit = async () => {
    if (!session || !newComment.trim()) return

    setSubmitting(true)
    setError(null)
    try {
      await onCreateComentario(cafeteriaId, newComment.trim(), session.accessToken)
      setNewComment('')
      // Recargar comentarios desde la página 1
      await loadComentarios(1)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Error al enviar comentario')
    } finally {
      setSubmitting(false)
    }
  }

  const formatDate = (dateString: string) => {
    try {
      const date = new Date(dateString)
      return date.toLocaleDateString('es-MX', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      })
    } catch {
      return dateString.split(' ')[0] // Solo la fecha sin hora
    }
  }

  return (
    <Box sx={{ mt: 4 }}>
      <Divider sx={{ my: 3 }} />
      <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mb: 2 }}>
        <Typography variant="h6" fontWeight={600} sx={{ color: designTokens.colors.texto }}>
          Comentarios
        </Typography>
        {comentarios && (
          <Typography variant="body2" color="text.secondary">
            ({comentarios.totalItems})
          </Typography>
        )}
      </Box>

      {/* Formulario para nuevo comentario (solo si está logueado) */}
      {session && (
        <Box sx={{ mb: 3 }}>
          <TextField
            fullWidth
            multiline
            rows={3}
            placeholder="Escribe un comentario..."
            value={newComment}
            onChange={(e) => setNewComment(e.target.value)}
            disabled={submitting}
            sx={{
              mb: 1,
              '& .MuiOutlinedInput-root': {
                backgroundColor: designTokens.colors.bgBody,
              },
            }}
          />
          <Box sx={{ display: 'flex', justifyContent: 'flex-end' }}>
            <Button
              variant="contained"
              startIcon={<SendIcon />}
              onClick={handleSubmit}
              disabled={submitting || !newComment.trim()}
              sx={{
                backgroundColor: designTokens.colors.principal,
                '&:hover': {
                  backgroundColor: designTokens.colors.sidebarDark,
                },
              }}
            >
              {submitting ? 'Enviando...' : 'Enviar'}
            </Button>
          </Box>
        </Box>
      )}

      {!session && (
        <Alert severity="info" sx={{ mb: 2 }}>
          Inicia sesión para dejar un comentario
        </Alert>
      )}

      {error && (
        <Alert severity="error" sx={{ mb: 2 }}>
          {error}
        </Alert>
      )}

      {/* Lista de comentarios */}
      {loading && (
        <Box sx={{ display: 'flex', justifyContent: 'center', py: 4 }}>
          <CircularProgress />
        </Box>
      )}

      {comentarios && comentarios.items.length === 0 && !loading && (
        <Box sx={{ textAlign: 'center', py: 4, color: 'text.secondary' }}>
          <Typography variant="body2">No hay comentarios aún</Typography>
        </Box>
      )}

      {comentarios && comentarios.items.length > 0 && (
        <>
          <List>
            {comentarios.items.map((comentario) => (
              <ListItem
                key={comentario.id}
                sx={{
                  flexDirection: 'column',
                  alignItems: 'flex-start',
                  py: 2,
                  px: 0,
                  borderBottom: `1px solid ${designTokens.colors.grisClaro}`,
                }}
              >
                <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mb: 1, width: '100%' }}>
                  <Avatar sx={{ bgcolor: designTokens.colors.principal, width: 32, height: 32 }}>
                    <PersonIcon fontSize="small" />
                  </Avatar>
                  <Box sx={{ flex: 1 }}>
                    <Typography variant="subtitle2" fontWeight={600}>
                      {comentario.nombre_usuario}
                    </Typography>
                    <Typography variant="caption" color="text.secondary">
                      {formatDate(comentario.fecha_alta)}
                    </Typography>
                  </Box>
                </Box>
                <Typography variant="body2" sx={{ pl: 5, whiteSpace: 'pre-wrap' }}>
                  {comentario.comentario}
                </Typography>
              </ListItem>
            ))}
          </List>

          {/* Paginación */}
          {comentarios.totalPages > 1 && (
            <Box sx={{ display: 'flex', justifyContent: 'center', mt: 3 }}>
              <Pagination
                count={comentarios.totalPages}
                page={page}
                onChange={(_, value) => loadComentarios(value)}
                color="primary"
                size="small"
              />
            </Box>
          )}
        </>
      )}
    </Box>
  )
}
