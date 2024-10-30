-- Inserciones en la tabla tbl_usuarios
INSERT INTO tbl_usuarios (nombre_usuario, nombre_persona, correo_usuario, passwd_usuario) 
VALUES
    ('Hugo', 'Hugo Alda', 'hugo.alda@gmail.com', '1234'),
    ('Beto', 'Roberto Noble', 'roberto.noble@gmail.com', '1234');

-- Inserciones en la tabla tbl_amigos
-- Insertar amistad entre usuarios
INSERT INTO tbl_amigos (id_usuarioa, id_usuariob) VALUES (1, 2);

-- Inserciones en la tabla tbl_conversaciones
-- Insertar conversación entre Hugo y Roberto
INSERT INTO tbl_conversaciones (id_usuarioa, id_usuariob) VALUES (1, 2);

-- Inserciones en la tabla tbl_mensajes
INSERT INTO tbl_mensajes (id_conversacion, mensaje) VALUES(1, 'Hola, ¿cómo estás?'),(1, 'Estoy bien, gracias. ¿Y tú?');

-- Inserciones en la tabla tbl_solicitudes_amistad
-- Hugo envió una solicitud de amistad a Roberto que fue aceptada
INSERT INTO tbl_solicitudes_amistad (id_solicitante, id_solicitado, estado) VALUES(1, 2, 'aceptada');  -- solicitud de Hugo a Roberto, aceptada