<?php
session_start(); // Inicia la sesión al principio del archivo

// Controlar la visibilidad de los formularios de búsqueda
if (isset($_POST['btn_agregar'])) {
    $_SESSION['mostrar_busqueda'] = true;
    $_SESSION['mostrar_busqueda_amigos'] = false;  
} elseif (isset($_POST['btn_amigos'])) {
    $_SESSION['mostrar_busqueda_amigos'] = true; 
    $_SESSION['mostrar_busqueda'] = false;
}

require_once 'conexion.php';

// Desactivar el autocommit
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
            <nav class="navbar navbar-light justify-content-between">
                <form action="" method="post">
                    <h4 class="navbar-brand btn-link"><?php echo $_SESSION['nombre_usuario']; ?></h4>
                    <button type="submit" name="btn_amigos" class="navbar-brand btn-link">Amigos</button>
                    <input type="submit" name="btn_agregar" value="+" id="btn_agregar">
                </form>  
            </nav>

            <!-- Búsqueda de Usuarios -->
            <?php if (isset($_SESSION['mostrar_busqueda']) && $_SESSION['mostrar_busqueda']) { ?>
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
                    // Solo ejecuta la búsqueda si el botón ha sido presionado y hay un término de búsqueda
                    if (isset($_POST['btn_busqueda_usuarios']) && !empty($_POST['query_usuarios'])) {
                        try {
                            $query_usuarios = mysqli_real_escape_string($conexion, $_POST['query_usuarios']);
                            $sql_usuarios = "SELECT * FROM tbl_usuarios WHERE nombre_usuario LIKE '%$query_usuarios%'";
                            $resultado = mysqli_query($conexion, $sql_usuarios);
                            if (!$resultado) {
                                throw new Exception("Error en la consulta: " . mysqli_error($conexion));
                            }

                            $usuarios = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
                            if (count($usuarios) > 0) {
                                echo "<table class='table table-hover'>";
                                foreach ($usuarios as $usuario) {
                                    echo "<tr><td>" . htmlspecialchars($usuario['nombre_usuario']) . "</td></tr>";
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
            <?php } ?>

            <!-- Búsqueda de Amigos -->
            <?php if (isset($_SESSION['mostrar_busqueda_amigos']) && $_SESSION['mostrar_busqueda_amigos']) { ?>
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
                if (isset($_POST['amigos-busqueda'])) {
                    try {
                        $amigoform = mysqli_real_escape_string($conexion, $_POST['amigos']);
                        $sql_busqueda_amigos = "SELECT u.nombre_usuario 
                                                FROM tbl_amigos a 
                                                INNER JOIN tbl_usuarios u ON a.id_amigo = u.id_usuario 
                                                WHERE u.nombre_usuario LIKE '%$amigoform%'";
                        $resultado = mysqli_query($conexion, $sql_busqueda_amigos);
                        
                        if (!$resultado) {
                            throw new Exception("Error en la consulta: " . mysqli_error($conexion));
                        }
                        
                        // Solo almacenar amigos encontrados si hay resultados
                        if (mysqli_num_rows($resultado) > 0) {
                            while ($amigo = mysqli_fetch_assoc($resultado)) {
                                $amigos[] = $amigo; // Almacena amigos encontrados
                            }
                        } else {
                            echo "<br><h6>No se encontraron amigos con ese nombre.</h6>";
                        }
                    
                    } catch (Exception $e) {
                        echo "<br><h6>" . htmlspecialchars($e->getMessage()) . "</h6>";
                    }
                } else {
                    // Si no se está buscando, obtener todos los amigos
                    try {
                        $sql_lista_amigos = "SELECT u.nombre_usuario 
                                             FROM tbl_amigos a 
                                             INNER JOIN tbl_usuarios u ON a.id_amigo = u.id_usuario";
                        
                        $consulta_lista_amigos = mysqli_query($conexion, $sql_lista_amigos);
                        while ($amigo = mysqli_fetch_assoc($consulta_lista_amigos)) {
                            $amigos[] = $amigo; // Almacena todos los amigos
                        }
                    } catch (Exception $e) {
                        echo "<br><h6>" . htmlspecialchars($e->getMessage()) . "</h6>";
                    }
                }

                // Mostrar amigos encontrados (filtrados o todos)
                if (count($amigos) > 0) {
                    echo "<table class='table table-hover'>";
                    foreach ($amigos as $amigo) {
                        echo "<tr><td>" . htmlspecialchars($amigo['nombre_usuario']) . "</td></tr>";   
                    }
                    echo "</table>";
                } else {
                    echo "<br><h6>No hay amigos disponibles.</h6>";
                }
                ?>
            <?php } ?>
        </div>

        <!-- Sección del chat -->
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
                        $sql = "SELECT mensaje, fecha_envio FROM tbl_mensajes";
                        $resultado = mysqli_query($conexion, $sql);
                        
                        if (!$resultado) {
                            throw new Exception("Error en la consulta: " . mysqli_error($conexion));
                        }

                        $chat = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
                        if (count($chat) > 0) {
                            echo "<div class='chat-container'>";
                            foreach ($chat as $mensaje) {
                                echo "<div class='mensaje'>";
                                echo "<div class='mensaje-texto'>" . htmlspecialchars($mensaje['mensaje']);
                                echo "<div class='mensaje-informacion'>" . htmlspecialchars($mensaje['fecha_envio']) . "</div>";
                                echo "</div></div>";
                            }
                            echo "</div>";
                        } else {
                            echo "<div id='vacio'><div>No hay mensajes en esta conversación.</div></div>";
                        }
                    } catch (Exception $e) {
                        echo "<br><h6>" . htmlspecialchars($e->getMessage()) . "</h6>";
                    }
                ?>
            </div>
            <div>
                <form action="insert.php" method="post">
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