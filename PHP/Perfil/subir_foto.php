<?php
session_start();
require_once "conexion.php";

$id_usuario = null;

// 1. Intentar leer desde el envío seguro de JavaScript (prioridad para AJAX)
if (isset($_POST['usuario_id_alterno']) && !empty($_POST['usuario_id_alterno'])) {
    $id_usuario = intval($_POST['usuario_id_alterno']);
} 
// 2. Si no viene por ahí, buscar en sesiones por si se ocupa en otra parte
unset($id_usuario); // Limpieza rápida
if (isset($_POST['usuario_id_alterno']) && !empty($_POST['usuario_id_alterno'])) {
    $id_usuario = intval($_POST['usuario_id_alterno']);
} elseif (isset($_SESSION['usuario_id'])) {
    $id_usuario = $_SESSION['usuario_id'];
} elseif (isset($_SESSION['id'])) {
    $id_usuario = $_SESSION['id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
    
    // Validamos que por fin tengamos un número de ID de usuario real
    if (empty($id_usuario) || $id_usuario === 0) {
        echo json_encode(["status" => "error", "message" => "El ID de usuario llegó vacío desde el formulario."]);
        exit;
    }

    $archivo = $_FILES['foto'];
    
    // Configuración
    $directorio_destino = "../../assets/Fotos_perfil/";
    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
    $nombre_archivo = "perfil_" . $id_usuario . "_" . time() . "." . $extension;
    $ruta_completa = $directorio_destino . $nombre_archivo;

    // Validar tipo de archivo (Seguridad)
    $permitidos = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array(strtolower($extension), $permitidos)) {
        echo json_encode(["status" => "error", "message" => "Formato no permitido"]);
        exit;
    }

    // Mover archivo
    if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
        
        // 🌟 CORREGIDO: Envolvemos '$id_usuario' entre comillas simples para evitar el error de sintaxis si viene vacío
        $query_old = "SELECT foto_perfil FROM usuarios WHERE id = '$id_usuario'";
        $res_old = $conn->query($query_old);
        
        if ($res_old && $res_old->num_rows > 0) {
            $old_foto = $res_old->fetch_assoc()['foto_perfil'];
            if ($old_foto != 'default_avatar.png' && file_exists($directorio_destino . $old_foto)) {
                unlink($directorio_destino . $old_foto);
            }
        }

        // Actualizar base de datos
        $stmt = $conn->prepare("UPDATE usuarios SET foto_perfil = ? WHERE id = ?");
        $stmt->bind_param("si", $nombre_archivo, $id_usuario);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "archivo" => $nombre_archivo]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al actualizar la BD"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No se pudo guardar el archivo"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Faltan parámetros en la petición"]);
}
exit();
?>