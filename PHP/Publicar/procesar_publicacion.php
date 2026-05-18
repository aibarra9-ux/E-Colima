<?php
session_start();
include("../Perfil/conexion.php");

// 1. Escudo de seguridad para Escritores y Admins (Mantenido igual)
$rol_texto = isset($_SESSION['rol']) ? $_SESSION['rol'] : '';
$rol_id = isset($_SESSION['rol_id']) ? (int)$_SESSION['rol_id'] : 0;

$es_admin = ($rol_texto === 'admin' || $rol_id === 1);
$es_escritor = ($rol_texto === 'escritor' || $rol_id === 3);

if (!isset($_SESSION['usuario']) || (!$es_admin && !$es_escritor)) {
    header("Location: ../Home/home.php");
    exit();
}

// 2. Capturar los datos del formulario de manera segura
$categoria_slug = $_POST['categoria'] ?? 'fauna';
$titulo = trim($_POST['titulo'] ?? '');
$contenido = trim($_POST['contenido'] ?? '');

// Capturamos el ID de la subcategoría enviado por el formulario
$subcategoria_id = !empty($_POST['subcategoria_id']) ? (int)$_POST['subcategoria_id'] : null;

// Asegurar el ID del autor desde la sesión
$autor_id = $_SESSION['usuario_id'] ?? ($_SESSION['id'] ?? 0);

// 3. Obtener el ID de la categoría principal mediante su slug
$sql_cat = "SELECT id FROM categorias WHERE slug = ? LIMIT 1";
$stmt_cat = $conn->prepare($sql_cat);
$stmt_cat->bind_param("s", $categoria_slug);
$stmt_cat->execute();
$result_cat = $stmt_cat->get_result();
$categoria_id = ($row = $result_cat->fetch_assoc()) ? (int)$row['id'] : 2; 

// 4. Procesar y guardar la imagen recortada (Base64)
$imagen_nombre = '';
if (!empty($_POST['imagen_recortada'])) {
    $directorio = "../../assets/Publicaciones/";
    if (!file_exists($directorio)) {
        mkdir($directorio, 0777, true);
    }
    $imagen_nombre = time() . '_' . uniqid() . '.jpg';
    $datos_imagen = explode(',', $_POST['imagen_recortada']);
    
    if (isset($datos_imagen[1])) {
        file_put_contents($directorio . $imagen_nombre, base64_decode($datos_imagen[1]));
    }
}

// 5. Inserción en la base de datos usando Prepared Statements
$sql = "INSERT INTO publicaciones (autor_id, categoria_id, subcategoria_id, titulo, contenido, imagen, estado, fecha_creacion) 
        VALUES (?, ?, ?, ?, ?, ?, 'pendiente', NOW())";

$stmt = $conn->prepare($sql);

// 🌟 AJUSTE QUIRÚRGICO: Si $subcategoria_id es NULL, MySQL prefiere recibir un tipo "s" (string/null) 
// o usar una vinculación directa. Para evitar problemas si el servidor es estricto, 
// validamos el tipo dinámicamente en el mapeo:
$tipo_subcategoria = is_null($subcategoria_id) ? "s" : "i"; 
$tipos_params = "ii" . $tipo_subcategoria . "sss"; // Ejemplo: "iissss" si es null, o "iiisss" si tiene ID

$stmt->bind_param($tipos_params, $autor_id, $categoria_id, $subcategoria_id, $titulo, $contenido, $imagen_nombre);

if ($stmt->execute()) {
    header("Location: publicar.php?categoria=$categoria_slug&success=1");
} else {
    // Si necesitas debugear un error de base de datos puedes usar: echo $stmt->error;
    header("Location: publicar.php?categoria=$categoria_slug&error=1");
}
exit();
?>