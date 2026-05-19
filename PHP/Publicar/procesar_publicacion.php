<?php
session_start();
include("../Perfil/conexion.php");

// 1. Escudo de seguridad para Escritores y Admins
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
$tipo_media = $_POST['tipo_media'] ?? 'imagen';
$subcategoria_id = !empty($_POST['subcategoria_id']) ? (int)$_POST['subcategoria_id'] : null;
$autor_id = $_SESSION['usuario_id'] ?? ($_SESSION['id'] ?? 0);

// 3. Obtener el ID de la categoría principal
$sql_cat = "SELECT id FROM categorias WHERE slug = ? LIMIT 1";
$stmt_cat = $conn->prepare($sql_cat);
$stmt_cat->bind_param("s", $categoria_slug);
$stmt_cat->execute();
$result_cat = $stmt_cat->get_result();
$categoria_id = ($row = $result_cat->fetch_assoc()) ? (int)$row['id'] : 2; 

$archivo_final_nombre = '';
$directorio = "../../assets/Publicaciones/";

if (!file_exists($directorio)) {
    mkdir($directorio, 0777, true);
}

// 4. PROCESAR SEGÚN EL TIPO MULTIMEDIA SELECCIONADO
if ($tipo_media === 'imagen') {
    // Procesar imagen recortada Base64 original
    if (!empty($_POST['imagen_recortada'])) {
        $archivo_final_nombre = time() . '_' . uniqid() . '.jpg';
        $datos_imagen = explode(',', $_POST['imagen_recortada']);
        if (isset($datos_imagen[1])) {
            file_put_contents($directorio . $archivo_final_nombre, base64_decode($datos_imagen[1]));
        }
    }
} else if ($tipo_media === 'video') {
    // Procesar archivo binario de video nativo
    if (isset($_FILES['archivo_video']) && $_FILES['archivo_video']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['archivo_video']['tmp_name'];
        $extension = pathinfo($_FILES['archivo_video']['name'], PATHINFO_EXTENSION);
        
        // Formatos permitidos de control
        $extensiones_validas = ['mp4', 'webm'];
        if (in_array(strtolower($extension), $extensiones_validas)) {
            $archivo_final_nombre = time() . '_' . uniqid() . '.' . $extension;
            move_uploaded_file($tmp_name, $directorio . $archivo_final_nombre);
        } else {
            header("Location: publicar.php?categoria=$categoria_slug&error=1");
            exit();
        }
    }
}

// En caso de que no se haya subido ningún recurso multimedia obligatorio
if (empty($archivo_final_nombre)) {
    header("Location: publicar.php?categoria=$categoria_slug&error=1");
    exit();
}

// 5. Inserción en la base de datos mapeando el nuevo parámetro tipo_media
$sql = "INSERT INTO publicaciones (autor_id, categoria_id, subcategoria_id, titulo, contenido, imagen, tipo_media, estado, fecha_creacion) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pendiente', NOW())";

$stmt = $conn->prepare($sql);

$tipo_subcategoria = is_null($subcategoria_id) ? "s" : "i"; 
$tipos_params = "ii" . $tipo_subcategoria . "ssss"; // Cambiado para aceptar la cadena del tipo de media adicional

$stmt->bind_param($tipos_params, $autor_id, $categoria_id, $subcategoria_id, $titulo, $contenido, $archivo_final_nombre, $tipo_media);

if ($stmt->execute()) {
    header("Location: publicar.php?categoria=$categoria_slug&success=1");
} else {
    header("Location: publicar.php?categoria=$categoria_slug&error=1");
}
exit();
?>