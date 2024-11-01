<?php // archivo insert_vaciar_conversaion.php 
// archivo insert_vaciar_conversacion.php
require_once '../conexion.php'; 
session_start();

if (isset($_POST['btn_vaciar_conversacion'])) {
    $id_usuario = $_SESSION['id_usuario'];
    $id_amigo = isset($_POST['id_amigo']) ? $_POST['id_amigo'] : null;

    if ($id_amigo) {
        // Comienza la transacción
        mysqli_autocommit($conexion, false);

        try {
            // Seleccionar el `id_conversacion` correspondiente a esta conversación entre el usuario y el amigo
            $sql_select = "SELECT id_conversacion 
                           FROM tbl_conversaciones 
                           WHERE (id_usuarioa = ? AND id_usuariob = ?) 
                           OR (id_usuarioa = ? AND id_usuariob = ?)";
                           
            $stmt_select = mysqli_prepare($conexion, $sql_select);
            mysqli_stmt_bind_param($stmt_select, 'iiii', $id_usuario, $id_amigo, $id_amigo, $id_usuario);
            mysqli_stmt_execute($stmt_select);
            mysqli_stmt_bind_result($stmt_select, $id_conversacion);
            mysqli_stmt_fetch($stmt_select);
            mysqli_stmt_close($stmt_select);

            // Verifica que se haya obtenido un id_conversacion válido
            if ($id_conversacion) {
                // Elimina todos los mensajes asociados a `id_conversacion`
                $sql_delete = "DELETE FROM tbl_mensajes WHERE id_conversacion = ?";
                $stmt_delete = mysqli_prepare($conexion, $sql_delete);
                mysqli_stmt_bind_param($stmt_delete, 'i', $id_conversacion);
                mysqli_stmt_execute($stmt_delete);
                mysqli_stmt_close($stmt_delete);

                // Confirma la transacción
                mysqli_commit($conexion);
                echo "La conversación ha sido vaciada exitosamente.";
            } else {
                echo "No se encontró la conversación.";
            }
        } catch (Exception $e) {
            // Rollback en caso de error
            mysqli_rollback($conexion);
            echo "Error al vaciar la conversación: " . htmlspecialchars($e->getMessage());
        }
    } else {
        echo "ID de amigo no válido.";
    }

    // Redirigir a la interfaz después de completar la acción
    header("Location: ../interfaz.php");
    exit;
} else {
    header("Location: error.html");
    exit;
}   

/*
        $sql = "DELETE FROM tbl_mensajes WHERE id_mensaje = $id_conversacion";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $id_conversacion);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
*/

