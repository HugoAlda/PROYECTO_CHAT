<?php
require_once '../conexion.php'; 
session_start();
    
if (isset($_POST['btn_mensaje']) && isset($_POST['id_amigo'])) {
    $mensaje = htmlspecialchars($_POST['mensaje']);
    $id_amigo = $_POST['id_amigo'];
    $id_usuario_actual = $_SESSION['id_usuario'];
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
            // Si existe la conversación, obtener su id
            $id_conversacion = $conversacion['id_conversacion'];
        } else {
            // Si no existe la conversación, crearla
            $sql_insert_conversacion = "INSERT INTO tbl_conversaciones (id_usuarioa, id_usuariob, fecha_inicio) VALUES (?, ?, NOW())";
            $stmt_insert_conv = mysqli_prepare($conexion, $sql_insert_conversacion);
            mysqli_stmt_bind_param($stmt_insert_conv, "ii", $id_usuario_actual, $id_amigo);
            mysqli_stmt_execute($stmt_insert_conv);
            $id_conversacion = mysqli_insert_id($conexion);  // Obtener el id de la conversación recién creada
        }

        // Insertar el mensaje en tbl_mensajes, incluyendo id_usuario para identificar al remitente
        $sql_insert_mensaje = "INSERT INTO tbl_mensajes (id_conversacion, id_usuario, mensaje, fecha_envio) VALUES (?, ?, ?, NOW())";
        $stmt_insert_msg = mysqli_prepare($conexion, $sql_insert_mensaje);
        mysqli_stmt_bind_param($stmt_insert_msg, "iis", $id_conversacion, $id_usuario_actual, $mensaje);
        mysqli_stmt_execute($stmt_insert_msg);
    
        // Confirmar la transacción
        mysqli_commit($conexion);
        header("Location: ../interfaz.php?id_amigo=" . $id_amigo);
        exit();
    } catch (Exception $e) {
        // En caso de error, hacer rollback de la transacción
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
?>
