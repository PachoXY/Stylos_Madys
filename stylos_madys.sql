-- =====================================================================
--  Base de datos: Stylos Madys - Gestión y agendamiento de citas
--  Motor: MySQL 8.0.16 o superior (necesario para que funcionen los CHECK)
--  Generado a partir del Diccionario de Datos (11 tablas)
-- =====================================================================

DROP DATABASE IF EXISTS stylos_madys;
CREATE DATABASE stylos_madys
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE stylos_madys;

-- ---------------------------------------------------------------------
-- 1. rol
-- ---------------------------------------------------------------------
CREATE TABLE rol (
  id_rol      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre_rol  VARCHAR(50)  NOT NULL,
  descripcion VARCHAR(200) NULL,
  PRIMARY KEY (id_rol),
  UNIQUE KEY uq_rol_nombre (nombre_rol),
  CONSTRAINT ck_rol_nombre CHECK (nombre_rol IN ('Usuario','Administrador','Profesional'))
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2. usuario
-- ---------------------------------------------------------------------
CREATE TABLE usuario (
  id_usuario     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_rol         INT UNSIGNED NOT NULL,
  nombres        VARCHAR(100) NOT NULL,
  apellidos      VARCHAR(100) NOT NULL,
  correo         VARCHAR(150) NOT NULL,
  telefono       VARCHAR(10)  NOT NULL,
  `contraseña`   VARCHAR(255) NOT NULL COMMENT 'Guardar siempre el hash (bcrypt/argon2), nunca texto plano',
  estado         ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
  fecha_registro DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  cedula         VARCHAR(10)  NULL,
  PRIMARY KEY (id_usuario),
  UNIQUE KEY uq_usuario_correo (correo),
  UNIQUE KEY uq_usuario_cedula (cedula),
  KEY idx_usuario_rol (id_rol),
  CONSTRAINT fk_usuario_rol FOREIGN KEY (id_rol) REFERENCES rol (id_rol)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT ck_usuario_correo   CHECK (correo REGEXP '^[^@ ]+@[^@ ]+\\.[^@ ]+$'),
  CONSTRAINT ck_usuario_telefono CHECK (telefono REGEXP '^[0-9]{7,10}$'),
  CONSTRAINT ck_usuario_cedula   CHECK (cedula IS NULL OR cedula REGEXP '^[0-9]{6,10}$')
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 3. servicio
-- ---------------------------------------------------------------------
CREATE TABLE servicio (
  id_servicio     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre_servicio VARCHAR(100) NOT NULL,
  descripcion     VARCHAR(255) NULL,
  estado          ENUM('Disponible','No disponible') NOT NULL DEFAULT 'Disponible',
  PRIMARY KEY (id_servicio),
  UNIQUE KEY uq_servicio_nombre (nombre_servicio)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 4. largo_cabello
-- ---------------------------------------------------------------------
CREATE TABLE largo_cabello (
  id_largo     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre_largo VARCHAR(50)  NOT NULL,
  descripcion  VARCHAR(255) NULL,
  imagen       VARCHAR(255) NOT NULL,
  estado       ENUM('Disponible','No disponible') NOT NULL DEFAULT 'Disponible',
  PRIMARY KEY (id_largo),
  UNIQUE KEY uq_largo_nombre (nombre_largo),
  CONSTRAINT ck_largo_nombre CHECK (nombre_largo IN ('Corto','Medio','Largo','Extra largo'))
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 5. servicio_largo  (precio y duración según el largo)
-- ---------------------------------------------------------------------
CREATE TABLE servicio_largo (
  id_servicio_largo INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  id_servicio       INT UNSIGNED  NOT NULL,
  id_largo          INT UNSIGNED  NOT NULL,
  precio            DECIMAL(10,2) NOT NULL,
  duracion_minutos  INT           NOT NULL,
  estado            VARCHAR(20)   NOT NULL DEFAULT 'Disponible',
  PRIMARY KEY (id_servicio_largo),
  UNIQUE KEY uq_servicio_largo (id_servicio, id_largo),
  KEY idx_sl_largo (id_largo),
  CONSTRAINT fk_sl_servicio FOREIGN KEY (id_servicio) REFERENCES servicio (id_servicio)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_sl_largo FOREIGN KEY (id_largo) REFERENCES largo_cabello (id_largo)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT ck_sl_precio   CHECK (precio >= 0),
  CONSTRAINT ck_sl_duracion CHECK (duracion_minutos > 0),
  CONSTRAINT ck_sl_estado   CHECK (estado IN ('Disponible','No disponible'))
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 6. horario  (disponibilidad semanal del profesional)
-- ---------------------------------------------------------------------
CREATE TABLE horario (
  id_horario  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_usuario  INT UNSIGNED NOT NULL COMMENT 'Debe ser un usuario con rol Profesional',
  dia_semana  VARCHAR(15)  NOT NULL,
  hora_inicio TIME         NOT NULL,
  hora_fin    TIME         NOT NULL,
  estado      VARCHAR(20)  NOT NULL DEFAULT 'Disponible',
  PRIMARY KEY (id_horario),
  KEY idx_horario_usuario (id_usuario),
  CONSTRAINT fk_horario_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT ck_horario_dia    CHECK (dia_semana IN ('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo')),
  CONSTRAINT ck_horario_horas  CHECK (hora_fin > hora_inicio),
  CONSTRAINT ck_horario_estado CHECK (estado IN ('Disponible','No disponible'))
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 7. bloqueo_horario
-- ---------------------------------------------------------------------
CREATE TABLE bloqueo_horario (
  id_bloqueo    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_horario    INT UNSIGNED NOT NULL,
  fecha_bloqueo DATE         NOT NULL,
  hora_inicio   TIME         NOT NULL,
  hora_fin      TIME         NOT NULL,
  motivo        VARCHAR(255) NULL,
  estado        VARCHAR(20)  NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (id_bloqueo),
  KEY idx_bloqueo_horario (id_horario, fecha_bloqueo),
  CONSTRAINT fk_bloqueo_horario FOREIGN KEY (id_horario) REFERENCES horario (id_horario)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT ck_bloqueo_horas  CHECK (hora_fin > hora_inicio),
  CONSTRAINT ck_bloqueo_estado CHECK (estado IN ('Activo','Inactivo'))
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 8. cita
-- ---------------------------------------------------------------------
CREATE TABLE cita (
  id_cita           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  id_usuario        INT UNSIGNED  NOT NULL COMMENT 'Cliente que agenda',
  id_servicio_largo INT UNSIGNED  NOT NULL,
  id_horario        INT UNSIGNED  NOT NULL,
  fecha_cita        DATE          NOT NULL,
  hora_inicio       TIME          NOT NULL,
  hora_fin          TIME          NOT NULL,
  precio_total      DECIMAL(10,2) NOT NULL,
  duracion_minutos  INT           NOT NULL,
  estado            ENUM('Pendiente','Confirmada','Finalizada','Cancelada') NOT NULL DEFAULT 'Pendiente',
  fecha_registro    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  observaciones     TEXT          NULL,
  PRIMARY KEY (id_cita),
  KEY idx_cita_usuario (id_usuario),
  KEY idx_cita_sl (id_servicio_largo),
  KEY idx_cita_agenda (id_horario, fecha_cita, hora_inicio),
  CONSTRAINT fk_cita_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_cita_sl FOREIGN KEY (id_servicio_largo) REFERENCES servicio_largo (id_servicio_largo)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_cita_horario FOREIGN KEY (id_horario) REFERENCES horario (id_horario)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT ck_cita_horas    CHECK (hora_fin > hora_inicio),
  CONSTRAINT ck_cita_precio   CHECK (precio_total >= 0),
  CONSTRAINT ck_cita_duracion CHECK (duracion_minutos > 0)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 9. calificacion  (una por cita)
-- ---------------------------------------------------------------------
CREATE TABLE calificacion (
  id_calificacion    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_cita            INT UNSIGNED NOT NULL,
  id_usuario         INT UNSIGNED NOT NULL,
  puntuacion         TINYINT      NOT NULL,
  comentario         VARCHAR(500) NULL,
  fecha_calificacion DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_calificacion),
  UNIQUE KEY uq_calificacion_cita (id_cita),
  KEY idx_calificacion_usuario (id_usuario),
  CONSTRAINT fk_calif_cita FOREIGN KEY (id_cita) REFERENCES cita (id_cita)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_calif_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT ck_calif_puntuacion CHECK (puntuacion BETWEEN 1 AND 5)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 10. factura
-- ---------------------------------------------------------------------
CREATE TABLE factura (
  id_factura     INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  numero_factura VARCHAR(20)   NOT NULL,
  id_cita        INT UNSIGNED  NOT NULL,
  id_usuario     INT UNSIGNED  NOT NULL,
  fecha_emision  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  subtotal       DECIMAL(10,2) NOT NULL,
  descuento      DECIMAL(10,2) NOT NULL DEFAULT 0,
  total          DECIMAL(10,2) NOT NULL,
  estado         ENUM('Emitida','Anulada') NOT NULL DEFAULT 'Emitida',
  PRIMARY KEY (id_factura),
  UNIQUE KEY uq_factura_numero (numero_factura),
  KEY idx_factura_cita (id_cita),
  KEY idx_factura_usuario (id_usuario),
  CONSTRAINT fk_factura_cita FOREIGN KEY (id_cita) REFERENCES cita (id_cita)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_factura_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT ck_factura_subtotal  CHECK (subtotal >= 0),
  CONSTRAINT ck_factura_descuento CHECK (descuento >= 0 AND descuento <= subtotal),
  CONSTRAINT ck_factura_total     CHECK (total >= 0 AND total = subtotal - descuento)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 11. detalle_factura
-- ---------------------------------------------------------------------
CREATE TABLE detalle_factura (
  id_detalle      INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  id_factura      INT UNSIGNED  NOT NULL,
  id_servicio     INT UNSIGNED  NOT NULL,
  cantidad        INT           NOT NULL DEFAULT 1,
  precio_unitario DECIMAL(10,2) NOT NULL,
  subtotal        DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (id_detalle),
  KEY idx_df_factura (id_factura),
  KEY idx_df_servicio (id_servicio),
  CONSTRAINT fk_df_factura FOREIGN KEY (id_factura) REFERENCES factura (id_factura)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_df_servicio FOREIGN KEY (id_servicio) REFERENCES servicio (id_servicio)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT ck_df_cantidad CHECK (cantidad >= 1),
  CONSTRAINT ck_df_precio   CHECK (precio_unitario > 0),
  CONSTRAINT ck_df_subtotal CHECK (subtotal = cantidad * precio_unitario)
) ENGINE=InnoDB;

-- =====================================================================
--  DATOS INICIALES
-- =====================================================================
INSERT INTO rol (nombre_rol, descripcion) VALUES
  ('Usuario',       'Cliente que agenda y consulta sus citas'),
  ('Administrador', 'Gestiona citas, clientes, servicios, horarios y reportes'),
  ('Profesional',   'Estilista que atiende las citas');

INSERT INTO largo_cabello (nombre_largo, descripcion, imagen) VALUES
  ('Corto',       'Cabello por encima de los hombros',  'img/largos/corto.jpg'),
  ('Medio',       'Cabello a la altura de los hombros', 'img/largos/medio.jpg'),
  ('Largo',       'Cabello hasta media espalda',        'img/largos/largo.jpg'),
  ('Extra largo', 'Cabello por debajo de media espalda','img/largos/extra-largo.jpg');

INSERT INTO servicio (nombre_servicio) VALUES
  ('Cepillados - Planchados'),
  ('Corte de dama y tintes'),
  ('Tratamientos capilares'),
  ('Repolarización - Hidratación'),
  ('Botox capilar'),
  ('Keratina'),
  ('Diagnóstico capilar con capilógrafo'),
  ('Terapia de construcción'),
  ('Relleno proteico'),
  ('Desintoxicación capilar');

-- Precios y duraciones de EJEMPLO tomados de las capturas del diseño; ajústalos a los reales.
INSERT INTO servicio_largo (id_servicio, id_largo, precio, duracion_minutos)
SELECT s.id_servicio, l.id_largo, x.precio, x.minutos
FROM (
  SELECT 'Cepillados - Planchados' n, 'Largo' l,  25000 precio,  90 minutos UNION ALL
  SELECT 'Corte de dama y tintes',    'Medio',    45000, 120 UNION ALL
  SELECT 'Tratamientos capilares',    'Largo',    60000, 120 UNION ALL
  SELECT 'Botox capilar',             'Largo',    80000, 120 UNION ALL
  SELECT 'Keratina',                  'Extra largo', 120000, 180 UNION ALL
  SELECT 'Diagnóstico capilar con capilógrafo', 'Medio', 35000, 60 UNION ALL
  SELECT 'Desintoxicación capilar',   'Corto',    40000,  90
) x
JOIN servicio s ON s.nombre_servicio = x.n
JOIN largo_cabello l ON l.nombre_largo = x.l;

-- =====================================================================
--  VISTA ÚTIL PARA LA AGENDA DEL ADMINISTRADOR
-- =====================================================================
CREATE VIEW v_agenda AS
SELECT c.id_cita, c.fecha_cita, c.hora_inicio, c.hora_fin, c.estado,
       CONCAT(u.nombres,' ',u.apellidos) AS cliente, u.telefono,
       s.nombre_servicio, l.nombre_largo, c.precio_total,
       CONCAT(p.nombres,' ',p.apellidos) AS profesional
FROM cita c
JOIN usuario u        ON u.id_usuario = c.id_usuario
JOIN servicio_largo sl ON sl.id_servicio_largo = c.id_servicio_largo
JOIN servicio s       ON s.id_servicio = sl.id_servicio
JOIN largo_cabello l  ON l.id_largo = sl.id_largo
JOIN horario h        ON h.id_horario = c.id_horario
JOIN usuario p        ON p.id_usuario = h.id_usuario;
