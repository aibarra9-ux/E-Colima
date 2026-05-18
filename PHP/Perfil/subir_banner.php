<?php
session_start();
require_once "conexion.php";

$id_usuario = null;

// 1. Capturamos el ID que manda de forma segura el JavaScript
if (isset($_POST['usuario_id_alterno']) && !empty($_POST['usuario_id_alterno'])) {
    $id_usuario = intval($_POST['usuario_id_alterno']);
} elseif (isset($_SESSION['usuario_id'])) {
    $id_usuario = $_SESSION['usuario_id'];
} elseif (isset($_SESSION['id'])) {
    $id_usuario = $_SESSION['id'];
}

// 🌟 IMPORTANTE: Validamos 'banner_perfil' que es la clave que manda el Form de arriba
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['banner_perfil'])) {
    
    if (empty($id_usuario) || $id_usuario === 0) {
        echo json_encode(["status" => "error", "message" => "ID de usuario vacío."]);
        exit;
    }

    $archivo = $_FILES['banner_perfil'];
    
    // Configuración de la carpeta destino de los banners
    $directorio_destino = "../../assets/Fotos_banner/";
    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
    $nombre_archivo = "banner_" . $id_usuario . "_" . time() . "." . $extension;
    $ruta_completa = $directorio_destino . $nombre_archivo;

    // Validar tipo de archivo
    $permitidos = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array(strtolower($extension), $permitidos)) {
        echo json_encode(["status" => "error", "message" => "Formato no permitido"]);
        exit;
    }

    // Mover archivo al servidor
    if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
        
        // Opcional: Borrar el banner anterior si existe y no es el default
        $query_old = "SELECT banner_perfil FROM usuarios WHERE id = '$id_usuario'";
        $res_old = $conn->query($query_old);
        if ($res_old && $res_old->num_rows > 0) {
            $old_banner = $res_old->fetch_assoc()['banner_perfil'];
            if ($old_banner != 'default_banner.jpg' && file_exists($directorio_destino . $old_banner)) {
                unlink($directorio_destino . $old_banner);
            }
        }

        // Actualizar base de datos afectando la columna 'banner_perfil'
        $stmt = $conn->prepare("UPDATE usuarios SET banner_perfil = ? WHERE id = ?");
        $stmt->bind_param("si", $nombre_archivo, $id_usuario);
        
        if ($stmt->execute()) {
            // Devolvemos la clave 'archivo' idéntica a tu otra función para que el JS la monte al vuelo
            echo json_encode(["status" => "success", "archivo" => $nombre_archivo]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al actualizar la base de datos."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No se pudo guardar el archivo del banner."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Petición no válida."]);
}
exit();
?>