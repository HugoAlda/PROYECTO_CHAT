<?php
    require_once '../conexion.php';
    session_start();
    if (isset($_POST['btn_solicitud'])) {
        // Usuario que solicita amistad, usando el ID almacenado en la sesión
        $id_usuarioa = $_SESSION['id_usuario'];
        $id_usuariob = $_GET['id'];
        echo $_SESSION['id_usuario'];
        try {
            $sql = "INSERT INTO tbl_solicitudes_amistad (id_solicitante, id_solicitado) VALUES (?, ?)";
            $stmt = mysqli_prepare($conexion, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ii", $id_usuarioa, $id_usuariob);
                mysqli_stmt_execute($stmt);

                // Cierra el statement
                mysqli_stmt_close($stmt);

                // Redirige al usuario a interfaz.php después de enviar la solicitud de amistad
                header("Location: ../interfaz.php");
                exit;
            }
        } catch (Exception $e) {
            echo "Error al enviar la solicitud de amistad: " . $e->getMessage();
        }
        }
        if (isset($_POST['btn_solicitud_update_ok'])) {

            // Usuario que elimina la solicitud, usando el ID almacenado en la sesión
            $id_usuarioa_ok = $_SESSION['id_usuario'];
            $id_usuariob_ok = $_GET['id'];
        
            // Creación de la amistad una vez aceptada la solicitud
            try {
                $sql = "INSERT INTO tbl_amigos (id_usuarioa, id_usuariob) VALUES (?, ?)";
                $stmt = mysqli_prepare($conexion, $sql);
        
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "ii", $id_usuarioa_ok, $id_usuariob_ok);
                    mysqli_stmt_execute($stmt);
                    
                    // Cierra el statement
                    mysqli_stmt_close($stmt);
                }
                $sql2 = "DELETE FROM tbl_solicitudes_amistad WHERE id_solicitante = ? AND id_solicitado = ?";
                $stmt2 = mysqli_prepare($conexion, $sql2);
                if ($stmt2) { // Aquí se cambió $stmt por $stmt2 para verificar el segundo statement
                    // Bind de parámetros
                    mysqli_stmt_bind_param($stmt2, "ii", $id_usuarioa_ok, $id_usuariob_ok);
                    
                    // Ejecutar la consulta
                    if (mysqli_stmt_execute($stmt2)) {
                        if (mysqli_stmt_affected_rows($stmt2) > 0) {
                            echo "Solicitud de amistad eliminada correctamente.";
                            header("Location: ../interfaz.php");
                            exit;
                        } else {
                            echo "No se encontró la solicitud para eliminar.";
                        }
                    } else {
                        echo "Error al eliminar la solicitud de amistad: " . mysqli_stmt_error($stmt2);
                    }
                    // Cerrar el statement
                    mysqli_stmt_close($stmt2);
                }  
            } catch (Exception $e) {
                echo "Error al crear la amistad: " . $e->getMessage();
            }
        }
    if (isset($_POST['btn_solicitud_update_nok'])) {
        // Usuario que elimina la solicitud, usando el ID almacenado en la sesión
        $id_usuarioa_ok = $_SESSION['id_usuario'];
        $id_usuariob_ok = $_GET['id'];
        try {
            // Consulta para eliminar la solicitud de amistad
            $sql = "DELETE FROM tbl_solicitudes_amistad WHERE id_solicitante = ? AND id_solicitado = ?";
            $stmt = mysqli_prepare($conexion, $sql);
            
            if ($stmt) {
                // Bind de parámetros
                mysqli_stmt_bind_param($stmt, "ii", $id_usuarioa_ok, $id_usuariob_ok);
                
                // Ejecutar la consulta
                if (mysqli_stmt_execute($stmt)) {
                    if (mysqli_stmt_affected_rows($stmt) > 0) {
                        echo "Solicitud de amistad eliminada correctamente.";
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
    
            // Redirigir al usuario a interfaz.php después de eliminar la solicitud de amistad
            header("Location: ../interfaz.php");
            exit;
    
        } catch (Exception $e) {
            echo "Error al rechazar la solicitud de amistad: " . $e->getMessage();
        }
    }