<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $confirmar = $_POST['confirmar'];

    if ($password != $confirmar) {
        $_SESSION['error_registro'] = "password";
        header("Location: registro.php");
        exit();
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (rol_id, username, email, password_hash, activo)
    VALUES (4, '$nombre', '$correo', '$passwordHash', 1)";

    try {
        $conn->query($sql);
        $_SESSION['usuario'] = $nombre;
        header("Location: ../Home/home.php?success=1");
        exit();

    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $_SESSION['error_registro'] = "duplicado";
        } else {
            $_SESSION['error_registro'] = "general";
        }
        header("Location: registro.php");
        exit();
    }

    $conn->close();
}
?>