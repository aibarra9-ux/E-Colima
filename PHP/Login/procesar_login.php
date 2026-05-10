<?php
session_start();
include "../Perfil/conexion.php";

$username = $_POST["username"];
$password = $_POST["password"];

// Usamos Prepared Statements para evitar que hackeen tu DB con SQL Injection
$stmt = $conn->prepare("SELECT id, username, password_hash, rol_id FROM usuarios WHERE username = ? AND activo = 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$resultado = $stmt->get_result();

if($resultado->num_rows > 0)
{
    $usuario = $resultado->fetch_assoc();

    if(password_verify($password, $usuario['password_hash']))
    {
        // GUARDAMOS LOS DATOS CLAVE EN LA SESIÓN
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario'] = $usuario['username'];
        $_SESSION['rol_id'] = $usuario['rol_id']; // <--- ESTO ES LO QUE NECESITAMOS

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