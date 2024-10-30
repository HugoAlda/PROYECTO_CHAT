<?php
require_once '../conexion.php';
session_start();

if (isset($_POST['btn_agregar_usuario'])) {
    
    // Usuario que solicita amistad, usando el ID almacenado en la sesión
    $id_usuarioa = $_SESSION['id_usuario'];
    $id_usuariob = $_GET['id'];
    echo $_SESSION['id_usuario'];
    try {
        $sql = "INSERT INTO tbl_amigos (id_usuarioa, id_usuariob) VALUES (?, ?)";
        $stmt = mysqli_prepare($conexion, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ii", $id_usuarioa, $id_usuariob);
            mysqli_stmt_execute($stmt);
            
            echo "Solicitud de amistad enviada.";
            
            // Cierra el statement
            mysqli_stmt_close($stmt);
        }
        
        // Redirige al usuario a interfaz.php después de enviar la solicitud de amistad
        header("Location: ../interfaz.php");
        exit;

    } catch (Exception $e) {
        echo "Error al enviar la solicitud de amistad: " . $e->getMessage();
    }
}
?>
