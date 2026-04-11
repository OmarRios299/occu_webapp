/**
 * Hook controlador para editar reglas de un producto en el menú general del propietario
 * Reutiliza la lógica del editor admin pero adaptado para propietarios
 */
import { useCallback, useEffect, useMemo, useState } from 'react'
import { useParams } from 'react-router-dom'
import { useAuth } from '@/store/auth/AuthProvider'
import { RestPropietarioMenuGeneralRepository } from '../services/RestPropietarioMenuGeneralRepository'
import type {
  ProductoReglas,
  ReglaCategoria,
  ReglaIngrediente,
  ReglaCategoriaPayload,
  ReglaIngredientePayload,
  CatalogoCategoria,
  CatalogoIngrediente,
} from '../types'

type ControllerState = {
  reglas: ProductoReglas | null
  loading: boolean
  saving: boolean
  error: string | null
  successMessage: string | null
}

type ControllerActions = {
  // Categorías
  agregarCategoria: (categoriaId: number) => void
  eliminarCategoria: (categoriaId: number) => void
  actualizarCategoria: (
    categoriaId: number,
    updates: Partial<ReglaCategoriaPayload>
  ) => void

  // Ingredientes
  toggleIngrediente: (ingredienteId: number, habilitado: boolean) => void
  actualizarIngrediente: (
    ingredienteId: number,
    updates: Partial<ReglaIngredientePayload>
  ) => void

  // Guardado
  guardar: () => Promise<void>
  recargar: () => Promise<void>
  limpiarMensajes: () => void
}

