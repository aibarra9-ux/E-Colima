<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'escritor') {
    header("Location: ../Home/home.php");
    exit();
}

include("../Perfil/conexion.php");

$categoria_slug = $_POST['categoria'] ?? 'fauna';
$titulo = $conn->real_escape_string($_POST['titulo'] ?? '');
$contenido = $conn->real_escape_string($_POST['contenido'] ?? '');
$autor_id = $_SESSION['usuario_id'] ?? 0;

$sql_cat = "SELECT id FROM categorias WHERE slug = '$categoria_slug' LIMIT 1";
$result_cat = $conn->query($sql_cat);
$categoria_id = ($result_cat && $row = $result_cat->fetch_assoc()) ? $row['id'] : 2;

// Guardar imagen
$imagen_nombre = '';
if (!empty($_POST['imagen_recortada'])) {
    $directorio = "../../assets/Publicaciones/";
    if (!file_exists($directorio)) mkdir($directorio, 0777, true);
    $imagen_nombre = time() . '_' . uniqid() . '.jpg';
    $datos_imagen = explode(',', $_POST['imagen_recortada']);
    file_put_contents($directorio . $imagen_nombre, base64_decode($datos_imagen[1]));
}

$sql = "INSERT INTO publicaciones (autor_id, categoria_id, titulo, contenido, imagen, estado, fecha_creacion) 
        VALUES ($autor_id, $categoria_id, '$titulo', '$contenido', '$imagen_nombre', 'pendiente', NOW())";

if ($conn->query($sql)) {
    header("Location: publicar.php?categoria=$categoria_slug&success=1");
} else {
    header("Location: publicar.php?categoria=$categoria_slug&error=1");
}
exit();
?>