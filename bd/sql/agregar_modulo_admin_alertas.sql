-- Script para agregar el módulo de Gestión de Alertas al sistema
-- Ejecutar este script en la base de datos

-- 1. Insertar el módulo en permisos_modulos
-- id_area = 1 es "Administración" (según los módulos existentes)
INSERT INTO `permisos_modulos` (`id_area`, `id_subarea`, `nombre`, `ruta`, `icono`, `estado`) 
VALUES (1, 0, 'Gestión de Alertas', 'admin_alertas', 'fas fa-bell', 0);

-- 2. Obtener el ID del módulo recién insertado (ajustar según el último ID)
-- Asumiendo que el último ID es 19, el nuevo será 20
SET @id_modulo_alertas = LAST_INSERT_ID();

-- 3. Asignar el módulo al nivel Administrador (id_nivel = 1)
INSERT INTO `admin_niveles_usuario_modulos` (`id_nivel`, `id_modulo`) 
VALUES (1, @id_modulo_alertas);

-- Si el LAST_INSERT_ID() no funciona, usar el ID manualmente:
-- INSERT INTO `admin_niveles_usuario_modulos` (`id_nivel`, `id_modulo`) 
-- VALUES (1, 20);

-- Verificar que se insertó correctamente
SELECT * FROM permisos_modulos WHERE ruta = 'admin_alertas';
SELECT * FROM admin_niveles_usuario_modulos WHERE id_modulo = @id_modulo_alertas;

