<?php

    $dbserver="localhost";
    $dbusername="root";
    $dbpassword="30891b92";
    $dbbasedatos="bd_chat";
    
    try {
    
        $conexion = mysqli_connect($dbserver, $dbusername,$dbpassword, $dbbasedatos);
    }
    catch (Exception $e) {
        echo "Error de conexión: ". $e->getMessage();
        die();
    }
