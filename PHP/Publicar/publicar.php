<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'escritor') {
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
    <title>Publicar en <?php echo $categoria_nombre; ?> — ECOLIMA</title>
    
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        :root {
            --verde-oscuro: #1a2a1a;
            --verde-medio: #2d5a3d;
            --verde-claro: #7ebd91;
            --crema: #f5f2eb;
            --blanco: #ffffff;
            --gris-texto: #5a5a5a;
            --sombra-suave: 0 4px 20px rgba(0, 0, 0, 0.06);
            --sombra-media: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--crema);
            min-height: 100vh;
            color: var(--verde-oscuro);
            padding: 40px 20px;
        }

        .contenedor {
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Header */
        .header-publicar {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 50px;
            flex-wrap: wrap;
        }

        .btn-volver {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--blanco);
            border: 1px solid rgba(0,0,0,0.08);
            color: var(--verde-oscuro);
            padding: 12px 22px;
            border-radius: 50px;
            text-decoration: none;
            font-family: 'League Spartan', sans-serif;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: var(--sombra-suave);
        }

        .btn-volver:hover {
            transform: translateX(-4px);
            box-shadow: var(--sombra-media);
            border-color: var(--verde-claro);
        }

        .titulo-pagina {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            font-weight: 600;
            color: var(--verde-oscuro);
            letter-spacing: -0.5px;
        }

        .badge-categoria {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--verde-oscuro);
            color: var(--blanco);
            padding: 8px 20px;
            border-radius: 50px;
            font-family: 'League Spartan', sans-serif;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* Layout */
        .layout-publicar {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 35px;
            align-items: start;
        }

        @media (max-width: 768px) {
            .layout-publicar {
                grid-template-columns: 1fr;
            }
        }

        /* Tarjeta formulario */
        .form-card {
            background: var(--blanco);
            border-radius: 20px;
            padding: 40px;
            box-shadow: var(--sombra-suave);
            border: 1px solid rgba(0,0,0,0.04);
        }

        .form-group {
            margin-bottom: 28px;
        }

        .form-group label {
            display: block;
            font-family: 'League Spartan', sans-serif;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--gris-texto);
            margin-bottom: 10px;
        }

        .form-group input[type="text"],
        .form-group textarea {
            width: 100%;
            padding: 14px 18px;
            background: #fafaf7;
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: 12px;
            color: var(--verde-oscuro);
            font-family: 'Segoe UI', system-ui, sans-serif;
            font-size: 1rem;
            transition: all 0.3s ease;
            outline: none;
            line-height: 1.6;
        }

        .form-group textarea {
            min-height: 280px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: var(--verde-claro);
            background: var(--blanco);
            box-shadow: 0 0 0 4px rgba(126, 189, 145, 0.1);
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #c0c0c0;
        }

        /* Contador */
        .contador-palabras {
            text-align: right;
            font-family: 'League Spartan', sans-serif;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 1px;
            margin-top: 8px;
            transition: color 0.3s ease;
        }

        .contador-palabras.ok { color: var(--verde-claro); }
        .contador-palabras.warning { color: #c9a44b; }
        .contador-palabras.error { color: #c96b6b; }

        /* Upload imagen */
        .upload-imagen {
            width: 100%;
            height: 180px;
            border: 2px dashed rgba(0,0,0,0.1);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #fafaf7;
            overflow: hidden;
        }

        .upload-imagen:hover {
            border-color: var(--verde-claro);
            background: #f5f9f6;
        }

        .upload-imagen img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .upload-placeholder {
            text-align: center;
            color: #b0b0b0;
            font-family: 'League Spartan', sans-serif;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }

        .upload-placeholder i {
            font-size: 2.2rem;
            display: block;
            margin-bottom: 10px;
            color: #ccc;
        }

        /* Botón publicar */
        .btn-publicar {
            width: 100%;
            padding: 16px;
            background: var(--verde-oscuro);
            border: none;
            border-radius: 14px;
            color: var(--blanco);
            font-family: 'League Spartan', sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-publicar:hover {
            background: var(--verde-medio);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(45, 90, 61, 0.3);
        }

        .btn-publicar:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Vista previa */
        .preview-card {
            background: var(--blanco);
            border-radius: 20px;
            padding: 30px;
            box-shadow: var(--sombra-suave);
            border: 1px solid rgba(0,0,0,0.04);
            position: sticky;
            top: 30px;
        }

        .preview-titulo-seccion {
            font-family: 'League Spartan', sans-serif;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: #aaa;
            margin-bottom: 25px;
            text-align: center;
        }

        .preview-tarjeta {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.06);
        }

        .preview-imagen {
            width: 100%;
            height: 220px;
            background: #f5f5f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ccc;
            font-size: 0.8rem;
            overflow: hidden;
        }

        .preview-imagen img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .preview-contenido {
            padding: 24px;
        }

        .preview-titulo {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--verde-oscuro);
            min-height: 24px;
        }

        .preview-titulo.vacio {
            color: #ccc;
            font-style: italic;
        }

        .preview-descripcion {
            font-size: 0.9rem;
            line-height: 1.7;
            color: #888;
            min-height: 50px;
        }

        .preview-descripcion.vacio {
            color: #ddd;
            font-style: italic;
        }

        /* Mensajes */
        .mensaje {
            padding: 16px 22px;
            border-radius: 14px;
            margin-bottom: 25px;
            font-family: 'League Spartan', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .mensaje-exito {
            background: #eaf7ec;
            border: 1px solid #c8e6c9;
            color: #2e7d32;
        }

        .mensaje-error {
            background: #fdecea;
            border: 1px solid #ffcdd2;
            color: #c62828;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <!-- Header -->
        <div class="header-publicar">
            <a href="../Home/home.php" class="btn-volver">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <h1 class="titulo-pagina">Crear publicación</h1>
            <span class="badge-categoria">
                <i class="fas fa-<?php echo $icono; ?>"></i>
                <?php echo $categoria_nombre; ?>
            </span>
        </div>

        <!-- Mensajes -->
        <?php if (isset($_GET['success'])): ?>
            <div class="mensaje mensaje-exito">
                <i class="fas fa-check-circle"></i> ¡Publicación creada! Pendiente de revisión.
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="mensaje mensaje-error">
                <i class="fas fa-exclamation-circle"></i> Error al publicar. Intenta de nuevo.
            </div>
        <?php endif; ?>

        <!-- Layout -->
        <div class="layout-publicar">
            
            <!-- Formulario -->
            <div class="form-card">
                <form action="procesar_publicacion.php" method="POST" enctype="multipart/form-data" id="formPublicar">
                    <input type="hidden" name="categoria" value="<?php echo $categoria; ?>">
                    <input type="hidden" name="imagen_recortada" id="imagenRecortadaData">
                    
                    <!-- Imagen -->
                    <div class="form-group">
                        <label>Imagen de portada</label>
                        <div class="upload-imagen" id="uploadContainer" onclick="document.getElementById('inputImagen').click()">
                            <img id="previewImagenUpload" src="" style="display:none;">
                            <div class="upload-placeholder" id="uploadPlaceholder">
                                <i class="fas fa-image"></i>
                                Subir imagen
                            </div>
                        </div>
                        <input type="file" id="inputImagen" accept="image/*" style="display:none;" onchange="cargarImagen(event)">
                    </div>

                    <!-- Título -->
                    <div class="form-group">
                        <label for="titulo">Título</label>
                        <input type="text" id="titulo" name="titulo" placeholder="Escribe un título claro y atractivo..." required oninput="actualizarPreview()">
                    </div>

                    <!-- Contenido -->
                    <div class="form-group">
                        <label for="contenido">Contenido</label>
                        <textarea id="contenido" name="contenido" placeholder="Comparte información valiosa sobre <?php echo $categoria_nombre; ?>..." required oninput="actualizarPreview(); contarPalabras()"></textarea>
                        <div class="contador-palabras ok" id="contadorPalabras">0 / <?php echo $limite_palabras; ?> palabras</div>
                    </div>

                    <!-- Botón -->
                    <button type="submit" class="btn-publicar" id="btnPublicar">
                        <i class="fas fa-paper-plane"></i> Publicar
                    </button>
                </form>
            </div>

            <!-- Vista previa -->
            <div class="preview-card">
                <div class="preview-titulo-seccion">Vista previa</div>
                <div class="preview-tarjeta">
                    <div class="preview-imagen" id="previewImagen">
                        <span>Sin imagen</span>
                    </div>
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