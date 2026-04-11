<?php

declare(strict_types=1);

namespace Occu\Api\Repositories;

use Occu\Api\Infrastructure\Db;
use PDO;

final class MenuProductosRepository
{
    private PDO $pdo;

    public function __construct(Db $db)
    {
        $this->pdo = $db->pdo();
    }

    /**
     * Obtiene productos paginados con búsqueda
     * 
     * @param array{
     *     page: int,
     *     pageSize: int,
     *     q?: string
     * } $filters
     * @return array{items: array, total: int}
     */
    public function findPaginated(array $filters): array
    {
        $page = $filters['page'];
        $pageSize = $filters['pageSize'];
        $offset = ($page - 1) * $pageSize;

        $busqueda = isset($filters['q']) && $filters['q'] !== '' 
            ? '%' . $filters['q'] . '%' 
            : null;

        $where = ['menu_productos.estado = 0'];
        $params = [];

        if ($busqueda) {
            $where[] = 'menu_productos.nombre LIKE :busqueda';
            $params[':busqueda'] = $busqueda;
        }

        $whereClause = implode(' AND ', $where);

        // Query para obtener items
        $sql = "SELECT
            menu_productos.id,
            menu_productos.nombre,
            menu_subcategorias.nombre AS subcategoria,
            menu_categorias.nombre AS categoria
            FROM menu_productos
            LEFT JOIN menu_subcategorias ON menu_productos.id_subcategoria = menu_subcategorias.id
            LEFT JOIN menu_categorias ON menu_subcategorias.id_categoria = menu_categorias.id
            WHERE {$whereClause}
            ORDER BY menu_productos.nombre ASC
            LIMIT :offset, :limit";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        // Query para contar total
        $countSql = "SELECT COUNT(*) as total
            FROM menu_productos
            WHERE {$whereClause}";

        $countStmt = $this->pdo->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $countStmt->execute();
        $total = (int)$countStmt->fetchColumn();

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    /**
     * Verifica si un producto existe
     */
    public function exists(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT 1 FROM menu_productos WHERE id = :id AND estado = 0 LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Obtiene las reglas completas de un producto (categorías aplicables + ingredientes)
     * 
     * @return array{
     *     categorias: array,
     *     ingredientes: array,
     *     catalogo_categorias: array,
     *     catalogo_ingredientes: array
     * }
     */
    public function getReglas(int $productoId): array
    {
        // Reglas por categoría (aplicables)
        $stmtCategorias = $this->pdo->prepare(
            "SELECT 
                mpric.id_producto,
                mpric.id_ingrediente_categoria,
                mic.nombre AS categoria_nombre,
                mpric.tipo_seleccion,
                mpric.seleccion_minima,
                mpric.seleccion_maxima,
                mpric.orden
            FROM menu_productos_reglas_ingredientes_categorias mpric
            INNER JOIN menu_ingredientes_categorias mic ON mpric.id_ingrediente_categoria = mic.id
            WHERE mpric.id_producto = :id AND mpric.estado = 0
            ORDER BY mpric.orden ASC, mic.nombre ASC"
        );
        $stmtCategorias->execute([':id' => $productoId]);
        $categorias = $stmtCategorias->fetchAll();

        // Reglas por ingrediente (solo de categorías aplicables)
        $categoriaIds = array_map(fn($c) => (int)$c['id_ingrediente_categoria'], $categorias);
        
        $ingredientes = [];
        if (!empty($categoriaIds)) {
            $placeholders = [];
            $params = [':id_producto' => $productoId];
            foreach ($categoriaIds as $idx => $catId) {
                $key = ':cat' . $idx;
                $placeholders[] = $key;
                $params[$key] = $catId;
            }

            $stmtIngredientes = $this->pdo->prepare(
                "SELECT 
                    mpri.id_producto,
                    mpri.id_ingrediente,
                    mi.nombre AS ingrediente_nombre,
                    mi.id_ingrediente_categoria,
                    mic.nombre AS categoria_nombre,
                    mpri.permite_cantidad,
                    mpri.cantidad_minima,
                    mpri.cantidad_maxima,
                    mpri.paso_cantidad,
                    mpri.cantidad_incluida,
                    mpri.tipo_precio,
                    mpri.precio_unitario,
                    mpri.es_recomendado,
                    mpri.estado
                FROM menu_productos_reglas_ingredientes mpri
                INNER JOIN menu_ingredientes mi ON mpri.id_ingrediente = mi.id
                INNER JOIN menu_ingredientes_categorias mic ON mi.id_ingrediente_categoria = mic.id
                WHERE mpri.id_producto = :id_producto 
                AND mi.id_ingrediente_categoria IN (" . implode(',', $placeholders) . ")
                AND mpri.estado = 0
                ORDER BY mic.nombre ASC, mi.nombre ASC"
            );
            foreach ($params as $key => $value) {
                $stmtIngredientes->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmtIngredientes->execute();
            $ingredientes = $stmtIngredientes->fetchAll();
        }

        // Catálogo: todas las categorías disponibles
        $stmtCatCategorias = $this->pdo->prepare(
            "SELECT id, nombre 
            FROM menu_ingredientes_categorias 
            WHERE estado = 0 
            ORDER BY nombre ASC"
        );
        $stmtCatCategorias->execute();
        $catalogoCategorias = $stmtCatCategorias->fetchAll();

        // Catálogo: ingredientes por categoría aplicable
        $catalogoIngredientes = [];
        if (!empty($categoriaIds)) {
            $placeholders = [];
            $params = [];
            foreach ($categoriaIds as $idx => $catId) {
                $key = ':cat' . $idx;
                $placeholders[] = $key;
                $params[$key] = $catId;
            }

            $stmtCatIngredientes = $this->pdo->prepare(
                "SELECT 
                    mi.id,
                    mi.nombre,
                    mi.id_ingrediente_categoria,
                    mic.nombre AS categoria_nombre
                FROM menu_ingredientes mi
                INNER JOIN menu_ingredientes_categorias mic ON mi.id_ingrediente_categoria = mic.id
                WHERE mi.id_ingrediente_categoria IN (" . implode(',', $placeholders) . ")
                AND mi.estado = 0
                ORDER BY mic.nombre ASC, mi.nombre ASC"
            );
            foreach ($params as $key => $value) {
                $stmtCatIngredientes->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmtCatIngredientes->execute();
            $catalogoIngredientes = $stmtCatIngredientes->fetchAll();
        }

        return [
            'categorias' => $categorias,
            'ingredientes' => $ingredientes,
            'catalogo_categorias' => $catalogoCategorias,
            'catalogo_ingredientes' => $catalogoIngredientes,
        ];
    }

    /**
     * Guarda las reglas de un producto (reemplazo total en transacción)
     * 
     * @param array{
     *     categorias: array<int, array{
     *         id_ingrediente_categoria: int,
     *         tipo_seleccion: 'unica'|'multiple',
     *         seleccion_minima: int,
     *         seleccion_maxima: int|null,
     *         orden: int
     *     }>,
     *     ingredientes: array<int, array{
     *         id_ingrediente: int,
     *         permite_cantidad: bool,
     *         cantidad_minima: int,
     *         cantidad_maxima: int,
     *         paso_cantidad: int,
     *         cantidad_incluida: int,
     *         tipo_precio: 'por_porcion'|'fijo',
     *         precio_unitario: float,
     *         es_recomendado: bool
     *     }>
     * } $reglas
     * @param int $userId
     * @return array{categorias_guardadas: int, ingredientes_guardados: int}
     */
    public function saveReglas(int $productoId, array $reglas, int $userId): array
    {
        $this->pdo->beginTransaction();
        try {
            $fechaAlta = date('Y-m-d H:i:s');

            // 1. Marcar reglas existentes como eliminadas (soft delete)
            $stmtDelCategorias = $this->pdo->prepare(
                "UPDATE menu_productos_reglas_ingredientes_categorias 
                SET estado = 2 
                WHERE id_producto = :id AND estado = 0"
            );
            $stmtDelCategorias->execute([':id' => $productoId]);

            $stmtDelIngredientes = $this->pdo->prepare(
                "UPDATE menu_productos_reglas_ingredientes 
                SET estado = 2 
                WHERE id_producto = :id AND estado = 0"
            );
            $stmtDelIngredientes->execute([':id' => $productoId]);

            // 2. Insertar/actualizar categorías
            $categoriasGuardadas = 0;
            foreach ($reglas['categorias'] ?? [] as $cat) {
                $stmtCat = $this->pdo->prepare(
                    "INSERT INTO menu_productos_reglas_ingredientes_categorias
                    (id_producto, id_ingrediente_categoria, tipo_seleccion, seleccion_minima, seleccion_maxima, orden, estado, id_alta, fecha_alta)
                    VALUES (:id_producto, :id_categoria, :tipo_seleccion, :seleccion_minima, :seleccion_maxima, :orden, 0, :id_alta, :fecha_alta)
                    ON DUPLICATE KEY UPDATE
                        tipo_seleccion = VALUES(tipo_seleccion),
                        seleccion_minima = VALUES(seleccion_minima),
                        seleccion_maxima = VALUES(seleccion_maxima),
                        orden = VALUES(orden),
                        estado = 0"
                );
                $stmtCat->execute([
                    ':id_producto' => $productoId,
                    ':id_categoria' => $cat['id_ingrediente_categoria'],
                    ':tipo_seleccion' => $cat['tipo_seleccion'],
                    ':seleccion_minima' => $cat['seleccion_minima'],
                    ':seleccion_maxima' => $cat['seleccion_maxima'] ?? null,
                    ':orden' => $cat['orden'],
                    ':id_alta' => $userId,
                    ':fecha_alta' => $fechaAlta,
                ]);
                $categoriasGuardadas++;
            }

            // 3. Insertar/actualizar ingredientes
            $ingredientesGuardados = 0;
            foreach ($reglas['ingredientes'] ?? [] as $ing) {
                // Aplicar reglas de negocio: si tipo_precio es 'fijo', forzar restricciones
                $permiteCantidad = $ing['tipo_precio'] === 'fijo' ? false : ($ing['permite_cantidad'] ?? false);
                $cantidadMaxima = $ing['tipo_precio'] === 'fijo' ? 1 : ($ing['cantidad_maxima'] ?? 1);
                
                $stmtIng = $this->pdo->prepare(
                    "INSERT INTO menu_productos_reglas_ingredientes
                    (id_producto, id_ingrediente, permite_cantidad, cantidad_minima, cantidad_maxima, paso_cantidad, cantidad_incluida, tipo_precio, precio_unitario, es_recomendado, estado, id_alta, fecha_alta)
                    VALUES (:id_producto, :id_ingrediente, :permite_cantidad, :cantidad_minima, :cantidad_maxima, :paso_cantidad, :cantidad_incluida, :tipo_precio, :precio_unitario, :es_recomendado, 0, :id_alta, :fecha_alta)
                    ON DUPLICATE KEY UPDATE
                        permite_cantidad = VALUES(permite_cantidad),
                        cantidad_minima = VALUES(cantidad_minima),
                        cantidad_maxima = VALUES(cantidad_maxima),
                        paso_cantidad = VALUES(paso_cantidad),
                        cantidad_incluida = VALUES(cantidad_incluida),
                        tipo_precio = VALUES(tipo_precio),
                        precio_unitario = VALUES(precio_unitario),
                        es_recomendado = VALUES(es_recomendado),
                        estado = 0"
                );
                $stmtIng->execute([
                    ':id_producto' => $productoId,
                    ':id_ingrediente' => $ing['id_ingrediente'],
                    ':permite_cantidad' => $permiteCantidad ? 1 : 0,
                    ':cantidad_minima' => $ing['cantidad_minima'],
                    ':cantidad_maxima' => $cantidadMaxima,
                    ':paso_cantidad' => $ing['paso_cantidad'],
                    ':cantidad_incluida' => $ing['cantidad_incluida'],
                    ':tipo_precio' => $ing['tipo_precio'],
                    ':precio_unitario' => $ing['precio_unitario'],
                    ':es_recomendado' => $ing['es_recomendado'] ? 1 : 0,
                    ':id_alta' => $userId,
                    ':fecha_alta' => $fechaAlta,
                ]);
                $ingredientesGuardados++;
            }

            $this->pdo->commit();

            return [
                'categorias_guardadas' => $categoriasGuardadas,
                'ingredientes_guardados' => $ingredientesGuardados,
            ];
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
