<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

header('Content-Type: application/json');

// Validación de la ruta del archivo de conexión
if (file_exists("conexion.php")) {
    include "conexion.php";
} else if (file_exists("../conexion.php")) {
    include "../conexion.php";
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se encontró el archivo conexion.php. Revisa la ruta en el backend.']);
    exit();
}

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesión no iniciada o caducada.']);
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

if (!isset($conn)) {
    echo json_encode(['status' => 'error', 'message' => 'La variable de conexión $conn no está definida. Revisa conexion.php']);
    exit();
}

// 🌟 INTERCEPTOR DE CAMBIO RÁPIDO PARA MODO OSCURO (NUEVA LÓGICA)
if (isset($_POST['toggle_dark_mode'])) {
    $modo_oscuro = isset($_POST['modo_oscuro']) ? (int)$_POST['modo_oscuro'] : 0;
    
    $query_dark = "UPDATE usuarios SET modo_oscuro = ? WHERE id = ?";
    $stmt_dark = $conn->prepare($query_dark);
    $stmt_dark->bind_param("ii", $modo_oscuro, $id_usuario);
    
    if ($stmt_dark->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Configuración actualizada correctamente.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el modo oscuro en la BD.']);
    }
    exit(); // Cortamos la ejecución aquí para ignorar las validaciones del resto del formulario
}

// --- LOGICA DE FORMULARIO COMPLETO ORIGINAL (INTACTA) ---
$nuevo_username  = isset($_POST['username']) ? trim($_POST['username']) : '';
$nuevo_email     = isset($_POST['email']) ? trim($_POST['email']) : '';
$password_actual = isset($_POST['password_actual']) ? $_POST['password_actual'] : '';
$password_nueva  = isset($_POST['password_nueva']) ? $_POST['password_nueva'] : '';
$codigo_ingresado = isset($_POST['codigo_verificacion']) ? trim($_POST['codigo_verificacion']) : '';

if (empty($nuevo_username) || empty($nuevo_email)) {
    echo json_encode(['status' => 'error', 'message' => 'El nombre y el correo son obligatorios.']);
    exit();
}

try {
    // 1. 🌟 Corregido: Seleccionamos 'password_hash' en lugar de 'password'
    $query_actual = "SELECT username, email, password_hash FROM usuarios WHERE id = ?";
    $stmt_actual = $conn->prepare($query_actual);
    $stmt_actual->bind_param("i", $id_usuario);
    $stmt_actual->execute();
    $user_db = $stmt_actual->get_result()->fetch_assoc();

    if (!$user_db) {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado en el sistema.']);
        exit();
    }

    // Solo se exige contraseña si se intenta cambiar por una nueva
    if (!empty($password_nueva)) {
        if (empty($password_actual)) {
            echo json_encode(['status' => 'error', 'message' => 'Debes introducir tu contraseña actual para confirmar el cambio.']);
            exit();
        }
        
        // 🌟 Corregido: Comparamos contra la clave 'password_hash' traída de tu BD
        if (!password_verify($password_actual, $user_db['password_hash']) && $password_actual !== $user_db['password_hash']) {
            echo json_encode(['status' => 'error', 'message' => 'La contraseña actual es incorrecta.']);
            exit();
        }
    }

    // 2. Validar duplicados ignorando al usuario dueño de la cuenta
    $query_check = "SELECT id, username, email FROM usuarios WHERE (username = ? OR email = ?) AND id != ?";
    $stmt_check = $conn->prepare($query_check);
    $stmt_check->bind_param("ssi", $nuevo_username, $nuevo_email, $id_usuario);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();

    while ($row = $res_check->fetch_assoc()) {
        if ($row['username'] === $nuevo_username) {
            echo json_encode(['status' => 'error', 'message' => 'El nombre de usuario ya está siendo usado por otra persona.']);
            exit();
        }
        if ($row['email'] === $nuevo_email) {
            echo json_encode(['status' => 'error', 'message' => 'El correo electrónico ya está registrado en otra cuenta.']);
            exit();
        }
    }

    // 3. Manejo de Verificación de Correo electrónico (Opcional)
    if ($nuevo_email !== $user_db['email'] && empty($codigo_ingresado)) {
        $codigo_temporal = rand(100000, 999990);
        $_SESSION['codigo_verificar_email'] = $codigo_temporal;
        $_SESSION['temporal_nuevo_email'] = $nuevo_email;

        echo json_encode([
            'status' => 'need_verification', 
            'message' => "Confirma tu nuevo correo. Código de prueba: $codigo_temporal"
        ]);
        exit();
    }

    if ($nuevo_email !== $user_db['email'] && !empty($codigo_ingresado)) {
        if ($codigo_ingresado != $_SESSION['codigo_verificar_email'] || $nuevo_email !== $_SESSION['temporal_nuevo_email']) {
            echo json_encode(['status' => 'error', 'message' => 'El código de verificación es incorrecto o ha expirado.']);
            exit();
        }
        unset($_SESSION['codigo_verificar_email']);
        unset($_SESSION['temporal_nuevo_email']);
    }

    // 4. Determinar qué contraseña se mantendrá
    $password_final = $user_db['password_hash'];
    if (!empty($password_nueva)) {
        $password_final = password_hash($password_nueva, PASSWORD_DEFAULT);
    }

    // 5. 🌟 Corregido: Modificamos el campo 'password_hash' en el UPDATE de la BD
    $query_update = "UPDATE usuarios SET username = ?, email = ?, password_hash = ? WHERE id = ?";
    $stmt_update = $conn->prepare($query_update);
    $stmt_update->bind_param("sssi", $nuevo_username, $nuevo_email, $password_final, $id_usuario);

    if ($stmt_update->execute()) {
        $_SESSION['usuario'] = $nuevo_username; // Sincroniza la sesión activa de PHP con el nuevo nombre
        echo json_encode(['status' => 'success', 'message' => 'Configuración actualizada correctamente.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al ejecutar la actualización en la BD.']);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Excepción en el servidor: ' . $e->getMessage()]);
}
exit();