import { useState } from 'react'
import {
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Paper,
  IconButton,
  Select,
  MenuItem,
  TextField,
  Box,
  Button,
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  FormControl,
  InputLabel,
  Alert,
  Typography,
  Tooltip,
} from '@mui/material'
import DeleteIcon from '@mui/icons-material/Delete'
import AddIcon from '@mui/icons-material/Add'
import InfoIcon from '@mui/icons-material/Info'
import { designTokens } from '@/theme/tokens'
import type { ReglaCategoria, CatalogoCategoria } from '../types'

// Ayuda contextual por categoría
const ayudaPorCategoria: Record<string, string> = {
  'Leche': 'Selección única: el cliente debe elegir exactamente un tipo de leche.',
  'Saborizantes /Jarabes': 'Selección múltiple con cantidad: el cliente puede elegir varios jarabes y especificar la cantidad de cada uno (ej: 2 vainilla, 1 caramelo).',
  'Tipo de Grano / Café': 'Selección única: el cliente elige un solo tipo de grano.',
  'Endulzantes': 'Selección múltiple: el cliente puede elegir varios endulzantes.',
  'Toppings': 'Selección múltiple: el cliente puede agregar varios toppings.',
}

interface ReglasCategoriasTableProps {
  categorias: ReglaCategoria[]
  catalogoCategorias: CatalogoCategoria[]
  onAgregar: (categoriaId: number) => void
  onEliminar: (categoriaId: number) => void
  onActualizar: (
    categoriaId: number,
    updates: Partial<ReglaCategoria>
  ) => void
}

