<?php
session_start();
require_once "conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_SESSION['usuario_id'];
    $nuevo_nombre = trim($_POST['username']);
    $nuevo_email = trim($_POST['email']);

    // 1. Validar que no estén vacíos
    if (empty($nuevo_nombre) || empty($nuevo_email)) {
        echo json_encode(["status" => "error", "message" => "Todos los campos son obligatorios."]);
        exit;
    }

    // 2. Validar formato del nombre (regex igual a tu pattern de HTML)
    if (!preg_match('/^[A-Za-z0-9_.]+$/', $nuevo_nombre) || strlen($nuevo_nombre) < 4) {
        echo json_encode(["status" => "error", "message" => "Nombre de usuario inválido (mín. 4 caracteres, solo letras, números, _ o .)."]);
        exit;
    }

    // 3. Validar formato de correo
    if (!filter_var($nuevo_email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "El formato del correo no es válido."]);
        exit;
    }

    // 4. (Opcional) Verificar si el correo ya existe en OTRO usuario
    $checkEmail = $conn->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
    $checkEmail->bind_param("si", $nuevo_email, $id);
    $checkEmail->execute();
    if ($checkEmail->get_result()->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Este correo ya está registrado por otro usuario."]);
        exit;
    }

    // 5. Proceder con la actualización
    $stmt = $conn->prepare("UPDATE usuarios SET username = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nuevo_nombre, $nuevo_email, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al guardar en la base de datos."]);
    }
}
?>