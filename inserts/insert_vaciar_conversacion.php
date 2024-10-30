<?php
require_once '../conexion.php'; 
session_start();

    if (isset($_POST['btn_vaciar_conversacion'])) {
        $id_mensaje = $_POST['id_mensaje'];
    
        $sql_delete_dependientes = "DELETE FROM tbl_mensajes WHERE id_conversacion = ?";
        $stmt = mysqli_prepare($conexion, $sql_delete_dependientes);
        mysqli_stmt_bind_param($stmt, "i", $id_mensaje);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }