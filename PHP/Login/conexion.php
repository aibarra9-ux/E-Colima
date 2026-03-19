<?php

    $host = "localhost";
    $user = "root";
    $pass = "";
    $db = "ecolima";

    $conn = new mysqli($host, $user, $pass, $db);

    if($conn->connect_error)
        {
          die("Error de conexion: ". $conn->connect_error);
        }

?>
