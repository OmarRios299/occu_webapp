<?php

declare(strict_types=1);

namespace Occu\Api\Repositories;

use Occu\Api\Infrastructure\Db;
use PDO;

final class PropietariosMenuGeneralRepository
{
    private PDO $pdo;

    public function __construct(Db $db)
    {
        $this->pdo = $db->pdo();
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Obtiene productos del catálogo OCCU con su estado en el menú del propietario
     * 
     * @param array{
     *     page: int,
     *     pageSize: int,
     *     q?: string,
     *     id_subcategoria?: int,
     *     estado?: int
     * } $filters
     * @param int $propietarioId
     * @return array{items: array, total: int}
     */
    public function findProductos(array $filters, int $propietarioId): array
    {
        $page = $filters['page'];
        $pageSize = $filters['pageSize'];
        $offset = ($page - 1) * $pageSize;

        $busqueda = isset($filters['q']) && $filters['q'] !== '' 
            ? '%' . $filters['q'] . '%' 
            : null;
        $idSubcategoria = isset($filters['id_subcategoria']) ? (int)$filters['id_subcategoria'] : null;
        $estadoFiltro = isset($filters['estado']) ? (int)$filters['estado'] : null;

        $where = ['mp.estado = 0', 'mp.registro_occu = 1'];
        $params = [':propietario_id' => $propietarioId];

        if ($busqueda) {
            $where[] = 'mp.nombre LIKE :busqueda';
            $params[':busqueda'] = $busqueda;
        }
        if ($idSubcategoria !== null) {
            $where[] = 'mp.id_subcategoria = :id_subcategoria';
            $params[':id_subcategoria'] = $idSubcategoria;
        }

        $whereClause = implode(' AND ', $where);

        // Query para obtener items con estado del propietario
        $sql = "SELECT 
            mp.id,
            mp.nombre,
            mp.id_subcategoria,
            ms.nombre AS subcategoria_nombre,
            ms.id_categoria,
            mc.nombre AS categoria_nombre,
            mp.imagen,
            COALESCE(pmp.estado, 0) AS estado_menu,
            COALESCE(pmp.precio_base, 0) AS precio_base,
            pmp.id AS id_propietario_menu_producto
        FROM menu_productos mp
        LEFT JOIN menu_subcategorias ms ON mp.id_subcategoria = ms.id
        LEFT JOIN menu_categorias mc ON ms.id_categoria = mc.id
        LEFT JOIN propietarios_menu_productos pmp ON pmp.id_producto = mp.id AND pmp.id_propietario = :propietario_id AND pmp.estado != 2
        WHERE {$whereClause}";

        if ($estadoFiltro !== null) {
            if ($estadoFiltro === 0) {
                $sql .= " AND (pmp.estado IS NULL OR pmp.estado = 0)";
            } else {
                $sql .= " AND pmp.estado = :estado_filtro";
                $params[':estado_filtro'] = $estadoFiltro;
            }
        }

        $sql .= " ORDER BY mc.nombre ASC, ms.nombre ASC, mp.nombre ASC
            LIMIT :offset, :limit";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Query para contar total
        // Usar la misma estructura que la consulta principal pero sin LIMIT
        $countSql = "SELECT COUNT(*) as total
            FROM menu_productos mp
            LEFT JOIN menu_subcategorias ms ON mp.id_subcategoria = ms.id
            LEFT JOIN menu_categorias mc ON ms.id_categoria = mc.id
            LEFT JOIN propietarios_menu_productos pmp ON pmp.id_producto = mp.id AND pmp.id_propietario = :propietario_id AND pmp.estado != 2
            WHERE {$whereClause}";
        
        if ($estadoFiltro !== null) {
            if ($estadoFiltro === 0) {
                $countSql .= " AND (pmp.estado IS NULL OR pmp.estado = 0)";
            } else {
                $countSql .= " AND pmp.estado = :estado_filtro";
            }
        }

        $countStmt = $this->pdo->prepare($countSql);
        // Usar los mismos parámetros que la consulta principal (sin offset/limit)
        foreach ($params as $key => $value) {
            if ($key !== ':offset' && $key !== ':limit') {
                $countStmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
        }
        $countStmt->execute();
        $total = (int)$countStmt->fetchColumn();

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    /**
     * Obtiene un producto con sus tamaños y estado en el menú del propietario
     */
    public function findProductoById(int $productoId, int $propietarioId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT 
                mp.id,
                mp.nombre,
                mp.id_subcategoria,
                ms.nombre AS subcategoria_nombre,
                ms.id_categoria,
                mc.nombre AS categoria_nombre,
                mp.imagen,
                COALESCE(pmp.estado, 0) AS estado_menu,
                COALESCE(pmp.precio_base, 0) AS precio_base,
                pmp.id AS id_propietario_menu_producto
            FROM menu_productos mp
            LEFT JOIN menu_subcategorias ms ON mp.id_subcategoria = ms.id
            LEFT JOIN menu_categorias mc ON ms.id_categoria = mc.id
            LEFT JOIN propietarios_menu_productos pmp ON pmp.id_producto = mp.id AND pmp.id_propietario = :propietario_id AND pmp.estado != 2
            WHERE mp.id = :producto_id AND mp.estado = 0 AND mp.registro_occu = 1"
        );
        $stmt->execute([
            ':producto_id' => $productoId,
            ':propietario_id' => $propietarioId,
        ]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$producto) {
            return null;
        }

        // Obtener tamaños con precios del propietario
        // Primero verificar si la tabla propietarios_menu_productos_tamanos existe
        $idPropietarioMenuProducto = $producto['id_propietario_menu_producto'] ?? null;
        $tablaTamanosExiste = false;
        
        try {
            $stmtCheck = $this->pdo->query("SHOW TABLES LIKE 'propietarios_menu_productos_tamanos'");
            $tablaTamanosExiste = $stmtCheck->rowCount() > 0;
        } catch (\Throwable $e) {
            // Si hay error, asumir que la tabla no existe
            $tablaTamanosExiste = false;
        }
        
        if ($idPropietarioMenuProducto && $tablaTamanosExiste) {
            // Si existe registro del propietario y la tabla existe, obtener tamaños con precios
            try {
                $stmtTamanos = $this->pdo->prepare(
                    "SELECT 
                        mpt.id AS id_tamano,
                        mpt.nombre AS tamano_nombre,
                        mpt.unidad_medida,
                        mpt.medida,
                        COALESCE(pmpt.precio, 0) AS precio,
                        COALESCE(pmpt.estado, 0) AS estado_tamano,
                        pmpt.id AS id_propietario_menu_producto_tamano
                    FROM menu_productos_tamanos mpt
                    LEFT JOIN propietarios_menu_productos_tamanos pmpt ON pmpt.id_tamano = mpt.id 
                        AND pmpt.id_propietario_menu_producto = :id_propietario_menu_producto
                        AND pmpt.estado != 2
                    WHERE mpt.estado = 0
                    ORDER BY CAST(mpt.medida AS UNSIGNED) ASC, mpt.nombre ASC"
                );
                $stmtTamanos->execute([':id_propietario_menu_producto' => $idPropietarioMenuProducto]);
                $producto['tamanos'] = $stmtTamanos->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Throwable $e) {
                // Si falla el JOIN, obtener solo tamaños del catálogo
                $stmtTamanos = $this->pdo->prepare(
                    "SELECT 
                        id AS id_tamano,
                        nombre AS tamano_nombre,
                        unidad_medida,
                        medida,
                        0 AS precio,
                        0 AS estado_tamano,
                        NULL AS id_propietario_menu_producto_tamano
                    FROM menu_productos_tamanos
                    WHERE estado = 0
                    ORDER BY CAST(medida AS UNSIGNED) ASC, nombre ASC"
                );
                $stmtTamanos->execute();
                $producto['tamanos'] = $stmtTamanos->fetchAll(PDO::FETCH_ASSOC);
            }
        } else {
            // Si no existe registro del propietario o la tabla no existe, obtener solo tamaños del catálogo
            $stmtTamanos = $this->pdo->prepare(
                "SELECT 
                    id AS id_tamano,
                    nombre AS tamano_nombre,
                    unidad_medida,
                    medida,
                    0 AS precio,
                    0 AS estado_tamano,
                    NULL AS id_propietario_menu_producto_tamano
                FROM menu_productos_tamanos
                WHERE estado = 0
                ORDER BY CAST(medida AS UNSIGNED) ASC, nombre ASC"
            );
            $stmtTamanos->execute();
            $producto['tamanos'] = $stmtTamanos->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $producto;
        
        $producto['tamanos'] = $stmtTamanos->fetchAll(PDO::FETCH_ASSOC);

        return $producto;
    }

    /**
     * Actualiza o crea el estado y precios de productos en el menú del propietario
     * 
     * @param array<int, array{
     *     id_producto: int,
     *     estado?: int,
     *     precio_base?: float,
     *     tamanos?: array<int, array{
     *         id_tamano: int,
     *         precio: float,
     *         estado?: int
     *     }>
     * }> $productos
     * @param int $propietarioId
     * @return array{actualizados: int}
     */
    public function updateProductos(array $productos, int $propietarioId): array
    {
        $this->pdo->beginTransaction();
        try {
            $fechaAlta = date('Y-m-d H:i:s');
            $actualizados = 0;

            foreach ($productos as $prod) {
                $productoId = (int)$prod['id_producto'];
                $estado = isset($prod['estado']) ? (int)$prod['estado'] : 0;
                $precioBase = isset($prod['precio_base']) ? (float)$prod['precio_base'] : 0.0;

                // Verificar si existe registro del propietario
                $stmtCheck = $this->pdo->prepare(
                    "SELECT id FROM propietarios_menu_productos 
                    WHERE id_propietario = :propietario_id AND id_producto = :producto_id AND estado != 2"
                );
                $stmtCheck->execute([
                    ':propietario_id' => $propietarioId,
                    ':producto_id' => $productoId,
                ]);
                $existe = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if ($existe) {
                    // Actualizar existente
                    $stmtUpdate = $this->pdo->prepare(
                        "UPDATE propietarios_menu_productos 
                        SET estado = :estado, precio_base = :precio_base
                        WHERE id = :id"
                    );
                    $stmtUpdate->execute([
                        ':id' => $existe['id'],
                        ':estado' => $estado,
                        ':precio_base' => $precioBase,
                    ]);
                    $idPropietarioMenuProducto = $existe['id'];
                } else {
                    // Crear nuevo
                    $stmtInsert = $this->pdo->prepare(
                        "INSERT INTO propietarios_menu_productos 
                        (id_propietario, id_producto, precio_base, estado, id_alta, fecha_alta)
                        VALUES (:propietario_id, :producto_id, :precio_base, :estado, :id_alta, :fecha_alta)"
                    );
                    $stmtInsert->execute([
                        ':propietario_id' => $propietarioId,
                        ':producto_id' => $productoId,
                        ':precio_base' => $precioBase,
                        ':estado' => $estado,
                        ':id_alta' => $propietarioId,
                        ':fecha_alta' => $fechaAlta,
                    ]);
                    $idPropietarioMenuProducto = (int)$this->pdo->lastInsertId();
                }

                // Actualizar tamaños si se proporcionan
                if (isset($prod['tamanos']) && is_array($prod['tamanos'])) {
                    foreach ($prod['tamanos'] as $tamano) {
                        $idTamano = (int)$tamano['id_tamano'];
                        $precio = (float)$tamano['precio'];
                        $estadoTamano = isset($tamano['estado']) ? (int)$tamano['estado'] : 0;

                        $stmtCheckTamano = $this->pdo->prepare(
                            "SELECT id FROM propietarios_menu_productos_tamanos 
                            WHERE id_propietario_menu_producto = :id_producto AND id_tamano = :id_tamano AND estado != 2"
                        );
                        $stmtCheckTamano->execute([
                            ':id_producto' => $idPropietarioMenuProducto,
                            ':id_tamano' => $idTamano,
                        ]);
                        $existeTamano = $stmtCheckTamano->fetch(PDO::FETCH_ASSOC);

                        if ($existeTamano) {
                            $stmtUpdateTamano = $this->pdo->prepare(
                                "UPDATE propietarios_menu_productos_tamanos 
                                SET precio = :precio, estado = :estado
                                WHERE id = :id"
                            );
                            $stmtUpdateTamano->execute([
                                ':id' => $existeTamano['id'],
                                ':precio' => $precio,
                                ':estado' => $estadoTamano,
                            ]);
                        } else {
                            $stmtInsertTamano = $this->pdo->prepare(
                                "INSERT INTO propietarios_menu_productos_tamanos 
                                (id_propietario_menu_producto, id_tamano, precio, estado)
                                VALUES (:id_producto, :id_tamano, :precio, :estado)"
                            );
                            $stmtInsertTamano->execute([
                                ':id_producto' => $idPropietarioMenuProducto,
                                ':id_tamano' => $idTamano,
                                ':precio' => $precio,
                                ':estado' => $estadoTamano,
                            ]);
                        }
                    }
                }

                $actualizados++;
            }

            $this->pdo->commit();
            return ['actualizados' => $actualizados];
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Obtiene las reglas completas de un producto en el menú del propietario
     * 
     * @return array{
     *     categorias: array,
     *     ingredientes: array,
     *     catalogo_categorias: array,
     *     catalogo_ingredientes: array
     * }
     */
    public function getReglas(int $productoId, int $propietarioId): array
    {
        // Obtener id_propietario_menu_producto
        $stmtProd = $this->pdo->prepare(
            "SELECT id FROM propietarios_menu_productos 
            WHERE id_producto = :producto_id AND id_propietario = :propietario_id AND estado != 2"
        );
        $stmtProd->execute([
            ':producto_id' => $productoId,
            ':propietario_id' => $propietarioId,
        ]);
        $prod = $stmtProd->fetch(PDO::FETCH_ASSOC);
        
        if (!$prod) {
            // Si no existe registro, devolver estructura vacía
            return [
                'categorias' => [],
                'ingredientes' => [],
                'catalogo_categorias' => [],
                'catalogo_ingredientes' => [],
            ];
        }

        $idPropietarioMenuProducto = (int)$prod['id'];

        // Reglas por categoría
        $stmtCategorias = $this->pdo->prepare(
            "SELECT 
                pmpric.id_propietario_menu_producto,
                pmpric.id_ingrediente_categoria,
                mic.nombre AS categoria_nombre,
                pmpric.tipo_seleccion,
                pmpric.seleccion_minima,
                pmpric.seleccion_maxima,
                pmpric.orden
            FROM propietarios_menu_productos_reglas_ingredientes_categorias pmpric
            INNER JOIN menu_ingredientes_categorias mic ON pmpric.id_ingrediente_categoria = mic.id
            WHERE pmpric.id_propietario_menu_producto = :id AND pmpric.estado = 0
            ORDER BY pmpric.orden ASC, mic.nombre ASC"
        );
        $stmtCategorias->execute([':id' => $idPropietarioMenuProducto]);
        $categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);

        // Reglas por ingrediente (solo de categorías aplicables)
        $categoriaIds = array_map(fn($c) => (int)$c['id_ingrediente_categoria'], $categorias);
        
        $ingredientes = [];
        if (!empty($categoriaIds)) {
            $placeholders = [];
            $params = [':id_producto' => $idPropietarioMenuProducto];
            foreach ($categoriaIds as $idx => $catId) {
                $key = ':cat' . $idx;
                $placeholders[] = $key;
                $params[$key] = $catId;
            }

            $stmtIngredientes = $this->pdo->prepare(
                "SELECT 
                    pmpri.id_propietario_menu_producto,
                    pmpri.id_ingrediente,
                    mi.nombre AS ingrediente_nombre,
                    mi.id_ingrediente_categoria,
                    mic.nombre AS categoria_nombre,
                    pmpri.permite_cantidad,
                    pmpri.cantidad_minima,
                    pmpri.cantidad_maxima,
                    pmpri.paso_cantidad,
                    pmpri.cantidad_incluida,
                    pmpri.tipo_precio,
                    pmpri.precio_unitario,
                    pmpri.es_recomendado,
                    pmpri.estado
                FROM propietarios_menu_productos_reglas_ingredientes pmpri
                INNER JOIN menu_ingredientes mi ON pmpri.id_ingrediente = mi.id
                INNER JOIN menu_ingredientes_categorias mic ON mi.id_ingrediente_categoria = mic.id
                WHERE pmpri.id_propietario_menu_producto = :id_producto 
                AND mi.id_ingrediente_categoria IN (" . implode(',', $placeholders) . ")
                AND pmpri.estado = 0
                ORDER BY mic.nombre ASC, mi.nombre ASC"
            );
            foreach ($params as $key => $value) {
                $stmtIngredientes->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmtIngredientes->execute();
            $ingredientes = $stmtIngredientes->fetchAll(PDO::FETCH_ASSOC);
        }

        // Catálogo: todas las categorías disponibles
        $stmtCatCategorias = $this->pdo->prepare(
            "SELECT id, nombre 
            FROM menu_ingredientes_categorias 
            WHERE estado = 0 
            ORDER BY nombre ASC"
        );
        $stmtCatCategorias->execute();
        $catalogoCategorias = $stmtCatCategorias->fetchAll(PDO::FETCH_ASSOC);

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
            $catalogoIngredientes = $stmtCatIngredientes->fetchAll(PDO::FETCH_ASSOC);
        }

        return [
            'categorias' => $categorias,
            'ingredientes' => $ingredientes,
            'catalogo_categorias' => $catalogoCategorias,
            'catalogo_ingredientes' => $catalogoIngredientes,
        ];
    }

    /**
     * Guarda las reglas de un producto en el menú del propietario (reemplazo total en transacción)
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
     * @param int $productoId
     * @param int $propietarioId
     * @return array{categorias_guardadas: int, ingredientes_guardados: int}
     */
    public function saveReglas(int $productoId, array $reglas, int $propietarioId): array
    {
        $this->pdo->beginTransaction();
        try {
            $fechaAlta = date('Y-m-d H:i:s');

            // Obtener o crear registro de propietarios_menu_productos
            $stmtProd = $this->pdo->prepare(
                "SELECT id FROM propietarios_menu_productos 
                WHERE id_producto = :producto_id AND id_propietario = :propietario_id AND estado != 2"
            );
            $stmtProd->execute([
                ':producto_id' => $productoId,
                ':propietario_id' => $propietarioId,
            ]);
            $prod = $stmtProd->fetch(PDO::FETCH_ASSOC);

            if (!$prod) {
                // Crear registro si no existe
                $stmtInsertProd = $this->pdo->prepare(
                    "INSERT INTO propietarios_menu_productos 
                    (id_propietario, id_producto, precio_base, estado, id_alta, fecha_alta)
                    VALUES (:propietario_id, :producto_id, 0, 0, :id_alta, :fecha_alta)"
                );
                $stmtInsertProd->execute([
                    ':propietario_id' => $propietarioId,
                    ':producto_id' => $productoId,
                    ':id_alta' => $propietarioId,
                    ':fecha_alta' => $fechaAlta,
                ]);
                $idPropietarioMenuProducto = (int)$this->pdo->lastInsertId();
            } else {
                $idPropietarioMenuProducto = (int)$prod['id'];
            }

            // 1. Marcar reglas existentes como eliminadas (soft delete)
            $stmtDelCategorias = $this->pdo->prepare(
                "UPDATE propietarios_menu_productos_reglas_ingredientes_categorias 
                SET estado = 2 
                WHERE id_propietario_menu_producto = :id AND estado = 0"
            );
            $stmtDelCategorias->execute([':id' => $idPropietarioMenuProducto]);

            $stmtDelIngredientes = $this->pdo->prepare(
                "UPDATE propietarios_menu_productos_reglas_ingredientes 
                SET estado = 2 
                WHERE id_propietario_menu_producto = :id AND estado = 0"
            );
            $stmtDelIngredientes->execute([':id' => $idPropietarioMenuProducto]);

            // 2. Insertar/actualizar categorías
            $categoriasGuardadas = 0;
            foreach ($reglas['categorias'] ?? [] as $cat) {
                $stmtCat = $this->pdo->prepare(
                    "INSERT INTO propietarios_menu_productos_reglas_ingredientes_categorias
                    (id_propietario_menu_producto, id_ingrediente_categoria, tipo_seleccion, seleccion_minima, seleccion_maxima, orden, estado, id_alta, fecha_alta)
                    VALUES (:id_producto, :id_categoria, :tipo_seleccion, :seleccion_minima, :seleccion_maxima, :orden, 0, :id_alta, :fecha_alta)
                    ON DUPLICATE KEY UPDATE
                        tipo_seleccion = VALUES(tipo_seleccion),
                        seleccion_minima = VALUES(seleccion_minima),
                        seleccion_maxima = VALUES(seleccion_maxima),
                        orden = VALUES(orden),
                        estado = 0"
                );
                $stmtCat->execute([
                    ':id_producto' => $idPropietarioMenuProducto,
                    ':id_categoria' => $cat['id_ingrediente_categoria'],
                    ':tipo_seleccion' => $cat['tipo_seleccion'],
                    ':seleccion_minima' => $cat['seleccion_minima'],
                    ':seleccion_maxima' => $cat['seleccion_maxima'] ?? null,
                    ':orden' => $cat['orden'],
                    ':id_alta' => $propietarioId,
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
                    "INSERT INTO propietarios_menu_productos_reglas_ingredientes
                    (id_propietario_menu_producto, id_ingrediente, permite_cantidad, cantidad_minima, cantidad_maxima, paso_cantidad, cantidad_incluida, tipo_precio, precio_unitario, es_recomendado, estado, id_alta, fecha_alta)
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
                    ':id_producto' => $idPropietarioMenuProducto,
                    ':id_ingrediente' => $ing['id_ingrediente'],
                    ':permite_cantidad' => $permiteCantidad ? 1 : 0,
                    ':cantidad_minima' => $ing['cantidad_minima'],
                    ':cantidad_maxima' => $cantidadMaxima,
                    ':paso_cantidad' => $ing['paso_cantidad'],
                    ':cantidad_incluida' => $ing['cantidad_incluida'],
                    ':tipo_precio' => $ing['tipo_precio'],
                    ':precio_unitario' => $ing['precio_unitario'],
                    ':es_recomendado' => $ing['es_recomendado'] ? 1 : 0,
                    ':id_alta' => $propietarioId,
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

    /**
     * Verifica que el propietario tiene acceso al producto
     */
    public function verificarAcceso(int $productoId, int $propietarioId): bool
    {
        // Verificar que el producto existe y es del catálogo OCCU
        $stmt = $this->pdo->prepare(
            "SELECT 1 FROM menu_productos 
            WHERE id = :id AND estado = 0 AND registro_occu = 1 LIMIT 1"
        );
        $stmt->execute([':id' => $productoId]);
        return (bool)$stmt->fetchColumn();
    }
}
