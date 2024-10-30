<?php
require_once '../conexion.php'; 
session_start();
    
    if (isset($_POST['btn_mensaje']) && isset($_POST['id_amigo'])) {
        $mensaje = htmlspecialchars($_POST['mensaje']);
        $id_amigo = $_POST['id_amigo'];
        $id_usuario_actual = $_SESSION['id_usuar io'];
        mysqli_autocommit($conexion, false); 
        try {
            // Consulta para verificar o crear una conversación
            $sql_conversacion = "SELECT id_conversacion 
                                 FROM tbl_conversaciones 
                                 WHERE (id_usuarioa = ? AND id_usuariob = ?)
                                    OR (id_usuarioa = ? AND id_usuariob = ?)";
            $stmt_conv = mysqli_prepare($conexion, $sql_conversacion);
            mysqli_stmt_bind_param($stmt_conv, "iiii", $id_usuario_actual, $id_amigo, $id_amigo, $id_usuario_actual);
            mysqli_stmt_execute($stmt_conv);
            $resultado_conv = mysqli_stmt_get_result($stmt_conv);
            $conversacion = mysqli_fetch_assoc($resultado_conv);
            
            if ($conversacion) {
                $id_conversacion = $conversacion['id_conversacion'];
            } else {
                // Insertar una nueva conversación si no existe
                $sql_insert_conversacion = "INSERT INTO tbl_conversaciones (id_usuarioa, id_usuariob) VALUES (?, ?)";
                $stmt_insert_conv = mysqli_prepare($conexion, $sql_insert_conversacion);
                mysqli_stmt_bind_param($stmt_insert_conv, "ii", $id_usuario_actual, $id_amigo);
                mysqli_stmt_execute($stmt_insert_conv);
                $id_conversacion = mysqli_insert_id($conexion); 
            }
        
            // Insertar el mensaje en tbl_mensajes
            $sql_insert_mensaje = "INSERT INTO tbl_mensajes (id_conversacion, mensaje, fecha_envio) VALUES (?, ?, NOW())";
            $stmt_insert_msg = mysqli_prepare($conexion, $sql_insert_mensaje);
            mysqli_stmt_bind_param($stmt_insert_msg, "is", $id_conversacion, $mensaje);
            mysqli_stmt_execute($stmt_insert_msg);
        
            // Confirmar la transacción
            mysqli_commit($conexion);
            echo "Mensaje enviado correctamente.";
            header("Location: interfaz.php?id_amigo=" . $id_amigo);
            exit();
        } catch (Exception $e) {
            mysqli_rollback($conexion);
            echo "Error al enviar el mensaje: " . $e->getMessage();
        }
    
        // Cerrar las sentencias después de completar la transacción o si hubo error
        mysqli_stmt_close($stmt_conv);
        if (isset($stmt_insert_conv)) mysqli_stmt_close($stmt_insert_conv);
        if (isset($stmt_insert_msg)) mysqli_stmt_close($stmt_insert_msg);
    } else {
        echo "Error: No se recibió el ID del amigo.";
    }