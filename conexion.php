<?php

    $dbserver="localhost";
    $dbusername="root";
    // Contraseña Roberto
    // $dbpasswordB="qazQAZ123";
    // Contraseña Hugo
    $dbpasswordH="qazQAZ123";
    $dbbasedatos="bd_chat";
    
    try {
    
        $conexion = mysqli_connect($dbserver, $dbusername,$dbpasswordH, $dbbasedatos);
    }
    catch (Exception $e) {
        echo "Error de conexión: ". $e->getMessage();
        die();
    }
