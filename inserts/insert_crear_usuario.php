<?php
require_once '../conexion.php'; 
session_start();
    
    if (isset($_POST['btn_crear_cuenta'])) {
        $usuario = isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : '';
        $nombre = isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '';
        $correo = isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : '';
        $passwd = isset($_POST['passwd']) ? htmlspecialchars($_POST['passwd']) : '';
        
        if (empty($usuario) || empty($passwd) || empty($nombre) || empty($correo)) {
            header('Location: crear.php?error=true');
            exit();
        } else {
            try {
                mysqli_autocommit($conexion, false);
                $sql = "INSERT INTO tbl_usuarios (nombre_usuario, nombre_persona, correo_usuario, passwd_usuario) VALUES (?,?,?,?)";
                $stmt = mysqli_prepare($conexion, $sql);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    throw new Exception(mysqli_error($conexion));
                }
                mysqli_stmt_bind_param($stmt, "ssss", $usuario, $nombre, $correo, $passwd);
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception(mysqli_error($conexion));
                }
                mysqli_stmt_close($stmt);
                mysqli_commit($conexion);
                header("Location: ../index.php");
                exit();
            } catch (Exception $e) {
                mysqli_rollback($conexion);
                echo "Se produjo un error: " . $e->getMessage();
            }
        }
    }