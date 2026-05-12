<?php
session_start();

if (!isset($_SESSION['usuario']) || ($_SESSION['rol'] !== 'escritor' && $_SESSION['rol'] !== 'admin')) {
    header("Location: ../Home/home.php");
    exit();
}

$categoria = $_GET['categoria'] ?? 'fauna';

$nombres_categorias = [
    'flora' => 'Flora',
    'fauna' => 'Fauna',
    'ecosistemas' => 'Ecosistemas',
    'noticias' => 'Noticias',
    'consejos' => 'Consejos'
];
$categoria_nombre = $nombres_categorias[$categoria] ?? 'Fauna';

$iconos = [
    'flora' => 'leaf',
    'fauna' => 'paw',
    'ecosistemas' => 'mountain',
    'noticias' => 'newspaper',
    'consejos' => 'lightbulb'
];
$icono = $iconos[$categoria] ?? 'paw';

$limites = [
    'flora' => 500,
    'fauna' => 500,
    'ecosistemas' => 500,
    'noticias' => 300,
    'consejos' => 200
];
$limite_palabras = $limites[$categoria] ?? 500;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar en <?php echo $categoria_nombre; ?> - ECOLIMA</title>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #0a1a0f 0%, #1a2f1a 50%, #0f1f0f 100%);
            min-height: 100vh;
            color: white;
            padding: 30px 20px;
        }
        .contenedor { max-width: 900px; margin: 0 auto; }
        .header-publicar {
            display: flex; align-items: center; gap: 15px;
            margin-bottom: 35px; flex-wrap: wrap;
        }
        .btn-volver {
            background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15);
            color: white; padding: 10px 18px; border-radius: 50px;
            text-decoration: none; font-family: 'League Spartan', sans-serif;
            font-size: 0.85rem; font-weight: 600; letter-spacing: 1px;
            transition: all 0.3s ease; display: flex; align-items: center; gap: 8px;
        }
        .btn-volver:hover { background: rgba(255,255,255,0.15); transform: translateX(-3px); }
        .titulo-pagina { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 600; color: #a8d5b5; }
        .badge-categoria {
            background: #4a7c5c; color: white; padding: 7px 18px; border-radius: 50px;
            font-family: 'League Spartan', sans-serif; font-size: 0.8rem; font-weight: 700;
            letter-spacing: 2px; text-transform: uppercase; display: flex; align-items: center; gap: 8px;
        }
        .layout-publicar { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
        @media (max-width: 768px) { .layout-publicar { grid-template-columns: 1fr; } }
        
        .form-card {
            background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px; padding: 30px; backdrop-filter: blur(10px); height: fit-content;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block; font-family: 'League Spartan', sans-serif; font-size: 0.8rem;
            font-weight: 600; letter-spacing: 2px; text-transform: uppercase;
            color: #a8d5b5; margin-bottom: 8px;
        }
        .form-group input[type="text"], .form-group textarea {
            width: 100%; padding: 12px 16px; background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.15); border-radius: 10px;
            color: white; font-family: 'Segoe UI', Arial, sans-serif; font-size: 0.95rem;
            transition: all 0.3s ease; outline: none;
        }
        .form-group textarea { min-height: 250px; resize: vertical; }
        .form-group input:focus, .form-group textarea:focus {
            border-color: #7ebd91; background: rgba(255,255,255,0.1);
            box-shadow: 0 0 20px rgba(126,189,145,0.15);
        }
        
        .contador-palabras {
            text-align: right; font-family: 'League Spartan', sans-serif;
            font-size: 0.8rem; font-weight: 600; margin-top: 5px;
            transition: color 0.3s ease;
        }
        .contador-palabras.ok { color: #7ebd91; }
        .contador-palabras.warning { color: #d4a853; }
        .contador-palabras.error { color: #d4786e; }
        
        .upload-imagen {
            width: 100%; height: 200px; border: 2px dashed rgba(255,255,255,0.2);
            border-radius: 15px; display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.3s ease; background: rgba(255,255,255,0.02);
            overflow: hidden;
        }
        .upload-imagen:hover { border-color: #7ebd91; background: rgba(255,255,255,0.05); }
        .upload-imagen img { width: 100%; height: 100%; object-fit: contain; }
        .upload-placeholder { text-align: center; color: rgba(255,255,255,0.4); font-family: 'League Spartan', sans-serif; font-size: 0.85rem; }
        .upload-placeholder i { font-size: 2.5rem; display: block; margin-bottom: 10px; }
        
        .btn-publicar {
            width: 100%; padding: 15px; background: linear-gradient(135deg, #3d6b4f, #5a9470);
            border: none; border-radius: 12px; color: white;
            font-family: 'League Spartan', sans-serif; font-size: 1rem; font-weight: 700;
            letter-spacing: 2px; text-transform: uppercase; cursor: pointer; transition: all 0.3s ease;
        }
        .btn-publicar:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(90,148,112,0.4); }
        .btn-publicar:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }
        
        .preview-card {
            background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px; padding: 25px; position: sticky; top: 30px;
        }
        .preview-titulo-seccion {
            font-family: 'League Spartan', sans-serif; font-size: 0.75rem; font-weight: 700;
            letter-spacing: 3px; text-transform: uppercase; color: #7ebd91;
            margin-bottom: 20px; text-align: center;
        }
        .preview-tarjeta { background: rgba(0,0,0,0.3); border-radius: 15px; overflow: hidden; }
        .preview-imagen {
            width: 100%; height: 250px; background: rgba(255,255,255,0.05);
            display: flex; align-items: center; justify-content: center;
            color: rgba(255,255,255,0.2); font-size: 0.8rem; overflow: hidden;
        }
        .preview-imagen img { width: 100%; height: 100%; object-fit: cover; }
        .preview-contenido { padding: 20px; }
        .preview-titulo { font-family: 'Playfair Display', serif; font-size: 1.2rem; font-weight: 600; margin-bottom: 10px; min-height: 24px; }
        .preview-titulo.vacio { color: rgba(255,255,255,0.2); font-style: italic; }
        .preview-descripcion { font-size: 0.9rem; line-height: 1.6; color: rgba(255,255,255,0.7); min-height: 50px; }
        .preview-descripcion.vacio { color: rgba(255,255,255,0.2); font-style: italic; }
        
        .mensaje { padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; font-family: 'League Spartan', sans-serif; font-weight: 600; font-size: 0.9rem; }
        .mensaje-exito { background: rgba(46,125,50,0.25); border: 1px solid rgba(76,175,80,0.4); color: #81c784; }
        .mensaje-error { background: rgba(198,40,40,0.25); border: 1px solid rgba(244,67,54,0.4); color: #ef9a9a; }
    </style>
</head>
<body>
    <div class="contenedor">
        <div class="header-publicar">
            <a href="../Home/home.php" class="btn-volver"><i class="fas fa-arrow-left"></i> Volver</a>
            <h1 class="titulo-pagina">Crear publicación</h1>
            <span class="badge-categoria"><i class="fas fa-<?php echo $icono; ?>"></i> <?php echo $categoria_nombre; ?></span>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="mensaje mensaje-exito"><i class="fas fa-check-circle"></i> ¡Publicación creada! Pendiente de revisión.</div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="mensaje mensaje-error"><i class="fas fa-exclamation-circle"></i> Error al publicar.</div>
        <?php endif; ?>

        <div class="layout-publicar">
            <div class="form-card">
                <form action="procesar_publicacion.php" method="POST" enctype="multipart/form-data" id="formPublicar">
                    <input type="hidden" name="categoria" value="<?php echo $categoria; ?>">
                    <input type="hidden" name="imagen_recortada" id="imagenRecortadaData">
                    
                    <div class="form-group">
                        <label>Imagen de portada</label>
                        <div class="upload-imagen" id="uploadContainer" onclick="document.getElementById('inputImagen').click()">
                            <img id="previewImagenUpload" src="" style="display:none;">
                            <div class="upload-placeholder" id="uploadPlaceholder">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Subir imagen</span>
                                <small style="display:block;margin-top:5px;opacity:0.6;">Se recortará a formato vertical</small>
                            </div>
                        </div>
                        <input type="file" id="inputImagen" accept="image/*" style="display:none;" onchange="cargarImagen(event)">
                    </div>

                    <div class="form-group">
                        <label for="titulo">Título</label>
                        <input type="text" id="titulo" name="titulo" placeholder="Escribe un título..." required oninput="actualizarPreview()">
                    </div>

                    <div class="form-group">
                        <label for="contenido">Contenido</label>
                        <textarea id="contenido" name="contenido" placeholder="Describe lo que quieras compartir..." required oninput="actualizarPreview(); contarPalabras()"></textarea>
                        <div class="contador-palabras ok" id="contadorPalabras">0 / <?php echo $limite_palabras; ?> palabras</div>
                    </div>

                    <button type="submit" class="btn-publicar" id="btnPublicar">
                        <i class="fas fa-paper-plane"></i> Publicar en <?php echo $categoria_nombre; ?>
                    </button>
                </form>
            </div>

            <div class="preview-card">
                <div class="preview-titulo-seccion"><i class="fas fa-eye"></i> Vista previa</div>
                <div class="preview-tarjeta">
                    <div class="preview-imagen" id="previewImagen"><span>Sin imagen</span></div>
                    <div class="preview-contenido">
                        <div class="preview-titulo vacio" id="previewTitulo">Título de la publicación</div>
                        <div class="preview-descripcion vacio" id="previewDescripcion">El contenido aparecerá aquí...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <canvas id="canvasRecorte" style="display:none;"></canvas>

    <script>
        const limitePalabras = <?php echo $limite_palabras; ?>;
        let imagenDataURL = null;

        function cargarImagen(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    // Recortar a formato vertical (3:4)
                    const canvas = document.getElementById('canvasRecorte');
                    const ctx = canvas.getContext('2d');
                    
                    const targetWidth = Math.min(img.width, img.height * 0.75);
                    const targetHeight = targetWidth * 4/3;
                    const sx = (img.width - targetWidth) / 2;
                    const sy = Math.max(0, (img.height - targetHeight) / 2);
                    
                    canvas.width = 900;
                    canvas.height = 1200;
                    ctx.drawImage(img, sx, sy, targetWidth, targetHeight, 0, 0, 900, 1200);
                    
                    imagenDataURL = canvas.toDataURL('image/jpeg', 0.9);
                    document.getElementById('imagenRecortadaData').value = imagenDataURL;
                    document.getElementById('previewImagenUpload').src = imagenDataURL;
                    document.getElementById('previewImagenUpload').style.display = 'block';
                    document.getElementById('uploadPlaceholder').style.display = 'none';
                    
                    actualizarPreview();
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        function contarPalabras() {
            const texto = document.getElementById('contenido').value.trim();
            const palabras = texto ? texto.split(/\s+/).length : 0;
            const contador = document.getElementById('contadorPalabras');
            contador.textContent = palabras + ' / ' + limitePalabras + ' palabras';
            contador.className = 'contador-palabras ';
            if (palabras > limitePalabras) {
                contador.className += 'error';
                document.getElementById('btnPublicar').disabled = true;
            } else if (palabras > limitePalabras * 0.8) {
                contador.className += 'warning';
                document.getElementById('btnPublicar').disabled = false;
            } else {
                contador.className += 'ok';
                document.getElementById('btnPublicar').disabled = false;
            }
        }

        function actualizarPreview() {
            const titulo = document.getElementById('titulo').value || 'Título de la publicación';
            const contenido = document.getElementById('contenido').value || 'El contenido aparecerá aquí...';
            const previewTitulo = document.getElementById('previewTitulo');
            const previewDescripcion = document.getElementById('previewDescripcion');
            const previewImagen = document.getElementById('previewImagen');
            
            previewTitulo.textContent = titulo;
            previewDescripcion.textContent = contenido.length > 200 ? contenido.substring(0, 200) + '...' : contenido;
            previewTitulo.className = 'preview-titulo' + (document.getElementById('titulo').value ? '' : ' vacio');
            previewDescripcion.className = 'preview-descripcion' + (document.getElementById('contenido').value ? '' : ' vacio');
            
            if (imagenDataURL) {
                previewImagen.innerHTML = '<img src="' + imagenDataURL + '" alt="Preview">';
            } else {
                previewImagen.innerHTML = '<span>Sin imagen</span>';
            }
        }
    </script>
</body>
</html>
