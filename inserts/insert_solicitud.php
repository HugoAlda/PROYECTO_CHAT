<?php
    require_once '../conexion.php';
    session_start();

    if (isset($_POST['btn_solicitud'])) {
        // Usuario que solicita amistad, usando el ID almacenado en la sesión
        $id_usuariob = isset( $_GET['id']) ? mysqli_real_escape_string($conexion, htmlspecialchars( $_GET['id'])) : '';
        $id_usuarioa = isset( $_SESSION['id_usuario']) ? mysqli_real_escape_string($conexion, htmlspecialchars( $_SESSION['id_usuario'])) : '';
        try {
            mysqli_autocommit($conexion, false);
            mysqli_begin_transaction($conexion, MYSQLI_TRANS_START_READ_WRITE);   
            $sql = "INSERT INTO tbl_solicitudes_amistad (id_solicitante, id_solicitado) VALUES (?, ?)";
            $stmt = mysqli_prepare($conexion, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ii", $id_usuarioa, $id_usuariob);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                mysqli_commit($conexion);
                
                header("Location: ../interfaz.php");
                exit;
            }
        } catch (Exception $e) {
            echo "Error al enviar la solicitud de amistad: " . $e->getMessage();
        }
    }

    if (isset($_POST['btn_solicitud'])) {
        // Usuario que solicita amistad, usando el ID almacenado en la sesión
        $id_usuariob = isset($_GET['id']) ? mysqli_real_escape_string($conexion, htmlspecialchars($_GET['id'])) : '';
        $id_usuarioa = isset($_SESSION['id_usuario']) ? mysqli_real_escape_string($conexion, htmlspecialchars($_SESSION['id_usuario'])) : '';
        try {
            mysqli_autocommit($conexion, false);
            mysqli_begin_transaction($conexion, MYSQLI_TRANS_START_READ_WRITE);
            
            $sql = "INSERT INTO tbl_solicitudes_amistad (id_solicitante, id_solicitado) VALUES (?, ?)";
            $stmt = mysqli_prepare($conexion, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ii", $id_usuarioa, $id_usuariob);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                mysqli_commit($conexion);
                header("Location: ../interfaz.php");
                exit;
            }
        } catch (Exception $e) {
            mysqli_rollback($conexion);
            echo "Error al enviar la solicitud de amistad: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['btn_solicitud_update_ok'])) {
        // Usuario que elimina la solicitud, usando el ID almacenado en la sesión
        $id_usuariob_ok = isset($_GET['id']) ? mysqli_real_escape_string($conexion, htmlspecialchars($_GET['id'])) : '';
        $id_usuarioa_ok = isset($_SESSION['id_usuario']) ? mysqli_real_escape_string($conexion, htmlspecialchars($_SESSION['id_usuario'])) : '';
        // Creación de la amistad una vez aceptada la solicitud
        try {
            mysqli_autocommit($conexion, false);
            mysqli_begin_transaction($conexion, MYSQLI_TRANS_START_READ_WRITE);
            
            // Consulta para eliminar la solicitud de amistad
            $sql2 = "DELETE FROM tbl_solicitudes_amistad WHERE id_solicitante = ? AND id_solicitado = ? OR id_solicitante = ? AND id_solicitado = ?";
            $stmt2 = mysqli_prepare($conexion, $sql2);
            if ($stmt2) {
                mysqli_stmt_bind_param($stmt2, "iiii", $id_usuarioa_ok, $id_usuariob_ok, $id_usuariob_ok, $id_usuarioa_ok);
                if (mysqli_stmt_execute($stmt2)) {
                    if (mysqli_stmt_affected_rows($stmt2) > 0) {
                        // echo "Solicitud de amistad eliminada correctamente.";
                    } else {
                        echo "No se encontró la solicitud para eliminar.";
                    }
                } else {
                    echo "Error al eliminar la solicitud de amistad: " . mysqli_stmt_error($stmt2);
                }
                mysqli_stmt_close($stmt2);
            }
    
            // Consulta para crear la amistad
            $sql = "INSERT INTO tbl_amigos (id_usuarioa, id_usuariob) VALUES (?, ?)";
            $stmt = mysqli_prepare($conexion, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ii", $id_usuarioa_ok, $id_usuariob_ok);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
    
            // Consulta para crear la conversación
            $sql3 = "INSERT INTO tbl_conversaciones (id_usuarioa, id_usuariob) VALUES (?, ?)";
            $stmt3 = mysqli_prepare($conexion, $sql3);
            if ($stmt3) {
                mysqli_stmt_bind_param($stmt3, "ii", $id_usuarioa_ok, $id_usuariob_ok);
                mysqli_stmt_execute($stmt3);
                mysqli_stmt_close($stmt3);
            }
    
            mysqli_commit($conexion);
            header("Location: ../interfaz.php");
            exit;
    
        } catch (Exception $e) {
            mysqli_rollback($conexion);
            echo "Error al crear la amistad: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['btn_solicitud_update_nok'])) {
        // Usuario que elimina la solicitud, usando el ID almacenado en la sesión
        $id_usuarioa_ok = $_SESSION['id_usuario'];
        $id_usuariob_ok = $_GET['id'];
    
        try {
            mysqli_autocommit($conexion, false);
            mysqli_begin_transaction($conexion, MYSQLI_TRANS_START_READ_WRITE);
            
            // Consulta para eliminar la solicitud de amistad
            $sql = "DELETE FROM tbl_solicitudes_amistad WHERE id_solicitante = ? AND id_solicitado = ? OR id_solicitante = ? AND id_solicitado = ?";
            $stmt = mysqli_prepare($conexion, $sql);
    
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "iiii", $id_usuarioa_ok, $id_usuariob_ok, $id_usuariob_ok, $id_usuarioa_ok);
    
                if (mysqli_stmt_execute($stmt)) {
                    if (!mysqli_stmt_affected_rows($stmt) > 0) {
                        // echo "Solicitud de amistad eliminada correctamente.";
                    } else {
                        echo "No se encontró la solicitud para eliminar.";
                    }
                } else {
                    echo "Error al eliminar la solicitud de amistad: " . mysqli_stmt_error($stmt);
                }
    
                // Cerrar el statement
                mysqli_stmt_close($stmt);
            } else {
                echo "Error al preparar la consulta: " . mysqli_error($conexion);
            }
    
            // Commit de la transacción
            mysqli_commit($conexion);
            
            // Redirigir al usuario a interfaz.php después de eliminar la solicitud de amistad
            header("Location: ../interfaz.php");
            exit;
    
        } catch (Exception $e) {
            mysqli_rollback($conexion);
            echo "Error al rechazar la solicitud de amistad: " . $e->getMessage();
        }
    }
    