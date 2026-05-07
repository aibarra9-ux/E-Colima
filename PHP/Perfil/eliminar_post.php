<?php
session_start();
require_once "conexion.php";

// 1. Verificación de seguridad básica
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1) {
    echo json_encode(["status" => "error", "message" => "No autorizado"]);
    exit;
}

if (isset($_GET['id'])) {
    $id_post = intval($_GET['id']);

    // 2. (Opcional) Obtener el nombre de la imagen para borrarla del servidor
    // Esto evita que la carpeta assets/Posts se llene de basura
    $query_img = "SELECT imagen FROM publicaciones WHERE id = ?";
    $stmt_img = $conn->prepare($query_img);
    $stmt_img->bind_param("i", $id_post);
    $stmt_img->execute();
    $resultado = $stmt_img->get_result();
    
    if ($row = $resultado->fetch_assoc()) {
        $ruta_imagen = "../../assets/fotos_post/" . $row['imagen'];
        // Borramos el archivo si existe y no es el default
        if ($row['imagen'] != 'default_post.jpg' && file_exists($ruta_imagen)) {
            unlink($ruta_imagen);
        }
    }

    // 3. Eliminar el registro de la base de datos
    $stmt = $conn->prepare("DELETE FROM publicaciones WHERE id = ?");
    $stmt->bind_param("i", $id_post);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Publicación eliminada"]);
    } else {
        echo json_encode(["status" => "error", "message" => "No se pudo eliminar de la base de datos"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "ID no proporcionado"]);
}