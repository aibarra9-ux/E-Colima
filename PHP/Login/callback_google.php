<?php
session_start();
include('../Perfil/conexion.php');

// Verificamos que Google envió el token
if (!isset($_POST['credential'])) {
    header('Location: login.php');
    exit();
}

$id_token = $_POST['credential'];

// Validamos el token consultando el endpoint seguro de Google
$url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $id_token;
$response = file_get_contents($url);
$payload = json_decode($response, true);

if (isset($payload['sub'])) {
    // Extraemos los datos seguros que nos da Google
    $google_id = $conn->real_escape_string($payload['sub']);
    $email = $conn->real_escape_string($payload['email']);
    
    // Google nos da el nombre completo en 'name', lo limpiamos para usarlo como 'username'
    $username = $conn->real_escape_string($payload['name']); 
    
    // 1. Buscamos si ya existe un usuario con este google_id o con este email
    $query = "SELECT * FROM usuarios WHERE google_id = '$google_id' OR email = '$email'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        
        // Si el usuario ya existía por email pero no tenía el google_id guardado, se lo vinculamos
        if (empty($usuario['google_id'])) {
            $conn->query("UPDATE usuarios SET google_id = '$google_id' WHERE id = " . $usuario['id']);
        }
    } else {
        // 2. Si es un usuario nuevo, lo REGISTRAMOS en la BD con tus columnas exactas
        // Enviamos NULL a password_hash (asegúrate en phpMyAdmin que acepte valores NULL)
        $insert = "INSERT INTO usuarios (username, email, google_id, rol_id, modo_oscuro, password_hash) 
                   VALUES ('$username', '$email', '$google_id', 2, 0, NULL)";
        
        if (!$conn->query($insert)) {
            // Si hay un error de base de datos, lo detendremos aquí para que lo veas en pantalla
            die("Error crítico al registrar el usuario: " . $conn->error);
        }
        
        // Obtenemos los datos del usuario recién registrado
        $id_nuevo = $conn->insert_id;
        $result = $conn->query("SELECT * FROM usuarios WHERE id = $id_nuevo");
        $usuario = $result->fetch_assoc();
    }

    // 3. INICIAMOS LA SESIÓN (Usando 'username' como lo hace tu sistema original)
    $_SESSION['usuario'] = $usuario['username']; 
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['rol_id'] = $usuario['rol_id'];

    // Redireccionamos directamente al Home
    header('Location: ../Home/home.php');
    exit();

} else {
    die("Autenticación rechazada por los servidores de Google.");
}
?>