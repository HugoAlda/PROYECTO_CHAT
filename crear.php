<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style-index.css">
    <title>Crear Cuenta / Iniciar Sesión</title>
</head>
<body>
    <div class="form-container">
        <form action="./inserts/insert_crear_usuario.php" method="post" class="login-form">
            <h2 class="form-title">Crear Cuenta</h2>
            <label for="usuario">Usuario</label>
            <input type="text" id="usuario" name="usuario">
            <br><br>
            <label for="nombre">Nombre y Apellidos</label>
            <input type="text" id="nombre" name="nombre">
            <br><br>
            <label for="correo">Correo</label>
            <input type="email" id="correo" name="correo">
            <br><br>
            <label for="contraseña">Contraseña</label>
            <input type="password" id="contraseña" name="passwd">
            <br><br>
            <input type="submit" name="btn_crear_cuenta" value="Crear Cuenta">
            <br><br>
            <?php
                if (isset($_GET['error'])) {
                    echo "<div class='error'><p>Algun campo es incorrecto o no esta rellenado</p></div>";
                }
            ?>
        </form>
        
        <!-- Botón para volver al formulario de inicio de sesión -->
        <form action="index.php" method="post">
            <input type="submit" name="Volver" value="Volver">
        </form>
    </div>
</body>
</html>