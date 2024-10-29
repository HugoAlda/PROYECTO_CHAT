-- Inserciones en la tabla tbl_usuarios
INSERT INTO tbl_usuarios (nombre_usuario, nombre_persona, correo_usuario, passwd_usuario) VALUES
('prueba1', 'Usuario Prueba Uno', 'prueba1@example.com', 'pass123'),
('prueba2', 'Usuario Prueba Dos', 'prueba2@example.com', 'pass123');

-- Inserciones en la tabla tbl_amigos
-- Primero se insertan los usuarios como amigos con sus respectivos IDs
INSERT INTO tbl_amigos (id_amigo, id_usuario) VALUES
(2, 1), -- prueba2 es amigo de prueba1 (id_usuario 1)
(1, 2); -- prueba2 (id_usuario 2) sin amigos por el momento

-- Inserciones en la tabla tbl_mensajes
INSERT INTO tbl_mensajes (mensaje) VALUES
('Hola, ¿cómo estás?'), 
('Estoy bien, gracias. ¿Y tú?');

-- Inserciones en la tabla tbl_conversaciones
INSERT INTO tbl_conversaciones (id_usuario_emisor, id_amigo_receptor) VALUES
(1, 2); -- prueba1 a prueba2 (no hace falta duplicar la entrada inversa ya que podemos obtenerla desde la tabla de mensajes)

-- Inserciones en la tabla tbl_mensajes_conversaciones
-- Aquí suponemos que los mensajes insertados anteriormente tienen los IDs 1 y 2 (se puede verificar después de la inserción de tbl_mensajes)
INSERT INTO tbl_mensajes_conversaciones (id_mensaje, id_conversacion) VALUES
(1, 1), -- El primer mensaje en la conversación entre prueba1 y prueba2
(2, 1); -- El segundo mensaje en la misma conversación

-- Inserciones en la tabla tbl_solicitudes_amistad
INSERT INTO tbl_solicitudes_amistad (id_solicitante, id_solicitado, estado) VALUES
(1, 2, 'aceptada'), -- prueba1 envió una solicitud a prueba2 y fue aceptada
(2, 1, 'aceptada'); -- prueba2 envió una solicitud a prueba1 que está pendiente
