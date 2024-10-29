<?php
$host = "localhost";
$dbname = "bd_chat";
$username = "root";
$password = "30891b92";

$conexion = mysqli_connect($host, $username, $password, $dbname);

if (!$conexion) {
    echo "Error de conexión: " . mysqli_connect_error();
}