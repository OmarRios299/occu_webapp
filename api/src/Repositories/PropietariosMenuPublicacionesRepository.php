<?php

declare(strict_types=1);

namespace Occu\Api\Repositories;

use Occu\Api\Infrastructure\Db;
use PDO;

final class PropietariosMenuPublicacionesRepository
{
    private PDO $pdo;

    public function __construct(Db $db)
    {
        $this->pdo = $db->pdo();
    }

    /**
     * Registra una publicación de menú
     * 
     * @param int $propietarioId
     * @param string $tipo 'todo' | 'solo_precios'
     * @param array<int> $cafeteriaIds
     * @param int $productosPublicados
     * @return int ID de la publicación registrada
     */
    public function registrarPublicacion(
        int $propietarioId,
        string $tipo,
        array $cafeteriaIds,
        int $productosPublicados
    ): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO propietarios_menu_publicaciones 
            (id_propietario, tipo, cafeterias_afectadas, productos_publicados, fecha_publicacion)
            VALUES (:propietario_id, :tipo, :cafeterias, :productos, :fecha)"
        );
        $stmt->execute([
            ':propietario_id' => $propietarioId,
            ':tipo' => $tipo,
            ':cafeterias' => json_encode($cafeteriaIds),
            ':productos' => $productosPublicados,
            ':fecha' => date('Y-m-d H:i:s'),
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Obtiene la fecha de última publicación para una cafetería
     * 
     * @param int $cafeteriaId
     * @return string|null Fecha de última publicación o null si nunca se publicó
     */
    public function getUltimaPublicacion(int $cafeteriaId): ?string
    {
        $stmt = $this->pdo->prepare(
            "SELECT MAX(fecha_publicacion) as ultima_fecha
            FROM propietarios_menu_publicaciones
            WHERE JSON_CONTAINS(cafeterias_afectadas, :cafeteria_id_json)"
        );
        $stmt->execute([':cafeteria_id_json' => json_encode($cafeteriaId)]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && $result['ultima_fecha'] ? $result['ultima_fecha'] : null;
    }

    /**
     * Obtiene el estado de actualización para múltiples cafeterías
     * 
     * @param array<int> $cafeteriaIds
     * @return array<int, array{fecha_ultima_publicacion: string|null, desactualizado: bool}>
     */
    public function getEstadoActualizacion(array $cafeteriaIds): array
    {
        if (empty($cafeteriaIds)) {
            return [];
        }

        $placeholders = [];
        $params = [];
        foreach ($cafeteriaIds as $idx => $cafId) {
            $key = ':caf' . $idx;
            $placeholders[] = $key;
            $params[$key] = json_encode($cafId);
        }

        // Obtener todas las publicaciones que afectan a estas cafeterías
        $stmt = $this->pdo->prepare(
            "SELECT fecha_publicacion, cafeterias_afectadas
            FROM propietarios_menu_publicaciones
            ORDER BY fecha_publicacion DESC"
        );
        $stmt->execute();
        $publicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Procesar resultados para cada cafetería
        $resultado = [];
        foreach ($cafeteriaIds as $cafId) {
            $ultimaFecha = null;
            foreach ($publicaciones as $pub) {
                $ids = json_decode($pub['cafeterias_afectadas'], true);
                if (is_array($ids) && in_array($cafId, $ids)) {
                    if (!$ultimaFecha || $pub['fecha_publicacion'] > $ultimaFecha) {
                        $ultimaFecha = $pub['fecha_publicacion'];
                    }
                }
            }
            $resultado[$cafId] = [
                'fecha_ultima_publicacion' => $ultimaFecha,
                'desactualizado' => $ultimaFecha === null,
            ];
        }

        return $resultado;
    }
}