export function ReglasCategoriasTable({
  categorias,
  catalogoCategorias,
  onAgregar,
  onEliminar,
  onActualizar,
}: ReglasCategoriasTableProps) {
  const [dialogOpen, setDialogOpen] = useState(false)
  const [selectedCategoriaId, setSelectedCategoriaId] = useState<number | ''>('')

  const categoriasDisponibles = catalogoCategorias.filter(
    (cat) => !categorias.some((c) => c.id_ingrediente_categoria === cat.id)
  )

  const handleAgregar = () => {
    if (selectedCategoriaId && typeof selectedCategoriaId === 'number') {
      onAgregar(selectedCategoriaId)
      setDialogOpen(false)
      setSelectedCategoriaId('')
    }
  }

  return (
    <>
      {/* Ayuda general */}
      <Alert severity="info" sx={{ mb: 2 }}>
        <Typography variant="body2" sx={{ fontWeight: 600, mb: 0.5 }}>
          Tipos de selección:
        </Typography>
        <Typography variant="body2" component="div">
          • <strong>Única:</strong> El cliente debe elegir exactamente una opción (ej: tipo de leche)
          <br />
          • <strong>Múltiple:</strong> El cliente puede elegir varias opciones. Usa "Máximo" para limitar o déjalo vacío para sin límite.
        </Typography>
      </Alert>

      <Box sx={{ mb: 2, display: 'flex', justifyContent: 'flex-end' }}>
        <Button
          variant="contained"
          startIcon={<AddIcon />}
          onClick={() => setDialogOpen(true)}
          disabled={categoriasDisponibles.length === 0}
          sx={{
            backgroundColor: designTokens.colors.principal,
            textTransform: 'none',
            '&:hover': {
              backgroundColor: designTokens.colors.sidebarDark,
            },
          }}
        >
          Agregar Categoría
        </Button>
      </Box>

      <TableContainer component={Paper}>
        <Table>
          <TableHead>
            <TableRow sx={{ backgroundColor: designTokens.colors.plantillaClaro }}>
              <TableCell sx={{ fontWeight: 600 }}>Categoría</TableCell>
              <TableCell sx={{ fontWeight: 600 }}>Tipo Selección</TableCell>
              <TableCell sx={{ fontWeight: 600 }}>Mínimo</TableCell>
              <TableCell sx={{ fontWeight: 600 }}>Máximo</TableCell>
              <TableCell sx={{ fontWeight: 600 }}>Orden</TableCell>
              <TableCell sx={{ fontWeight: 600 }} align="right">
                Acciones
              </TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {categorias.length === 0 ? (
              <TableRow>
                <TableCell colSpan={6} align="center" sx={{ py: 4 }}>
                  No hay categorías aplicables. Agrega una para comenzar.
                </TableCell>
              </TableRow>
            ) : (
              categorias.map((cat) => {
                const ayuda = ayudaPorCategoria[cat.categoria_nombre]
                return (
                  <TableRow key={cat.id_ingrediente_categoria} hover>
                    <TableCell sx={{ fontWeight: 500 }}>
                      <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                        {cat.categoria_nombre}
                        {ayuda && (
                          <Tooltip title={ayuda} arrow>
                            <IconButton size="small" sx={{ p: 0.5 }}>
                              <InfoIcon fontSize="small" sx={{ color: designTokens.colors.principal }} />
                            </IconButton>
                          </Tooltip>
                        )}
                      </Box>
                    </TableCell>
                  <TableCell>
                    <Select
                      value={cat.tipo_seleccion}
                      onChange={(e) =>
                        onActualizar(cat.id_ingrediente_categoria, {
                          tipo_seleccion: e.target.value as 'unica' | 'multiple',
                          seleccion_maxima:
                            e.target.value === 'unica' ? 1 : cat.seleccion_maxima,
                        })
                      }
                      size="small"
                      sx={{ minWidth: 120 }}
                    >
                      <MenuItem value="unica">Única</MenuItem>
                      <MenuItem value="multiple">Múltiple</MenuItem>
                    </Select>
                  </TableCell>
                  <TableCell>
                    <TextField
                      type="number"
                      value={cat.seleccion_minima}
                      onChange={(e) =>
                        onActualizar(cat.id_ingrediente_categoria, {
                          seleccion_minima: parseInt(e.target.value, 10) || 0,
                        })
                      }
                      size="small"
                      inputProps={{ min: 0 }}
                      sx={{ width: 80 }}
                    />
                  </TableCell>
                  <TableCell>
                    <TextField
                      type="number"
                      value={cat.seleccion_maxima ?? ''}
                      onChange={(e) =>
                        onActualizar(cat.id_ingrediente_categoria, {
                          seleccion_maxima:
                            e.target.value === ''
                              ? null
                              : parseInt(e.target.value, 10) || null,
                        })
                      }
                      placeholder="Sin límite"
                      size="small"
                      inputProps={{ min: cat.seleccion_minima }}
                      sx={{ width: 100 }}
                    />
                  </TableCell>
                  <TableCell>
                    <TextField
                      type="number"
                      value={cat.orden}
                      onChange={(e) =>
                        onActualizar(cat.id_ingrediente_categoria, {
                          orden: parseInt(e.target.value, 10) || 0,
                        })
                      }
                      size="small"
                      sx={{ width: 80 }}
                    />
                  </TableCell>
                  <TableCell align="right">
                    <IconButton
                      onClick={() => onEliminar(cat.id_ingrediente_categoria)}
                      sx={{
                        color: designTokens.colors.rojo,
                        '&:hover': {
                          backgroundColor: `${designTokens.colors.rojo}20`,
                        },
                      }}
                    >
                      <DeleteIcon />
                    </IconButton>
                  </TableCell>
                </TableRow>
                )
              })
            )}
          </TableBody>
        </Table>
      </TableContainer>

      {/* Dialog para agregar categoría */}
      <Dialog open={dialogOpen} onClose={() => setDialogOpen(false)}>
        <DialogTitle>Agregar Categoría</DialogTitle>
        <DialogContent>
          <FormControl fullWidth sx={{ mt: 2 }}>
            <InputLabel>Categoría</InputLabel>
            <Select
              value={selectedCategoriaId}
              onChange={(e) => setSelectedCategoriaId(e.target.value as number)}
              label="Categoría"
            >
              {categoriasDisponibles.map((cat) => (
                <MenuItem key={cat.id} value={cat.id}>
                  {cat.nombre}
                </MenuItem>
              ))}
            </Select>
          </FormControl>
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setDialogOpen(false)}>Cancelar</Button>
          <Button
            onClick={handleAgregar}
            variant="contained"
            disabled={!selectedCategoriaId}
            sx={{
              backgroundColor: designTokens.colors.principal,
              '&:hover': {
                backgroundColor: designTokens.colors.sidebarDark,
              },
            }}
          >
            Agregar
          </Button>
        </DialogActions>
      </Dialog>
    </>
  )
}
