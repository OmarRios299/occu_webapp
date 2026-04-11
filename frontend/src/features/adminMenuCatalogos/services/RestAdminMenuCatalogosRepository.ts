/**
 * Implementación REST del repositorio de catálogos de menú
 */
import { requestJson } from '@/services/http/httpClient'
import type { AdminMenuCatalogosRepository } from './AdminMenuCatalogosRepository'
import type {
  Paginated,
  Categoria,
  CategoriaCreatePayload,
  CategoriaUpdatePayload,
  Subcategoria,
  SubcategoriaCreatePayload,
  SubcategoriaUpdatePayload,
  Producto,
  ProductoCreatePayload,
  ProductoUpdatePayload,
  Tamano,
  TamanoCreatePayload,
  TamanoUpdatePayload,
  IngredienteCategoria,
  IngredienteCategoriaCreatePayload,
  IngredienteCategoriaUpdatePayload,
  Ingrediente,
  IngredienteCreatePayload,
  IngredienteUpdatePayload,
  ApiPaginatedResponse,
  ApiResponse,
} from '../types'

export class RestAdminMenuCatalogosRepository implements AdminMenuCatalogosRepository {
  // Categorías
  async getCategorias(
    page: number,
    pageSize: number,
    token: string,
    q?: string
  ): Promise<Paginated<Categoria>> {
    const params = new URLSearchParams()
    params.set('page', page.toString())
    params.set('pageSize', pageSize.toString())
    if (q) params.set('q', q)

    const response = await requestJson<ApiResponse<ApiPaginatedResponse<Categoria>>>(
      `/admin/catalogos/menu/categorias?${params.toString()}`,
      { token }
    )

    const paginatedData = response.data
    return {
      items: paginatedData.data,
      page: paginatedData.page,
      pageSize: paginatedData.pageSize,
      totalItems: paginatedData.totalItems,
      totalPages: paginatedData.totalPages,
    }
  }

  async getCategoriaById(id: number, token: string): Promise<Categoria> {
    const response = await requestJson<ApiResponse<Categoria>>(`/admin/catalogos/menu/categorias/${id}`, {
      token,
    })
    return response.data
  }

  async createCategoria(data: CategoriaCreatePayload, token: string): Promise<{ id: number; message: string }> {
    const response = await requestJson<ApiResponse<{ id: number; message: string }>>(
      '/admin/catalogos/menu/categorias',
      {
        method: 'POST',
        body: data,
        token,
      }
    )
    return response.data
  }

  async updateCategoria(id: number, data: CategoriaUpdatePayload, token: string): Promise<{ message: string }> {
    const response = await requestJson<ApiResponse<{ message: string }>>(
      `/admin/catalogos/menu/categorias/${id}`,
      {
        method: 'PUT',
        body: data,
        token,
      }
    )
    return response.data
  }

  async deleteCategoria(id: number, token: string): Promise<{ message: string }> {
    const response = await requestJson<ApiResponse<{ message: string }>>(
      `/admin/catalogos/menu/categorias/${id}`,
      {
        method: 'DELETE',
        token,
      }
    )
    return response.data
  }

  // Subcategorías
  async getSubcategorias(
    page: number,
    pageSize: number,
    token: string,
    q?: string,
    idCategoria?: number
  ): Promise<Paginated<Subcategoria>> {
    const params = new URLSearchParams()
    params.set('page', page.toString())
    params.set('pageSize', pageSize.toString())
    if (q) params.set('q', q)
    if (idCategoria !== undefined) params.set('id_categoria', idCategoria.toString())

    const response = await requestJson<ApiResponse<ApiPaginatedResponse<Subcategoria>>>(
      `/admin/catalogos/menu/subcategorias?${params.toString()}`,
      { token }
    )

    const paginatedData = response.data
    return {
      items: paginatedData.data,
      page: paginatedData.page,
      pageSize: paginatedData.pageSize,
      totalItems: paginatedData.totalItems,
      totalPages: paginatedData.totalPages,
    }
  }

  async getSubcategoriaById(id: number, token: string): Promise<Subcategoria> {
    const response = await requestJson<ApiResponse<Subcategoria>>(`/admin/catalogos/menu/subcategorias/${id}`, {
      token,
    })
    return response.data
  }

  async createSubcategoria(
    data: SubcategoriaCreatePayload,
    token: string
  ): Promise<{ id: number; message: string }> {
    const response = await requestJson<ApiResponse<{ id: number; message: string }>>(
      '/admin/catalogos/menu/subcategorias',
      {
        method: 'POST',
        body: data,
        token,
      }
    )
    return response.data
  }

