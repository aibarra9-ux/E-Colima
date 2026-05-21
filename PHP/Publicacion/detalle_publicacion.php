<?php
include('../Perfil/conexion.php');
session_start();

// 1. Verificamos si hay sesión activa para el navbar
$sesion_activa = isset($_SESSION['usuario']);
if ($sesion_activa) {
    $rol = isset($_SESSION['rol_id']) ? (int)$_SESSION['rol_id'] : 0; 
    $ruta_perfil = ($rol === 1) ? '../Perfil/dashboard_perfil.php' : '../Perfil/perfil.php';
} else {
    $ruta_perfil = '../Login/login.php'; 
}

// 2. Comprobar Modo Oscuro del usuario actual
$modo_oscuro = 0;
if (isset($_SESSION['usuario_id'])) {
    $id_user = (int)$_SESSION['usuario_id'];
    $query_theme = "SELECT modo_oscuro FROM usuarios WHERE id = $id_user";
    $result_theme = $conn->query($query_theme);
    if ($result_theme && $row_theme = $result_theme->fetch_assoc()) {
        $modo_oscuro = (int)$row_theme['modo_oscuro'];
    }
}

// 3. CAPTURA DEL ID DE LA PUBLICACIÓN
$id_publicacion = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_publicacion === 0) {
    header("Location: ../Home/home.php");
    exit();
}

// 4. CONSULTA LA PUBLICACIÓN EN LA BASE DE DATOS (🌟 MODIFICADO: Añadido p.tipo_media)
$query_post = "SELECT p.titulo, p.contenido, p.imagen, p.tipo_media, p.fecha_creacion, u.username AS autor, c.nombre AS categoria 
               FROM publicaciones p
               JOIN usuarios u ON p.autor_id = u.id
               JOIN categorias c ON p.categoria_id = c.id
               WHERE p.id = ? AND p.estado = 'publicado'";

$stmt = $conn->prepare($query_post);
$stmt->bind_param("i", $id_publicacion);
$stmt->execute();
$publicacion = $stmt->get_result()->fetch_assoc();

// Si la publicación no existe en la BD, redirigimos
if (!$publicacion) {
    echo "La publicación no existe o no ha sido aprobada.";
    exit();
}

