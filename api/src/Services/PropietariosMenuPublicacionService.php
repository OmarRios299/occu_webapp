<?php

declare(strict_types=1);

namespace Occu\Api\Services;

use Occu\Api\Repositories\PropietariosMenuGeneralRepository;
use Occu\Api\Repositories\PropietariosMenuPublicacionesRepository;
use Occu\Api\Repositories\OwnerCafeteriasRepository;
use PDO;

final class PropietariosMenuPublicacionService
{
    private PropietariosMenuGeneralRepository $menuGeneralRepo;
    private PropietariosMenuPublicacionesRepository $publicacionesRepo;
    private OwnerCafeteriasRepository $cafeteriasRepo;

    public function __construct(
        PropietariosMenuGeneralRepository $menuGeneralRepo,
        PropietariosMenuPublicacionesRepository $publicacionesRepo,
        OwnerCafeteriasRepository $cafeteriasRepo
    ) {
        $this->menuGeneralRepo = $menuGeneralRepo;
        $this->publicacionesRepo = $publicacionesRepo;
        $this->cafeteriasRepo = $cafeteriasRepo;
    }

    /**
     * Publica el menú general a las cafeterías especificadas
     * 
     * @param int $propietarioId
     * @param array<int> $cafeteriaIds
     * @param string $modo 'todo' | 'solo_precios'
     * @return array{publicadas: int, productos_publicados: int}
     * @throws \RuntimeException Si alguna cafetería no pertenece al propietario
     */
    public function publicarMenu(
        int $propietarioId,
        array $cafeteriaIds,
        string $modo
    ): array {
        // Validar que todas las cafeterías pertenezcan al propietario
        foreach ($cafeteriaIds as $cafId) {
            $cafeteria = $this->cafeteriasRepo->findById($cafId, $propietarioId, 'Propietario');
            if (!$cafeteria) {
                throw new \RuntimeException("La cafetería {$cafId} no pertenece al propietario o no existe");
            }
        }

        // Obtener productos activos del menú general (sin límite de página)
        // Necesitamos obtener todos los productos activos
        $productos = $this->menuGeneralRepo->findProductos(
            ['page' => 1, 'pageSize' => 10000, 'estado' => 1],
            $propietarioId
        );
        
        // Si hay más productos, obtenerlos en lotes
        $todosProductos = $productos['items'];
        $totalPages = $productos['totalPages'] ?? 1;
        for ($page = 2; $page <= $totalPages; $page++) {
            $masProductos = $this->menuGeneralRepo->findProductos(
                ['page' => $page, 'pageSize' => 10000, 'estado' => 1],
                $propietarioId
            );
            $todosProductos = array_merge($todosProductos, $masProductos['items']);
        }

        $productosPublicados = 0;
        $this->menuGeneralRepo->getPdo()->beginTransaction();
        try {
            foreach ($cafeteriaIds as $cafId) {
                foreach ($todosProductos as $producto) {
                    if ($modo === 'todo') {
                        // Publicar todo: productos, precios, tamaños y reglas
                        $this->publicarProductoCompleto($cafId, $producto['id'], $propietarioId);
                    } else {
                        // Solo precios: actualizar solo precios base y por tamaño
                        $this->publicarSoloPrecios($cafId, $producto['id'], $propietarioId);
                    }
                    $productosPublicados++;
                }
            }

            // Registrar publicación
            $this->publicacionesRepo->registrarPublicacion(
                $propietarioId,
                $modo,
                $cafeteriaIds,
                $productosPublicados
            );

            $this->menuGeneralRepo->getPdo()->commit();
            return [
                'publicadas' => count($cafeteriaIds),
                'productos_publicados' => $productosPublicados,
            ];
        } catch (\Throwable $e) {
            $this->menuGeneralRepo->getPdo()->rollBack();
            throw $e;
        }
    }

