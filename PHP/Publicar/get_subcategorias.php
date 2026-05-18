<?php
// 1. Conexión a la base de datos (subiendo un nivel para buscar la carpeta Perfil)
include("../Perfil/conexion.php");

// Le avisamos al navegador que este archivo va a responder con formato JSON, no con HTML
header('Content-Type: application/json');

// 2. Capturar el ID de la categoría que envía el JavaScript
$categoria_id = isset($_GET['categoria_id']) ? (int)$_GET['categoria_id'] : 0;

if ($categoria_id === 0) {
    echo json_encode([]);
    exit();
}

// 3. Consultar las subcategorías correspondientes en la Base de Datos
// ⚠️ IMPORTANTE: Asegúrate de que tu tabla en la base de datos se llame exactamente 'subcategorias'
$sql = "SELECT id, nombre FROM subcategorias WHERE categoria_id = ? ORDER BY nombre ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $categoria_id);
$stmt->execute();
$result = $stmt->get_result();

$subcategorias = [];
while ($row = $result->fetch_assoc()) {
    $subcategorias[] = [
        'id' => (int)$row['id'],
        'nombre' => $row['nombre']
    ];
}

// 4. Devolver los datos en el formato limpio que JavaScript sí puede entender
echo json_encode($subcategorias);
exit();
?>