<?php
// 1. Cabecera para que el navegador sepa que enviamos JSON
header('Content-Type: application/json');

// 2. Iniciar sesión y conexión
session_start();
require_once "conexion.php";

// 3. Verificación de seguridad (Solo admin puede ver esto)
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1) {
    echo json_encode(["status" => "error", "message" => "No autorizado"]);
    exit;
}

// 4. La Consulta SQL (Ajustada a tu imagen de la BD)
// Filtramos para que NO aparezcan las que tienen fecha_eliminacion
$sql = "SELECT p.id, p.titulo, p.fecha_publicacion, u.username as autor 
        FROM publicaciones p
        INNER JOIN usuarios u ON p.autor_id = u.id
        WHERE p.estado = 'publicado' 
        AND p.fecha_eliminacion IS NULL
        ORDER BY p.fecha_publicacion DESC";

$resultado = $conn->query($sql);
$publicaciones = []; // Siempre inicializamos como array vacío

// 5. Procesar resultados
if ($resultado && $resultado->num_rows > 0) {
    while($row = $resultado->fetch_assoc()) {
        // Formateamos la fecha para que se vea bonita: "04 May, 2026"
        if ($row['fecha_publicacion']) {
            $row['fecha'] = date("d M, Y", strtotime($row['fecha_publicacion']));
        } else {
            $row['fecha'] = "Sin fecha";
        }
        $publicaciones[] = $row;
    }
}

// 6. El Eco final: Siempre enviamos el arreglo (vacío o lleno)
echo json_encode($publicaciones);
