<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style-index.css">
    <title>Crear Cuenta / Iniciar Sesión</title>
</head>
<body>

<?php
    // Determinar qué formulario mostrar
    $mostrarCrearCuenta = false;
    if (isset($_POST['btn_crear'])) {
        $mostrarCrearCuenta = true; // Se ha pulsado "Crear Cuenta"
    }

    // Volver al formulario de inicio de sesión
    if (isset($_POST['Volver'])) {
        $mostrarCrearCuenta = false; // Se ha pulsado "Volver"
    }
?>

<?php
// Mostrar el formulario correspondiente
if (!$mostrarCrearCuenta) {
    // Formulario de Iniciar Sesión
    ?>
    <form action="insert.php" method="post" class="login-form">
        <h2 class="form-title">Iniciar Sesión</h2>
        <label for="nombre_usuario">Usuario</label>
        <input type="text" id="nombre_usuario" name="nombre_usuario">
        <br><br>
        <label for="passwd_usuario">Contraseña</label>
        <input type="password" id="passwd_usuario" name="passwd_usuario">
        <br><br>
        <input type="submit" name="btn_iniciar" value="Iniciar Sesión">
    </form>
    
    <!-- Formulario separado para "Crear Cuenta" -->
    <form action="" method="post">
        <input type="submit" name="btn_crear" value="Crear Cuenta">
    </form>
    <?php
} else {
    // Formulario de Crear Cuenta
    ?>
    <form action="insert.php" method="post" class="login-form">
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
    </form>

    <!-- Botón para volver al formulario de inicio de sesión -->
    <form action="" method="post">
        <input type="submit" name="Volver" value="Volver">
    </form>
    <?php
}
?>

</body>
</html>