    /**
     * Publica un producto completo (precios + reglas)
     */
    private function publicarProductoCompleto(int $cafeteriaId, int $productoId, int $propietarioId): void
    {
        $pdo = $this->menuGeneralRepo->getPdo();
        $fechaAlta = date('Y-m-d H:i:s');

        // 1. Obtener datos del menú general
        $productoGeneral = $this->menuGeneralRepo->findProductoById($productoId, $propietarioId);
        if (!$productoGeneral) {
            return;
        }

        // 2. Crear/actualizar registro en cafeterias_menu_productos
        $stmtCheck = $pdo->prepare(
            "SELECT id FROM cafeterias_menu_productos 
            WHERE id_cafeteria = :cafeteria_id AND id_producto = :producto_id AND estado != 2"
        );
        $stmtCheck->execute([
            ':cafeteria_id' => $cafeteriaId,
            ':producto_id' => $productoId,
        ]);
        $existe = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($existe) {
            $stmtUpdate = $pdo->prepare(
                "UPDATE cafeterias_menu_productos 
                SET precio_base = :precio_base, estado = 0
                WHERE id = :id"
            );
            $stmtUpdate->execute([
                ':id' => $existe['id'],
                ':precio_base' => (float)$productoGeneral['precio_base'],
            ]);
            $idCafeteriaMenuProducto = (int)$existe['id'];
        } else {
            $stmtInsert = $pdo->prepare(
                "INSERT INTO cafeterias_menu_productos 
                (id_cafeteria, id_producto, precio_base, estado, id_alta, fecha_alta)
                VALUES (:cafeteria_id, :producto_id, :precio_base, 0, :id_alta, :fecha_alta)"
            );
            $stmtInsert->execute([
                ':cafeteria_id' => $cafeteriaId,
                ':producto_id' => $productoId,
                ':precio_base' => (float)$productoGeneral['precio_base'],
                ':id_alta' => $propietarioId,
                ':fecha_alta' => $fechaAlta,
            ]);
            $idCafeteriaMenuProducto = (int)$pdo->lastInsertId();
        }

        // 3. Copiar tamaños
        if (!empty($productoGeneral['tamanos'])) {
            foreach ($productoGeneral['tamanos'] as $tamano) {
                if ($tamano['estado_tamano'] === 1) { // Solo tamaños activos
                    $stmtCheckTamano = $pdo->prepare(
                        "SELECT id FROM cafeterias_menu_productos_tamanos 
                        WHERE id_cafeteria_menu_producto = :id_producto AND id_tamano = :id_tamano AND estado != 2"
                    );
                    $stmtCheckTamano->execute([
                        ':id_producto' => $idCafeteriaMenuProducto,
                        ':id_tamano' => $tamano['id_tamano'],
                    ]);
                    $existeTamano = $stmtCheckTamano->fetch(PDO::FETCH_ASSOC);

                    if ($existeTamano) {
                        $stmtUpdateTamano = $pdo->prepare(
                            "UPDATE cafeterias_menu_productos_tamanos 
                            SET precio = :precio, estado = 0
                            WHERE id = :id"
                        );
                        $stmtUpdateTamano->execute([
                            ':id' => $existeTamano['id'],
                            ':precio' => (float)$tamano['precio'],
                        ]);
                    } else {
                        $stmtInsertTamano = $pdo->prepare(
                            "INSERT INTO cafeterias_menu_productos_tamanos 
                            (id_cafeteria_menu_producto, id_tamano, precio, estado)
                            VALUES (:id_producto, :id_tamano, :precio, 0)"
                        );
                        $stmtInsertTamano->execute([
                            ':id_producto' => $idCafeteriaMenuProducto,
                            ':id_tamano' => $tamano['id_tamano'],
                            ':precio' => (float)$tamano['precio'],
                        ]);
                    }
                }
            }
        }

        // 4. Copiar reglas (solo si modo es 'todo')
        $reglas = $this->menuGeneralRepo->getReglas($productoId, $propietarioId);
        $this->copiarReglasACafeteria($idCafeteriaMenuProducto, $reglas, $propietarioId);
    }

