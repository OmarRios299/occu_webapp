<?php

declare(strict_types=1);

namespace Occu\Api\Repositories;

use Occu\Api\Infrastructure\Db;
use PDO;

final class CafeteriasMenuRepository
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
     * Obtiene el menú completo de una cafetería organizado por categorías y subcategorías
     * Solo productos activos (estado = 0)
     * 
     * @param int $cafeteriaId
     * @return array Estructura: [{categoria: {...}, subcategorias: [{subcategoria: {...}, productos: [...]}]}]
     */
    public function getMenu(int $cafeteriaId): array
    {
        // Verificar que la cafetería existe y está activa
        $stmtCheck = $this->pdo->prepare("SELECT id FROM cafeterias WHERE id = :id AND estado = 0");
        $stmtCheck->bindValue(':id', $cafeteriaId, PDO::PARAM_INT);
        $stmtCheck->execute();
        if (!$stmtCheck->fetch()) {
            return [];
        }

        // Obtener productos activos del menú de la cafetería con sus categorías/subcategorías
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT
                mc.id AS categoria_id,
                mc.nombre AS categoria_nombre,
                ms.id AS subcategoria_id,
                ms.nombre AS subcategoria_nombre,
                mp.id AS producto_id,
                mp.nombre AS producto_nombre,
                mp.imagen AS producto_imagen,
                cmp.precio_base AS producto_precio_base,
                cmp.id AS id_cafeteria_menu_producto
            FROM cafeterias_menu_productos cmp
            INNER JOIN menu_productos mp ON mp.id = cmp.id_producto
            INNER JOIN menu_subcategorias ms ON ms.id = mp.id_subcategoria
            INNER JOIN menu_categorias mc ON mc.id = ms.id_categoria
            WHERE cmp.id_cafeteria = :cafeteria_id
            AND cmp.estado = 0
            AND mp.estado = 0
            AND ms.estado = 0
            AND mc.estado = 0
            ORDER BY mc.id, ms.id, mp.nombre
        ");
        $stmt->bindValue(':cafeteria_id', $cafeteriaId, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Organizar por categoría -> subcategoría -> productos
        $menu = [];
        $categoriasMap = [];

        foreach ($rows as $row) {
            $catId = (int)$row['categoria_id'];
            $subcatId = (int)$row['subcategoria_id'];
            $prodId = (int)$row['producto_id'];

            // Agregar categoría si no existe
            if (!isset($categoriasMap[$catId])) {
                $categoria = [
                    'id' => $catId,
                    'nombre' => $row['categoria_nombre'],
                    'subcategorias' => [],
                ];
                $categoriasMap[$catId] = count($menu);
                $menu[] = $categoria;
            }

            $catIndex = $categoriasMap[$catId];
            $subcategorias = &$menu[$catIndex]['subcategorias'];

            // Agregar subcategoría si no existe
            $subcatKey = null;
            foreach ($subcategorias as $idx => $subcat) {
                if ($subcat['id'] === $subcatId) {
                    $subcatKey = $idx;
                    break;
                }
            }

            if ($subcatKey === null) {
                $subcategoria = [
                    'id' => $subcatId,
                    'nombre' => $row['subcategoria_nombre'],
                    'productos' => [],
                ];
                $subcategorias[] = $subcategoria;
                $subcatKey = count($subcategorias) - 1;
            }

            // Agregar producto
            $imagenUrl = \Occu\Api\Support\PublicUrl::toAbsolute($row['producto_imagen'] ?? '');

            $producto = [
                'id' => $prodId,
                'nombre' => $row['producto_nombre'],
                'imagen' => $imagenUrl,
                'precio_base' => (float)$row['producto_precio_base'],
                'id_cafeteria_menu_producto' => (int)$row['id_cafeteria_menu_producto'],
            ];

            $subcategorias[$subcatKey]['productos'][] = $producto;
        }

        return $menu;
    }

    /**
     * Obtiene el detalle completo de un producto del menú de una cafetería
     * Incluye tamaños activos, reglas por categoría e ingredientes con reglas
     * 
     * @param int $cafeteriaId
     * @param int $productoId
     * @return array|null null si no existe o no está activo
     */
    public function getProducto(int $cafeteriaId, int $productoId): ?array
    {
        // Obtener producto base del menú de la cafetería
        $stmt = $this->pdo->prepare("
            SELECT
                mp.id AS producto_id,
                mp.nombre AS producto_nombre,
                mp.imagen AS producto_imagen,
                cmp.precio_base AS precio_base,
                cmp.id AS id_cafeteria_menu_producto
            FROM cafeterias_menu_productos cmp
            INNER JOIN menu_productos mp ON mp.id = cmp.id_producto
            WHERE cmp.id_cafeteria = :cafeteria_id
            AND cmp.id_producto = :producto_id
            AND cmp.estado = 0
            AND mp.estado = 0
        ");
        $stmt->bindValue(':cafeteria_id', $cafeteriaId, PDO::PARAM_INT);
        $stmt->bindValue(':producto_id', $productoId, PDO::PARAM_INT);
        $stmt->execute();
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$producto) {
            return null;
        }

        $idCafeteriaMenuProducto = (int)$producto['id_cafeteria_menu_producto'];
        $imagenUrl = \Occu\Api\Support\PublicUrl::toAbsolute($producto['producto_imagen'] ?? '');

        $result = [
            'id' => (int)$producto['producto_id'],
            'nombre' => $producto['producto_nombre'],
            'imagen' => $imagenUrl,
            'precio_base' => (float)$producto['precio_base'],
            'id_cafeteria_menu_producto' => $idCafeteriaMenuProducto,
            'tamanos' => [],
            'reglas_categorias' => [],
        ];

        // Obtener tamaños activos
        $stmtTamanos = $this->pdo->prepare("
            SELECT
                mpt.id AS tamano_id,
                mpt.nombre AS tamano_nombre,
                mpt.unidad_medida,
                mpt.medida,
                cmpt.precio AS precio
            FROM cafeterias_menu_productos_tamanos cmpt
            INNER JOIN menu_productos_tamanos mpt ON mpt.id = cmpt.id_tamano
            WHERE cmpt.id_cafeteria_menu_producto = :id_cafeteria_menu_producto
            AND cmpt.estado = 0
            AND mpt.estado = 0
            ORDER BY CAST(mpt.medida AS UNSIGNED) ASC, mpt.nombre ASC
        ");
        $stmtTamanos->bindValue(':id_cafeteria_menu_producto', $idCafeteriaMenuProducto, PDO::PARAM_INT);
        $stmtTamanos->execute();
        $tamanos = $stmtTamanos->fetchAll(PDO::FETCH_ASSOC);

        foreach ($tamanos as $tamano) {
            $result['tamanos'][] = [
                'id' => (int)$tamano['tamano_id'],
                'nombre' => $tamano['tamano_nombre'],
                'unidad_medida' => $tamano['unidad_medida'],
                'medida' => $tamano['medida'],
                'precio' => (float)$tamano['precio'],
            ];
        }

        // Obtener reglas por categoría de ingredientes (activas)
        $stmtReglasCategorias = $this->pdo->prepare("
            SELECT
                cmric.id AS regla_categoria_id,
                mic.id AS categoria_id,
                mic.nombre AS categoria_nombre,
                cmric.tipo_seleccion,
                cmric.seleccion_minima,
                cmric.seleccion_maxima,
                cmric.orden
            FROM cafeterias_menu_productos_reglas_ingredientes_categorias cmric
            INNER JOIN menu_ingredientes_categorias mic ON mic.id = cmric.id_ingrediente_categoria
            WHERE cmric.id_cafeteria_menu_producto = :id_cafeteria_menu_producto
            AND cmric.estado = 0
            AND mic.estado = 0
            ORDER BY cmric.orden ASC, mic.nombre ASC
        ");
        $stmtReglasCategorias->bindValue(':id_cafeteria_menu_producto', $idCafeteriaMenuProducto, PDO::PARAM_INT);
        $stmtReglasCategorias->execute();
        $reglasCategorias = $stmtReglasCategorias->fetchAll(PDO::FETCH_ASSOC);

        foreach ($reglasCategorias as $reglaCat) {
            $categoriaId = (int)$reglaCat['categoria_id'];
            $reglaCategoriaId = (int)$reglaCat['regla_categoria_id'];

            // Obtener ingredientes de esta categoría con sus reglas
            $stmtIngredientes = $this->pdo->prepare("
                SELECT
                    cmri.id AS regla_ingrediente_id,
                    mi.id AS ingrediente_id,
                    mi.nombre AS ingrediente_nombre,
                    cmri.permite_cantidad,
                    cmri.cantidad_minima,
                    cmri.cantidad_maxima,
                    cmri.paso_cantidad,
                    cmri.cantidad_incluida,
                    cmri.tipo_precio,
                    cmri.precio_unitario,
                    cmri.es_recomendado,
                    cmri.agotado
                FROM cafeterias_menu_productos_reglas_ingredientes cmri
                INNER JOIN menu_ingredientes mi ON mi.id = cmri.id_ingrediente
                WHERE cmri.id_cafeteria_menu_producto = :id_cafeteria_menu_producto
                AND mi.id_ingrediente_categoria = :categoria_id
                AND cmri.estado = 0
                AND mi.estado = 0
                ORDER BY mi.nombre ASC
            ");
            $stmtIngredientes->bindValue(':id_cafeteria_menu_producto', $idCafeteriaMenuProducto, PDO::PARAM_INT);
            $stmtIngredientes->bindValue(':categoria_id', $categoriaId, PDO::PARAM_INT);
            $stmtIngredientes->execute();
            $ingredientes = $stmtIngredientes->fetchAll(PDO::FETCH_ASSOC);

            $ingredientesData = [];
            foreach ($ingredientes as $ing) {
                $ingredientesData[] = [
                    'id' => (int)$ing['ingrediente_id'],
                    'nombre' => $ing['ingrediente_nombre'],
                    'permite_cantidad' => (bool)$ing['permite_cantidad'],
                    'cantidad_minima' => (int)$ing['cantidad_minima'],
                    'cantidad_maxima' => (int)$ing['cantidad_maxima'],
                    'paso_cantidad' => (int)$ing['paso_cantidad'],
                    'cantidad_incluida' => (int)$ing['cantidad_incluida'],
                    'tipo_precio' => $ing['tipo_precio'],
                    'precio_unitario' => (float)$ing['precio_unitario'],
                    'es_recomendado' => (bool)$ing['es_recomendado'],
                    'agotado' => (bool)$ing['agotado'],
                ];
            }

            $result['reglas_categorias'][] = [
                'id' => $categoriaId,
                'nombre' => $reglaCat['categoria_nombre'],
                'tipo_seleccion' => $reglaCat['tipo_seleccion'],
                'seleccion_minima' => (int)$reglaCat['seleccion_minima'],
                'seleccion_maxima' => $reglaCat['seleccion_maxima'] !== null ? (int)$reglaCat['seleccion_maxima'] : null,
                'orden' => (int)$reglaCat['orden'],
                'ingredientes' => $ingredientesData,
            ];
        }

        return $result;
    }
}