// Guardar datos en variables PHP
$titulo_original = htmlspecialchars($publicacion['titulo']);
$contenido_original = htmlspecialchars($publicacion['contenido']);
$categoria_original = htmlspecialchars($publicacion['categoria']);
$autor_original = htmlspecialchars($publicacion['autor']);
$fecha_original = date("d/m/Y", strtotime($publicacion['fecha_creacion']));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_original; ?> - ECOLIMA</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=League+Spartan:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        :root {
            --bg-color: <?php echo ($modo_oscuro === 1) ? '#0f172a' : '#f8fafc'; ?>;
            --card-bg: <?php echo ($modo_oscuro === 1) ? '#1e293b' : '#ffffff'; ?>;
            --text-main: <?php echo ($modo_oscuro === 1) ? '#f8fafc' : '#1e293b'; ?>;
            --text-muted: <?php echo ($modo_oscuro === 1) ? '#94a3b8' : '#64748b'; ?>;
            --accent-color: #2d5a27;
            --border-color: <?php echo ($modo_oscuro === 1) ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.08)'; ?>;
        }

        body {
            margin: 0; padding: 0;
            background-color: var(--bg-color);
            color: var(--text-main);
            font-family: 'Outfit', sans-serif;
        }

        /* Barra Superior Básica Sincronizada */
        .barra-superior {
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 5%; background: var(--card-bg);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .imagen-logo { height: 40px; cursor: pointer; }
        .btn-volver {
            background: var(--accent-color); color: white;
            text-decoration: none; padding: 8px 20px;
            border-radius: 8px; font-weight: 600; font-size: 0.9rem;
            transition: 0.2s;
        }
        .btn-volver:hover { transform: scale(1.03); opacity: 0.9; }

        /* Contenedor del Artículo */
        .article-container {
            max-width: 800px; margin: 40px auto; padding: 0 20px;
        }
        .article-meta {
            font-size: 0.95rem; color: var(--text-muted);
            margin-bottom: 10px; display: flex; gap: 15px;
        }
        .article-meta span i { color: var(--accent-color); margin-right: 5px; }
        
        .article-title {
            font-family: 'League Spartan', sans-serif;
            font-size: 2.8rem; font-weight: 700; line-height: 1.2;
            margin: 0 0 25px 0;
        }
        
        /* Estilos unificados para imagen y video del artículo */
        .article-img, .article-video {
            width: 100%; max-height: 450px; object-fit: cover;
            border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            margin-bottom: 30px; display: block;
        }
        
        .article-content {
            font-size: 1.2rem; line-height: 1.8;
            white-space: pre-line; opacity: 0.95;
            margin-bottom: 50px;
        }

        /* SECCIÓN DE COMENTARIOS MEJORADA */
        .seccion-comentarios {
            margin-top: 60px;
            padding-top: 40px;
            border-top: 2px solid var(--border-color);
        }
        
        .seccion-comentarios h3 {
            font-family: 'League Spartan', sans-serif;
            font-size: 1.8rem;
            margin-top: 0;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Caja de Texto y Formulario */
        .form-comentario {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            margin-bottom: 35px;
        }
        
        .form-comentario textarea {
            width: 100%;
            height: 100px;
            background: var(--bg-color);
            color: var(--text-main);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 14px;
            font-family: 'Outfit', sans-serif;
            font-size: 1rem;
            resize: none;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }
        
        .form-comentario textarea:focus {
            outline: none;
            border-color: var(--accent-color);
        }
        
        .btn-comentar {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 10px 22px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            margin-top: 12px;
            transition: 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-comentar:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        /* Lista y Tarjetas de Comentarios individuales */
        .lista-comentarios {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        
        .tarjeta-comentario {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.01);
            position: relative;
        }
        
        .header-comentario {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .autor-comentario {
            font-weight: 600;
            font-size: 1.05rem;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .autor-comentario i {
            color: var(--accent-color);
            font-size: 1.2rem;
        }
        
        .fecha-comentario {
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        
        .texto-comentario {
            margin: 0;
            font-size: 1.05rem;
            line-height: 1.5;
            color: var(--text-main);
            opacity: 0.9;
            word-break: break-word;
        }
        
        .aviso-sesion {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            padding: 15px 20px;
            border-radius: 10px;
            color: var(--text-muted);
            font-size: 1rem;
        }
    </style>
</head>
<body>

    <div class="barra-superior">
        <img src="../../assets/Home/logomini.png" alt="Logo" class="imagen-logo" onclick="window.location.href='../Home/home.php'">
        <a href="javascript:history.back()" class="btn-volver"><i class="fas fa-arrow-left"></i> <span data-translate="true">Volver</span></a>
    </div>

    <main class="article-container">
        <div class="article-meta">
            <span><i class="fas fa-folder"></i> <span class="categoria-texto" data-original="<?php echo $categoria_original; ?>"><?php echo $categoria_original; ?></span></span>
            <span><i class="fas fa-user"></i> <span data-translate="true">Por:</span> <span class="autor-texto"><?php echo $autor_original; ?></span></span>
            <span><i class="fas fa-calendar-alt"></i> <span class="fecha-texto"><?php echo $fecha_original; ?></span></span>
        </div>
        
        <h1 class="article-title titulo-publicacion" data-original="<?php echo $titulo_original; ?>"><?php echo $titulo_original; ?></h1>
        
        <?php if (!empty($publicacion['imagen'])): ?>
            <?php if (isset($publicacion['tipo_media']) && $publicacion['tipo_media'] === 'video'): ?>
                <video src="../../assets/Publicaciones/<?php echo htmlspecialchars($publicacion['imagen']); ?>" controls autoplay muted loop playsinline class="article-video"></video>
            <?php else: ?>
                <img src="../../assets/Publicaciones/<?php echo htmlspecialchars($publicacion['imagen']); ?>" alt="Imagen del artículo" class="article-img">
            <?php endif; ?>
        <?php endif; ?>

        <div class="article-content contenido-publicacion" data-original="<?php echo $contenido_original; ?>"><?php echo $contenido_original; ?></div>

        <section class="seccion-comentarios">
            <h3><i class="far fa-comments"></i> <span data-translate="true">Comentarios</span></h3>

            <?php if (isset($_SESSION['usuario'])): ?>
                <div class="form-comentario">
                    <textarea id="txtComentario" placeholder="Escribe un comentario respetuoso..." data-translate-placeholder="true"></textarea>
                    <button class="btn-comentar" onclick="enviarComentario()">
                        <i class="far fa-paper-plane"></i> <span data-translate="true">Publicar comentario</span>
                    </button>
                </div>
            <?php else: ?>
                <div class="aviso-sesion">
                    <i class="fas fa-info-circle"></i> <span data-translate="true">Debes</span> <a href="../Login/login.php" style="color: var(--accent-color); font-weight: 600; text-decoration: none;" data-translate="true">iniciar sesión</a> <span data-translate="true">para dejar un comentario.</span>
                </div>
            <?php endif; ?>

            <div class="lista-comentarios" id="contenedorComentarios"></div>
        </section>
    </main>

<script>
const idPublicacionActual = <?php echo isset($_GET['id']) ? (int)$_GET['id'] : 0; ?>;

// Array para almacenar comentarios originales
let comentariosOriginales = [];

// Función para traducir comentarios
async function traducirComentarios() {
    const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
    if (idiomaActual !== 'en') return comentariosOriginales;
    
    // Extraer todos los contenidos de comentarios para traducir
    const textosATraducir = comentariosOriginales.map(com => com.contenido_original);
    
    if (textosATraducir.length === 0) return comentariosOriginales;
    
    if (typeof window.traducirBloquePublicacion === 'function') {
        const traducidos = await window.traducirBloquePublicacion(textosATraducir);
        
        // Aplicar traducciones
        comentariosOriginales.forEach((com, idx) => {
            if (traducidos[idx]) {
                com.contenido_traducido = traducidos[idx];
            }
        });
    }
    
    return comentariosOriginales;
}

// Función para renderizar comentarios
function renderizarComentarios() {
    const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
    const divContenedor = document.getElementById("contenedorComentarios");
    
    if (!divContenedor) return;
    
    if (comentariosOriginales.length === 0) {
        const mensajeVacio = idiomaActual === 'en' 
            ? "Be the first to comment on this post."
            : "Sé el primero en comentar esta publicación.";
        divContenedor.innerHTML = `<p style="color: var(--text-muted); font-style: italic; padding-left: 5px;">${mensajeVacio}</p>`;
        return;
    }

    divContenedor.innerHTML = "";
    
    comentariosOriginales.forEach(com => {
        let contenidoMostrar = com.contenido_original;
        
        if (idiomaActual === 'en' && com.contenido_traducido) {
            contenidoMostrar = com.contenido_traducido;
        }
        
        divContenedor.innerHTML += `
            <div class="tarjeta-comentario">
                <div class="header-comentario">
                    <div class="autor-comentario">
                        <i class="fas fa-user-circle"></i> @${escapeHTML(com.username)}
                    </div>
                    <span class="fecha-comentario">${com.fecha}</span>
                </div>
                <p class="texto-comentario">${escapeHTML(contenidoMostrar)}</p>
            </div>
        `;
    });
}

async function cargarComentarios() {
    if (!idPublicacionActual) return;

    try {
        const response = await fetch(`comentarios_backend.php?publicacion_id=${idPublicacionActual}`);
        let comentarios = await response.json();

        // Guardar comentarios originales
        comentariosOriginales = comentarios.map(com => ({
            ...com,
            contenido_original: com.contenido,
            contenido_traducido: null,
            username: com.username,
            fecha: com.fecha
        }));
        
        // Traducir si es necesario
        await traducirComentarios();
        renderizarComentarios();
        
    } catch (err) {
        console.error("Error al cargar comentarios:", err);
    }
}

function escapeHTML(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

// Función para traducir la publicación completa
async function traducirPublicacion() {
    const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
    if (idiomaActual !== 'en') return;
    
    const tituloElement = document.querySelector('.titulo-publicacion');
    const contenidoElement = document.querySelector('.contenido-publicacion');
    const categoriaElement = document.querySelector('.categoria-texto');
    
    if (!tituloElement || !contenidoElement) return;
    
    const tituloOriginal = tituloElement.getAttribute('data-original');
    const contenidoOriginal = contenidoElement.getAttribute('data-original');
    const categoriaOriginal = categoriaElement ? categoriaElement.getAttribute('data-original') : '';
    
    // Traducir en bloque
    const textosATraducir = [];
    if (tituloOriginal) textosATraducir.push(tituloOriginal);
    if (contenidoOriginal) textosATraducir.push(contenidoOriginal);
    if (categoriaOriginal) textosATraducir.push(categoriaOriginal);
    
    if (textosATraducir.length === 0) return;
    
    if (typeof window.traducirBloquePublicacion === 'function') {
        const traducidos = await window.traducirBloquePublicacion(textosATraducir);
        if (traducidos && traducidos.length > 0) {
            if (traducidos[0]) tituloElement.textContent = traducidos[0];
            if (traducidos[1]) contenidoElement.textContent = traducidos[1];
            if (traducidos[2] && categoriaElement) categoriaElement.textContent = traducidos[2];
        }
    }
}

function enviarComentario() {
    const txtArea = document.getElementById("txtComentario");
    const texto = txtArea.value.trim();

    if (!texto) {
        const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
        const msgVacio = idiomaActual === 'en' ? "Comment cannot be empty." : "El comentario no puede estar vacío.";
        alert(msgVacio);
        return;
    }

    fetch('comentarios_backend.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            publicacion_id: idPublicacionActual,
            contenido: texto
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            txtArea.value = ""; 
            cargarComentarios(); 
        } else {
            alert(data.message);
        }
    })
    .catch(err => console.error("Error al enviar comentario:", err));
}

// Escuchar cambios de idioma
window.addEventListener('idioma-cambiado', () => {
    console.log("[Detalle Publicacion] Idioma cambiado, traduciendo publicación y comentarios...");
    traducirPublicacion();
    cargarComentarios();
});

document.addEventListener("DOMContentLoaded", () => {
    cargarComentarios();
    traducirPublicacion();
});
</script>

<script src="../../JavaScript/Traduccion/traduccion.js"></script>
</body>
</html>