import { useState, useMemo } from 'react'
import {
  Box,
  TextField,
  FormControl,
  InputLabel,
  Select,
  MenuItem,
  Paper,
  Checkbox,
  FormControlLabel,
  Typography,
  InputAdornment,
  Chip,
} from '@mui/material'
import SearchIcon from '@mui/icons-material/Search'
import { designTokens } from '@/theme/tokens'
import type { ReglaIngrediente, CatalogoIngrediente } from '../types'

interface ReglasIngredientesListProps {
  ingredientes: ReglaIngrediente[]
  catalogoIngredientes: CatalogoIngrediente[]
  categoriaIds: number[]
  onToggle: (ingredienteId: number, habilitado: boolean) => void
  onActualizar: (
    ingredienteId: number,
    updates: Partial<ReglaIngrediente>
  ) => void
}

export function ReglasIngredientesList({
  ingredientes,
  catalogoIngredientes,
  categoriaIds,
  onToggle,
  onActualizar,
}: ReglasIngredientesListProps) {
  const [searchQuery, setSearchQuery] = useState('')
  const [categoriaFiltro, setCategoriaFiltro] = useState<number | ''>('')

  // Filtrar ingredientes del catálogo por categorías aplicables
  const ingredientesFiltrados = useMemo(() => {
    let filtered = catalogoIngredientes.filter((ing) =>
      categoriaIds.includes(ing.id_ingrediente_categoria)
    )

    if (categoriaFiltro) {
      filtered = filtered.filter(
        (ing) => ing.id_ingrediente_categoria === categoriaFiltro
      )
    }

    if (searchQuery) {
      const query = searchQuery.toLowerCase()
      filtered = filtered.filter((ing) =>
        ing.nombre.toLowerCase().includes(query)
      )
    }

    return filtered
  }, [catalogoIngredientes, categoriaIds, categoriaFiltro, searchQuery])

  const categoriasUnicas = useMemo(() => {
    const cats = new Map<number, string>()
    ingredientesFiltrados.forEach((ing) => {
      if (!cats.has(ing.id_ingrediente_categoria)) {
        cats.set(ing.id_ingrediente_categoria, ing.categoria_nombre)
      }
    })
    return Array.from(cats.entries()).map(([id, nombre]) => ({ id, nombre }))
  }, [ingredientesFiltrados])

  const getReglaIngrediente = (ingredienteId: number): ReglaIngrediente | null => {
    return ingredientes.find((i) => i.id_ingrediente === ingredienteId) || null
  }

  return (
    <Box>
      {/* Filtros */}
      <Box sx={{ mb: 3, display: 'flex', gap: 2, flexWrap: 'wrap' }}>
        <TextField
          placeholder="Buscar ingrediente..."
          value={searchQuery}
          onChange={(e) => setSearchQuery(e.target.value)}
          InputProps={{
            startAdornment: (
              <InputAdornment position="start">
                <SearchIcon />
              </InputAdornment>
            ),
          }}
          sx={{
            flex: 1,
            minWidth: 200,
            backgroundColor: 'white',
            '& .MuiOutlinedInput-root': {
              borderRadius: designTokens.spacing.borderRadius.medium,
            },
          }}
        />
        <FormControl sx={{ minWidth: 200 }}>
          <InputLabel>Categoría</InputLabel>
          <Select
            value={categoriaFiltro}
            onChange={(e) => setCategoriaFiltro(e.target.value as number | '')}
            label="Categoría"
          >
            <MenuItem value="">Todas</MenuItem>
            {categoriasUnicas.map((cat) => (
              <MenuItem key={cat.id} value={cat.id}>
                {cat.nombre}
              </MenuItem>
            ))}
          </Select>
        </FormControl>
      </Box>

      {/* Lista de ingredientes */}
      {ingredientesFiltrados.length === 0 ? (
        <Paper sx={{ p: 4, textAlign: 'center' }}>
          <Typography color="text.secondary">
            No se encontraron ingredientes
          </Typography>
        </Paper>
      ) : (
        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
          {ingredientesFiltrados.map((ing) => {
            const regla = getReglaIngrediente(ing.id)
            const habilitado = regla !== null

            return (
              <Paper key={ing.id} sx={{ p: 2 }}>
                <Box sx={{ display: 'flex', alignItems: 'flex-start', gap: 2 }}>
                  {/* Toggle habilitado */}
                  <FormControlLabel
                    control={
                      <Checkbox
                        checked={habilitado}
                        onChange={(e) => onToggle(ing.id, e.target.checked)}
                        sx={{
                          color: designTokens.colors.principal,
                          '&.Mui-checked': {
                            color: designTokens.colors.principal,
                          },
                        }}
                      />
                    }
                    label={
                      <Box>
                        <Typography variant="body1" sx={{ fontWeight: 500 }}>
                          {ing.nombre}
                        </Typography>
                        <Chip
                          label={ing.categoria_nombre}
                          size="small"
                          sx={{
                            mt: 0.5,
                            backgroundColor: designTokens.colors.plantillaClaro,
                            color: designTokens.colors.texto,
                          }}
                        />
                      </Box>
                    }
                    sx={{ flex: 1 }}
                  />

                  {/* Campos de regla (solo si está habilitado) */}
                  {habilitado && regla && (
                    <Box
                      sx={{
                        display: 'flex',
                        flexDirection: 'column',
                        gap: 2,
                        flex: 2,
                        pt: 1,
                      }}
                    >
                      {/* Permite cantidad (deshabilitado si tipo_precio es 'fijo') */}
                      <FormControlLabel
                        control={
                          <Checkbox
                            checked={regla.permite_cantidad}
                            onChange={(e) =>
                              onActualizar(ing.id, {
                                permite_cantidad: e.target.checked,
                              })
                            }
                            disabled={regla.tipo_precio === 'fijo'}
                          />
                        }
                        label="Permite cantidad"
                      />
                      {regla.tipo_precio === 'fijo' && (
                        <Typography variant="caption" color="text.secondary" sx={{ ml: 4, display: 'block' }}>
                          El tipo de precio "Fijo" no permite cantidad variable
                        </Typography>
                      )}

                      {/* Campos de cantidad */}
                      {regla.permite_cantidad && (
                        <Box>
                          <Typography variant="caption" color="text.secondary" sx={{ mb: 1, display: 'block' }}>
                            Configuración de cantidad: el cliente podrá especificar cuántas porciones de este ingrediente desea.
                          </Typography>
                          <Box sx={{ display: 'flex', gap: 2, flexWrap: 'wrap' }}>
                            <TextField
                              label="Mínimo"
                              type="number"
                              value={regla.cantidad_minima}
                              onChange={(e) =>
                                onActualizar(ing.id, {
                                  cantidad_minima: parseInt(e.target.value, 10) || 0,
                                })
                              }
                              size="small"
                              inputProps={{ min: 0 }}
                              sx={{ width: 100 }}
                              helperText="Cantidad mínima"
                            />
                          <TextField
                            label="Máximo"
                            type="number"
                            value={regla.cantidad_maxima}
                            onChange={(e) =>
                              onActualizar(ing.id, {
                                cantidad_maxima: parseInt(e.target.value, 10) || 1,
                              })
                            }
                            size="small"
                            inputProps={{ min: regla.cantidad_minima }}
                            sx={{ width: 100 }}
                            helperText="Cantidad máxima"
                            disabled={regla.tipo_precio === 'fijo'}
                          />
                            <TextField
                              label="Paso"
                              type="number"
                              value={regla.paso_cantidad}
                              onChange={(e) =>
                                onActualizar(ing.id, {
                                  paso_cantidad: parseInt(e.target.value, 10) || 1,
                                })
                              }
                              size="small"
                              inputProps={{ min: 1 }}
                              sx={{ width: 100 }}
                              helperText="Incremento"
                            />
                            <TextField
                              label="Incluida"
                              type="number"
                              value={regla.cantidad_incluida}
                              onChange={(e) =>
                                onActualizar(ing.id, {
                                  cantidad_incluida: parseInt(e.target.value, 10) || 0,
                                })
                              }
                              size="small"
                              inputProps={{ min: 0 }}
                              sx={{ width: 100 }}
                              helperText="Gratis"
                            />
                          </Box>
                        </Box>
                      )}

                      {/* Tipo precio y precio unitario */}
                      <Box sx={{ display: 'flex', gap: 2, alignItems: 'center' }}>
                        <FormControl sx={{ minWidth: 150 }}>
                          <InputLabel>Tipo Precio</InputLabel>
                          <Select
                            value={regla.tipo_precio}
                            onChange={(e) => {
                              const nuevoTipo = e.target.value as 'por_porcion' | 'fijo'
                              // La lógica de negocio se maneja en el controller
                              onActualizar(ing.id, {
                                tipo_precio: nuevoTipo,
                              })
                            }}
                            label="Tipo Precio"
                            size="small"
                          >
                            <MenuItem value="por_porcion">Por Porción</MenuItem>
                            <MenuItem value="fijo">Fijo</MenuItem>
                          </Select>
                        </FormControl>
                        <TextField
                          label="Precio Unitario"
                          type="number"
                          value={regla.precio_unitario}
                          onChange={(e) =>
                            onActualizar(ing.id, {
                              precio_unitario: parseFloat(e.target.value) || 0,
                            })
                          }
                          size="small"
                          inputProps={{ min: 0, step: 0.01 }}
                          InputProps={{
                            startAdornment: (
                              <InputAdornment position="start">$</InputAdornment>
                            ),
                          }}
                          sx={{ width: 150 }}
                        />
                      </Box>

                      {/* Recomendado (solo resaltado, no autoselección) */}
                      <FormControlLabel
                        control={
                          <Checkbox
                            checked={regla.es_recomendado}
                            onChange={(e) =>
                              onActualizar(ing.id, {
                                es_recomendado: e.target.checked,
                              })
                            }
                          />
                        }
                        label={
                          <Box>
                            <Typography component="span">Recomendado</Typography>
                            <Typography variant="caption" color="text.secondary" sx={{ display: 'block', ml: 0 }}>
                              Solo resalta visualmente, no selecciona automáticamente
                            </Typography>
                          </Box>
                        }
                      />
                    </Box>
                  )}
                </Box>
              </Paper>
            )
          })}
        </Box>
      )}
    </Box>
  )
}
