<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include("conexion.php");

$nombre = $_POST['nombre'];
$correo = $_POST['correo'];
$password = $_POST['password'];
$confirmar = $_POST['confirmar'];

if ($password != $confirmar) {
    echo "Las contraseñas no coinciden";
    exit();
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Verificar si ya existe el usuario o correo
$verificar = "SELECT * FROM usuarios WHERE username='$nombre' OR email='$correo'";
$resultado = $conn->query($verificar);

if ($resultado->num_rows > 0) {
    $_SESSION['mensaje'] = "El usuario o correo ya existe";
    header("Location: registro.php");
    exit();
}

$sql = "INSERT INTO usuarios (rol_id, username, email, password_hash, activo)
VALUES (4, '$nombre', '$correo', '$passwordHash', 1)";

try {
    $conn->query($sql);
    $_SESSION['mensaje'] = "Usuario registrado correctamente";
    $_SESSION['usuario'] = $nombre;
} catch (mysqli_sql_exception $e) {
    $_SESSION['mensaje'] = "El usuario o correo ya existe";
}

header("Location: ../Home/home.php");
exit();

$conn->close();

?>