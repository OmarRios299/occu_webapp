<?php

declare(strict_types=1);

namespace Occu\Api\Repositories;

use Occu\Api\Infrastructure\Db;
use PDO;

final class UserRepository
{
    private PDO $pdo;

    public function __construct(Db $db)
    {
        $this->pdo = $db->pdo();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM admin_usuarios WHERE correo_electronico = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM admin_usuarios WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByProvider(string $provider, string $providerUserId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM admin_usuarios WHERE proveedor = :p AND id_usuario_proveedor = :pid AND estado = 0 LIMIT 1');
        $stmt->execute([':p' => $provider, ':pid' => $providerUserId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM admin_usuarios WHERE correo_electronico = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        return (bool)$stmt->fetchColumn();
    }

    public function createInternalUser(array $data): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO admin_usuarios
            (nombre, apellido, id_ciudad, correo_electronico, contrasena, nivel, imagen, id_alta, fecha_alta, pin, proveedor, verificado, estado)
            VALUES
            (:nombre, :apellido, :id_ciudad, :correo, :contrasena, :nivel, :imagen, :id_alta, :fecha_alta, :pin, 'Interno', 'No', 0)"
        );

        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':apellido' => $data['apellido'],
            ':id_ciudad' => $data['id_ciudad'],
            ':correo' => $data['correo'],
            ':contrasena' => $data['contrasena'],
            ':nivel' => $data['nivel'],
            ':imagen' => $data['imagen'],
            ':id_alta' => $data['id_alta'],
            ':fecha_alta' => $data['fecha_alta'],
            ':pin' => $data['pin'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function createGoogleUser(array $data): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO admin_usuarios
            (nombre, apellido, id_ciudad, correo_electronico, nivel, imagen, proveedor, id_usuario_proveedor, verificado, estado, id_alta, fecha_alta)
            VALUES
            (:nombre, :apellido, :id_ciudad, :correo, :nivel, :imagen, 'google', :id_usuario_proveedor, 'No', 0, :id_alta, :fecha_alta)"
        );

        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':apellido' => $data['apellido'],
            ':id_ciudad' => $data['id_ciudad'],
            ':correo' => $data['correo'],
            ':nivel' => $data['nivel'],
            ':imagen' => $data['imagen'],
            ':id_usuario_proveedor' => $data['id_usuario_proveedor'],
            ':id_alta' => $data['id_alta'],
            ':fecha_alta' => $data['fecha_alta'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function markVerified(int $id): void
    {
        $stmt = $this->pdo->prepare("UPDATE admin_usuarios SET verificado = 'Si' WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    public function updatePin(string $email, string $pin): void
    {
        $stmt = $this->pdo->prepare('UPDATE admin_usuarios SET pin = :pin WHERE correo_electronico = :email');
        $stmt->execute([':pin' => $pin, ':email' => $email]);
    }

    public function completeGoogleInfo(int $id, int $idCiudad, string $nivel): void
    {
        $stmt = $this->pdo->prepare("UPDATE admin_usuarios SET id_ciudad = :id_ciudad, nivel = :nivel, verificado = 'Si' WHERE id = :id");
        $stmt->execute([':id' => $id, ':id_ciudad' => $idCiudad, ':nivel' => $nivel]);
    }
}