  async updateSubcategoria(
    id: number,
    data: SubcategoriaUpdatePayload,
    token: string
  ): Promise<{ message: string }> {
    const response = await requestJson<ApiResponse<{ message: string }>>(
      `/admin/catalogos/menu/subcategorias/${id}`,
      {
        method: 'PUT',
        body: data,
        token,
      }
    )
    return response.data
  }

  async deleteSubcategoria(id: number, token: string): Promise<{ message: string }> {
    const response = await requestJson<ApiResponse<{ message: string }>>(
      `/admin/catalogos/menu/subcategorias/${id}`,
      {
        method: 'DELETE',
        token,
      }
    )
    return response.data
  }

  // Productos
  async getProductos(
    page: number,
    pageSize: number,
    token: string,
    q?: string,
    idSubcategoria?: number
  ): Promise<Paginated<Producto>> {
    const params = new URLSearchParams()
    params.set('page', page.toString())
    params.set('pageSize', pageSize.toString())
    if (q) params.set('q', q)
    if (idSubcategoria !== undefined) params.set('id_subcategoria', idSubcategoria.toString())

    const response = await requestJson<ApiResponse<ApiPaginatedResponse<Producto>>>(
      `/admin/catalogos/menu/productos?${params.toString()}`,
      { token }
    )

    const paginatedData = response.data
    return {
      items: paginatedData.data,
      page: paginatedData.page,
      pageSize: paginatedData.pageSize,
      totalItems: paginatedData.totalItems,
      totalPages: paginatedData.totalPages,
    }
  }

  async getProductoById(id: number, token: string): Promise<Producto> {
    const response = await requestJson<ApiResponse<Producto>>(`/admin/catalogos/menu/productos/${id}`, {
      token,
    })
    return response.data
  }

  async createProducto(data: ProductoCreatePayload, token: string): Promise<{ id: number; message: string }> {
    const response = await requestJson<ApiResponse<{ id: number; message: string }>>(
      '/admin/catalogos/menu/productos',
      {
        method: 'POST',
        body: data,
        token,
      }
    )
    return response.data
  }

  async updateProducto(id: number, data: ProductoUpdatePayload, token: string): Promise<{ message: string }> {
    const response = await requestJson<ApiResponse<{ message: string }>>(
      `/admin/catalogos/menu/productos/${id}`,
      {
        method: 'PUT',
        body: data,
        token,
      }
    )
    return response.data
  }

  async deleteProducto(id: number, token: string): Promise<{ message: string }> {
    const response = await requestJson<ApiResponse<{ message: string }>>(
      `/admin/catalogos/menu/productos/${id}`,
      {
        method: 'DELETE',
        token,
      }
    )
    return response.data
  }

  // Tamaños
  async getTamanos(page: number, pageSize: number, token: string, q?: string): Promise<Paginated<Tamano>> {
    const params = new URLSearchParams()
    params.set('page', page.toString())
    params.set('pageSize', pageSize.toString())
    if (q) params.set('q', q)

    const response = await requestJson<ApiResponse<ApiPaginatedResponse<Tamano>>>(
      `/admin/catalogos/menu/tamanos?${params.toString()}`,
      { token }
    )

    const paginatedData = response.data
    return {
      items: paginatedData.data,
      page: paginatedData.page,
      pageSize: paginatedData.pageSize,
      totalItems: paginatedData.totalItems,
      totalPages: paginatedData.totalPages,
    }
  }

  async getTamanoById(id: number, token: string): Promise<Tamano> {
    const response = await requestJson<ApiResponse<Tamano>>(`/admin/catalogos/menu/tamanos/${id}`, {
      token,
    })
    return response.data
  }

  async createTamano(data: TamanoCreatePayload, token: string): Promise<{ id: number; message: string }> {
    const response = await requestJson<ApiResponse<{ id: number; message: string }>>(
      '/admin/catalogos/menu/tamanos',
      {
        method: 'POST',
        body: data,
        token,
      }
    )
    return response.data
  }

  async updateTamano(id: number, data: TamanoUpdatePayload, token: string): Promise<{ message: string }> {
    const response = await requestJson<ApiResponse<{ message: string }>>(`/admin/catalogos/menu/tamanos/${id}`, {
      method: 'PUT',
      body: data,
      token,
    })
    return response.data
  }

