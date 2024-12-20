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
        <form action="./inserts/insert_iniciar_sesion.php" method="post" class="login-form">
            <h2 class="form-title">Iniciar Sesión</h2>
            <label for="nombre_usuario">Usuario</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario">
            <br><br>
            <label for="passwd_usuario">Contraseña</label>
            <input type="password" id="passwd_usuario" name="passwd_usuario">
            <br><br>
            <input type="submit" name="btn_iniciar" value="Iniciar Sesión">
            <br><br>
            <?php
                if (isset($_GET['error']) ) {
                    echo "<div class='error'><p>Algun campo es incorrecto o no esta rellenado</p></div>";
                }
            ?>
        </form>

        <!-- Formulario para "Crear Cuenta" -->
        <form action="crear.php" method="post" class="create-account-form">
            <input type="submit" name="btn_crear" value="Crear Cuenta">
        </form>
    </div>
</body>
</html>
