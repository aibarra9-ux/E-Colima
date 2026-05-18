<?php
session_start();
include "conexion.php"; // Se asume que está en la misma carpeta corporativa del perfil

header('Content-Type: application/json');

// 🛡️ Filtro de seguridad: Verificar sesión activa
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesión inválida o caducada.']);
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

// 📥 Capturar el estado enviado desde el JavaScript (1 para oscuro, 0 para claro)
$modo_oscuro = isset($_POST['modo_oscuro']) ? intval($_POST['modo_oscuro']) : 0;

// 📝 Actualizar la preferencia en la base de datos
$query = "UPDATE usuarios SET modo_oscuro = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $modo_oscuro, $id_usuario);

if ($stmt->execute()) {
    // 🌟 Sincronizamos la variable de sesión para mantener el estado globalmente
    $_SESSION['modo_oscuro'] = $modo_oscuro;
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar la preferencia en la Base de Datos.']);
}
exit();