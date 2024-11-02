<?php

session_start();

// Destruir todas las variables de sesión
session_unset();

//Destruir la sesión para resetear el pedido
session_destroy();

header('Location: index.php');