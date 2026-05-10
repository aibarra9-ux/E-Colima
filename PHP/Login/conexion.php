<?php
    $host = "127.0.0.1"; // Usar la IP es más directo para instalaciones nativas
    $user = "root";
    $pass = "c1a2r3O4"; // <--- CAMBIA ESTO POR TU CLAVE REAL
    $db = "ECOLIMA";
    $port = "3306"; // Si lo cambiaste en el archivo my.ini, pon 3308 aquí

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