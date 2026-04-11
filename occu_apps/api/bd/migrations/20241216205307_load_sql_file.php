<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class LoadSqlFile extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $sqlFile = __DIR__ . '/../initial_vnext.sql'; // Ajusta la ruta según tu estructura

        // Leer el contenido del archivo
        $sql = file_get_contents($sqlFile);

        if ($sql === false) {
            throw new \RuntimeException("No se pudo leer el archivo: $sqlFile");
        }

        // Ejecutar el SQL
        $this->execute($sql);
    }

    public function down(): void
    {
        // Opcional: revertir los cambios, por ejemplo, eliminando tablas
        $this->execute('DROP TABLE IF EXISTS users');
    }
}
