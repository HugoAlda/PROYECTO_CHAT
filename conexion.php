<?php

    $dbserver="localhost";
    $dbusername="root";
    // Contraseña Roberto
    // $dbpasswordB="qazQAZ123";
    // Contraseña Hugo
    $dbpasswordH="30891b92";
    $dbbasedatos="bd_chat";
    
    try {
    
        $conexion = mysqli_connect($dbserver, $dbusername,$dbpasswordH, $dbbasedatos);
    }
    catch (Exception $e) {
        echo "Error de conexión: ". $e->getMessage();
        die();
    }
