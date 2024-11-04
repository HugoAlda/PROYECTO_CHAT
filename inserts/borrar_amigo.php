<?php   
require_once '../conexion.php';
session_start();    

if (isset($_POST['btn_eliminar_amigo'])) {
    // Usuario que elimina la solicitud, usando el ID almacenado en la sesión
    $id_usuarioa = isset($_SESSION['id_usuario']) ? mysqli_real_escape_string($conexion, htmlspecialchars($_SESSION['id_usuario'])) : '';
    $id_usuariob = isset($_GET['id_amigo']) ? mysqli_real_escape_string($conexion, htmlspecialchars($_GET['id_amigo'])) : ''; // Asegúrate de que esta clave está correcta
    
    echo $id_usuarioa;  // Para depuración
    echo $id_usuariob;  // Para depuración

    if ($id_usuarioa && $id_usuariob) {
        try {
            mysqli_autocommit($conexion, false);
            mysqli_begin_transaction($conexion, MYSQLI_TRANS_START_READ_WRITE);

            // Consulta para eliminar la solicitud de amistad
            $sql = "DELETE FROM tbl_amigos WHERE (id_usuarioa = ? AND id_usuariob = ?) OR (id_usuarioa = ? AND id_usuariob = ?)";
            $stmt = mysqli_prepare($conexion, $sql);
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "iiii", $id_usuarioa, $id_usuariob, $id_usuariob, $id_usuarioa);

                if (mysqli_stmt_execute($stmt)) {
                    if (mysqli_stmt_affected_rows($stmt) > 0) {
                        // Commit de la transacción
                        mysqli_commit($conexion);
                        // Redirigir al usuario a interfaz.php después de eliminar la solicitud de amistad
                        header("Location: ../interfaz.php");
                        exit;
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
        
            // Si no se realizó la eliminación, hacer rollback
            mysqli_rollback($conexion);
        
        } catch (Exception $e) {
            mysqli_rollback($conexion);
            echo "Error al rechazar la solicitud de amistad: " . $e->getMessage();
        }
    } else {
        echo "Faltan datos para procesar la solicitud.";
    }
}
?>
