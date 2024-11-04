<?php

    $dbserver="localhost";
    $dbusername="root";
    // Cambiar contraseña segun quien se cree la base de datos
    $dbpassword="1478";
    $dbbasedatos="bd_chat";
    
    try {
    
        $conexion = mysqli_connect($dbserver, $dbusername,$dbpassword, $dbbasedatos);
    }catch (Exception $e) {
        echo "Error de conexión: ". $e->getMessage();
        die();
    }