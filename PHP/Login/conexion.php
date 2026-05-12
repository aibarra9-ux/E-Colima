<?php
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db = "ecolima";
    // Agregamos el puerto como quinto parámetro
    $conn = new mysqli($host, $user, $pass, $db, $port);

    if($conn->connect_error)
    {
        die("Error de conexion: " . $conn->connect_error);
    }
    
    // Establecer el juego de caracteres a utf8mb4 para evitar problemas con acentos
    $conn->set_charset("utf8mb4");

    // echo "Conectado con éxito a Workbench"; 
?>
