<?php
session_start();
include "../Perfil/conexion.php";

// Validamos que la petición venga exclusivamente por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST['username'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';
    
    // 1. OBTENEMOS EL CÓDIGO QUE ESCRIBIÓ EL USUARIO
    $codigo_ingresado = $_POST['codigo_ingresado'] ?? '';

    // 2. VALIDACIÓN DE CONTRASEÑAS (Doble check por seguridad)
    if ($password != $confirmar) {
        $_SESSION['error_registro'] = "password";
        header("Location: registro.php");
        exit();
    }

    // 3. VALIDACIÓN DEL CÓDIGO OTP
    if (!isset($_SESSION['codigo_verificacion']) || $codigo_ingresado != $_SESSION['codigo_verificacion']) {
        $_SESSION['error_registro'] = "codigo_incorrecto";
        header("Location: registro.php");
        exit();
    }

    // 4. SI LLEGÓ AQUÍ, TODO ESTÁ BIEN. PROCEDEMOS AL REGISTRO
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Nota: rol_id 4 es usuario normal, activo 4 (según tu lógica previa)
    $sql = "INSERT INTO usuarios (rol_id, username, email, password_hash, activo)
            VALUES (4, '$nombre', '$correo', '$passwordHash', 4)";

    try {
        if ($conn->query($sql)) {
            // Limpiamos los códigos de la sesión porque ya se usaron
            unset($_SESSION['codigo_verificacion']);
            unset($_SESSION['correo_verificado']);
            
            $_SESSION['usuario'] = $nombre;
            $_SESSION['rol_id'] = 4; // Clave para que Home.php no tire error al registrarse
            
            header("Location: ../Home/home.php?success=1");
            exit();
        }

    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $_SESSION['error_registro'] = "duplicado";
        } else {
            echo "Error real: " . $e->getMessage();
            exit();
        }
        header("Location: registro.php");
        exit();
    }

} else {
    // Si intentan entrar al archivo escribiendo la URL directamente
    header("Location: registro.php");
    exit();
}
?>