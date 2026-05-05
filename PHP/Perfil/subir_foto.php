<?php
session_start();
require_once "conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
    $id_usuario = $_SESSION['usuario_id'];
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
        
        // Opcional: Borrar la foto anterior si no es la default
        $query_old = "SELECT foto_perfil FROM usuarios WHERE id = $id_usuario";
        $res_old = $conn->query($query_old);
        $old_foto = $res_old->fetch_assoc()['foto_perfil'];
        if ($old_foto != 'default_avatar.png' && file_exists($directorio_destino . $old_foto)) {
            unlink($directorio_destino . $old_foto);
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
}
