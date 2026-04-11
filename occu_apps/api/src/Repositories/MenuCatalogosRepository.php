<?php

declare(strict_types=1);

namespace Occu\Api\Repositories;

use Occu\Api\Infrastructure\Db;
use PDO;

final class MenuCatalogosRepository
{
    private PDO $pdo;

    public function __construct(Db $db)
    {
        $this->pdo = $db->pdo();
    }

    // ==========================================================
    // CATEGORÍAS
    // ==========================================================

    /**
     * Lista categorías con paginación y búsqueda
     * 
     * @param array{
     *     page: int,
     *     pageSize: int,
     *     q?: string
     * } $filters
     * @return array{items: array, total: int}
     */
    public function findCategorias(array $filters): array
    {
        $page = $filters['page'];
        $pageSize = $filters['pageSize'];
        $offset = ($page - 1) * $pageSize;

        $busqueda = isset($filters['q']) && $filters['q'] !== '' 
            ? '%' . $filters['q'] . '%' 
            : null;

        $where = ['estado != 2'];
        $params = [];

        if ($busqueda) {
            $where[] = 'nombre LIKE :busqueda';
            $params[':busqueda'] = $busqueda;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT id, nombre, imagen, estado, es_bebida, id_alta, fecha_alta
            FROM menu_categorias
            WHERE {$whereClause}
            ORDER BY nombre ASC
            LIMIT :offset, :limit";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        $countSql = "SELECT COUNT(*) as total FROM menu_categorias WHERE {$whereClause}";
        $countStmt = $this->pdo->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $countStmt->execute();
        $total = (int)$countStmt->fetchColumn();

        return ['items' => $items, 'total' => $total];
    }

    public function findCategoriaById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, nombre, imagen, estado, es_bebida, id_alta, fecha_alta
            FROM menu_categorias
            WHERE id = :id AND estado != 2"
        );
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function createCategoria(array $data, int $userId): int
    {
        $fechaAlta = date('Y-m-d H:i:s');
        
        // Validar nombre único
        $stmtCheck = $this->pdo->prepare(
            "SELECT id FROM menu_categorias WHERE nombre = :nombre AND estado != 2 LIMIT 1"
        );
        $stmtCheck->execute([':nombre' => trim($data['nombre'])]);
        if ($stmtCheck->fetch()) {
            throw new \RuntimeException('Ya existe una categoría con ese nombre');
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO menu_categorias (nombre, imagen, estado, es_bebida, id_alta, fecha_alta)
            VALUES (:nombre, :imagen, :estado, :es_bebida, :id_alta, :fecha_alta)"
        );
        $stmt->execute([
            ':nombre' => trim($data['nombre']),
            ':imagen' => $data['imagen'] ?? '',
            ':estado' => $data['estado'] ?? 0,
            ':es_bebida' => $data['es_bebida'] ?? 'No',
            ':id_alta' => $userId,
            ':fecha_alta' => $fechaAlta,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function updateCategoria(int $id, array $data, int $userId): bool
    {
        // Validar que existe
        if (!$this->findCategoriaById($id)) {
            return false;
        }

        // Validar nombre único (excluyendo el actual)
        if (isset($data['nombre'])) {
            $stmtCheck = $this->pdo->prepare(
                "SELECT id FROM menu_categorias WHERE nombre = :nombre AND id != :id AND estado != 2 LIMIT 1"
            );
            $stmtCheck->execute([
                ':nombre' => trim($data['nombre']),
                ':id' => $id,
            ]);
            if ($stmtCheck->fetch()) {
                throw new \RuntimeException('Ya existe otra categoría con ese nombre');
            }
        }

        $fields = [];
        $params = [':id' => $id];

        if (isset($data['nombre'])) {
            $fields[] = 'nombre = :nombre';
            $params[':nombre'] = trim($data['nombre']);
        }
        if (isset($data['imagen'])) {
            $fields[] = 'imagen = :imagen';
            $params[':imagen'] = $data['imagen'];
        }
        if (isset($data['estado'])) {
            $fields[] = 'estado = :estado';
            $params[':estado'] = (int)$data['estado'];
        }
        if (isset($data['es_bebida'])) {
            $fields[] = 'es_bebida = :es_bebida';
            $params[':es_bebida'] = $data['es_bebida'];
        }

        if (empty($fields)) {
            return true;
        }

        $sql = "UPDATE menu_categorias SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteCategoria(int $id): bool
    {
        // Verificar que no tenga subcategorías activas
        $stmtCheck = $this->pdo->prepare(
            "SELECT COUNT(*) FROM menu_subcategorias WHERE id_categoria = :id AND estado != 2"
        );
        $stmtCheck->execute([':id' => $id]);
        if ($stmtCheck->fetchColumn() > 0) {
            throw new \RuntimeException('No se puede eliminar la categoría porque tiene subcategorías asociadas');
        }

        $stmt = $this->pdo->prepare(
            "UPDATE menu_categorias SET estado = 2 WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }

    // ==========================================================
    // SUBCATEGORÍAS
    // ==========================================================

    /**
     * Lista subcategorías con paginación y búsqueda
     */
    public function findSubcategorias(array $filters): array
    {
        $page = $filters['page'];
        $pageSize = $filters['pageSize'];
        $offset = ($page - 1) * $pageSize;

        $busqueda = isset($filters['q']) && $filters['q'] !== '' 
            ? '%' . $filters['q'] . '%' 
            : null;
        $idCategoria = isset($filters['id_categoria']) ? (int)$filters['id_categoria'] : null;

        $where = ['ms.estado != 2'];
        $params = [];

        if ($busqueda) {
            $where[] = 'ms.nombre LIKE :busqueda';
            $params[':busqueda'] = $busqueda;
        }
        if ($idCategoria !== null) {
            $where[] = 'ms.id_categoria = :id_categoria';
            $params[':id_categoria'] = $idCategoria;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT ms.id, ms.nombre, ms.id_categoria, mc.nombre AS categoria_nombre, 
                ms.estado, ms.registro_occu, ms.id_propietario, ms.fecha_alta
            FROM menu_subcategorias ms
            LEFT JOIN menu_categorias mc ON ms.id_categoria = mc.id
            WHERE {$whereClause}
            ORDER BY mc.nombre ASC, ms.nombre ASC
            LIMIT :offset, :limit";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        $countSql = "SELECT COUNT(*) as total 
            FROM menu_subcategorias ms 
            WHERE {$whereClause}";
        $countStmt = $this->pdo->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $countStmt->execute();
        $total = (int)$countStmt->fetchColumn();

        return ['items' => $items, 'total' => $total];
    }

    public function findSubcategoriaById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT ms.id, ms.nombre, ms.id_categoria, mc.nombre AS categoria_nombre,
                ms.estado, ms.registro_occu, ms.id_propietario, ms.fecha_alta
            FROM menu_subcategorias ms
            LEFT JOIN menu_categorias mc ON ms.id_categoria = mc.id
            WHERE ms.id = :id AND ms.estado != 2"
        );
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function createSubcategoria(array $data, int $userId): int
    {
        $fechaAlta = date('Y-m-d H:i:s');

        // Validar que la categoría existe
        $stmtCat = $this->pdo->prepare(
            "SELECT id FROM menu_categorias WHERE id = :id AND estado != 2 LIMIT 1"
        );
        $stmtCat->execute([':id' => (int)$data['id_categoria']]);
        if (!$stmtCat->fetch()) {
            throw new \RuntimeException('La categoría especificada no existe');
        }

        // Validar nombre único dentro de la categoría
        $stmtCheck = $this->pdo->prepare(
            "SELECT id FROM menu_subcategorias 
            WHERE nombre = :nombre AND id_categoria = :id_categoria AND estado != 2 LIMIT 1"
        );
        $stmtCheck->execute([
            ':nombre' => trim($data['nombre']),
            ':id_categoria' => (int)$data['id_categoria'],
        ]);
        if ($stmtCheck->fetch()) {
            throw new \RuntimeException('Ya existe una subcategoría con ese nombre en esta categoría');
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO menu_subcategorias (nombre, id_categoria, estado, registro_occu, id_propietario, fecha_alta)
            VALUES (:nombre, :id_categoria, :estado, 1, :id_propietario, :fecha_alta)"
        );
        $stmt->execute([
            ':nombre' => trim($data['nombre']),
            ':id_categoria' => (int)$data['id_categoria'],
            ':estado' => $data['estado'] ?? 0,
            ':id_propietario' => 0, // OCCU
            ':fecha_alta' => $fechaAlta,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function updateSubcategoria(int $id, array $data, int $userId): bool
    {
        if (!$this->findSubcategoriaById($id)) {
            return false;
        }

        // Validar categoría si se cambia
        if (isset($data['id_categoria'])) {
            $stmtCat = $this->pdo->prepare(
                "SELECT id FROM menu_categorias WHERE id = :id AND estado != 2 LIMIT 1"
            );
            $stmtCat->execute([':id' => (int)$data['id_categoria']]);
            if (!$stmtCat->fetch()) {
                throw new \RuntimeException('La categoría especificada no existe');
            }
        }

        // Validar nombre único dentro de la categoría
        if (isset($data['nombre'])) {
            $current = $this->findSubcategoriaById($id);
            $idCategoria = $data['id_categoria'] ?? $current['id_categoria'];
            
            $stmtCheck = $this->pdo->prepare(
                "SELECT id FROM menu_subcategorias 
                WHERE nombre = :nombre AND id_categoria = :id_categoria AND id != :id AND estado != 2 LIMIT 1"
            );
            $stmtCheck->execute([
                ':nombre' => trim($data['nombre']),
                ':id_categoria' => (int)$idCategoria,
                ':id' => $id,
            ]);
            if ($stmtCheck->fetch()) {
                throw new \RuntimeException('Ya existe otra subcategoría con ese nombre en esta categoría');
            }
        }

        $fields = [];
        $params = [':id' => $id];

        if (isset($data['nombre'])) {
            $fields[] = 'nombre = :nombre';
            $params[':nombre'] = trim($data['nombre']);
        }
        if (isset($data['id_categoria'])) {
            $fields[] = 'id_categoria = :id_categoria';
            $params[':id_categoria'] = (int)$data['id_categoria'];
        }
        if (isset($data['estado'])) {
            $fields[] = 'estado = :estado';
            $params[':estado'] = (int)$data['estado'];
        }

        if (empty($fields)) {
            return true;
        }

        $sql = "UPDATE menu_subcategorias SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteSubcategoria(int $id): bool
    {
        // Verificar que no tenga productos activos
        $stmtCheck = $this->pdo->prepare(
            "SELECT COUNT(*) FROM menu_productos WHERE id_subcategoria = :id AND estado != 2"
        );
        $stmtCheck->execute([':id' => $id]);
        if ($stmtCheck->fetchColumn() > 0) {
            throw new \RuntimeException('No se puede eliminar la subcategoría porque tiene productos asociados');
        }

        $stmt = $this->pdo->prepare(
            "UPDATE menu_subcategorias SET estado = 2 WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }

    // ==========================================================
    // PRODUCTOS
    // ==========================================================

    /**
     * Lista productos con paginación y búsqueda
     */
    public function findProductos(array $filters): array
    {
        $page = $filters['page'];
        $pageSize = $filters['pageSize'];
        $offset = ($page - 1) * $pageSize;

        $busqueda = isset($filters['q']) && $filters['q'] !== '' 
            ? '%' . $filters['q'] . '%' 
            : null;
        $idSubcategoria = isset($filters['id_subcategoria']) ? (int)$filters['id_subcategoria'] : null;

        $where = ['mp.estado != 2'];
        $params = [];

        if ($busqueda) {
            $where[] = 'mp.nombre LIKE :busqueda';
            $params[':busqueda'] = $busqueda;
        }
        if ($idSubcategoria !== null) {
            $where[] = 'mp.id_subcategoria = :id_subcategoria';
            $params[':id_subcategoria'] = $idSubcategoria;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT mp.id, mp.nombre, mp.id_subcategoria, ms.nombre AS subcategoria_nombre,
                ms.id_categoria, mc.nombre AS categoria_nombre,
                mp.imagen, mp.estado, mp.registro_occu, mp.id_alta, mp.fecha_alta
            FROM menu_productos mp
            LEFT JOIN menu_subcategorias ms ON mp.id_subcategoria = ms.id
            LEFT JOIN menu_categorias mc ON ms.id_categoria = mc.id
            WHERE {$whereClause}
            ORDER BY mc.nombre ASC, ms.nombre ASC, mp.nombre ASC
            LIMIT :offset, :limit";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        $countSql = "SELECT COUNT(*) as total 
            FROM menu_productos mp 
            WHERE {$whereClause}";
        $countStmt = $this->pdo->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $countStmt->execute();
        $total = (int)$countStmt->fetchColumn();

        return ['items' => $items, 'total' => $total];
    }

    public function findProductoById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT mp.id, mp.nombre, mp.id_subcategoria, ms.nombre AS subcategoria_nombre,
                ms.id_categoria, mc.nombre AS categoria_nombre,
                mp.imagen, mp.estado, mp.registro_occu, mp.id_alta, mp.fecha_alta
            FROM menu_productos mp
            LEFT JOIN menu_subcategorias ms ON mp.id_subcategoria = ms.id
            LEFT JOIN menu_categorias mc ON ms.id_categoria = mc.id
            WHERE mp.id = :id AND mp.estado != 2"
        );
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function createProducto(array $data, int $userId): int
    {
        $fechaAlta = date('Y-m-d H:i:s');

        // Validar que la subcategoría existe
        $stmtSub = $this->pdo->prepare(
            "SELECT id FROM menu_subcategorias WHERE id = :id AND estado != 2 LIMIT 1"
        );
        $stmtSub->execute([':id' => (int)$data['id_subcategoria']]);
        if (!$stmtSub->fetch()) {
            throw new \RuntimeException('La subcategoría especificada no existe');
        }

        // Validar nombre único dentro de la subcategoría
        $stmtCheck = $this->pdo->prepare(
            "SELECT id FROM menu_productos 
            WHERE nombre = :nombre AND id_subcategoria = :id_subcategoria AND estado != 2 LIMIT 1"
        );
        $stmtCheck->execute([
            ':nombre' => trim($data['nombre']),
            ':id_subcategoria' => (int)$data['id_subcategoria'],
        ]);
        if ($stmtCheck->fetch()) {
            throw new \RuntimeException('Ya existe un producto con ese nombre en esta subcategoría');
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO menu_productos (nombre, id_subcategoria, imagen, estado, registro_occu, id_alta, fecha_alta)
            VALUES (:nombre, :id_subcategoria, :imagen, :estado, 1, :id_alta, :fecha_alta)"
        );
        $stmt->execute([
            ':nombre' => trim($data['nombre']),
            ':id_subcategoria' => (int)$data['id_subcategoria'],
            ':imagen' => $data['imagen'] ?? '',
            ':estado' => $data['estado'] ?? 0,
            ':id_alta' => $userId,
            ':fecha_alta' => $fechaAlta,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function updateProducto(int $id, array $data, int $userId): bool
    {
        if (!$this->findProductoById($id)) {
            return false;
        }

        // Validar subcategoría si se cambia
        if (isset($data['id_subcategoria'])) {
            $stmtSub = $this->pdo->prepare(
                "SELECT id FROM menu_subcategorias WHERE id = :id AND estado != 2 LIMIT 1"
            );
            $stmtSub->execute([':id' => (int)$data['id_subcategoria']]);
            if (!$stmtSub->fetch()) {
                throw new \RuntimeException('La subcategoría especificada no existe');
            }
        }

        // Validar nombre único dentro de la subcategoría
        if (isset($data['nombre'])) {
            $current = $this->findProductoById($id);
            $idSubcategoria = $data['id_subcategoria'] ?? $current['id_subcategoria'];
            
            $stmtCheck = $this->pdo->prepare(
                "SELECT id FROM menu_productos 
                WHERE nombre = :nombre AND id_subcategoria = :id_subcategoria AND id != :id AND estado != 2 LIMIT 1"
            );
            $stmtCheck->execute([
                ':nombre' => trim($data['nombre']),
                ':id_subcategoria' => (int)$idSubcategoria,
                ':id' => $id,
            ]);
            if ($stmtCheck->fetch()) {
                throw new \RuntimeException('Ya existe otro producto con ese nombre en esta subcategoría');
            }
        }

        $fields = [];
        $params = [':id' => $id];

        if (isset($data['nombre'])) {
            $fields[] = 'nombre = :nombre';
            $params[':nombre'] = trim($data['nombre']);
        }
        if (isset($data['id_subcategoria'])) {
            $fields[] = 'id_subcategoria = :id_subcategoria';
            $params[':id_subcategoria'] = (int)$data['id_subcategoria'];
        }
        if (isset($data['imagen'])) {
            $fields[] = 'imagen = :imagen';
            $params[':imagen'] = $data['imagen'];
        }
        if (isset($data['estado'])) {
            $fields[] = 'estado = :estado';
            $params[':estado'] = (int)$data['estado'];
        }

        if (empty($fields)) {
            return true;
        }

        $sql = "UPDATE menu_productos SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteProducto(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE menu_productos SET estado = 2 WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }

    // ==========================================================
    // TAMAÑOS
    // ==========================================================

    /**
     * Lista tamaños con paginación y búsqueda
     */
    public function findTamanos(array $filters): array
    {
        $page = $filters['page'];
        $pageSize = $filters['pageSize'];
        $offset = ($page - 1) * $pageSize;

        $busqueda = isset($filters['q']) && $filters['q'] !== '' 
            ? '%' . $filters['q'] . '%' 
            : null;

        $where = ['estado != 2'];
        $params = [];

        if ($busqueda) {
            $where[] = '(nombre LIKE :busqueda OR unidad_medida LIKE :busqueda OR medida LIKE :busqueda)';
            $params[':busqueda'] = $busqueda;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT id, nombre, unidad_medida, medida, estado
            FROM menu_productos_tamanos
            WHERE {$whereClause}
            ORDER BY CAST(medida AS UNSIGNED) ASC, nombre ASC
            LIMIT :offset, :limit";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        $countSql = "SELECT COUNT(*) as total FROM menu_productos_tamanos WHERE {$whereClause}";
        $countStmt = $this->pdo->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $countStmt->execute();
        $total = (int)$countStmt->fetchColumn();

        return ['items' => $items, 'total' => $total];
    }

    public function findTamanoById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, nombre, unidad_medida, medida, estado
            FROM menu_productos_tamanos
            WHERE id = :id AND estado != 2"
        );
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function createTamano(array $data, int $userId): int
    {
        // Validar nombre único
        $stmtCheck = $this->pdo->prepare(
            "SELECT id FROM menu_productos_tamanos WHERE nombre = :nombre AND estado != 2 LIMIT 1"
        );
        $stmtCheck->execute([':nombre' => trim($data['nombre'])]);
        if ($stmtCheck->fetch()) {
            throw new \RuntimeException('Ya existe un tamaño con ese nombre');
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO menu_productos_tamanos (nombre, unidad_medida, medida, estado)
            VALUES (:nombre, :unidad_medida, :medida, :estado)"
        );
        $stmt->execute([
            ':nombre' => trim($data['nombre']),
            ':unidad_medida' => trim($data['unidad_medida']),
            ':medida' => trim($data['medida']),
            ':estado' => $data['estado'] ?? 0,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function updateTamano(int $id, array $data, int $userId): bool
    {
        if (!$this->findTamanoById($id)) {
            return false;
        }

        // Validar nombre único
        if (isset($data['nombre'])) {
            $stmtCheck = $this->pdo->prepare(
                "SELECT id FROM menu_productos_tamanos WHERE nombre = :nombre AND id != :id AND estado != 2 LIMIT 1"
            );
            $stmtCheck->execute([
                ':nombre' => trim($data['nombre']),
                ':id' => $id,
            ]);
            if ($stmtCheck->fetch()) {
                throw new \RuntimeException('Ya existe otro tamaño con ese nombre');
            }
        }

        $fields = [];
        $params = [':id' => $id];

        if (isset($data['nombre'])) {
            $fields[] = 'nombre = :nombre';
            $params[':nombre'] = trim($data['nombre']);
        }
        if (isset($data['unidad_medida'])) {
            $fields[] = 'unidad_medida = :unidad_medida';
            $params[':unidad_medida'] = trim($data['unidad_medida']);
        }
        if (isset($data['medida'])) {
            $fields[] = 'medida = :medida';
            $params[':medida'] = trim($data['medida']);
        }
        if (isset($data['estado'])) {
            $fields[] = 'estado = :estado';
            $params[':estado'] = (int)$data['estado'];
        }

        if (empty($fields)) {
            return true;
        }

        $sql = "UPDATE menu_productos_tamanos SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteTamano(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE menu_productos_tamanos SET estado = 2 WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }

    // ==========================================================
    // CATEGORÍAS DE INGREDIENTES
    // ==========================================================

    /**
     * Lista categorías de ingredientes con paginación y búsqueda
     */
    public function findIngredientesCategorias(array $filters): array
    {
        $page = $filters['page'];
        $pageSize = $filters['pageSize'];
        $offset = ($page - 1) * $pageSize;

        $busqueda = isset($filters['q']) && $filters['q'] !== '' 
            ? '%' . $filters['q'] . '%' 
            : null;

        $where = ['estado != 2'];
        $params = [];

        if ($busqueda) {
            $where[] = 'nombre LIKE :busqueda';
            $params[':busqueda'] = $busqueda;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT id, nombre, para_bebidas, para_alimentos, estado, id_alta, fecha_alta
            FROM menu_ingredientes_categorias
            WHERE {$whereClause}
            ORDER BY nombre ASC
            LIMIT :offset, :limit";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        $countSql = "SELECT COUNT(*) as total FROM menu_ingredientes_categorias WHERE {$whereClause}";
        $countStmt = $this->pdo->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $countStmt->execute();
        $total = (int)$countStmt->fetchColumn();

        return ['items' => $items, 'total' => $total];
    }

    public function findIngredienteCategoriaById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, nombre, para_bebidas, para_alimentos, estado, id_alta, fecha_alta
            FROM menu_ingredientes_categorias
            WHERE id = :id AND estado != 2"
        );
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function createIngredienteCategoria(array $data, int $userId): int
    {
        $fechaAlta = date('Y-m-d H:i:s');

        // Validar nombre único
        $stmtCheck = $this->pdo->prepare(
            "SELECT id FROM menu_ingredientes_categorias WHERE nombre = :nombre AND estado != 2 LIMIT 1"
        );
        $stmtCheck->execute([':nombre' => trim($data['nombre'])]);
        if ($stmtCheck->fetch()) {
            throw new \RuntimeException('Ya existe una categoría de ingredientes con ese nombre');
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO menu_ingredientes_categorias (nombre, para_bebidas, para_alimentos, estado, id_alta, fecha_alta)
            VALUES (:nombre, :para_bebidas, :para_alimentos, :estado, :id_alta, :fecha_alta)"
        );
        $stmt->execute([
            ':nombre' => trim($data['nombre']),
            ':para_bebidas' => $data['para_bebidas'] ?? '',
            ':para_alimentos' => $data['para_alimentos'] ?? '',
            ':estado' => $data['estado'] ?? 0,
            ':id_alta' => $userId,
            ':fecha_alta' => $fechaAlta,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function updateIngredienteCategoria(int $id, array $data, int $userId): bool
    {
        if (!$this->findIngredienteCategoriaById($id)) {
            return false;
        }

        // Validar nombre único
        if (isset($data['nombre'])) {
            $stmtCheck = $this->pdo->prepare(
                "SELECT id FROM menu_ingredientes_categorias WHERE nombre = :nombre AND id != :id AND estado != 2 LIMIT 1"
            );
            $stmtCheck->execute([
                ':nombre' => trim($data['nombre']),
                ':id' => $id,
            ]);
            if ($stmtCheck->fetch()) {
                throw new \RuntimeException('Ya existe otra categoría de ingredientes con ese nombre');
            }
        }

        $fields = [];
        $params = [':id' => $id];

        if (isset($data['nombre'])) {
            $fields[] = 'nombre = :nombre';
            $params[':nombre'] = trim($data['nombre']);
        }
        if (isset($data['para_bebidas'])) {
            $fields[] = 'para_bebidas = :para_bebidas';
            $params[':para_bebidas'] = $data['para_bebidas'];
        }
        if (isset($data['para_alimentos'])) {
            $fields[] = 'para_alimentos = :para_alimentos';
            $params[':para_alimentos'] = $data['para_alimentos'];
        }
        if (isset($data['estado'])) {
            $fields[] = 'estado = :estado';
            $params[':estado'] = (int)$data['estado'];
        }

        if (empty($fields)) {
            return true;
        }

        $sql = "UPDATE menu_ingredientes_categorias SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteIngredienteCategoria(int $id): bool
    {
        // Verificar que no tenga ingredientes activos
        $stmtCheck = $this->pdo->prepare(
            "SELECT COUNT(*) FROM menu_ingredientes WHERE id_ingrediente_categoria = :id AND estado != 2"
        );
        $stmtCheck->execute([':id' => $id]);
        if ($stmtCheck->fetchColumn() > 0) {
            throw new \RuntimeException('No se puede eliminar la categoría porque tiene ingredientes asociados');
        }

        $stmt = $this->pdo->prepare(
            "UPDATE menu_ingredientes_categorias SET estado = 2 WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }

    // ==========================================================
    // INGREDIENTES
    // ==========================================================

    /**
     * Lista ingredientes con paginación y búsqueda
     */
    public function findIngredientes(array $filters): array
    {
        $page = $filters['page'];
        $pageSize = $filters['pageSize'];
        $offset = ($page - 1) * $pageSize;

        $busqueda = isset($filters['q']) && $filters['q'] !== '' 
            ? '%' . $filters['q'] . '%' 
            : null;
        $idCategoria = isset($filters['id_ingrediente_categoria']) ? (int)$filters['id_ingrediente_categoria'] : null;

        $where = ['mi.estado != 2'];
        $params = [];

        if ($busqueda) {
            $where[] = 'mi.nombre LIKE :busqueda';
            $params[':busqueda'] = $busqueda;
        }
        if ($idCategoria !== null) {
            $where[] = 'mi.id_ingrediente_categoria = :id_categoria';
            $params[':id_categoria'] = $idCategoria;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT mi.id, mi.nombre, mi.id_ingrediente_categoria, mic.nombre AS categoria_nombre,
                mi.registro_occu, mi.estado, mi.id_alta, mi.fecha_alta
            FROM menu_ingredientes mi
            LEFT JOIN menu_ingredientes_categorias mic ON mi.id_ingrediente_categoria = mic.id
            WHERE {$whereClause}
            ORDER BY mic.nombre ASC, mi.nombre ASC
            LIMIT :offset, :limit";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        $countSql = "SELECT COUNT(*) as total 
            FROM menu_ingredientes mi 
            WHERE {$whereClause}";
        $countStmt = $this->pdo->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $countStmt->execute();
        $total = (int)$countStmt->fetchColumn();

        return ['items' => $items, 'total' => $total];
    }

    public function findIngredienteById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT mi.id, mi.nombre, mi.id_ingrediente_categoria, mic.nombre AS categoria_nombre,
                mi.registro_occu, mi.estado, mi.id_alta, mi.fecha_alta
            FROM menu_ingredientes mi
            LEFT JOIN menu_ingredientes_categorias mic ON mi.id_ingrediente_categoria = mic.id
            WHERE mi.id = :id AND mi.estado != 2"
        );
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function createIngrediente(array $data, int $userId): int
    {
        $fechaAlta = date('Y-m-d H:i:s');

        // Validar que la categoría existe
        $stmtCat = $this->pdo->prepare(
            "SELECT id FROM menu_ingredientes_categorias WHERE id = :id AND estado != 2 LIMIT 1"
        );
        $stmtCat->execute([':id' => (int)$data['id_ingrediente_categoria']]);
        if (!$stmtCat->fetch()) {
            throw new \RuntimeException('La categoría de ingredientes especificada no existe');
        }

        // Validar nombre único dentro de la categoría
        $stmtCheck = $this->pdo->prepare(
            "SELECT id FROM menu_ingredientes 
            WHERE nombre = :nombre AND id_ingrediente_categoria = :id_categoria AND estado != 2 LIMIT 1"
        );
        $stmtCheck->execute([
            ':nombre' => trim($data['nombre']),
            ':id_categoria' => (int)$data['id_ingrediente_categoria'],
        ]);
        if ($stmtCheck->fetch()) {
            throw new \RuntimeException('Ya existe un ingrediente con ese nombre en esta categoría');
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO menu_ingredientes (nombre, id_ingrediente_categoria, registro_occu, estado, id_alta, fecha_alta)
            VALUES (:nombre, :id_ingrediente_categoria, 1, :estado, :id_alta, :fecha_alta)"
        );
        $stmt->execute([
            ':nombre' => trim($data['nombre']),
            ':id_ingrediente_categoria' => (int)$data['id_ingrediente_categoria'],
            ':estado' => $data['estado'] ?? 0,
            ':id_alta' => $userId,
            ':fecha_alta' => $fechaAlta,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function updateIngrediente(int $id, array $data, int $userId): bool
    {
        if (!$this->findIngredienteById($id)) {
            return false;
        }

        // Validar categoría si se cambia
        if (isset($data['id_ingrediente_categoria'])) {
            $stmtCat = $this->pdo->prepare(
                "SELECT id FROM menu_ingredientes_categorias WHERE id = :id AND estado != 2 LIMIT 1"
            );
            $stmtCat->execute([':id' => (int)$data['id_ingrediente_categoria']]);
            if (!$stmtCat->fetch()) {
                throw new \RuntimeException('La categoría de ingredientes especificada no existe');
            }
        }

        // Validar nombre único dentro de la categoría
        if (isset($data['nombre'])) {
            $current = $this->findIngredienteById($id);
            $idCategoria = $data['id_ingrediente_categoria'] ?? $current['id_ingrediente_categoria'];
            
            $stmtCheck = $this->pdo->prepare(
                "SELECT id FROM menu_ingredientes 
                WHERE nombre = :nombre AND id_ingrediente_categoria = :id_categoria AND id != :id AND estado != 2 LIMIT 1"
            );
            $stmtCheck->execute([
                ':nombre' => trim($data['nombre']),
                ':id_categoria' => (int)$idCategoria,
                ':id' => $id,
            ]);
            if ($stmtCheck->fetch()) {
                throw new \RuntimeException('Ya existe otro ingrediente con ese nombre en esta categoría');
            }
        }

        $fields = [];
        $params = [':id' => $id];

        if (isset($data['nombre'])) {
            $fields[] = 'nombre = :nombre';
            $params[':nombre'] = trim($data['nombre']);
        }
        if (isset($data['id_ingrediente_categoria'])) {
            $fields[] = 'id_ingrediente_categoria = :id_ingrediente_categoria';
            $params[':id_ingrediente_categoria'] = (int)$data['id_ingrediente_categoria'];
        }
        if (isset($data['estado'])) {
            $fields[] = 'estado = :estado';
            $params[':estado'] = (int)$data['estado'];
        }

        if (empty($fields)) {
            return true;
        }

        $sql = "UPDATE menu_ingredientes SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteIngrediente(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE menu_ingredientes SET estado = 2 WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }

    // ==========================================================
    // MÉTODOS AUXILIARES PARA LISTAS SIMPLES (sin paginación)
    // ==========================================================

    /**
     * Obtiene todas las categorías activas (para dropdowns)
     */
    public function getAllCategorias(): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, nombre FROM menu_categorias WHERE estado = 0 ORDER BY nombre ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtiene todas las subcategorías activas (para dropdowns)
     */
    public function getAllSubcategorias(?int $idCategoria = null): array
    {
        $sql = "SELECT ms.id, ms.nombre, ms.id_categoria, mc.nombre AS categoria_nombre
            FROM menu_subcategorias ms
            LEFT JOIN menu_categorias mc ON ms.id_categoria = mc.id
            WHERE ms.estado = 0";
        
        $params = [];
        if ($idCategoria !== null) {
            $sql .= " AND ms.id_categoria = :id_categoria";
            $params[':id_categoria'] = $idCategoria;
        }
        
        $sql .= " ORDER BY mc.nombre ASC, ms.nombre ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene todas las categorías de ingredientes activas (para dropdowns)
     */
    public function getAllIngredientesCategorias(): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, nombre FROM menu_ingredientes_categorias WHERE estado = 0 ORDER BY nombre ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
