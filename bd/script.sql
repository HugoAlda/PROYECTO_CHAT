CREATE DATABASE bd_chat;

USE bd_chat;

CREATE TABLE tbl_usuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nombre_usuario VARCHAR(50) NOT NULL,
    nombre_persona VARCHAR(50),
    correo_usuario VARCHAR(50) UNIQUE NOT NULL,
    passwd_usuario VARCHAR(255) NOT NULL
);

CREATE TABLE tbl_amigos (
    id_amistad INT PRIMARY KEY AUTO_INCREMENT,
    id_amigo INT,
    id_usuario INT
);

ALTER TABLE tbl_amigos
ADD CONSTRAINT fk_usuario_amigo FOREIGN KEY (id_usuario) REFERENCES tbl_usuarios(id_usuario);

ALTER TABLE tbl_amigos
ADD CONSTRAINT fk_amigo_usuario FOREIGN KEY (id_amigo) REFERENCES tbl_usuarios(id_usuario);

ALTER TABLE tbl_amigos ADD INDEX (id_amigo);

CREATE TABLE tbl_mensajes (
    id_mensaje INT PRIMARY KEY AUTO_INCREMENT,
    mensaje VARCHAR(255) NOT NULL,
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tbl_conversaciones (
    id_conversacion INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario_emisor INT,
    id_amigo_receptor INT
);

ALTER TABLE tbl_conversaciones
ADD CONSTRAINT fk_usuario_emisor FOREIGN KEY (id_usuario_emisor) REFERENCES tbl_usuarios(id_usuario);

ALTER TABLE tbl_conversaciones
ADD CONSTRAINT fk_amigo_receptor FOREIGN KEY (id_amigo_receptor) REFERENCES tbl_amigos(id_amigo);

CREATE TABLE tbl_mensajes_conversaciones (
    id_mensaje_conversacion INT PRIMARY KEY AUTO_INCREMENT,
    id_mensaje INT,
    id_conversacion INT
);

ALTER TABLE tbl_mensajes_conversaciones
ADD CONSTRAINT fk_mensaje_conversacion FOREIGN KEY (id_mensaje) REFERENCES tbl_mensajes(id_mensaje);

ALTER TABLE tbl_mensajes_conversaciones
ADD CONSTRAINT fk_conversacion_mensaje FOREIGN KEY (id_conversacion) REFERENCES tbl_conversaciones(id_conversacion);

CREATE TABLE tbl_solicitudes_amistad (
    id_solicitud INT PRIMARY KEY AUTO_INCREMENT,
    id_solicitante INT NOT NULL,
    id_solicitado INT NOT NULL,
    estado ENUM('pendiente', 'aceptada', 'rechazada') DEFAULT 'pendiente',
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE tbl_solicitudes_amistad
ADD CONSTRAINT fk_solicitante_usuario FOREIGN KEY (id_solicitante) REFERENCES tbl_usuarios(id_usuario);

ALTER TABLE tbl_solicitudes_amistad
ADD CONSTRAINT fk_solicitado_usuario FOREIGN KEY (id_solicitado) REFERENCES tbl_usuarios(id_usuario);