<?php

session_start();

include "conexion.php";

$username = $_POST["username"];
$password = $_POST["password"];

$sql = "SELECT * FROM usuarios WHERE username = '$username' AND activo = 1";

$resultado = $conn->query($sql);

if($resultado-> num_rows > 0)
{
    $usuario = $resultado->fetch_assoc();

    if(password_verify($password, $usuario['password_hash']))
    {
        $_SESSION['usuario'] = $usuario['username'];

        header("Location: ../index.php");
        exit();
    }
    
    else
    {
        echo "Contraseña incorrecta";
    }

}
else
{
    echo "usuario no encontrado";
}

?>