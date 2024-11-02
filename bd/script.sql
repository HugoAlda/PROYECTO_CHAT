CREATE DATABASE bd_chat;
USE bd_chat;

-- Tabla de usuarios
CREATE TABLE tbl_usuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nombre_usuario VARCHAR(50) NOT NULL,
    nombre_persona VARCHAR(50) NULL,
    correo_usuario VARCHAR(50) NOT NULL,
    passwd_usuario VARCHAR(255) NOT NULL
);

-- Tabla de amistades
CREATE TABLE tbl_amigos (
    id_amistad INT PRIMARY KEY AUTO_INCREMENT,
    id_usuarioa INT NOT NULL,
    id_usuariob INT NOT NULL,
    fecha_amistad TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de conversaciones
CREATE TABLE tbl_conversaciones (
    id_conversacion INT PRIMARY KEY AUTO_INCREMENT,
    id_usuarioa INT NOT NULL,
    id_usuariob INT NOT NULL,
    fecha_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de mensajes (incluyendo id_usuario para identificar al remitente)
CREATE TABLE tbl_mensajes (
    id_mensaje INT PRIMARY KEY AUTO_INCREMENT,
    id_conversacion INT NOT NULL,
    id_usuario INT NOT NULL,  -- Nueva columna para identificar al usuario que envió el mensaje
    mensaje TEXT NOT NULL,
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de solicitudes de amistad
CREATE TABLE tbl_solicitudes_amistad (
    id_solicitud INT PRIMARY KEY AUTO_INCREMENT,
    id_solicitante INT NOT NULL,
    id_solicitado INT NOT NULL,
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Claves foráneas para la tabla tbl_mensajes
ALTER TABLE tbl_mensajes
ADD CONSTRAINT fk_id_conversacion FOREIGN KEY (id_conversacion) REFERENCES tbl_conversaciones(id_conversacion),
ADD CONSTRAINT fk_id_usuario FOREIGN KEY (id_usuario) REFERENCES tbl_usuarios(id_usuario);  -- Nueva clave foránea para id_usuario

-- Claves foráneas para la tabla tbl_amigos
ALTER TABLE tbl_amigos
ADD CONSTRAINT fk_amigos_usuarioa FOREIGN KEY (id_usuarioa) REFERENCES tbl_usuarios(id_usuario),
ADD CONSTRAINT fk_amigos_usuariob FOREIGN KEY (id_usuariob) REFERENCES tbl_usuarios(id_usuario);

-- Claves foráneas para la tabla tbl_conversaciones
ALTER TABLE tbl_conversaciones
ADD CONSTRAINT fk_conversacion_usuarioa FOREIGN KEY (id_usuarioa) REFERENCES tbl_usuarios(id_usuario),
ADD CONSTRAINT fk_conversacion_usuariob FOREIGN KEY (id_usuariob) REFERENCES tbl_usuarios(id_usuario);

-- Claves foráneas para la tabla tbl_solicitudes_amistad
ALTER TABLE tbl_solicitudes_amistad
ADD CONSTRAINT fk_solicitud_solicitante FOREIGN KEY (id_solicitante) REFERENCES tbl_usuarios(id_usuario),
ADD CONSTRAINT fk_solicitud_solicitado FOREIGN KEY (id_solicitado) REFERENCES tbl_usuarios(id_usuario);
