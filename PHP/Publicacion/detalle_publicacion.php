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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($publicacion['titulo']); ?> - ECOLIMA</title>
    
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
        <a href="javascript:history.back()" class="btn-volver"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <main class="article-container">
        <div class="article-meta">
            <span><i class="fas fa-folder"></i> <?php echo htmlspecialchars($publicacion['categoria']); ?></span>
            <span><i class="fas fa-user"></i> Por: <?php echo htmlspecialchars($publicacion['autor']); ?></span>
            <span><i class="fas fa-calendar-alt"></i> <?php echo date("d/m/Y", strtotime($publicacion['fecha_creacion'])); ?></span>
        </div>
        
        <h1 class="article-title"><?php echo htmlspecialchars($publicacion['titulo']); ?></h1>
        
        <?php if (!empty($publicacion['imagen'])): ?>
            <?php if (isset($publicacion['tipo_media']) && $publicacion['tipo_media'] === 'video'): ?>
                <video src="../../assets/Publicaciones/<?php echo htmlspecialchars($publicacion['imagen']); ?>" controls autoplay muted loop playsinline class="article-video"></video>
            <?php else: ?>
                <img src="../../assets/Publicaciones/<?php echo htmlspecialchars($publicacion['imagen']); ?>" alt="Imagen del artículo" class="article-img">
            <?php endif; ?>
        <?php endif; ?>

        <div class="article-content">
            <?php echo htmlspecialchars($publicacion['contenido']); ?>
        </div>

        <section class="seccion-comentarios">
            <h3><i class="far fa-comments"></i> Comentarios</h3>

            <?php if (isset($_SESSION['usuario'])): ?>
                <div class="form-comentario">
                    <textarea id="txtComentario" placeholder="Escribe un comentario respetuoso..."></textarea>
                    <button class="btn-comentar" onclick="enviarComentario()">
                        <i class="far fa-paper-plane"></i> Publicar comentario
                    </button>
                </div>
            <?php else: ?>
                <div class="aviso-sesion">
                    <i class="fas fa-info-circle"></i> Debes <a href="../Login/login.php" style="color: var(--accent-color); font-weight: 600; text-decoration: none;">iniciar sesión</a> para dejar un comentario.
                </div>
            <?php endif; ?>

            <div class="lista-comentarios" id="contenedorComentarios"></div>
        </section>
    </main>

<script>
const idPublicacionActual = <?php echo isset($_GET['id']) ? (int)$_GET['id'] : 0; ?>;

function cargarComentarios() {
    if (!idPublicacionActual) return;

    fetch(`comentarios_backend.php?publicacion_id=${idPublicacionActual}`)
        .then(res => res.json())
        .then(comentarios => {
            const divContenedor = document.getElementById("contenedorComentarios");
            divContenedor.innerHTML = "";

            if (comentarios.length === 0) {
                divContenedor.innerHTML = `<p style="color: var(--text-muted); font-style: italic; padding-left: 5px;">Sé el primero en comentar esta publicación.</p>`;
                return;
            }

            comentarios.forEach(com => {
                divContenedor.innerHTML += `
                    <div class="tarjeta-comentario">
                        <div class="header-comentario">
                            <div class="autor-comentario">
                                <i class="fas fa-user-circle"></i> @${com.username}
                            </div>
                            <span class="fecha-comentario">${com.fecha}</span>
                        </div>
                        <p class="texto-comentario">${com.contenido}</p>
                    </div>
                `;
            });
        })
        .catch(err => console.error("Error al cargar comentarios:", err));
}

function enviarComentario() {
    const txtArea = document.getElementById("txtComentario");
    const texto = txtArea.value.trim();

    if (!texto) {
        alert("El comentario no puede estar vacío.");
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

document.addEventListener("DOMContentLoaded", cargarComentarios);
</script>

</body>
</html>