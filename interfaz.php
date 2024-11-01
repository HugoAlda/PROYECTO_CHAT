<?php
    require_once 'conexion.php';
    session_start();
    
    $nombre_usuario = $_SESSION['nombre_usuario']; 
    $id_usuario = "";
    try {
        $sql = "SELECT id_usuario FROM tbl_usuarios WHERE nombre_usuario = ?";
        $stmt = mysqli_stmt_init($conexion);
        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $nombre_usuario); 
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $usuario = mysqli_fetch_assoc($result);
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
        }
    } catch (Exception $e) {
        echo "<br><h6>" . htmlspecialchars($e->getMessage()) . "</h6>";
        exit;
    }
    
    mysqli_autocommit($conexion, false);
    mysqli_begin_transaction($conexion, MYSQLI_TRANS_START_READ_WRITE);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./css/style-interfaz.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <title>CHAT</title>
</head>
<body>
    <div class="contenedor">
        <div class="solicitud">
            <nav class="navbar">
                <form action="" method="post">
                    <button type="submit" name="btn_amigos" class="navbar-brand btn-link">Amigos</button>
                </form>
                <form action="./destruir.php">
                    <button type="submit" class="navbar-brand btn-link">Cerrar Session</button>
                </form>
                <form action="" method="post">
                    <input type="submit" name="btn_agregar" value="+" id="btn_agregar">
                </form>  
            </nav>
                <div class="input-group">
                    <form action="" method="post" id="amigos-navbar">
                        <div class="input-group">
                            <div class="form-outline" data-mdb-input-init>
                                <input type="search" name="query_usuarios" id="form1" class="form-control" placeholder="Buscar usuarios...">
                            </div>
                            <button type="submit" name="btn_busqueda_usuarios" class="" data-mdb-ripple-init>
                                <i class="fa-solid fa-magnifying-glass" style="color: #ffffff;"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <?php       
                    if (isset($_POST['btn_busqueda_usuarios']) && !empty($_POST['query_usuarios'])) {
                        try {
                            $query_usuarios = mysqli_real_escape_string($conexion, $_POST['query_usuarios']);
                            $sql_usuarios = "SELECT * FROM tbl_usuarios WHERE nombre_usuario LIKE '%$query_usuarios%'";
                            $resultado = mysqli_query($conexion, $sql_usuarios);
                            $usuarios = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
                            if (count($usuarios) > 0) {
                                echo "<table class='table table-hover'>";
                                foreach ($usuarios as $usuario) {
                                    echo "<tr>
                                        <td>" . htmlspecialchars($usuario['nombre_usuario']) . "</td>
                                        <td>
                                            <form action='./inserts/insert_solicitud.php?id=" . $usuario['id_usuario'] . "' method='post'>
                                                <input type='submit' name='btn_solicitud' value='+'>
                                            </form>
                                        </td>
                                    </tr>";
                                }                            
                                echo "</table>";
                            } else {
                                echo "<br><h6>No se encontraron usuarios con ese nombre.</h6>";
                            }
                        } catch (Exception $e) {
                            echo "<br><h6>" . htmlspecialchars($e->getMessage()) . "</h6>";
                        }
                    }
                ?>
                <br><br>
                <h2>Solicitudes</h2>
                <?php       
                    try {
                        // Prepara la consulta con INNER JOIN
                        $sql_solicitudes = "SELECT u.nombre_persona, sa.*
                                            FROM tbl_solicitudes_amistad sa
                                            INNER JOIN tbl_usuarios u ON sa.id_solicitante = u.id_usuario
                                            WHERE sa.id_solicitado = ?";
                        
                        $stmt = mysqli_prepare($conexion, $sql_solicitudes);
                        echo $_SESSION['id_usuario']; 
                        if ($stmt) {
                            $id_solicitado = $_SESSION['id_usuario']; 
                            
                            mysqli_stmt_bind_param($stmt, "i", $id_solicitado);
                            mysqli_stmt_execute($stmt);
                            // Obtiene el resultado
                            $resultado = mysqli_stmt_get_result($stmt);
                            $solicitudes = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
                            if (count($solicitudes) > 0) {
                                echo "<table class='table table-hover'>";
                                foreach ($solicitudes as $solicitud) {
                                    echo "<tr>
                                        <td>" . htmlspecialchars($solicitud['nombre_persona']) . "</td>
                                        <td>
                                            <form action='./inserts/insert_solicitud.php?id=" . htmlspecialchars($solicitud['id_solicitante']) . "' method='post'>
                                                <input type='submit' name='btn_solicitud_update_ok' value='+'>
                                                <input type='submit' name='btn_solicitud_update_nok' value='-'>

                                            </form>
                                        </td>
                                    </tr>";
                                }                            
                                echo "</table>";
                            } else {
                                echo "<br><h6>No se encontraron solicitudes de amistad.</h6>";
                            }
                            mysqli_stmt_close($stmt);
                        }
                    } catch (Exception $e) {
                        echo "<br><h6>" . htmlspecialchars($e->getMessage()) . "</h6>";
                    }
                ?>
            <?php if (isset($_POST['mostrar_busqueda_amigos']) && $_SESSION['mostrar_busqueda_amigos']) { ?>
                <div class="input-group">
                    <form action="" method="post" id="agregar-navbar">
                        <div class="input-group">
                            <div class="form-outline" data-mdb-input-init>
                                <input type="search" name="amigos" id="form1" class="form-control" placeholder="Buscar amigos..." value="<?php echo isset($_POST['amigos']) ? htmlspecialchars($_POST['amigos']) : ''; ?>">
                            </div>
                            <button type="submit" name="amigos-busqueda" class="" data-mdb-ripple-init>
                                <i class="fa-solid fa-magnifying-glass" style="color: #ffffff;"></i>
                            </button>
                        </div>
                    </form>
                </div>
            <?php
            $amigos = []; // Inicializa el array para almacenar amigos

            // Manejo de la búsqueda de amigos
            // Código de búsqueda de amigos
            if (isset($_POST['amigos-busqueda'])) {
                try {
                    $amigoform = mysqli_real_escape_string($conexion, $_POST['amigos']);
                    $sql_busqueda_amigos = "SELECT u.id_usuario, u.nombre_usuario 
                                            FROM tbl_amigos a 
                                            INNER JOIN tbl_usuarios u ON a.id_amigo = u.id_usuario 
                                            WHERE u.nombre_usuario LIKE '%$amigoform%'";
                    $resultado = mysqli_query($conexion, $sql_busqueda_amigos);
                    
                    if (mysqli_num_rows($resultado) > 0) {
                        $amigos = mysqli_fetch_all($resultado, MYSQLI_ASSOC); 
                    } else {
                        echo "<br><h6>No se encontraron amigos con ese nombre.</h6>";
                    }

                } catch (Exception $e) {
                    echo "<br><h6>" . htmlspecialchars($e->getMessage()) . "</h6>";
                }
            } else {
                try {
                    $sql_lista_amigos = "SELECT u.id_usuario, u.nombre_usuario 
                                        FROM tbl_amigos a 
                                        INNER JOIN tbl_usuarios u ON a.id_amigo = u.id_usuario";
                    $consulta_lista_amigos = mysqli_query($conexion, $sql_lista_amigos);
                    $amigos = mysqli_fetch_all($consulta_lista_amigos, MYSQLI_ASSOC); 
                } catch (Exception $e) {
                    echo "<br><h6>" . htmlspecialchars($e->getMessage()) . "</h6>";
                }
            }

            // Mostrar amigos encontrados (filtrados o todos)
            if (count($amigos) > 0) {
                echo "<table class='table table-hover'>";
                foreach ($amigos as $amigo) {
                    // Añadir un enlace o un botón para seleccionar el amigo
                    echo "<tr><td><a href='?id_amigo=" . htmlspecialchars($amigo['id_usuario']) . "'>" . htmlspecialchars($amigo['nombre_usuario']) . "</a></td></tr>";   
                }
                echo "</table>";
            } else {
                echo "<br><h6>No hay amigos disponibles.</h6>";
            }
        ?>
        <?php } ?>
        </div>
        <div class="chat">
            <div>
                <div class="header">
                    <h1><?php echo $_SESSION['nombre_usuario']; ?></h1>
                    <form action="insert.php" method="POST">
                        <input type="submit" class="btn-del" name="btn_vaciar_conversacion" value="Vaciar Conversación">
                    </form>
                </div>
            </div>
            <div class="mensajes">
            <?php
                try {
                    // Paso 1: Obtener `id_conversacion` entre el usuario actual y el amigo
                    $sql_conversation = "SELECT id_conversacion 
                                        FROM tbl_conversaciones 
                                        WHERE (id_usuario_emisor = ? AND id_amigo_receptor = ?)
                                        OR (id_usuario_emisor = ? AND id_amigo_receptor = ?)";

                    $stmt_conv = mysqli_stmt_init($conexion);
                    if (mysqli_stmt_prepare($stmt_conv, $sql_conversation)) {
                        // Vincular parámetros
                        mysqli_stmt_bind_param($stmt_conv, "iiii", $id_usuario, $id_amigo, $id_amigo, $id_usuario);
                        mysqli_stmt_execute($stmt_conv);

                        // Obtener el resultado
                        $result_conv = mysqli_stmt_get_result($stmt_conv);
                        $conversation = mysqli_fetch_assoc($result_conv);

                        // Verificar si la conversación existe
                        if ($conversation) {
                            $id_conversacion = $conversation['id_conversacion'];
                            echo "<h1>ID de la Conversación: " . htmlspecialchars($id_conversacion) . "</h1>";

                            // Paso 2: Obtener los mensajes de la conversación
                            $sql_messages = "SELECT m.mensaje, m.fecha_envio, u.nombre_usuario
                                            FROM tbl_mensajes m
                                            INNER JOIN tbl_mensajes_conversacion mc ON m.id_mensaje = mc.id_mensaje
                                            INNER JOIN tbl_usuarios u ON mc.id_usuario = u.id_usuario
                                            WHERE mc.id_conversacion = ?
                                            ORDER BY m.fecha_envio";

                            $stmt_msg = mysqli_stmt_init($conexion);
                            if (mysqli_stmt_prepare($stmt_msg, $sql_messages)) {
                                mysqli_stmt_bind_param($stmt_msg, "i", $id_conversacion);
                                mysqli_stmt_execute($stmt_msg);
                                $result_msg = mysqli_stmt_get_result($stmt_msg);
                                $chat = mysqli_fetch_all($result_msg, MYSQLI_ASSOC);

                                // Mostrar mensajes
                                if (count($chat) > 0) {
                                    echo "<div class='chat-container'>";
                                    foreach ($chat as $mensaje) {
                                        echo "<div class='mensaje'>";
                                        echo "<div class='mensaje-texto'>" . htmlspecialchars($mensaje['mensaje']) . "</div>";
                                        echo "<div class='mensaje-informacion'>" . htmlspecialchars($mensaje['nombre_usuario']) . " - " . htmlspecialchars($mensaje['fecha_envio']) . "</div>";
                                        echo "</div>";
                                    }
                                    echo "</div>";
                                } else {
                                    echo "<div id='vacio'><div>No hay mensajes en esta conversación.</div></div>";
                                }
                            }
                        } else {
                            echo "<div id='vacio'><div>No hay conversación entre estos usuarios.</div></div>";
                        }
                    }

                    // Confirmar la transacción
                    mysqli_commit($conexion);

                } catch (Exception $e) {
                    // En caso de error, hacer rollback de la transacción
                    mysqli_rollback($conexion);
                    echo "<br><h6>Error: " . htmlspecialchars($e->getMessage()) . "</h6>";
                }
                ?>
            </div>
            <div>
                <form action="./inserts/insert_mensaje.php" method="post">
                    <input type="hidden" name="id_amigo" value="<?php echo isset($_GET['id_amigo']) ? $_GET['id_amigo'] : ''; ?>">                    
                    <input type="text" name="mensaje" placeholder="Escribe un mensaje...">
                    <button type="submit" name="btn_mensaje">Enviar</button>
                </form>
            </div>
        </div>
    </div>

    <?php
    mysqli_commit($conexion);
    ?>
</body>
</html>
