<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class VnextMinimalSeed extends AbstractSeed
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        // Para que sea 100% reproducible (IDs fijos)
        $this->execute('SET FOREIGN_KEY_CHECKS=0;');

        // Truncar en orden inverso de dependencias
        $tablesToTruncate = [
            'ventas_items_personalizaciones',
            'ventas_items',
            'ventas',
            'ventas_carrito_items_personalizaciones',
            'ventas_carrito_items',
            'ventas_carrito',
            'propietarios_menu_publicaciones',
            'propietarios_menu_productos_reglas_ingredientes',
            'propietarios_menu_productos_reglas_ingredientes_categorias',
            'propietarios_menu_productos_tamanos',
            'propietarios_menu_productos',
            'cafeterias_menu_productos_reglas_ingredientes',
            'cafeterias_menu_productos_reglas_ingredientes_categorias',
            'cafeterias_menu_productos_tamanos',
            'cafeterias_menu_productos',
            'menu_productos_reglas_ingredientes',
            'menu_productos_reglas_ingredientes_categorias',
            'menu_ingredientes',
            'menu_ingredientes_categorias',
            'menu_productos_tamanos',
            'menu_productos',
            'menu_subcategorias',
            'menu_categorias',
            'cafeterias_comentarios',
            'cafeterias_imagenes',
            'cafeterias_servicios',
            'cafeteria_horarios',
            'cafeterias',
            'servicios',
            'api_refresh_tokens',
            'admin_usuarios',
            'ciudades',
            'entidades_federativas',
            'paises',
        ];

        foreach ($tablesToTruncate as $table) {
            $this->execute(sprintf('TRUNCATE TABLE `%s`;', $table));
        }

        // =========================
        // Geografía
        // =========================
        $this->table('paises')->insert([
            ['id' => 1, 'nombre' => 'México', 'estado' => 0],
        ])->saveData();

        $this->table('entidades_federativas')->insert([
            ['id' => 1, 'id_pais' => 1, 'nombre' => 'Baja California', 'estado' => 0],
        ])->saveData();

        $this->table('ciudades')->insert([
            [
                'id' => 1,
                'id_entidad_federativa' => 1,
                'nombre' => 'Tijuana',
                'coordenadas' => '32.5149,-117.0382',
                'zona_horaria' => 'America/Tijuana',
                'estado' => 0,
            ],
        ])->saveData();

        // =========================
        // Usuarios (Auth)
        // Credenciales de demo:
        // - admin@occu.test / Admin123!
        // - owner@occu.test / Owner123!
        // - cliente@occu.test / Cliente123!
        // =========================
        // IMPORTANTÍSIMO: la API actual valida con `crypt()` + salt legacy (ver AuthService::LEGACY_CRYPT_SALT).
        // Si usamos `password_hash()` aquí, el login fallará.
        $legacySalt = '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$';
        $adminHash = crypt('Admin123!', $legacySalt);
        $ownerHash = crypt('Owner123!', $legacySalt);
        $clienteHash = crypt('Cliente123!', $legacySalt);

        $this->table('admin_usuarios')->insert([
            [
                'id' => 1,
                'nombre' => 'Admin',
                'apellido' => 'OCCU',
                'id_ciudad' => 1,
                'correo_electronico' => 'admin@occu.test',
                'contrasena' => $adminHash,
                'nivel' => 'Administrador',
                'imagen' => null,
                'pin' => null,
                'proveedor' => 'Interno',
                'id_usuario_proveedor' => null,
                'verificado' => 'Si',
                'estado' => 0,
                'id_alta' => 1,
                'fecha_alta' => $now,
            ],
            [
                'id' => 2,
                'nombre' => 'Propietario',
                'apellido' => 'Demo',
                'id_ciudad' => 1,
                'correo_electronico' => 'owner@occu.test',
                'contrasena' => $ownerHash,
                'nivel' => 'Propietario',
                'imagen' => null,
                'pin' => null,
                'proveedor' => 'Interno',
                'id_usuario_proveedor' => null,
                'verificado' => 'Si',
                'estado' => 0,
                'id_alta' => 1,
                'fecha_alta' => $now,
            ],
            [
                'id' => 3,
                'nombre' => 'Cliente',
                'apellido' => 'Demo',
                'id_ciudad' => 1,
                'correo_electronico' => 'cliente@occu.test',
                'contrasena' => $clienteHash,
                'nivel' => 'Cliente',
                'imagen' => null,
                'pin' => null,
                'proveedor' => 'Interno',
                'id_usuario_proveedor' => null,
                'verificado' => 'Si',
                'estado' => 0,
                'id_alta' => 1,
                'fecha_alta' => $now,
            ],
        ])->saveData();

        // =========================
        // Servicios
        // =========================
        $this->table('servicios')->insert([
            ['id' => 1, 'nombre' => 'WiFi', 'imagen' => '', 'estado' => 0],
            ['id' => 2, 'nombre' => 'Pet friendly', 'imagen' => '', 'estado' => 0],
        ])->saveData();

        // =========================
        // Cafetería
        // =========================
        $this->table('cafeterias')->insert([
            [
                'id' => 1,
                'nombre' => 'Café Demo OCCU',
                'imagen' => '',
                'id_ciudad' => 1,
                'direccion' => 'Av. Demo 123, Centro',
                'telefono' => '6640000000',
                'correo_electronico' => 'cafe.demo@occu.app',
                'latitud' => '32.5149',
                'longitud' => '-117.0382',
                'horario_apertura' => '08:00:00',
                'horario_cierre' => '20:00:00',
                'horario_diferente' => 'NO',
                'descripcion' => 'Cafetería de prueba para OCCU vNext.',
                'id_usuario' => 2,
                'estado' => 0,
                'id_alta' => 2,
                'fecha_alta' => $now,
            ],
        ])->saveData();

        $this->table('cafeteria_horarios')->insert([
            ['id' => 1, 'id_cafeteria' => 1, 'dia' => 'Lunes', 'hora_apertura' => '08:00:00', 'hora_cierre' => '20:00:00', 'cerrado' => 'NO'],
            ['id' => 2, 'id_cafeteria' => 1, 'dia' => 'Martes', 'hora_apertura' => '08:00:00', 'hora_cierre' => '20:00:00', 'cerrado' => 'NO'],
            ['id' => 3, 'id_cafeteria' => 1, 'dia' => 'Miercoles', 'hora_apertura' => '08:00:00', 'hora_cierre' => '20:00:00', 'cerrado' => 'NO'],
            ['id' => 4, 'id_cafeteria' => 1, 'dia' => 'Jueves', 'hora_apertura' => '08:00:00', 'hora_cierre' => '20:00:00', 'cerrado' => 'NO'],
            ['id' => 5, 'id_cafeteria' => 1, 'dia' => 'Viernes', 'hora_apertura' => '08:00:00', 'hora_cierre' => '20:00:00', 'cerrado' => 'NO'],
            ['id' => 6, 'id_cafeteria' => 1, 'dia' => 'Sabado', 'hora_apertura' => '09:00:00', 'hora_cierre' => '18:00:00', 'cerrado' => 'NO'],
            ['id' => 7, 'id_cafeteria' => 1, 'dia' => 'Domingo', 'hora_apertura' => '09:00:00', 'hora_cierre' => '18:00:00', 'cerrado' => 'NO'],
        ])->saveData();

        $this->table('cafeterias_imagenes')->insert([
            ['id' => 1, 'id_cafeteria' => 1, 'imagen' => 'demo-1.jpg', 'descripcion' => 'Interior', 'estado' => 0],
        ])->saveData();

        $this->table('cafeterias_servicios')->insert([
            ['id' => 1, 'id_servicio' => 1, 'id_cafeteria' => 1, 'estado' => 0],
            ['id' => 2, 'id_servicio' => 2, 'id_cafeteria' => 1, 'estado' => 0],
        ])->saveData();

        $this->table('cafeterias_comentarios')->insert([
            [
                'id' => 1,
                'id_cafeteria' => 1,
                'id_usuario' => 3,
                'comentario' => 'Excelente café y buena atención.',
                'fecha_alta' => $now,
                'estado' => 0,
            ],
        ])->saveData();

        // =========================
        // Menú (Catálogos)
        // =========================
        $this->table('menu_categorias')->insert([
            [
                'id' => 1,
                'nombre' => 'Bebidas',
                'imagen' => '',
                'es_bebida' => 'Si',
                'estado' => 0,
                'id_alta' => 1,
                'fecha_alta' => $now,
            ],
        ])->saveData();

        $this->table('menu_subcategorias')->insert([
            [
                'id' => 1,
                'nombre' => 'Cafés calientes',
                'id_categoria' => 1,
                'registro_occu' => 1,
                'id_propietario' => 0,
                'estado' => 0,
                'fecha_alta' => $now,
            ],
        ])->saveData();

        $this->table('menu_productos')->insert([
            [
                'id' => 1,
                'nombre' => 'Latte',
                'id_subcategoria' => 1,
                'imagen' => '',
                'registro_occu' => 1,
                'id_alta' => 1,
                'fecha_alta' => $now,
                'estado' => 0,
            ],
        ])->saveData();

        $this->table('menu_productos_tamanos')->insert([
            ['id' => 1, 'nombre' => '12 oz', 'unidad_medida' => 'Oz', 'medida' => '12', 'estado' => 0],
            ['id' => 2, 'nombre' => '16 oz', 'unidad_medida' => 'Oz', 'medida' => '16', 'estado' => 0],
        ])->saveData();

        $this->table('menu_ingredientes_categorias')->insert([
            [
                'id' => 1,
                'nombre' => 'Tipo de leche',
                'para_bebidas' => 'Si',
                'para_alimentos' => 'No',
                'estado' => 0,
                'id_alta' => 1,
                'fecha_alta' => $now,
            ],
            [
                'id' => 2,
                'nombre' => 'Jarabes',
                'para_bebidas' => 'Si',
                'para_alimentos' => 'No',
                'estado' => 0,
                'id_alta' => 1,
                'fecha_alta' => $now,
            ],
        ])->saveData();

        $this->table('menu_ingredientes')->insert([
            [
                'id' => 1,
                'nombre' => 'Leche entera',
                'id_ingrediente_categoria' => 1,
                'registro_occu' => 1,
                'estado' => 0,
                'id_alta' => 1,
                'fecha_alta' => $now,
            ],
            [
                'id' => 2,
                'nombre' => 'Leche de avena',
                'id_ingrediente_categoria' => 1,
                'registro_occu' => 1,
                'estado' => 0,
                'id_alta' => 1,
                'fecha_alta' => $now,
            ],
            [
                'id' => 3,
                'nombre' => 'Vainilla',
                'id_ingrediente_categoria' => 2,
                'registro_occu' => 1,
                'estado' => 0,
                'id_alta' => 1,
                'fecha_alta' => $now,
            ],
        ])->saveData();

        // =========================
        // Plantillas OCCU por producto (reglas v2)
        // =========================
        $this->table('menu_productos_reglas_ingredientes_categorias')->insert([
            [
                'id' => 1,
                'id_producto' => 1,
                'id_ingrediente_categoria' => 1,
                'tipo_seleccion' => 'unica',
                'seleccion_minima' => 1,
                'seleccion_maxima' => 1,
                'orden' => 1,
                'estado' => 0,
                'id_alta' => 1,
                'fecha_alta' => $now,
            ],
            [
                'id' => 2,
                'id_producto' => 1,
                'id_ingrediente_categoria' => 2,
                'tipo_seleccion' => 'multiple',
                'seleccion_minima' => 0,
                'seleccion_maxima' => null,
                'orden' => 2,
                'estado' => 0,
                'id_alta' => 1,
                'fecha_alta' => $now,
            ],
        ])->saveData();

        $this->table('menu_productos_reglas_ingredientes')->insert([
            [
                'id' => 1,
                'id_producto' => 1,
                'id_ingrediente' => 1,
                'permite_cantidad' => 0,
                'cantidad_minima' => 0,
                'cantidad_maxima' => 1,
                'paso_cantidad' => 1,
                'cantidad_incluida' => 0,
                'tipo_precio' => 'fijo',
                'precio_unitario' => 0,
                'es_recomendado' => 1,
                'estado' => 0,
                'id_alta' => 1,
                'fecha_alta' => $now,
            ],
            [
                'id' => 2,
                'id_producto' => 1,
                'id_ingrediente' => 2,
                'permite_cantidad' => 0,
                'cantidad_minima' => 0,
                'cantidad_maxima' => 1,
                'paso_cantidad' => 1,
                'cantidad_incluida' => 0,
                'tipo_precio' => 'fijo',
                'precio_unitario' => 5.00,
                'es_recomendado' => 0,
                'estado' => 0,
                'id_alta' => 1,
                'fecha_alta' => $now,
            ],
            [
                'id' => 3,
                'id_producto' => 1,
                'id_ingrediente' => 3,
                'permite_cantidad' => 1,
                'cantidad_minima' => 0,
                'cantidad_maxima' => 3,
                'paso_cantidad' => 1,
                'cantidad_incluida' => 1,
                'tipo_precio' => 'por_porcion',
                'precio_unitario' => 3.00,
                'es_recomendado' => 0,
                'estado' => 0,
                'id_alta' => 1,
                'fecha_alta' => $now,
            ],
        ])->saveData();

        // =========================
        // Menú general del propietario (habilita el producto para publicar)
        // Nota: la API actual usa `estado=1` como “incluido/en menú”.
        // =========================
        $this->table('propietarios_menu_productos')->insert([
            [
                'id' => 1,
                'id_propietario' => 2,
                'id_producto' => 1,
                'precio_base' => 45.00,
                'habilitado' => 1,
                'estado' => 1,
                'id_alta' => 2,
                'fecha_alta' => $now,
            ],
        ])->saveData();

        $this->table('propietarios_menu_productos_tamanos')->insert([
            [
                'id' => 1,
                'id_propietario_menu_producto' => 1,
                'id_tamano' => 1,
                'precio' => 45.00,
                'habilitado' => 1,
                'estado' => 1,
            ],
            [
                'id' => 2,
                'id_propietario_menu_producto' => 1,
                'id_tamano' => 2,
                'precio' => 50.00,
                'habilitado' => 1,
                'estado' => 1,
            ],
        ])->saveData();

        $this->table('propietarios_menu_productos_reglas_ingredientes_categorias')->insert([
            [
                'id' => 1,
                'id_propietario_menu_producto' => 1,
                'id_ingrediente_categoria' => 1,
                'tipo_seleccion' => 'unica',
                'seleccion_minima' => 1,
                'seleccion_maxima' => 1,
                'orden' => 1,
                'estado' => 0,
                'id_alta' => 2,
                'fecha_alta' => $now,
            ],
            [
                'id' => 2,
                'id_propietario_menu_producto' => 1,
                'id_ingrediente_categoria' => 2,
                'tipo_seleccion' => 'multiple',
                'seleccion_minima' => 0,
                'seleccion_maxima' => null,
                'orden' => 2,
                'estado' => 0,
                'id_alta' => 2,
                'fecha_alta' => $now,
            ],
        ])->saveData();

        $this->table('propietarios_menu_productos_reglas_ingredientes')->insert([
            [
                'id' => 1,
                'id_propietario_menu_producto' => 1,
                'id_ingrediente' => 1,
                'permite_cantidad' => 0,
                'cantidad_minima' => 0,
                'cantidad_maxima' => 1,
                'paso_cantidad' => 1,
                'cantidad_incluida' => 0,
                'tipo_precio' => 'fijo',
                'precio_unitario' => 0,
                'es_recomendado' => 1,
                'estado' => 0,
                'id_alta' => 2,
                'fecha_alta' => $now,
            ],
            [
                'id' => 2,
                'id_propietario_menu_producto' => 1,
                'id_ingrediente' => 2,
                'permite_cantidad' => 0,
                'cantidad_minima' => 0,
                'cantidad_maxima' => 1,
                'paso_cantidad' => 1,
                'cantidad_incluida' => 0,
                'tipo_precio' => 'fijo',
                'precio_unitario' => 5.00,
                'es_recomendado' => 0,
                'estado' => 0,
                'id_alta' => 2,
                'fecha_alta' => $now,
            ],
            [
                'id' => 3,
                'id_propietario_menu_producto' => 1,
                'id_ingrediente' => 3,
                'permite_cantidad' => 1,
                'cantidad_minima' => 0,
                'cantidad_maxima' => 3,
                'paso_cantidad' => 1,
                'cantidad_incluida' => 1,
                'tipo_precio' => 'por_porcion',
                'precio_unitario' => 3.00,
                'es_recomendado' => 0,
                'estado' => 0,
                'id_alta' => 2,
                'fecha_alta' => $now,
            ],
        ])->saveData();

        // =========================
        // Publicación real a la cafetería (menú que ve el cliente)
        // =========================
        $this->table('cafeterias_menu_productos')->insert([
            [
                'id' => 1,
                'id_cafeteria' => 1,
                'id_producto' => 1,
                'precio_base' => 45.00,
                'estado' => 0,
                'id_alta' => 2,
                'fecha_alta' => $now,
            ],
        ])->saveData();

        $this->table('cafeterias_menu_productos_tamanos')->insert([
            ['id' => 1, 'id_cafeteria_menu_producto' => 1, 'id_tamano' => 1, 'precio' => 45.00, 'estado' => 0],
            ['id' => 2, 'id_cafeteria_menu_producto' => 1, 'id_tamano' => 2, 'precio' => 50.00, 'estado' => 0],
        ])->saveData();

        // Reglas por cafetería (copiadas desde la plantilla, pero con pricing por cafetería)
        $this->table('cafeterias_menu_productos_reglas_ingredientes_categorias')->insert([
            [
                'id' => 1,
                'id_cafeteria_menu_producto' => 1,
                'id_ingrediente_categoria' => 1,
                'tipo_seleccion' => 'unica',
                'seleccion_minima' => 1,
                'seleccion_maxima' => 1,
                'orden' => 1,
                'estado' => 0,
            ],
            [
                'id' => 2,
                'id_cafeteria_menu_producto' => 1,
                'id_ingrediente_categoria' => 2,
                'tipo_seleccion' => 'multiple',
                'seleccion_minima' => 0,
                'seleccion_maxima' => null,
                'orden' => 2,
                'estado' => 0,
            ],
        ])->saveData();

        $this->table('cafeterias_menu_productos_reglas_ingredientes')->insert([
            [
                'id' => 1,
                'id_cafeteria_menu_producto' => 1,
                'id_ingrediente' => 1,
                'agotado' => 0,
                'permite_cantidad' => 0,
                'cantidad_minima' => 0,
                'cantidad_maxima' => 1,
                'paso_cantidad' => 1,
                'cantidad_incluida' => 0,
                'tipo_precio' => 'fijo',
                'precio_unitario' => 0,
                'es_recomendado' => 1,
                'estado' => 0,
            ],
            [
                'id' => 2,
                'id_cafeteria_menu_producto' => 1,
                'id_ingrediente' => 2,
                'agotado' => 0,
                'permite_cantidad' => 0,
                'cantidad_minima' => 0,
                'cantidad_maxima' => 1,
                'paso_cantidad' => 1,
                'cantidad_incluida' => 0,
                'tipo_precio' => 'fijo',
                'precio_unitario' => 5.00,
                'es_recomendado' => 0,
                'estado' => 0,
            ],
            [
                'id' => 3,
                'id_cafeteria_menu_producto' => 1,
                'id_ingrediente' => 3,
                'agotado' => 0,
                'permite_cantidad' => 1,
                'cantidad_minima' => 0,
                'cantidad_maxima' => 3,
                'paso_cantidad' => 1,
                'cantidad_incluida' => 1,
                'tipo_precio' => 'por_porcion',
                'precio_unitario' => 3.00,
                'es_recomendado' => 0,
                'estado' => 0,
            ],
        ])->saveData();

        // Auditoría mínima de publicación (opcional, pero útil para el panel)
        $this->table('propietarios_menu_publicaciones')->insert([
            [
                'id' => 1,
                'id_propietario' => 2,
                'tipo' => 'todo',
                'cafeterias_afectadas' => json_encode([1], JSON_THROW_ON_ERROR),
                'productos_publicados' => 1,
                'fecha_publicacion' => $now,
            ],
        ])->saveData();

        $this->execute('SET FOREIGN_KEY_CHECKS=1;');
    }
}

