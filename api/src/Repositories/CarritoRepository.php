<?php

declare(strict_types=1);

namespace Occu\Api\Repositories;

use Occu\Api\Infrastructure\Db;
use PDO;

final class CarritoRepository
{
    private PDO $pdo;

    public function __construct(Db $db)
    {
        $this->pdo = $db->pdo();
    }

    /**
     * Obtiene o crea un carrito activo para un usuario y cafetería
     * 
     * @param int $userId
     * @param int $cafeteriaId
     * @return int ID del carrito
     */
    public function obtenerOCrearCarrito(int $userId, int $cafeteriaId): int
    {
        // Buscar carrito activo existente
        $stmt = $this->pdo->prepare("
            SELECT id FROM ventas_carrito
            WHERE id_usuario = :user_id
            AND id_cafeteria = :cafeteria_id
            AND estado = 0
            LIMIT 1
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':cafeteria_id', $cafeteriaId, PDO::PARAM_INT);
        $stmt->execute();
        $carrito = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($carrito) {
            return (int)$carrito['id'];
        }

        // Crear nuevo carrito
        $stmt = $this->pdo->prepare("
            INSERT INTO ventas_carrito (id_cafeteria, id_usuario, fecha_alta)
            VALUES (:cafeteria_id, :user_id, NOW())
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':cafeteria_id', $cafeteriaId, PDO::PARAM_INT);
        $stmt->execute();

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Busca un item del carrito que coincida exactamente con producto, tamaño y personalizaciones
     * 
     * @param int $carritoId
     * @param int $productoId
     * @param int|null $tamanoId
     * @param array<int, int> $selecciones Map de ingredienteId => cantidad
     * @return array|null Item encontrado o null
     */
    public function buscarItemIgual(int $carritoId, int $productoId, ?int $tamanoId, array $selecciones): ?array
    {
        // Normalizar tamanoId (NULL se convierte en 0 para comparación)
        $tamanoIdNormalizado = $tamanoId ?? 0;

        // Obtener todos los items del carrito con este producto y tamaño
        $stmt = $this->pdo->prepare("
            SELECT 
                vci.id,
                vci.id_producto,
                vci.id_tamano,
                vci.cantidad
            FROM ventas_carrito_items vci
            WHERE vci.id_carrito = :carrito_id
            AND vci.id_producto = :producto_id
            AND COALESCE(vci.id_tamano, 0) = :tamano_id
            AND vci.estado = 0
        ");
        $stmt->bindValue(':carrito_id', $carritoId, PDO::PARAM_INT);
        $stmt->bindValue(':producto_id', $productoId, PDO::PARAM_INT);
        $stmt->bindValue(':tamano_id', $tamanoIdNormalizado, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Para cada item, verificar si las personalizaciones coinciden exactamente
        foreach ($items as $item) {
            $itemId = (int)$item['id'];

            // Obtener personalizaciones de este item
            $stmtPers = $this->pdo->prepare("
                SELECT id_ingrediente, cantidad
                FROM ventas_carrito_items_personalizaciones
                WHERE id_carrito_item = :item_id
                AND estado = 0
            ");
            $stmtPers->bindValue(':item_id', $itemId, PDO::PARAM_INT);
            $stmtPers->execute();
            $personalizaciones = $stmtPers->fetchAll(PDO::FETCH_ASSOC);

            // Convertir a map
            $persMap = [];
            foreach ($personalizaciones as $pers) {
                $persMap[(int)$pers['id_ingrediente']] = (int)$pers['cantidad'];
            }

            // Comparar con las selecciones recibidas
            if (count($persMap) !== count($selecciones)) {
                continue;
            }

            $coincide = true;
            foreach ($selecciones as $ingId => $cantidad) {
                if (!isset($persMap[$ingId]) || $persMap[$ingId] !== $cantidad) {
                    $coincide = false;
                    break;
                }
            }

            if ($coincide) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Agrega un item al carrito
     * 
     * @param int $carritoId
     * @param int $productoId
     * @param int|null $tamanoId
     * @param int $cantidad
     * @return int ID del item creado
     */
    public function agregarItem(int $carritoId, int $productoId, ?int $tamanoId, int $cantidad): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO ventas_carrito_items (id_carrito, id_producto, id_tamano, cantidad, fecha_alta)
            VALUES (:carrito_id, :producto_id, :tamano_id, :cantidad, NOW())
        ");
        $stmt->bindValue(':carrito_id', $carritoId, PDO::PARAM_INT);
        $stmt->bindValue(':producto_id', $productoId, PDO::PARAM_INT);
        $stmt->bindValue(':tamano_id', $tamanoId, $tamanoId !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmt->execute();

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Actualiza la cantidad de un item existente
     * 
     * @param int $itemId
     * @param int $nuevaCantidad
     */
    public function actualizarCantidadItem(int $itemId, int $nuevaCantidad): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE ventas_carrito_items
            SET cantidad = :cantidad
            WHERE id = :item_id
        ");
        $stmt->bindValue(':item_id', $itemId, PDO::PARAM_INT);
        $stmt->bindValue(':cantidad', $nuevaCantidad, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Guarda las personalizaciones (snapshot) de un item del carrito
     * 
     * @param int $itemId
     * @param array<array{ingrediente_id: int, categoria_id: int|null, nombre: string, cantidad: int, precio_unitario: float, monto_total: float}> $personalizaciones
     */
    public function guardarPersonalizaciones(int $itemId, array $personalizaciones): void
    {
        // Eliminar personalizaciones existentes del item
        $stmtDelete = $this->pdo->prepare("
            DELETE FROM ventas_carrito_items_personalizaciones
            WHERE id_carrito_item = :item_id
        ");
        $stmtDelete->bindValue(':item_id', $itemId, PDO::PARAM_INT);
        $stmtDelete->execute();

        // Insertar nuevas personalizaciones
        $stmtInsert = $this->pdo->prepare("
            INSERT INTO ventas_carrito_items_personalizaciones (
                id_carrito_item,
                id_ingrediente,
                id_ingrediente_categoria_snapshot,
                nombre_ingrediente_snapshot,
                cantidad,
                precio_unitario_snapshot,
                monto_total_snapshot,
                fecha_alta
            ) VALUES (
                :item_id,
                :ingrediente_id,
                :categoria_id,
                :nombre,
                :cantidad,
                :precio_unitario,
                :monto_total,
                NOW()
            )
        ");

        foreach ($personalizaciones as $pers) {
            $stmtInsert->bindValue(':item_id', $itemId, PDO::PARAM_INT);
            $stmtInsert->bindValue(':ingrediente_id', $pers['ingrediente_id'], PDO::PARAM_INT);
            $stmtInsert->bindValue(':categoria_id', $pers['categoria_id'], $pers['categoria_id'] !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmtInsert->bindValue(':nombre', $pers['nombre'], PDO::PARAM_STR);
            $stmtInsert->bindValue(':cantidad', $pers['cantidad'], PDO::PARAM_INT);
            $stmtInsert->bindValue(':precio_unitario', $pers['precio_unitario'], PDO::PARAM_STR);
            $stmtInsert->bindValue(':monto_total', $pers['monto_total'], PDO::PARAM_STR);
            $stmtInsert->execute();
        }
    }

    /**
     * Obtiene el carrito completo con items y personalizaciones
     * 
     * @param int $userId
     * @param int|null $cafeteriaId Si es null, obtiene cualquier carrito activo del usuario
     * @return array|null Carrito con items o null si no existe
     */
    public function obtenerCarrito(int $userId, ?int $cafeteriaId = null): ?array
    {
        $where = "vc.id_usuario = :user_id AND vc.estado = 0";
        $params = [':user_id' => $userId];

        if ($cafeteriaId !== null) {
            $where .= " AND vc.id_cafeteria = :cafeteria_id";
            $params[':cafeteria_id'] = $cafeteriaId;
        }

        $sql = "
            SELECT 
                vc.id AS carrito_id,
                vc.id_cafeteria,
                vc.fecha_alta AS fecha_carrito,
                c.nombre AS cafeteria_nombre,
                c.imagen AS cafeteria_imagen
            FROM ventas_carrito vc
            INNER JOIN cafeterias c ON c.id = vc.id_cafeteria
            WHERE {$where}
            ORDER BY vc.fecha_alta DESC
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_INT);
        }
        $stmt->execute();
        $carrito = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$carrito) {
            return null;
        }

        $carritoId = (int)$carrito['carrito_id'];

        // Obtener items del carrito
        $stmtItems = $this->pdo->prepare("
            SELECT 
                vci.id AS item_id,
                vci.id_producto,
                vci.id_tamano,
                vci.cantidad,
                mp.nombre AS producto_nombre,
                mp.imagen AS producto_imagen,
                mpt.nombre AS tamano_nombre,
                mpt.unidad_medida AS tamano_unidad_medida,
                mpt.medida AS tamano_medida,
                COALESCE(cmpt.precio, cmp.precio_base) AS precio_unitario
            FROM ventas_carrito_items vci
            INNER JOIN menu_productos mp ON mp.id = vci.id_producto
            LEFT JOIN cafeterias_menu_productos cmp ON cmp.id_cafeteria = :cafeteria_id AND cmp.id_producto = vci.id_producto
            LEFT JOIN menu_productos_tamanos mpt ON mpt.id = vci.id_tamano
            LEFT JOIN cafeterias_menu_productos_tamanos cmpt ON cmpt.id_cafeteria_menu_producto = cmp.id AND cmpt.id_tamano = vci.id_tamano AND cmpt.estado = 0
            WHERE vci.id_carrito = :carrito_id
            AND vci.estado = 0
            ORDER BY vci.fecha_alta ASC
        ");
        $stmtItems->bindValue(':carrito_id', $carritoId, PDO::PARAM_INT);
        $stmtItems->bindValue(':cafeteria_id', (int)$carrito['id_cafeteria'], PDO::PARAM_INT);
        $stmtItems->execute();
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        // Obtener personalizaciones de cada item
        foreach ($items as &$item) {
            $itemId = (int)$item['item_id'];

            $stmtPers = $this->pdo->prepare("
                SELECT 
                    id_ingrediente,
                    nombre_ingrediente_snapshot,
                    cantidad,
                    precio_unitario_snapshot,
                    monto_total_snapshot
                FROM ventas_carrito_items_personalizaciones
                WHERE id_carrito_item = :item_id
                AND estado = 0
            ");
            $stmtPers->bindValue(':item_id', $itemId, PDO::PARAM_INT);
            $stmtPers->execute();
            $personalizaciones = $stmtPers->fetchAll(PDO::FETCH_ASSOC);

            $item['personalizaciones'] = array_map(function ($pers) {
                return [
                    'id_ingrediente' => (int)$pers['id_ingrediente'],
                    'nombre' => $pers['nombre_ingrediente_snapshot'],
                    'cantidad' => (int)$pers['cantidad'],
                    'precio_unitario' => (float)$pers['precio_unitario_snapshot'],
                    'monto_total' => (float)$pers['monto_total_snapshot'],
                ];
            }, $personalizaciones);

            // Calcular subtotal del item (precio_unitario * cantidad + sum de personalizaciones)
            $subtotalPersonalizaciones = array_sum(array_column($item['personalizaciones'], 'monto_total'));
            $precioUnitario = (float)$item['precio_unitario'];
            $cantidad = (int)$item['cantidad'];
            $item['subtotal'] = ($precioUnitario * $cantidad) + ($subtotalPersonalizaciones * $cantidad);
        }

        $carrito['items'] = $items;
        $carrito['total'] = array_sum(array_column($items, 'subtotal'));

        return $carrito;
    }
}
