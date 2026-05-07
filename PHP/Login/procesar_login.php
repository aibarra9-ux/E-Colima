<?php
session_start();
include "conexion.php";

$username = $_POST["username"];
$password = $_POST["password"];

$sql = "SELECT * FROM usuarios WHERE username = '$username' AND activo = 1";
$resultado = $conn->query($sql);

if($resultado->num_rows > 0)
{
    $usuario = $resultado->fetch_assoc();

    if(password_verify($password, $usuario['password_hash']))
    {
        // ✅ Guardar datos básicos
        $_SESSION['usuario'] = $usuario['username'];
        $_SESSION['usuario_id'] = $usuario['id'];

        // ✅ NUEVO: Guardar el rol según el rol_id de la BD
        switch ($usuario['rol_id']) {
            case 1:
                $_SESSION['rol'] = 'admin';
                break;
            case 2:
                $_SESSION['rol'] = 'editor';
                break;
            case 3:
                $_SESSION['rol'] = 'escritor';  // ← Este es el tuyo
                break;
            case 4:
                $_SESSION['rol'] = 'usuario';
                break;
            default:
                $_SESSION['rol'] = 'usuario';
        }

        header("Location: ../Home/home.php?success=1");
        exit();
    }
    else
    {
        header("Location: login.php?error=password");
        exit();
    }
}
else
{
    header("Location: login.php?error=usuario");
    exit();
}
?>