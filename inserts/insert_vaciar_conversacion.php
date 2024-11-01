<?php
    require_once '../conexion.php'; 
    session_start();

    if (isset($_POST['btn_vaciar_conversacion'])) {

        $sql = "DELETE FROM tbl_mensajes WHERE id_mensaje = id_conversacion";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, 'i');
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: ../interfaz.php");
        exit;
    }