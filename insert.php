<?php
    require_once 'conexion.php'; // Asegúrate de que la conexión a la base de datos esté configurada correctamente.

    if (isset($_POST['btn_mensaje'])) {
        // Obtener el mensaje del formulario
        $mensaje = htmlspecialchars($_POST['mensaje']);
        mysqli_autocommit($conexion, false);
        mysqli_begin_transaction($conexion, MYSQLI_TRANS_START_READ_WRITE);
        // Preparar la consulta para insertar el mensaje
        $sql = "INSERT INTO tbl_mensajes (mensaje) VALUES (?)";

        // Preparar la consulta
        $stmt = mysqli_prepare($conexion, $sql);

        // Vincular el parámetro
        mysqli_stmt_bind_param($stmt, "s", $mensaje);
        mysqli_stmt_execute($stmt);
        mysqli_commit($conexion);

        // Ejecutar la consulta
        if (mysqli_stmt_execute($stmt)) {
            echo "Mensaje enviado correctamente.";
            header("Location: interfaz.php"); // Redireccionar a la página principal después de enviar el mensaje
        } else {
            echo "Error al enviar el mensaje.";
        }

        // Cerrar la sentencia
        mysqli_stmt_close($stmt);
    }
    if (isset($_POST['btn_crear_cuenta'])) {
        // Obtener los datos de los inputs del formulario
        $usuario = htmlspecialchars($_POST['usuario']);
        $nombre = htmlspecialchars($_POST['nombre']);
        $correo = htmlspecialchars($_POST['correo']);
        $passwd = htmlspecialchars($_POST['passwd']);
        try {
            mysqli_autocommit($conexion, false);
            mysqli_begin_transaction($conexion, MYSQLI_TRANS_START_READ_WRITE);
    
            // Preparar la consulta para insertar el mensaje
            $sql = "INSERT INTO tbl_usuarios (nombre_usuario, nombre_persona, correo_usuario, passwd_usuario) VALUES (?,?,?,?)";
            
            // Preparar la consulta
            $stmt = mysqli_stmt_init($conexion);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                throw new Exception(mysqli_error($conexion));
            }
    
            // Vincular el parámetro
            mysqli_stmt_bind_param($stmt, "ssss", $usuario, $nombre, $correo, $passwd);
    
            // Ejecutar la consulta
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception(mysqli_error($conexion));
            }
    
            // Cerrar la sentencia
            mysqli_stmt_close($stmt);
    
            // Confirmar la transacción
            mysqli_commit($conexion);
            // Redireccionar a index.php
            header("Location: index.php");
            exit();
    
        } catch (Exception $e) {
            // Manejar cualquier excepción lanzada y mostrar el mensaje de error
            mysqli_rollback($conexion);
            echo "Se produjo un error: " . $e->getMessage();
        } 
        mysqli_close($conexion); // Cerrar la conexión al final
    }
    

    if (isset($_POST['btn_iniciar'])) {
        
        session_start(); // Iniciar la sesión al principio

        // Verificar si se ha enviado el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener el nombre de usuario y la contraseña del formulario
            $usuario = htmlspecialchars($_POST['nombre_usuario']);
            $passwd_usuario = htmlspecialchars($_POST['passwd_usuario']);
        
            // Verificar que los campos no estén vacíos
            if (empty($usuario) || empty($passwd_usuario)) {
                echo "<p style='color:red;'>Por favor completa ambos campos.</p>";
                exit();
            }
        
            // Consulta para seleccionar el usuario basado en el nombre de usuario
            $sql = "SELECT nombre_usuario, correo_usuario, passwd_usuario FROM tbl_usuarios WHERE nombre_usuario = ?";
        
            // Preparar la consulta
            $stmt = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($stmt, "s", $usuario);
        
            // Ejecutar la consulta
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        
            // Verificar si se encontró un usuario
            if ($usuario_db = mysqli_fetch_assoc($result)) {
                // Verificar la contraseña
                if ($passwd_usuario === $usuario_db['passwd_usuario']) {
                    $_SESSION['nombre_usuario'] = $usuario_db['nombre_usuario'];
                    header("Location: interfaz.php");
                    exit();
                } else {
                    echo "<p style='color:red;'>Contraseña incorrecta.</p>";
                }
            } else {
                echo "<p style='color:red;'>Usuario no encontrado.</p>";
            }
            
            // Cerrar la sentencia
            mysqli_stmt_close($stmt);
        }

        // Cerrar la conexión a la base de datos
        mysqli_close($conexion);

    }

    if (isset($_POST['btn_vaciar_conversacion'])) { // Asegúrate que el nombre del botón coincida
        // Paso 1: Eliminar los mensajes de las conversaciones
        $sql = "DELETE FROM tbl_mensajes_conversaciones WHERE id_conversacion = ?"; // Asegúrate de que tienes el ID de conversación
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_conversacion); // Cambia $id_conversacion según necesites
        mysqli_stmt_execute($stmt);
            
        // Paso 2: Eliminar los mensajes
        $sql = "DELETE FROM tbl_mensajes"; // Cambia <condición> según necesites
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_execute($stmt);
            
        // Paso 3: Finalmente, eliminar la conversación si es necesario
        $sql = "DELETE FROM tbl_conversaciones WHERE id_conversacion = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_conversacion);
        mysqli_stmt_execute($stmt);
            
        // Cerrar la conexión
        mysqli_stmt_close($stmt);
        mysqli_close($conexion);

        header("Location: interfaz.php");
        exit();
    }