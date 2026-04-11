-- OCCU vNext - Initial SQL (baseline)
-- Crea desde 0 las tablas mínimas requeridas por la API actual:
-- Auth, Cafeterías (público + owner), Servicios, Menú (catálogos + publicación),
-- Reglas v2 (plantillas + cafetería), Carrito + snapshots, y auditoría de publicaciones.
--
-- Charset/Collation: utf8mb4 / utf8mb4_general_ci
-- Engine: InnoDB

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET FOREIGN_KEY_CHECKS = 0;

-- ==========================================================
-- Geografía (paises -> entidades_federativas -> ciudades)
-- ==========================================================
CREATE TABLE IF NOT EXISTS paises (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(120) NOT NULL,
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,1=inactivo,2=eliminado',
  PRIMARY KEY (id),
  UNIQUE KEY uniq_paises_nombre (nombre),
  KEY idx_paises_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS entidades_federativas (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_pais INT UNSIGNED NOT NULL,
  nombre VARCHAR(120) NOT NULL,
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,1=inactivo,2=eliminado',
  PRIMARY KEY (id),
  UNIQUE KEY uniq_entidad_por_pais (id_pais, nombre),
  KEY idx_entidades_pais (id_pais),
  KEY idx_entidades_estado (estado),
  CONSTRAINT fk_entidades_pais FOREIGN KEY (id_pais) REFERENCES paises(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS ciudades (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_entidad_federativa INT UNSIGNED NOT NULL,
  nombre VARCHAR(120) NOT NULL,
  coordenadas VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'legacy: string (lat,long o polígono)',
  zona_horaria VARCHAR(50) NULL DEFAULT NULL,
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,1=inactivo,2=eliminado',
  PRIMARY KEY (id),
  UNIQUE KEY uniq_ciudad_por_entidad (id_entidad_federativa, nombre),
  KEY idx_ciudades_entidad (id_entidad_federativa),
  KEY idx_ciudades_estado (estado),
  CONSTRAINT fk_ciudades_entidad FOREIGN KEY (id_entidad_federativa) REFERENCES entidades_federativas(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ==========================================================
-- Usuarios (Auth)
-- ==========================================================
CREATE TABLE IF NOT EXISTS admin_usuarios (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(120) NOT NULL,
  apellido VARCHAR(120) NOT NULL,
  id_ciudad INT UNSIGNED NULL DEFAULT NULL,
  correo_electronico VARCHAR(190) NOT NULL,
  contrasena VARCHAR(255) NULL DEFAULT NULL,
  nivel VARCHAR(30) NOT NULL COMMENT 'Cliente|Propietario|Administrador',
  imagen TEXT NULL DEFAULT NULL,
  pin VARCHAR(10) NULL DEFAULT NULL,
  proveedor VARCHAR(20) NULL DEFAULT NULL COMMENT 'Interno|google|...',
  id_usuario_proveedor TEXT NULL DEFAULT NULL,
  verificado VARCHAR(2) NOT NULL DEFAULT 'No' COMMENT 'Si|No',
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,1=inactivo,2=eliminado',
  id_alta INT UNSIGNED NOT NULL DEFAULT 1,
  fecha_alta DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_admin_usuarios_email (correo_electronico),
  KEY idx_admin_usuarios_ciudad (id_ciudad),
  KEY idx_admin_usuarios_nivel (nivel),
  KEY idx_admin_usuarios_estado (estado),
  KEY idx_admin_usuarios_proveedor (proveedor(12)),
  CONSTRAINT fk_admin_usuarios_ciudad FOREIGN KEY (id_ciudad) REFERENCES ciudades(id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Índice compuesto para búsquedas por proveedor (el texto no se puede indexar completo; usamos prefijo)
CREATE INDEX idx_admin_usuarios_provider_lookup
  ON admin_usuarios (proveedor, correo_electronico);

-- ==========================================================
-- API Refresh Tokens (Auth)
-- ==========================================================
CREATE TABLE IF NOT EXISTS api_refresh_tokens (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_usuario INT UNSIGNED NOT NULL,
  token_hash VARCHAR(64) NOT NULL,
  replaced_by_hash VARCHAR(64) NULL DEFAULT NULL,
  ip_address VARCHAR(45) NULL DEFAULT NULL,
  user_agent TEXT NULL DEFAULT NULL,
  created_at DATETIME NOT NULL,
  expires_at DATETIME NOT NULL,
  revoked_at DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_token_hash (token_hash),
  KEY idx_api_rt_usuario (id_usuario),
  KEY idx_api_rt_expires (expires_at),
  CONSTRAINT fk_api_rt_usuario FOREIGN KEY (id_usuario) REFERENCES admin_usuarios(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ==========================================================
-- Servicios
-- ==========================================================
CREATE TABLE IF NOT EXISTS servicios (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(120) NOT NULL,
  imagen VARCHAR(255) NOT NULL DEFAULT '',
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,1=inactivo,2=eliminado',
  PRIMARY KEY (id),
  UNIQUE KEY uniq_servicios_nombre (nombre),
  KEY idx_servicios_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ==========================================================
-- Cafeterías
-- ==========================================================
CREATE TABLE IF NOT EXISTS cafeterias (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(160) NOT NULL,
  imagen VARCHAR(255) NOT NULL DEFAULT '',
  id_ciudad INT UNSIGNED NOT NULL,
  direccion VARCHAR(255) NOT NULL,
  telefono VARCHAR(30) NOT NULL DEFAULT '',
  correo_electronico VARCHAR(190) NULL DEFAULT NULL,
  latitud VARCHAR(50) NOT NULL DEFAULT '',
  longitud VARCHAR(50) NOT NULL DEFAULT '',
  horario_apertura TIME NULL DEFAULT NULL,
  horario_cierre TIME NULL DEFAULT NULL,
  horario_diferente VARCHAR(3) NOT NULL DEFAULT 'NO' COMMENT 'SI|NO',
  descripcion TEXT NOT NULL,
  id_usuario INT UNSIGNED NOT NULL COMMENT 'propietario',
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activa,1=inactiva,2=eliminada',
  id_alta INT UNSIGNED NOT NULL,
  fecha_alta DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_cafeterias_nombre (nombre),
  UNIQUE KEY uniq_cafeterias_correo (correo_electronico),
  KEY idx_cafeterias_ciudad (id_ciudad),
  KEY idx_cafeterias_owner (id_usuario),
  KEY idx_cafeterias_estado (estado),
  CONSTRAINT fk_cafeterias_ciudad FOREIGN KEY (id_ciudad) REFERENCES ciudades(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_cafeterias_owner FOREIGN KEY (id_usuario) REFERENCES admin_usuarios(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS cafeteria_horarios (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_cafeteria INT UNSIGNED NOT NULL,
  dia VARCHAR(20) NOT NULL,
  hora_apertura TIME NULL DEFAULT NULL,
  hora_cierre TIME NULL DEFAULT NULL,
  cerrado VARCHAR(3) NOT NULL DEFAULT 'NO' COMMENT 'SI|NO',
  PRIMARY KEY (id),
  UNIQUE KEY uniq_cafeteria_dia (id_cafeteria, dia),
  KEY idx_caf_horarios_caf (id_cafeteria),
  CONSTRAINT fk_caf_horarios_caf FOREIGN KEY (id_cafeteria) REFERENCES cafeterias(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS cafeterias_imagenes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_cafeteria INT UNSIGNED NOT NULL,
  imagen VARCHAR(255) NOT NULL,
  descripcion VARCHAR(255) NOT NULL DEFAULT '',
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activa,1=inactiva,2=eliminada',
  PRIMARY KEY (id),
  KEY idx_caf_img_caf (id_cafeteria),
  KEY idx_caf_img_estado (estado),
  CONSTRAINT fk_caf_img_caf FOREIGN KEY (id_cafeteria) REFERENCES cafeterias(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS cafeterias_servicios (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_servicio INT UNSIGNED NOT NULL,
  id_cafeteria INT UNSIGNED NOT NULL,
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,2=eliminado',
  PRIMARY KEY (id),
  UNIQUE KEY uniq_caf_serv (id_servicio, id_cafeteria),
  KEY idx_caf_serv_caf (id_cafeteria),
  KEY idx_caf_serv_serv (id_servicio),
  KEY idx_caf_serv_estado (estado),
  CONSTRAINT fk_caf_serv_serv FOREIGN KEY (id_servicio) REFERENCES servicios(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_caf_serv_caf FOREIGN KEY (id_cafeteria) REFERENCES cafeterias(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS cafeterias_comentarios (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_cafeteria INT UNSIGNED NOT NULL,
  id_usuario INT UNSIGNED NOT NULL,
  comentario TEXT NOT NULL,
  fecha_alta DATETIME NOT NULL,
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,1=inactivo,2=eliminado',
  PRIMARY KEY (id),
  KEY idx_caf_com_caf (id_cafeteria),
  KEY idx_caf_com_usr (id_usuario),
  KEY idx_caf_com_fecha (fecha_alta),
  KEY idx_caf_com_estado (estado),
  CONSTRAINT fk_caf_com_caf FOREIGN KEY (id_cafeteria) REFERENCES cafeterias(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_caf_com_usr FOREIGN KEY (id_usuario) REFERENCES admin_usuarios(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ==========================================================
-- Menú (Catálogos)
-- ==========================================================
CREATE TABLE IF NOT EXISTS menu_categorias (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(120) NOT NULL,
  imagen VARCHAR(255) NOT NULL DEFAULT '',
  es_bebida VARCHAR(2) NOT NULL DEFAULT 'No' COMMENT 'Si|No',
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,1=inactivo,2=eliminado',
  id_alta INT UNSIGNED NOT NULL,
  fecha_alta DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_menu_cat_nombre (nombre),
  KEY idx_menu_cat_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS menu_subcategorias (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(120) NOT NULL,
  id_categoria INT UNSIGNED NOT NULL,
  registro_occu INT NOT NULL DEFAULT 1 COMMENT '1=OCCU,2=Propietario',
  id_propietario INT UNSIGNED NOT NULL DEFAULT 0,
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,1=inactivo,2=eliminado',
  fecha_alta DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_menu_subcat (id_categoria, nombre),
  KEY idx_menu_subcat_cat (id_categoria),
  KEY idx_menu_subcat_estado (estado),
  CONSTRAINT fk_menu_subcat_cat FOREIGN KEY (id_categoria) REFERENCES menu_categorias(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS menu_productos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(160) NOT NULL,
  id_subcategoria INT UNSIGNED NOT NULL,
  imagen VARCHAR(255) NOT NULL DEFAULT '',
  registro_occu INT NOT NULL DEFAULT 1 COMMENT '1=OCCU,2=Propietario',
  id_alta INT UNSIGNED NOT NULL,
  fecha_alta DATETIME NOT NULL,
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,1=inactivo,2=eliminado',
  PRIMARY KEY (id),
  UNIQUE KEY uniq_menu_producto (id_subcategoria, nombre),
  KEY idx_menu_prod_sub (id_subcategoria),
  KEY idx_menu_prod_estado (estado),
  CONSTRAINT fk_menu_prod_sub FOREIGN KEY (id_subcategoria) REFERENCES menu_subcategorias(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS menu_productos_tamanos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(80) NOT NULL,
  unidad_medida VARCHAR(30) NOT NULL DEFAULT 'Oz',
  medida VARCHAR(20) NOT NULL DEFAULT '',
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,1=inactivo,2=eliminado',
  PRIMARY KEY (id),
  UNIQUE KEY uniq_menu_tamano (nombre),
  KEY idx_menu_tamano_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS menu_ingredientes_categorias (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(120) NOT NULL,
  para_bebidas VARCHAR(2) NOT NULL DEFAULT 'Si',
  para_alimentos VARCHAR(2) NOT NULL DEFAULT 'Si',
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,1=inactivo,2=eliminado',
  id_alta INT UNSIGNED NOT NULL,
  fecha_alta DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_menu_ing_cat (nombre),
  KEY idx_menu_ing_cat_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS menu_ingredientes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(160) NOT NULL,
  id_ingrediente_categoria INT UNSIGNED NOT NULL,
  registro_occu INT NOT NULL DEFAULT 1 COMMENT '1=OCCU,2=Propietario',
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,1=inactivo,2=eliminado',
  id_alta INT UNSIGNED NOT NULL,
  fecha_alta DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_menu_ing (id_ingrediente_categoria, nombre),
  KEY idx_menu_ing_cat (id_ingrediente_categoria),
  KEY idx_menu_ing_estado (estado),
  CONSTRAINT fk_menu_ing_cat FOREIGN KEY (id_ingrediente_categoria) REFERENCES menu_ingredientes_categorias(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ==========================================================
-- Plantillas OCCU por producto (reglas v2)
-- ==========================================================
CREATE TABLE IF NOT EXISTS menu_productos_reglas_ingredientes_categorias (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_producto INT UNSIGNED NOT NULL,
  id_ingrediente_categoria INT UNSIGNED NOT NULL,
  tipo_seleccion VARCHAR(15) NOT NULL DEFAULT 'unica' COMMENT 'unica|multiple',
  seleccion_minima INT NOT NULL DEFAULT 0,
  seleccion_maxima INT NULL DEFAULT NULL COMMENT 'NULL = sin límite',
  orden INT NOT NULL DEFAULT 0,
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,1=inactivo,2=eliminado',
  id_alta INT UNSIGNED NOT NULL,
  fecha_alta DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_mpric_producto_categoria (id_producto, id_ingrediente_categoria),
  KEY idx_mpric_producto (id_producto),
  KEY idx_mpric_categoria (id_ingrediente_categoria),
  CONSTRAINT fk_mpric_producto FOREIGN KEY (id_producto) REFERENCES menu_productos(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_mpric_cat FOREIGN KEY (id_ingrediente_categoria) REFERENCES menu_ingredientes_categorias(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS menu_productos_reglas_ingredientes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_producto INT UNSIGNED NOT NULL,
  id_ingrediente INT UNSIGNED NOT NULL,
  permite_cantidad TINYINT(1) NOT NULL DEFAULT 0,
  cantidad_minima INT NOT NULL DEFAULT 0,
  cantidad_maxima INT NOT NULL DEFAULT 1,
  paso_cantidad INT NOT NULL DEFAULT 1,
  cantidad_incluida INT NOT NULL DEFAULT 0,
  tipo_precio VARCHAR(20) NOT NULL DEFAULT 'por_porcion' COMMENT 'por_porcion|fijo',
  precio_unitario DECIMAL(10,2) NOT NULL DEFAULT 0,
  es_recomendado TINYINT(1) NOT NULL DEFAULT 0,
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,1=inactivo,2=eliminado',
  id_alta INT UNSIGNED NOT NULL,
  fecha_alta DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_mpri_producto_ingrediente (id_producto, id_ingrediente),
  KEY idx_mpri_producto (id_producto),
  KEY idx_mpri_ingrediente (id_ingrediente),
  CONSTRAINT fk_mpri_producto FOREIGN KEY (id_producto) REFERENCES menu_productos(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_mpri_ing FOREIGN KEY (id_ingrediente) REFERENCES menu_ingredientes(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ==========================================================
-- Menú por cafetería (publicación real) + reglas v2 por cafetería
-- ==========================================================
CREATE TABLE IF NOT EXISTS cafeterias_menu_productos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_cafeteria INT UNSIGNED NOT NULL,
  id_producto INT UNSIGNED NOT NULL,
  precio_base DECIMAL(10,2) NOT NULL DEFAULT 0,
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,1=inactivo,2=eliminado',
  id_alta INT UNSIGNED NOT NULL,
  fecha_alta DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_cmp_cafeteria_producto (id_cafeteria, id_producto),
  KEY idx_cmp_cafeteria_estado (id_cafeteria, estado),
  KEY idx_cmp_producto (id_producto),
  CONSTRAINT fk_cmp_caf FOREIGN KEY (id_cafeteria) REFERENCES cafeterias(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_cmp_prod FOREIGN KEY (id_producto) REFERENCES menu_productos(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS cafeterias_menu_productos_tamanos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_cafeteria_menu_producto INT UNSIGNED NOT NULL,
  id_tamano INT UNSIGNED NOT NULL,
  precio DECIMAL(10,2) NOT NULL DEFAULT 0,
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,1=inactivo,2=eliminado',
  PRIMARY KEY (id),
  UNIQUE KEY uniq_cmpt_producto_tamano (id_cafeteria_menu_producto, id_tamano),
  KEY idx_cmpt_producto_estado (id_cafeteria_menu_producto, estado),
  KEY idx_cmpt_tamano (id_tamano),
  CONSTRAINT fk_cmpt_cmp FOREIGN KEY (id_cafeteria_menu_producto) REFERENCES cafeterias_menu_productos(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_cmpt_tamano FOREIGN KEY (id_tamano) REFERENCES menu_productos_tamanos(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS cafeterias_menu_productos_reglas_ingredientes_categorias (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_cafeteria_menu_producto INT UNSIGNED NOT NULL,
  id_ingrediente_categoria INT UNSIGNED NOT NULL,
  tipo_seleccion VARCHAR(15) NOT NULL DEFAULT 'unica' COMMENT 'unica|multiple',
  seleccion_minima INT NOT NULL DEFAULT 0,
  seleccion_maxima INT NULL DEFAULT NULL COMMENT 'NULL = sin límite',
  orden INT NOT NULL DEFAULT 0,
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,1=inactivo,2=eliminado',
  PRIMARY KEY (id),
  UNIQUE KEY uniq_cmp_ric_producto_categoria (id_cafeteria_menu_producto, id_ingrediente_categoria),
  KEY idx_cmp_ric_producto_estado (id_cafeteria_menu_producto, estado),
  KEY idx_cmp_ric_categoria (id_ingrediente_categoria),
  CONSTRAINT fk_cmp_ric_cmp FOREIGN KEY (id_cafeteria_menu_producto) REFERENCES cafeterias_menu_productos(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_cmp_ric_cat FOREIGN KEY (id_ingrediente_categoria) REFERENCES menu_ingredientes_categorias(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS cafeterias_menu_productos_reglas_ingredientes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_cafeteria_menu_producto INT UNSIGNED NOT NULL,
  id_ingrediente INT UNSIGNED NOT NULL,
  agotado TINYINT(1) NOT NULL DEFAULT 0,
  permite_cantidad TINYINT(1) NOT NULL DEFAULT 0,
  cantidad_minima INT NOT NULL DEFAULT 0,
  cantidad_maxima INT NOT NULL DEFAULT 1,
  paso_cantidad INT NOT NULL DEFAULT 1,
  cantidad_incluida INT NOT NULL DEFAULT 0,
  tipo_precio VARCHAR(20) NOT NULL DEFAULT 'por_porcion' COMMENT 'por_porcion|fijo',
  precio_unitario DECIMAL(10,2) NOT NULL DEFAULT 0,
  es_recomendado TINYINT(1) NOT NULL DEFAULT 0,
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,1=inactivo,2=eliminado',
  PRIMARY KEY (id),
  UNIQUE KEY uniq_cmp_ri_producto_ingrediente (id_cafeteria_menu_producto, id_ingrediente),
  KEY idx_cmp_ri_producto_estado (id_cafeteria_menu_producto, estado),
  KEY idx_cmp_ri_ingrediente (id_ingrediente),
  CONSTRAINT fk_cmp_ri_cmp FOREIGN KEY (id_cafeteria_menu_producto) REFERENCES cafeterias_menu_productos(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_cmp_ri_ing FOREIGN KEY (id_ingrediente) REFERENCES menu_ingredientes(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ==========================================================
-- Menú general del propietario + reglas (para publicar a cafeterías)
-- Nota: `habilitado` indica si el producto/tamaño está incluido en el menú general.
--       `estado` mantiene la semántica estándar de ciclo de vida (soft delete).
-- ==========================================================
CREATE TABLE IF NOT EXISTS propietarios_menu_productos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_propietario INT UNSIGNED NOT NULL,
  id_producto INT UNSIGNED NOT NULL,
  precio_base DECIMAL(10,2) NOT NULL DEFAULT 0,
  habilitado TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=no incluido,1=incluido',
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,1=inactivo,2=eliminado',
  id_alta INT UNSIGNED NOT NULL,
  fecha_alta DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_pmp_propietario_producto (id_propietario, id_producto),
  KEY idx_pmp_propietario_habilitado (id_propietario, habilitado, estado),
  KEY idx_pmp_producto (id_producto),
  CONSTRAINT fk_pmp_propietario FOREIGN KEY (id_propietario) REFERENCES admin_usuarios(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_pmp_producto FOREIGN KEY (id_producto) REFERENCES menu_productos(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS propietarios_menu_productos_tamanos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_propietario_menu_producto INT UNSIGNED NOT NULL,
  id_tamano INT UNSIGNED NOT NULL,
  precio DECIMAL(10,2) NOT NULL DEFAULT 0,
  habilitado TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=no incluido,1=incluido',
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,1=inactivo,2=eliminado',
  PRIMARY KEY (id),
  UNIQUE KEY uniq_pmpt_producto_tamano (id_propietario_menu_producto, id_tamano),
  KEY idx_pmpt_producto_habilitado (id_propietario_menu_producto, habilitado, estado),
  KEY idx_pmpt_tamano (id_tamano),
  CONSTRAINT fk_pmpt_pmp FOREIGN KEY (id_propietario_menu_producto) REFERENCES propietarios_menu_productos(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_pmpt_tamano FOREIGN KEY (id_tamano) REFERENCES menu_productos_tamanos(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS propietarios_menu_productos_reglas_ingredientes_categorias (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_propietario_menu_producto INT UNSIGNED NOT NULL,
  id_ingrediente_categoria INT UNSIGNED NOT NULL,
  tipo_seleccion VARCHAR(15) NOT NULL DEFAULT 'unica' COMMENT 'unica|multiple',
  seleccion_minima INT NOT NULL DEFAULT 0,
  seleccion_maxima INT NULL DEFAULT NULL COMMENT 'NULL = sin límite',
  orden INT NOT NULL DEFAULT 0,
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,1=inactivo,2=eliminado',
  id_alta INT UNSIGNED NOT NULL,
  fecha_alta DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_pmpric_producto_categoria (id_propietario_menu_producto, id_ingrediente_categoria),
  KEY idx_pmpric_producto (id_propietario_menu_producto),
  KEY idx_pmpric_categoria (id_ingrediente_categoria),
  CONSTRAINT fk_pmpric_pmp FOREIGN KEY (id_propietario_menu_producto) REFERENCES propietarios_menu_productos(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_pmpric_cat FOREIGN KEY (id_ingrediente_categoria) REFERENCES menu_ingredientes_categorias(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS propietarios_menu_productos_reglas_ingredientes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_propietario_menu_producto INT UNSIGNED NOT NULL,
  id_ingrediente INT UNSIGNED NOT NULL,
  permite_cantidad TINYINT(1) NOT NULL DEFAULT 0,
  cantidad_minima INT NOT NULL DEFAULT 0,
  cantidad_maxima INT NOT NULL DEFAULT 1,
  paso_cantidad INT NOT NULL DEFAULT 1,
  cantidad_incluida INT NOT NULL DEFAULT 0,
  tipo_precio VARCHAR(20) NOT NULL DEFAULT 'por_porcion' COMMENT 'por_porcion|fijo',
  precio_unitario DECIMAL(10,2) NOT NULL DEFAULT 0,
  es_recomendado TINYINT(1) NOT NULL DEFAULT 0,
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,1=inactivo,2=eliminado',
  id_alta INT UNSIGNED NOT NULL,
  fecha_alta DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_pmpri_producto_ingrediente (id_propietario_menu_producto, id_ingrediente),
  KEY idx_pmpri_producto (id_propietario_menu_producto),
  KEY idx_pmpri_ingrediente (id_ingrediente),
  CONSTRAINT fk_pmpri_pmp FOREIGN KEY (id_propietario_menu_producto) REFERENCES propietarios_menu_productos(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_pmpri_ing FOREIGN KEY (id_ingrediente) REFERENCES menu_ingredientes(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS propietarios_menu_publicaciones (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_propietario INT UNSIGNED NOT NULL,
  tipo VARCHAR(20) NOT NULL DEFAULT 'todo' COMMENT 'todo|solo_precios',
  cafeterias_afectadas JSON NOT NULL,
  productos_publicados INT NOT NULL DEFAULT 0,
  fecha_publicacion DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_pmp_propietario (id_propietario),
  KEY idx_pmp_fecha (fecha_publicacion),
  CONSTRAINT fk_publ_propietario FOREIGN KEY (id_propietario) REFERENCES admin_usuarios(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ==========================================================
-- Carrito + snapshots (v2)
-- ==========================================================
CREATE TABLE IF NOT EXISTS ventas_carrito (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_cafeteria INT UNSIGNED NOT NULL,
  id_usuario INT UNSIGNED NOT NULL,
  fecha_alta DATETIME NOT NULL,
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,1=procesado,2=eliminado',
  PRIMARY KEY (id),
  KEY idx_vc_usuario_estado (id_usuario, estado),
  KEY idx_vc_cafeteria_estado (id_cafeteria, estado),
  CONSTRAINT fk_vc_caf FOREIGN KEY (id_cafeteria) REFERENCES cafeterias(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_vc_usr FOREIGN KEY (id_usuario) REFERENCES admin_usuarios(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS ventas_carrito_items (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_carrito INT UNSIGNED NOT NULL,
  id_producto INT UNSIGNED NOT NULL,
  id_tamano INT UNSIGNED NULL DEFAULT NULL,
  cantidad INT NOT NULL DEFAULT 1,
  fecha_alta DATETIME NOT NULL,
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,2=eliminado',
  PRIMARY KEY (id),
  KEY idx_vci_carrito (id_carrito),
  KEY idx_vci_producto (id_producto),
  KEY idx_vci_estado (estado),
  CONSTRAINT fk_vci_carrito FOREIGN KEY (id_carrito) REFERENCES ventas_carrito(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_vci_producto FOREIGN KEY (id_producto) REFERENCES menu_productos(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_vci_tamano FOREIGN KEY (id_tamano) REFERENCES menu_productos_tamanos(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS ventas_carrito_items_personalizaciones (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_carrito_item INT UNSIGNED NOT NULL,
  id_ingrediente INT UNSIGNED NOT NULL,
  id_ingrediente_categoria_snapshot INT UNSIGNED NULL DEFAULT NULL,
  nombre_ingrediente_snapshot VARCHAR(255) NOT NULL DEFAULT '',
  cantidad INT NOT NULL DEFAULT 0,
  precio_unitario_snapshot DECIMAL(10,2) NOT NULL DEFAULT 0,
  monto_total_snapshot DECIMAL(10,2) NOT NULL DEFAULT 0,
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,2=eliminado',
  fecha_alta DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_vcip_item_ingrediente (id_carrito_item, id_ingrediente),
  KEY idx_vcip_item (id_carrito_item),
  KEY idx_vcip_ingrediente (id_ingrediente),
  CONSTRAINT fk_vcip_item FOREIGN KEY (id_carrito_item) REFERENCES ventas_carrito_items(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_vcip_ing FOREIGN KEY (id_ingrediente) REFERENCES menu_ingredientes(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ==========================================================
-- Pedido / Venta (cierra el ciclo a partir del carrito)
-- Nota: aún no usado por la API actual, pero requerido por el modelo v2.
-- ==========================================================
CREATE TABLE IF NOT EXISTS ventas (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_cafeteria INT UNSIGNED NOT NULL,
  id_usuario INT UNSIGNED NOT NULL,
  id_carrito INT UNSIGNED NULL DEFAULT NULL COMMENT 'carrito origen (opcional)',
  codigo_retiro VARCHAR(12) NOT NULL DEFAULT '' COMMENT 'código para recoger/entregar',
  nota_cliente VARCHAR(500) NOT NULL DEFAULT '',
  monto_subtotal_productos DECIMAL(10,2) NOT NULL DEFAULT 0,
  monto_subtotal_personalizaciones DECIMAL(10,2) NOT NULL DEFAULT 0,
  monto_total DECIMAL(10,2) NOT NULL DEFAULT 0,
  estado_pedido VARCHAR(20) NOT NULL DEFAULT 'pendiente' COMMENT 'pendiente|aceptado|rechazado|preparando|listo|entregado|cancelado',
  motivo_rechazo VARCHAR(500) NOT NULL DEFAULT '',
  fecha_alta DATETIME NOT NULL,
  fecha_aceptado DATETIME NULL DEFAULT NULL,
  fecha_rechazo DATETIME NULL DEFAULT NULL,
  fecha_listo DATETIME NULL DEFAULT NULL,
  fecha_entregado DATETIME NULL DEFAULT NULL,
  fecha_cancelado DATETIME NULL DEFAULT NULL,
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,2=eliminado',
  PRIMARY KEY (id),
  KEY idx_ventas_usuario_estado (id_usuario, estado_pedido, estado),
  KEY idx_ventas_cafeteria_estado (id_cafeteria, estado_pedido, estado),
  KEY idx_ventas_fecha (fecha_alta),
  KEY idx_ventas_carrito (id_carrito),
  CONSTRAINT fk_ventas_caf FOREIGN KEY (id_cafeteria) REFERENCES cafeterias(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_ventas_usr FOREIGN KEY (id_usuario) REFERENCES admin_usuarios(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_ventas_carrito FOREIGN KEY (id_carrito) REFERENCES ventas_carrito(id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS ventas_items (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_venta INT UNSIGNED NOT NULL,
  id_producto INT UNSIGNED NOT NULL,
  id_tamano INT UNSIGNED NULL DEFAULT NULL,
  nombre_producto_snapshot VARCHAR(255) NOT NULL DEFAULT '',
  nombre_tamano_snapshot VARCHAR(255) NOT NULL DEFAULT '',
  precio_unitario_snapshot DECIMAL(10,2) NOT NULL DEFAULT 0,
  cantidad INT NOT NULL DEFAULT 1,
  monto_personalizaciones_unitario_snapshot DECIMAL(10,2) NOT NULL DEFAULT 0,
  monto_total_unitario_snapshot DECIMAL(10,2) NOT NULL DEFAULT 0,
  monto_total_snapshot DECIMAL(10,2) NOT NULL DEFAULT 0,
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,2=eliminado',
  fecha_alta DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_vi_venta (id_venta),
  KEY idx_vi_producto (id_producto),
  KEY idx_vi_estado (estado),
  CONSTRAINT fk_vi_venta FOREIGN KEY (id_venta) REFERENCES ventas(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_vi_producto FOREIGN KEY (id_producto) REFERENCES menu_productos(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_vi_tamano FOREIGN KEY (id_tamano) REFERENCES menu_productos_tamanos(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Snapshots de personalizaciones por item de venta (histórico)
CREATE TABLE IF NOT EXISTS ventas_items_personalizaciones (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_venta_item INT UNSIGNED NOT NULL,
  id_ingrediente INT UNSIGNED NOT NULL,
  id_ingrediente_categoria_snapshot INT UNSIGNED NULL DEFAULT NULL,
  nombre_ingrediente_snapshot VARCHAR(255) NOT NULL DEFAULT '',
  cantidad INT NOT NULL DEFAULT 0,
  precio_unitario_snapshot DECIMAL(10,2) NOT NULL DEFAULT 0,
  monto_total_snapshot DECIMAL(10,2) NOT NULL DEFAULT 0,
  estado INT NOT NULL DEFAULT 0 COMMENT '0=activo,2=eliminado',
  fecha_alta DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_vip_item_ingrediente (id_venta_item, id_ingrediente),
  KEY idx_vip_venta_item (id_venta_item),
  KEY idx_vip_ingrediente (id_ingrediente),
  CONSTRAINT fk_vip_item FOREIGN KEY (id_venta_item) REFERENCES ventas_items(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_vip_ing FOREIGN KEY (id_ingrediente) REFERENCES menu_ingredientes(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 1;

