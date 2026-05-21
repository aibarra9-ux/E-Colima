<?php
session_start();
require_once "conexion.php";

// 1. Verificamos que se reciba 'banner' (coincidiendo con el FormData de JS)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['banner'])) {
    $id_usuario = $_SESSION['usuario_id'];
    $archivo = $_FILES['banner'];
    
    // 2. Configuración de rutas y nombres
    $directorio_destino = "../../assets/Fotos_banner/"; 
    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
    
    // Usamos prefijo 'banner_' para distinguirlo de las fotos de perfil
    $nombre_archivo = "banner_" . $id_usuario . "_" . time() . "." . $extension;
    $ruta_completa = $directorio_destino . $nombre_archivo;

    // 3. Validar tipo de archivo
    $permitidos = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array(strtolower($extension), $permitidos)) {
        echo json_encode(["status" => "error", "message" => "Formato no permitido"]);
        exit;
    }

    // 4. Procesar la subida
    if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
        
        // 5. Borrar el banner anterior para no saturar el servidor
        // Buscamos específicamente en la columna 'banner_perfil'
        $query_old = "SELECT banner_perfil FROM usuarios WHERE id = $id_usuario";
        $res_old = $conn->query($query_old);
        
        if ($res_old && $row = $res_old->fetch_assoc()) {
            $old_banner = $row['banner_perfil'];
            // Solo borramos si existe y no es el archivo por defecto
            if ($old_banner && $old_banner != 'default_banner.png' && file_exists($directorio_destino . $old_banner)) {
                unlink($directorio_destino . $old_banner);
            }
        }

        // 6. Actualizar la columna 'banner_perfil' en la BD
        $stmt = $conn->prepare("UPDATE usuarios SET banner_perfil = ? WHERE id = ?");
        $stmt->bind_param("si", $nombre_archivo, $id_usuario);
        
        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success", 
                "archivo" => $nombre_archivo,
                "message" => "Banner actualizado correctamente"
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al actualizar la BD"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No se pudo guardar el archivo en el servidor"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "No se recibió ninguna imagen"]);
}