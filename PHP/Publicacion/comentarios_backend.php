<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once "../Perfil/conexion.php"; // Asegúrate de que esta ruta apunte correctamente a tu archivo de conexión

$metodo = $_SERVER['REQUEST_METHOD'];

// 1. GUARDAR UN COMENTARIO (POST)
if ($metodo === 'POST') {
    if (!isset($_SESSION['usuario_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Debes iniciar sesión para comentar.']);
        exit;
    }

    // Leer los datos JSON que envía el JavaScript
    $datos = json_decode(file_get_contents("php://input"), true);
    $publicacion_id = isset($datos['publicacion_id']) ? (int)$datos['publicacion_id'] : 0;
    $contenido = isset($datos['contenido']) ? trim($datos['contenido']) : '';
    $usuario_id = $_SESSION['usuario_id'];
    
    // Por defecto lo ponemos en 1 para que sea visible de inmediato. 
    // Cambiar a 0 si deseas que requiera moderación de un administrador.
    $aprobado = 1; 

    if ($publicacion_id <= 0 || empty($contenido)) {
        echo json_encode(['status' => 'error', 'message' => 'El comentario no puede estar vacío.']);
        exit;
    }

    // Consulta adaptada exactamente a tus columnas
    $sql = "INSERT INTO comentarios (publicacion_id, usuario_id, contenido, aprobado) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisi", $publicacion_id, $usuario_id, $contenido, $aprobado);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Comentario publicado con éxito.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al guardar el comentario en la base de datos.']);
    }
    exit;
}

// 2. LEER COMENTARIOS (GET)
if ($metodo === 'GET') {
    $publicacion_id = isset($_GET['publicacion_id']) ? (int)$_GET['publicacion_id'] : 0;

    if ($publicacion_id <= 0) {
        echo json_encode([]);
        exit;
    }

    // Traemos los comentarios uniendo la tabla usuarios para mostrar el nombre en pantalla
    // Filtramos por aprobado = 1 para que solo se muestren los aprobados
    $sql = "SELECT c.contenido, c.fecha_creacion, u.username 
            FROM comentarios c
            INNER JOIN usuarios u ON c.usuario_id = u.id
            WHERE c.publicacion_id = ? AND c.aprobado = 1
            ORDER BY c.fecha_creacion DESC";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $publicacion_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $comentarios = [];
    while ($row = $resultado->fetch_assoc()) {
        $comentarios[] = [
            'username' => $row['username'],
            'contenido' => $row['contenido'],
            'fecha' => date("d M, Y H:i", strtotime($row['fecha_creacion']))
        ];
    }

    echo json_encode($comentarios);
    exit;
}
?>