    /**
     * Publica solo los precios (sin tocar reglas)
     */
    private function publicarSoloPrecios(int $cafeteriaId, int $productoId, int $propietarioId): void
    {
        $pdo = $this->menuGeneralRepo->getPdo();
        $productoGeneral = $this->menuGeneralRepo->findProductoById($productoId, $propietarioId);
        if (!$productoGeneral) {
            return;
        }

        // Solo actualizar precios si el producto ya existe en la cafetería
        $stmtCheck = $pdo->prepare(
            "SELECT id FROM cafeterias_menu_productos 
            WHERE id_cafeteria = :cafeteria_id AND id_producto = :producto_id AND estado != 2"
        );
        $stmtCheck->execute([
            ':cafeteria_id' => $cafeteriaId,
            ':producto_id' => $productoId,
        ]);
        $existe = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($existe) {
            $idCafeteriaMenuProducto = (int)$existe['id'];

            // Actualizar precio base
            $stmtUpdate = $pdo->prepare(
                "UPDATE cafeterias_menu_productos 
                SET precio_base = :precio_base
                WHERE id = :id"
            );
            $stmtUpdate->execute([
                ':id' => $idCafeteriaMenuProducto,
                ':precio_base' => (float)$productoGeneral['precio_base'],
            ]);

            // Actualizar precios por tamaño
            if (!empty($productoGeneral['tamanos'])) {
                foreach ($productoGeneral['tamanos'] as $tamano) {
                    if ($tamano['estado_tamano'] === 1) {
                        $stmtUpdateTamano = $pdo->prepare(
                            "UPDATE cafeterias_menu_productos_tamanos 
                            SET precio = :precio
                            WHERE id_cafeteria_menu_producto = :id_producto 
                            AND id_tamano = :id_tamano AND estado != 2"
                        );
                        $stmtUpdateTamano->execute([
                            ':id_producto' => $idCafeteriaMenuProducto,
                            ':id_tamano' => $tamano['id_tamano'],
                            ':precio' => (float)$tamano['precio'],
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Copia las reglas del menú general a la cafetería
     */
    private function copiarReglasACafeteria(int $idCafeteriaMenuProducto, array $reglas, int $propietarioId): void
    {
        $pdo = $this->menuGeneralRepo->getPdo();
        $fechaAlta = date('Y-m-d H:i:s');

        // 1. Marcar reglas existentes como eliminadas
        $stmtDelCategorias = $pdo->prepare(
            "UPDATE cafeterias_menu_productos_reglas_ingredientes_categorias 
            SET estado = 2 
            WHERE id_cafeteria_menu_producto = :id AND estado = 0"
        );
        $stmtDelCategorias->execute([':id' => $idCafeteriaMenuProducto]);

        $stmtDelIngredientes = $pdo->prepare(
            "UPDATE cafeterias_menu_productos_reglas_ingredientes 
            SET estado = 2 
            WHERE id_cafeteria_menu_producto = :id AND estado = 0"
        );
        $stmtDelIngredientes->execute([':id' => $idCafeteriaMenuProducto]);

        // 2. Insertar categorías
        foreach ($reglas['categorias'] ?? [] as $cat) {
            $stmtCat = $pdo->prepare(
                "INSERT INTO cafeterias_menu_productos_reglas_ingredientes_categorias
                (id_cafeteria_menu_producto, id_ingrediente_categoria, tipo_seleccion, seleccion_minima, seleccion_maxima, orden, estado)
                VALUES (:id_producto, :id_categoria, :tipo_seleccion, :seleccion_minima, :seleccion_maxima, :orden, 0)
                ON DUPLICATE KEY UPDATE
                    tipo_seleccion = VALUES(tipo_seleccion),
                    seleccion_minima = VALUES(seleccion_minima),
                    seleccion_maxima = VALUES(seleccion_maxima),
                    orden = VALUES(orden),
                    estado = 0"
            );
            $stmtCat->execute([
                ':id_producto' => $idCafeteriaMenuProducto,
                ':id_categoria' => (int)$cat['id_ingrediente_categoria'],
                ':tipo_seleccion' => $cat['tipo_seleccion'],
                ':seleccion_minima' => (int)$cat['seleccion_minima'],
                ':seleccion_maxima' => isset($cat['seleccion_maxima']) ? (int)$cat['seleccion_maxima'] : null,
                ':orden' => (int)$cat['orden'],
            ]);
        }

        // 3. Insertar ingredientes
        foreach ($reglas['ingredientes'] ?? [] as $ing) {
            // Convertir valores booleanos de la BD (pueden venir como 0/1 o true/false)
            $permiteCantidadOriginal = is_bool($ing['permite_cantidad']) 
                ? $ing['permite_cantidad'] 
                : ((int)$ing['permite_cantidad'] === 1);
            $esRecomendadoOriginal = is_bool($ing['es_recomendado']) 
                ? $ing['es_recomendado'] 
                : ((int)($ing['es_recomendado'] ?? 0) === 1);
            
            // Aplicar reglas de negocio: si tipo_precio es 'fijo', forzar restricciones
            $permiteCantidad = $ing['tipo_precio'] === 'fijo' ? false : $permiteCantidadOriginal;
            $cantidadMaxima = $ing['tipo_precio'] === 'fijo' ? 1 : ((int)$ing['cantidad_maxima'] ?? 1);
            
            $stmtIng = $pdo->prepare(
                "INSERT INTO cafeterias_menu_productos_reglas_ingredientes
                (id_cafeteria_menu_producto, id_ingrediente, permite_cantidad, cantidad_minima, cantidad_maxima, paso_cantidad, cantidad_incluida, tipo_precio, precio_unitario, es_recomendado, estado)
                VALUES (:id_producto, :id_ingrediente, :permite_cantidad, :cantidad_minima, :cantidad_maxima, :paso_cantidad, :cantidad_incluida, :tipo_precio, :precio_unitario, :es_recomendado, 0)
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
                ':id_producto' => $idCafeteriaMenuProducto,
                ':id_ingrediente' => (int)$ing['id_ingrediente'],
                ':permite_cantidad' => $permiteCantidad ? 1 : 0,
                ':cantidad_minima' => (int)$ing['cantidad_minima'],
                ':cantidad_maxima' => (int)$cantidadMaxima,
                ':paso_cantidad' => (int)$ing['paso_cantidad'],
                ':cantidad_incluida' => (int)$ing['cantidad_incluida'],
                ':tipo_precio' => $ing['tipo_precio'],
                ':precio_unitario' => (float)$ing['precio_unitario'],
                ':es_recomendado' => $esRecomendadoOriginal ? 1 : 0,
            ]);
        }
    }
}