export function usePropietarioProductoReglasController(): {
  state: ControllerState
  actions: ControllerActions
} {
  const { id } = useParams<{ id: string }>()
  const { session } = useAuth()
  const repository = useMemo(() => new RestPropietarioMenuGeneralRepository(), [])

  const productoId = id ? parseInt(id, 10) : 0

  const [reglas, setReglas] = useState<ProductoReglas | null>(null)
  const [loading, setLoading] = useState(false)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [successMessage, setSuccessMessage] = useState<string | null>(null)

  // Cargar reglas
  const loadReglas = useCallback(async () => {
    if (!productoId || productoId <= 0) {
      setError('ID de producto inválido')
      return
    }

    if (!session) {
      setError('No hay sesión activa')
      return
    }

    setLoading(true)
    setError(null)

    try {
      const data = await repository.getProductoReglas(productoId, session.accessToken)
      setReglas(data)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Error al cargar reglas')
    } finally {
      setLoading(false)
    }
  }, [repository, productoId, session])

  useEffect(() => {
    loadReglas()
  }, [loadReglas])

  // Actions (similar al editor admin)
  const actions: ControllerActions = {
    agregarCategoria: useCallback(
      (categoriaId: number) => {
        if (!reglas) return

        const categoria = reglas.catalogo_categorias.find((c) => c.id === categoriaId)
        if (!categoria) return

        if (
          reglas.categorias.some((c) => c.id_ingrediente_categoria === categoriaId)
        ) {
          return
        }

        const nuevaCategoria: ReglaCategoria = {
          id_propietario_menu_producto: 0, // Se asignará al guardar
          id_ingrediente_categoria: categoriaId,
          categoria_nombre: categoria.nombre,
          tipo_seleccion: 'unica',
          seleccion_minima: 0,
          seleccion_maxima: 1,
          orden:
            reglas.categorias.length > 0
              ? Math.max(...reglas.categorias.map((c) => c.orden)) + 10
              : 10,
        }

        setReglas({
          ...reglas,
          categorias: [...reglas.categorias, nuevaCategoria],
        })
      },
      [reglas]
    ),

    eliminarCategoria: useCallback(
      (categoriaId: number) => {
        if (!reglas) return

        setReglas({
          ...reglas,
          categorias: reglas.categorias.filter(
            (c) => c.id_ingrediente_categoria !== categoriaId
          ),
          ingredientes: reglas.ingredientes.filter(
            (ing) => ing.id_ingrediente_categoria !== categoriaId
          ),
        })
      },
      [reglas]
    ),

    actualizarCategoria: useCallback(
      (categoriaId: number, updates: Partial<ReglaCategoriaPayload>) => {
        if (!reglas) return

        setReglas({
          ...reglas,
          categorias: reglas.categorias.map((c) =>
            c.id_ingrediente_categoria === categoriaId ? { ...c, ...updates } : c
          ),
        })
      },
      [reglas]
    ),

    toggleIngrediente: useCallback(
      (ingredienteId: number, habilitado: boolean) => {
        if (!reglas) return

        const ingrediente = reglas.catalogo_ingredientes.find((i) => i.id === ingredienteId)
        if (!ingrediente) return

        if (habilitado) {
          const existe = reglas.ingredientes.some((i) => i.id_ingrediente === ingredienteId)
          if (!existe) {
            const nuevaRegla: ReglaIngrediente = {
              id_propietario_menu_producto: 0, // Se asignará al guardar
              id_ingrediente: ingredienteId,
              ingrediente_nombre: ingrediente.nombre,
              id_ingrediente_categoria: ingrediente.id_ingrediente_categoria,
              categoria_nombre: ingrediente.categoria_nombre,
              permite_cantidad: false,
              cantidad_minima: 0,
              cantidad_maxima: 1,
              paso_cantidad: 1,
              cantidad_incluida: 0,
              tipo_precio: 'por_porcion',
              precio_unitario: 0,
              es_recomendado: false,
              estado: 0,
            }
            setReglas({
              ...reglas,
              ingredientes: [...reglas.ingredientes, nuevaRegla],
            })
          }
        } else {
          setReglas({
            ...reglas,
            ingredientes: reglas.ingredientes.filter(
              (i) => i.id_ingrediente !== ingredienteId
            ),
          })
        }
      },
      [reglas]
    ),

    actualizarIngrediente: useCallback(
      (ingredienteId: number, updates: Partial<ReglaIngredientePayload>) => {
        if (!reglas) return

        // Si tipo_precio cambia a 'fijo', forzar permite_cantidad=false y cantidad_maxima=1
        if (updates.tipo_precio === 'fijo') {
          updates.permite_cantidad = false
          updates.cantidad_maxima = 1
        }

        setReglas({
          ...reglas,
          ingredientes: reglas.ingredientes.map((i) => {
            if (i.id_ingrediente === ingredienteId) {
              const updated = { ...i, ...updates }
              if (updated.tipo_precio === 'fijo') {
                updated.permite_cantidad = false
                updated.cantidad_maxima = 1
              }
              return updated
            }
            return i
          }),
        })
      },
      [reglas]
    ),

    guardar: useCallback(async () => {
      if (!reglas || !session) return

      setSaving(true)
      setError(null)
      setSuccessMessage(null)

      try {
        const payload = {
          categorias: reglas.categorias.map((c) => ({
            id_ingrediente_categoria: c.id_ingrediente_categoria,
            tipo_seleccion: c.tipo_seleccion,
            seleccion_minima: c.seleccion_minima,
            seleccion_maxima: c.seleccion_maxima,
            orden: c.orden,
          })),
          ingredientes: reglas.ingredientes.map((i) => {
            // Aplicar reglas de negocio antes de enviar
            const permiteCantidad = i.tipo_precio === 'fijo' ? false : i.permite_cantidad
            const cantidadMaxima = i.tipo_precio === 'fijo' ? 1 : i.cantidad_maxima

            return {
              id_ingrediente: i.id_ingrediente,
              permite_cantidad: permiteCantidad,
              cantidad_minima: i.cantidad_minima,
              cantidad_maxima: cantidadMaxima,
              paso_cantidad: i.paso_cantidad,
              cantidad_incluida: i.cantidad_incluida,
              tipo_precio: i.tipo_precio,
              precio_unitario: i.precio_unitario,
              es_recomendado: i.es_recomendado,
            }
          }),
        }

        const result = await repository.guardarReglas(productoId, payload, session.accessToken)
        setSuccessMessage(
          `Reglas guardadas exitosamente: ${result.categorias_guardadas} categoría(s), ${result.ingredientes_guardados} ingrediente(s)`
        )

        await loadReglas()
      } catch (err) {
        const httpError = err as { payload?: { message?: string } }
        const mensajeError =
          httpError?.payload?.message || (err instanceof Error ? err.message : 'Error al guardar reglas')
        setError(mensajeError)
      } finally {
        setSaving(false)
      }
    }, [reglas, repository, productoId, session, loadReglas]),

    recargar: useCallback(async () => {
      await loadReglas()
    }, [loadReglas]),

    limpiarMensajes: useCallback(() => {
      setError(null)
      setSuccessMessage(null)
    }, []),
  }

  return {
    state: {
      reglas,
      loading,
      saving,
      error,
      successMessage,
    },
    actions,
  }
}
