<?php
    require_once 'conexion.php';

    session_start();

    // Verificar si la sesión está iniciada
    if (!isset($_SESSION['nombre_usuario'])) {
        header("Location: index.php?session_no_iniciada");
        exit();
    }

    // Verificar si el usuario existe en la base de datos
    $nombre_usuario = $_SESSION['nombre_usuario'];
    $sql = "SELECT id_usuario FROM tbl_usuarios WHERE nombre_usuario = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "s", $nombre_usuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($resultado) === 0) {
        header("Location: destruir.php");
        exit();
    }

    mysqli_autocommit($conexion, false);
    mysqli_begin_transaction($conexion, MYSQLI_TRANS_START_READ_WRITE);

    try {
        // Obtener el ID del usuario
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
                <div class="navbar-div">
                    <form action="" method="post">
                        <button type="submit" name="btn_amigos" class="navbar-brand btn-link">Amigos</button>
                        <input type="submit" name="btn_agregar" value="Añadir Amigos" id="btn_agregar">
                    </form>
                    <form action="./destruir.php" id="form-cerrar">
                        <button type="submit" class="navbar-brand btn-link">Cerrar Sesión</button>
                    </form>
                </div>
            </nav>
            <?php if (isset($_POST['btn_agregar']) || isset($_POST['btn_busqueda_usuarios'])) { ?>
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
                            $sql_usuarios = "SELECT * FROM tbl_usuarios WHERE nombre_usuario LIKE '%$query_usuarios%' OR nombre_persona LIKE '%$query_usuarios%'";
                            $resultado = mysqli_query($conexion, $sql_usuarios);
                            $usuarios = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
                            if (count($usuarios) > 0) {
                                echo "<table>";
                                foreach ($usuarios as $usuario) {
                                    // Verificar si ya son amigos
                                    $id_usuario_actual = $_SESSION['id_usuario'];
                                    $id_usuario_solicitado = $usuario['id_usuario'];

                                    $sql_amigos = "SELECT * FROM tbl_amigos WHERE (id_usuarioa = ? AND id_usuariob = ?) OR (id_usuariob = ? AND id_usuarioa = ?)";
                                    $stmt_amigos = mysqli_prepare($conexion, $sql_amigos);
                                    mysqli_stmt_bind_param($stmt_amigos, "iiii", $id_usuario_actual, $id_usuario_solicitado, $id_usuario_actual, $id_usuario_solicitado);
                                    mysqli_stmt_execute($stmt_amigos);
                                    $resultado_amigos = mysqli_stmt_get_result($stmt_amigos);

                                    // Solo mostrar el botón si no son amigos
                                    if (mysqli_num_rows($resultado_amigos) === 0) {
                                        echo "<tr>
                                            <td>" . htmlspecialchars($usuario['nombre_usuario']) . "</td>
                                            <td>
                                                <form class='tr-solicitud' action='./inserts/insert_solicitud.php?id=" . $usuario['id_usuario'] . "' method='post'>
                                                    <input type='submit' name='btn_solicitud' value='+' id='btn_agregar'>
                                                </form>
                                            </td>
                                        </tr>";
                                    } else {
                                        echo "<tr>
                                            <td>" . htmlspecialchars($usuario['nombre_usuario']) . " | Ya son amigos |</td>
                                            <td></td>
                                        </tr>";
                                    }
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
                        if ($stmt) {
                            $id_solicitado = $_SESSION['id_usuario']; 
                            
                            mysqli_stmt_bind_param($stmt, "i", $id_solicitado);
                            mysqli_stmt_execute($stmt);
                            // Obtiene el resultado
                            $resultado = mysqli_stmt_get_result($stmt);
                            $solicitudes = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
                            if (count($solicitudes) > 0) {
                                echo "<br><table>";
                                foreach ($solicitudes as $solicitud) {
                                    echo "<tr>
                                        <td>" . htmlspecialchars($solicitud['nombre_persona']) . "</td>
                                        <td>
                                            <form class='tr-solicitud' action='./inserts/insert_solicitud.php?id=" . htmlspecialchars($solicitud['id_solicitante']) . "' method='post'>
                                                <input type='submit' name='btn_solicitud_update_ok' value='+' id='btn_agregar'>
                                                <input type='submit' name='btn_solicitud_update_nok' value='-' id='btn_agregar'>
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
            <?php } else { ?>
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

            // Busqueda de amigos
            if (isset($_POST['amigos-busqueda'])) {
                try {
                    $amigoform = mysqli_real_escape_string($conexion, $_POST['amigos']);
                    $id_usuario_actual = $_SESSION['id_usuario'];
                    $sql_busqueda_amigos = "SELECT u.id_usuario, u.nombre_usuario, u.nombre_persona 
                                            FROM tbl_amigos a 
                                            INNER JOIN tbl_usuarios u ON 
                                                (a.id_usuarioa = u.id_usuario AND a.id_usuariob = $id_usuario_actual) 
                                                OR (a.id_usuariob = u.id_usuario AND a.id_usuarioa = $id_usuario_actual)
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
                    $id_usuario = $_SESSION['id_usuario'];
                    $sql_lista_amigos = "SELECT u.id_usuario, u.nombre_usuario
                                         FROM tbl_amigos a 
                                         INNER JOIN tbl_usuarios u 
                                         ON ((a.id_usuarioa = $id_usuario AND a.id_usuariob = u.id_usuario) 
                                         OR (a.id_usuariob = $id_usuario AND a.id_usuarioa = u.id_usuario)) 
                                         WHERE u.id_usuario != $id_usuario";
                    
                    $consulta_lista_amigos = mysqli_query($conexion, $sql_lista_amigos);
                    $amigos = mysqli_fetch_all($consulta_lista_amigos, MYSQLI_ASSOC);
                } catch (Exception $e) {
                    echo "<br><h6>" . htmlspecialchars($e->getMessage()) . "</h6>";
                }
                
            }

            // Mostrar amigos encontrados (filtrados o todos)
            if (count($amigos) > 0) {
                echo "<table>";
                foreach ($amigos as $amigo) {
                    echo "<tr>
                        <td>
                            <a href='?id_amigo=" . htmlspecialchars($amigo['id_usuario']) . "'>" . htmlspecialchars($amigo['nombre_usuario']) . "</a>
                        </td>
                        <td style='text-align: right;'>
                            <form method='POST' action='./inserts/borrar_amigo.php?id_amigo=" . htmlspecialchars($amigo['id_usuario']) . "'>
                                <input type='submit' name='btn_eliminar_amigo' value='Eliminar' class='btn-del'>
                            </form>
                        </td>
                    </tr>";
                }
                echo "</table>";
            } else {
                echo "<br><h6>No hay amigos disponibles.</h6>";
            }
            
        ?>
        <?php } ?>
        </div>
        
        <div class="chat">
            <?php if (isset($_GET['id_amigo']) && !empty($_GET['id_amigo'])) { 
                // Obtener el nombre del amigo seleccionado
                $id_amigo = $_GET['id_amigo'];
                $sql_nombre_amigo = "SELECT nombre_usuario 
                                     FROM tbl_usuarios 
                                     WHERE id_usuario = ?";
                $stmt_nombre_amigo = mysqli_prepare($conexion, $sql_nombre_amigo);
                mysqli_stmt_bind_param($stmt_nombre_amigo, "i", $id_amigo);
                mysqli_stmt_execute($stmt_nombre_amigo);
                
                $resultado_nombre_amigo = mysqli_stmt_get_result($stmt_nombre_amigo);
                $amigo = mysqli_fetch_assoc($resultado_nombre_amigo);
                $nombre_amigo = htmlspecialchars($amigo['nombre_usuario']);
            ?>
            <div>
                <div class="header">
                    <h1><?php echo $nombre_amigo; ?></h1>
                    <form action="./inserts/insert_vaciar_conversacion.php" method="POST">
                        <input type="hidden" name="id_amigo" value="<?php echo $id_amigo; ?>">                    
                        <input type="submit" class="btn-del" name="btn_vaciar_conversacion" value="Vaciar Conversación">
                    </form>
                </div>
            </div>
            <div class="mensajes">
                <?php
                    try {
                        // Iniciar la transacción
                        mysqli_begin_transaction($conexion);
                    
                        // Consulta SQL para obtener los mensajes de la conversación entre dos usuarios
                        $sql_chat = "SELECT m.mensaje, m.fecha_envio, u.nombre_usuario, m.id_usuario
                                     FROM tbl_mensajes m
                                     INNER JOIN tbl_conversaciones c ON c.id_conversacion = m.id_conversacion
                                     INNER JOIN tbl_usuarios u ON m.id_usuario = u.id_usuario
                                     WHERE (c.id_usuarioa = ? AND c.id_usuariob = ?)
                                     OR (c.id_usuarioa = ? AND c.id_usuariob = ?)
                                     ORDER BY m.fecha_envio";
    
                        // Preparar la consulta
                        $stmt_chat = mysqli_stmt_init($conexion);
                        if (mysqli_stmt_prepare($stmt_chat, $sql_chat)) {
                            // Obtener los IDs del usuario actual y del amigo
                            $id_usuario = $_SESSION['id_usuario'];
                        
                            mysqli_stmt_bind_param($stmt_chat, "iiii", $id_usuario, $id_amigo, $id_amigo, $id_usuario);
                            mysqli_stmt_execute($stmt_chat);
                            $result_chat = mysqli_stmt_get_result($stmt_chat);
                            $chat = mysqli_fetch_all($result_chat, MYSQLI_ASSOC);
                        
                            // Mostrar los mensajes intercalados
                            if (count($chat) > 0) {
                                echo "<div class='chat-container'>";
                                foreach ($chat as $mensaje) {
                                    // Determinar si el mensaje es del usuario actual o del amigo
                                    if ($mensaje['id_usuario'] == $_SESSION['id_usuario']) {
                                        // Mensaje del usuario actual
                                        echo "<br><div class='mensaje mio'>";
                                            echo "<div class='mensaje-texto'>" . htmlspecialchars($mensaje['mensaje']) . "</div>";
                                            echo "<div class='mensaje-informacion'>" . htmlspecialchars($mensaje['nombre_usuario']) . " - " . htmlspecialchars($mensaje['fecha_envio']) . "</div>";
                                        echo "</div>";
                                    } else if ($mensaje['id_usuario'] == $id_amigo) {
                                        // Mensaje del amigo
                                        echo "<br><div class='mensaje amigo'>";
                                            echo "<div class='mensaje-texto'>" . htmlspecialchars($mensaje['mensaje']) . "</div>";
                                            echo "<div class='mensaje-informacion'>" . htmlspecialchars($mensaje['nombre_usuario']) . " - " . htmlspecialchars($mensaje['fecha_envio']) . "</div>";
                                        echo "</div>";
                                    }
                                }
                                echo "</div>";
                            } else {
                                echo "<div id='vacio'><div>No hay mensajes en esta conversación.</div></div>";
                            }
                        } else {
                            echo "<div id='vacio'><div>Error en la consulta de mensajes.</div></div>";
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
                    <input type="hidden" name="id_amigo" value="<?php echo $id_amigo; ?>">                    
                    <input type="text" name="mensaje" placeholder="Escribe un mensaje...">
                    <button type="submit" name="btn_mensaje">Enviar</button>
                </form>
            </div>
            <?php } else { ?>
                <div id="default-chat"><div>No has seleccionado a ningún amigo para chatear.</div></div>
            <?php } ?>
        </div>
    </div>

    <?php
        mysqli_commit($conexion);
    ?>
</body>
</html>