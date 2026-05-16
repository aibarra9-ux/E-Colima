<?php
session_start();

// EVITAR QUE EL BOTÓN "ATRÁS" DESHAGA LA ACCIÓN (CONTROL DE CACHÉ)
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

include("../Perfil/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_post']) && isset($_POST['accion'])) {
    
    $id_post = intval($_POST['id_post']);
    $accion = $_POST['accion'];

    if ($accion === 'aprobar') {
        // Palomita: Cambia estado a publicado inmediatamente
        $sql = "UPDATE publicaciones SET estado = 'publicado', estado_interno = NULL, motivo_rechazo = NULL WHERE id = $id_post";
    } 
    elseif ($accion === 'rechazar') {
        // Equis: Pasa a 'rechazado' para sacarlo de este panel, pero interno 'borrador' para el autor
        $motivo = isset($_POST['motivo']) ? $conn->real_escape_string($_POST['motivo']) : '';
        $sql = "UPDATE publicaciones SET estado = 'rechazado', estado_interno = 'borrador', motivo_rechazo = '$motivo' WHERE id = $id_post";
    } 
    else {
        header("Location: administrar_solicitudes.php");
        exit();
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: administrar_solicitudes.php?actualizado=1");
        exit();
    } else {
        echo "Error al actualizar la base de datos: " . $conn->error;
    }

} else {
    header("Location: administrar_solicitudes.php");
    exit();
}
?>