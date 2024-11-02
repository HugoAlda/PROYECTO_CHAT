<?php
require_once '../conexion.php'; 
session_start();  

if (isset($_POST['btn_crear_cuenta'])) {
    // Sanitizar entradas de usuario
    $usuario = isset($_POST['usuario']) ? mysqli_real_escape_string($conexion, htmlspecialchars(trim($_POST['usuario']))) : '';
    $nombre = isset($_POST['nombre']) ? mysqli_real_escape_string($conexion, htmlspecialchars(trim($_POST['nombre']))) : '';
    $correo = isset($_POST['correo']) ? mysqli_real_escape_string($conexion, htmlspecialchars(trim($_POST['correo']))) : '';
    $passwd = isset($_POST['passwd']) ? mysqli_real_escape_string($conexion, htmlspecialchars(trim($_POST['passwd']))) : '';

    // Validaciones
    if (empty($usuario) || empty($passwd) || empty($nombre) || empty($correo)) {
        header('Location: ../crear.php?error=campos_vacios');
        exit();
    }
    if (strlen($usuario) < 3 || !preg_match('/^[a-zA-Z]+[a-zA-Z0-9]*$/', $usuario)) {
        header('Location: ../crear.php?error=usuario_invalido');
        exit();
    }
    if (strlen($nombre) < 3  || !preg_match('/^[a-zA-Z]+[a-zA-Z0-9]*$/', $nombre)) {
        header('Location: ../crear.php?error=nombre_invalido');
        exit();
    }
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header('Location: ../crear.php?error=correo_invalido');
        exit();
    }
    if (strlen($passwd) < 8) {
        header('Location: ../crear.php?error=contrasena_invalida');
        exit();
    }
    
    // Encriptar la contraseña utilizando BCRYPT
    $passwd_encriptada = password_hash($passwd, PASSWORD_BCRYPT);
    
    try {
        mysqli_autocommit($conexion, false);
        mysqli_begin_transaction($conexion, MYSQLI_TRANS_START_READ_WRITE);
        
        $sql = "INSERT INTO tbl_usuarios (nombre_usuario, nombre_persona, correo_usuario, passwd_usuario) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($conexion);
        
        if (mysqli_prepare($conexion, $sql)) {
            // Usar la contraseña encriptada en la consulta
            mysqli_stmt_bind_param($stmt, "ssss", $usuario, $nombre, $correo, $passwd_encriptada);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            mysqli_commit($conexion);
            header("Location: ../index.php");
            exit();
        } else {
            throw new Exception("Error al preparar la consulta.");
        }
    } catch (Exception $e) {
        mysqli_rollback($conexion);
        echo "Se produjo un error: " . htmlspecialchars($e->getMessage());
    }

    mysqli_close($conexion);
}