  async deleteTamano(id: number, token: string): Promise<{ message: string }> {
    const response = await requestJson<ApiResponse<{ message: string }>>(`/admin/catalogos/menu/tamanos/${id}`, {
      method: 'DELETE',
      token,
    })
    return response.data
  }

  // Categorías de Ingredientes
  async getIngredientesCategorias(
    page: number,
    pageSize: number,
    token: string,
    q?: string
  ): Promise<Paginated<IngredienteCategoria>> {
    const params = new URLSearchParams()
    params.set('page', page.toString())
    params.set('pageSize', pageSize.toString())
    if (q) params.set('q', q)

    const response = await requestJson<ApiResponse<ApiPaginatedResponse<IngredienteCategoria>>>(
      `/admin/catalogos/menu/ingredientes-categorias?${params.toString()}`,
      { token }
    )

    const paginatedData = response.data
    return {
      items: paginatedData.data,
      page: paginatedData.page,
      pageSize: paginatedData.pageSize,
      totalItems: paginatedData.totalItems,
      totalPages: paginatedData.totalPages,
    }
  }

  async getIngredienteCategoriaById(id: number, token: string): Promise<IngredienteCategoria> {
    const response = await requestJson<ApiResponse<IngredienteCategoria>>(
      `/admin/catalogos/menu/ingredientes-categorias/${id}`,
      { token }
    )
    return response.data
  }

  async createIngredienteCategoria(
    data: IngredienteCategoriaCreatePayload,
    token: string
  ): Promise<{ id: number; message: string }> {
    const response = await requestJson<ApiResponse<{ id: number; message: string }>>(
      '/admin/catalogos/menu/ingredientes-categorias',
      {
        method: 'POST',
        body: data,
        token,
      }
    )
    return response.data
  }

  async updateIngredienteCategoria(
    id: number,
    data: IngredienteCategoriaUpdatePayload,
    token: string
  ): Promise<{ message: string }> {
    const response = await requestJson<ApiResponse<{ message: string }>>(
      `/admin/catalogos/menu/ingredientes-categorias/${id}`,
      {
        method: 'PUT',
        body: data,
        token,
      }
    )
    return response.data
  }

  async deleteIngredienteCategoria(id: number, token: string): Promise<{ message: string }> {
    const response = await requestJson<ApiResponse<{ message: string }>>(
      `/admin/catalogos/menu/ingredientes-categorias/${id}`,
      {
        method: 'DELETE',
        token,
      }
    )
    return response.data
  }

  // Ingredientes
  async getIngredientes(
    page: number,
    pageSize: number,
    token: string,
    q?: string,
    idCategoria?: number
  ): Promise<Paginated<Ingrediente>> {
    const params = new URLSearchParams()
    params.set('page', page.toString())
    params.set('pageSize', pageSize.toString())
    if (q) params.set('q', q)
    if (idCategoria !== undefined) params.set('id_ingrediente_categoria', idCategoria.toString())

    const response = await requestJson<ApiResponse<ApiPaginatedResponse<Ingrediente>>>(
      `/admin/catalogos/menu/ingredientes?${params.toString()}`,
      { token }
    )

    const paginatedData = response.data
    return {
      items: paginatedData.data,
      page: paginatedData.page,
      pageSize: paginatedData.pageSize,
      totalItems: paginatedData.totalItems,
      totalPages: paginatedData.totalPages,
    }
  }

  async getIngredienteById(id: number, token: string): Promise<Ingrediente> {
    const response = await requestJson<ApiResponse<Ingrediente>>(`/admin/catalogos/menu/ingredientes/${id}`, {
      token,
    })
    return response.data
  }

  async createIngrediente(data: IngredienteCreatePayload, token: string): Promise<{ id: number; message: string }> {
    const response = await requestJson<ApiResponse<{ id: number; message: string }>>(
      '/admin/catalogos/menu/ingredientes',
      {
        method: 'POST',
        body: data,
        token,
      }
    )
    return response.data
  }

  async updateIngrediente(id: number, data: IngredienteUpdatePayload, token: string): Promise<{ message: string }> {
    const response = await requestJson<ApiResponse<{ message: string }>>(
      `/admin/catalogos/menu/ingredientes/${id}`,
      {
        method: 'PUT',
        body: data,
        token,
      }
    )
    return response.data
  }

  async deleteIngrediente(id: number, token: string): Promise<{ message: string }> {
    const response = await requestJson<ApiResponse<{ message: string }>>(
      `/admin/catalogos/menu/ingredientes/${id}`,
      {
        method: 'DELETE',
        token,
      }
    )
    return response.data
  }
}
