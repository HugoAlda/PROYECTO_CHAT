<?php
require_once '../conexion.php'; 
session_start();

if (isset($_POST['btn_iniciar'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $usuario = isset($_POST['nombre_usuario']) ? mysqli_real_escape_string($conexion, htmlspecialchars(trim($_POST['nombre_usuario']))) : '';
        $passwd_usuario = isset($_POST['passwd_usuario']) ? mysqli_real_escape_string($conexion, htmlspecialchars(trim($_POST['passwd_usuario']))) : '';        
        $_SESSION['nombre_usuario'] = $usuario;

        if (empty($usuario) || empty($passwd_usuario)) {
            header('Location:../index.php?error=campos_vacios');
            exit();
        }

        try {
            mysqli_autocommit($conexion, false);
            mysqli_begin_transaction($conexion, MYSQLI_TRANS_START_READ_WRITE);
            
            $sql = "SELECT id_usuario, nombre_usuario, passwd_usuario FROM tbl_usuarios WHERE nombre_usuario = ?";
            $stmt = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($stmt, "s", $usuario);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($usuario_db = mysqli_fetch_assoc($result)) {
                // Verificar la contraseña encriptada
                if (password_verify($passwd_usuario, $usuario_db['passwd_usuario'])) {
                    $_SESSION['nombre_usuario'] = $usuario_db['nombre_usuario'];
                    header("Location: ../interfaz.php");
                    exit();
                } else {
                    header('Location:../index.php?error=contrasena_incorrecta');
                }
            } else {
                header('Location:../index.php?error=usuario_no_encontrado');
            }

            mysqli_stmt_close($stmt);
            mysqli_commit($conexion);
        } catch (Exception $e) {
            mysqli_rollback($conexion);
            echo "Se produjo un error: " . htmlspecialchars($e->getMessage());
        }
    }
    mysqli_close($conexion);
}
