<?php
session_start();

$modo_oscuro = 0;

// 1. Verificamos si hay una sesión activa de usuario
if (!isset($_SESSION['usuario'])) {
    header("Location: ../Login/login.php");
    exit();
}

// 2. Capturamos las variables de rol (tanto en texto como en ID numérico si existen)
$rol_texto = isset($_SESSION['rol']) ? $_SESSION['rol'] : '';
$rol_id = isset($_SESSION['rol_id']) ? (int)$_SESSION['rol_id'] : 0;

// 3. DEFINICIÓN DE PERMISOS:
$es_admin = ($rol_texto === 'admin' || $rol_id === 1);
$es_escritor = ($rol_texto === 'escritor' || $rol_id === 3);

// 4. CONTROL DE ACCESO
if (!$es_admin && !$es_escritor) {
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

// 🌟 CORREGIDO: Eliminamos '$sesion_activa' ya que validamos la sesión en el paso 1
if (isset($_SESSION['usuario_id'])) {
    require_once "../../PHP/Perfil/conexion.php"; // Asegúrate de que esta ruta a tu archivo de conexión sea la correcta
    $id_user = (int)$_SESSION['usuario_id'];
    $query_theme = "SELECT modo_oscuro FROM usuarios WHERE id = $id_user";
    $result_theme = $conn->query($query_theme);
    if ($result_theme && $row_theme = $result_theme->fetch_assoc()) {
        $modo_oscuro = (int)$row_theme['modo_oscuro'];
    }
}
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
    
    <?php if ($modo_oscuro === 1): ?>
        <link rel="stylesheet" href="../../CSS/Publicar/estilo_oscuro.css">
    <?php else: ?>
        <link rel="stylesheet" href="../../CSS/Publicar/estilo.css">
    <?php endif; ?>

</head>
<body>
    <div class="contenedor">
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
                                <i class="fas fa-image"></i>
                                Subir imagen
                            </div>
                        </div>
                        <input type="file" id="inputImagen" accept="image/*" style="display:none;" onchange="cargarImagen(event)">
                    </div>

                    <div class="form-group">
                        <label for="titulo">Título</label>
                        <input type="text" id="titulo" name="titulo" placeholder="Escribe un título claro y atractivo..." required oninput="actualizarPreview()">
                    </div>

                    <div class="form-group">
                        <label for="selectSubcategoria">Subcategoría</label>
                        <select id="selectSubcategoria" name="subcategoria_id" required>
                            <option value="">Cargando subcategorías...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="contenido">Contenido</label>
                        <textarea id="contenido" name="contenido" placeholder="Comparte información valiosa sobre <?php echo $categoria_nombre; ?>..." required oninput="actualizarPreview(); contarPalabras()"></textarea>
                        <div class="contador-palabras ok" id="contadorPalabras">0 / <?php echo $limite_palabras; ?> palabras</div>
                    </div>

                    <button type="submit" class="btn-publicar" id="btnPublicar">
                        <i class="fas fa-paper-plane"></i> Publicar
                    </button>
                </form>
            </div>

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

    <div class="recortador-overlay" id="recortadorOverlay">
        <div class="recortador-container" id="recortadorContainer">
            <img id="imagenRecortar" src="" alt="Recortar">
            <div class="mascara-recorte" id="mascaraRecorte"></div>
        </div>
        <div class="botones-recorte">
            <button class="btn-recorte btn-cancelar-recorte" onclick="cancelarRecorte()">Cancelar</button>
            <button class="btn-recorte btn-confirmar-recorte" onclick="confirmarRecorte()">Recortar</button>
        </div>
    </div>
    <canvas id="canvasRecorte" style="display:none;"></canvas>

    <script>
    const limitePalabras = <?php echo $limite_palabras; ?>;
    let imagenDataURL = null;
    let imagenOriginal = null;

    // 🌟 NUEVO: Lógica automática para cargar Subcategorías según la URL actual
    document.addEventListener("DOMContentLoaded", function() {
        const categoriasIds = {
            'flora': 1,
            'fauna': 2,
            'ecosistemas': 3,
            'noticias': 4,
            'consejos': 5
        };

        const categoriaActual = "<?php echo $categoria; ?>"; 
        const idReal = categoriasIds[categoriaActual] || 2; // Fauna por defecto si no coincide

        cargarSubcategorias(idReal);
    });

    function cargarSubcategorias(categoriaId) {
        const selectSub = document.getElementById("selectSubcategoria");
        if (!selectSub) return;

        fetch(`get_subcategorias.php?categoria_id=${categoriaId}`)
            .then(response => response.json())
            .then(data => {
                selectSub.innerHTML = '<option value="">Selecciona una subcategoría</option>';
                
                if (data.length === 0) {
                    selectSub.innerHTML = '<option value="">General (Sin subcategorías)</option>';
                    return;
                }

                data.forEach(sub => {
                    selectSub.innerHTML += `<option value="${sub.id}">${sub.nombre}</option>`;
                });
            })
            .catch(error => {
                console.error("Error al cargar subcategorías:", error);
                selectSub.innerHTML = '<option value="">Error al cargar opciones</option>';
            });
    }

    // ========== SUBIR IMAGEN ==========
    function cargarImagen(event) {
        const file = event.target.files[0];
        if (!file) return;
            
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                imagenOriginal = img;
                abrirRecortador(e.target.result);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    // ========== RECORTADOR ==========
    let escalaRecorte = 1;
    let offsetX = 0, offsetY = 0;
    let arrastrando = false, inicioX, inicioY, inicioOffX, inicioOffY;

    function abrirRecortador(src) {
        document.getElementById('imagenRecortar').src = src;
        document.getElementById('recortadorOverlay').classList.add('activo');
        escalaRecorte = 1;
        offsetX = 0;
        offsetY = 0;
        actualizarTransform();
    }

    function actualizarTransform() {
        const img = document.getElementById('imagenRecortar');
        img.style.transform = `translate(${offsetX}px, ${offsetY}px) scale(${escalaRecorte})`;
        img.style.transformOrigin = 'center center';
    }

    function cancelarRecorte() {
        document.getElementById('recortadorOverlay').classList.remove('activo');
        document.getElementById('inputImagen').value = '';
        imagenOriginal = null;
    }

    function confirmarRecorte() {
        const img = document.getElementById('imagenRecortar');
        const mascara = document.getElementById('mascaraRecorte');
        const container = document.getElementById('recortadorContainer');
            
        const imgRect = img.getBoundingClientRect();
        const maskRect = mascara.getBoundingClientRect();
        const containerRect = container.getBoundingClientRect();
            
        const escalaX = imagenOriginal.width / imgRect.width;
        const escalaY = imagenOriginal.height / imgRect.height;
            
        const sx = (maskRect.left - imgRect.left) * escalaX;
        const sy = (maskRect.top - imgRect.top) * escalaY;
        const sw = maskRect.width * escalaX;
        const sh = maskRect.height * escalaY;
            
        const canvas = document.getElementById('canvasRecorte');
        canvas.width = 900;
        canvas.height = 1200;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(imagenOriginal, sx, sy, sw, sh, 0, 0, 900, 1200);
            
        imagenDataURL = canvas.toDataURL('image/jpeg', 0.9);
        document.getElementById('imagenRecortadaData').value = imagenDataURL;
        document.getElementById('previewImagenUpload').src = imagenDataURL;
        document.getElementById('previewImagenUpload').style.display = 'block';
        document.getElementById('uploadPlaceholder').style.display = 'none';
            
        document.getElementById('recortadorOverlay').classList.remove('activo');
        actualizarPreview();
    }

    // Eventos de arrastre
    document.getElementById('recortadorContainer').addEventListener('mousedown', function(e) {
        arrastrando = true;
        inicioX = e.clientX;
        inicioY = e.clientY;
        inicioOffX = offsetX;
        inicioOffY = offsetY;
        e.preventDefault();
    });

    document.addEventListener('mousemove', function(e) {
        if (!arrastrando) return;
        offsetX = inicioOffX + (e.clientX - inicioX);
        offsetY = inicioOffY + (e.clientY - inicioY);
        actualizarTransform();
    });

    document.addEventListener('mouseup', function() {
        arrastrando = false;
    });

    // Zoom con rueda
    document.getElementById('recortadorContainer').addEventListener('wheel', function(e) {
        e.preventDefault();
        if (e.deltaY < 0) escalaRecorte = Math.min(3, escalaRecorte + 0.05);
        else escalaRecorte = Math.max(0.3, escalaRecorte - 0.05);
        actualizarTransform();
    });

    // ========== CONTADOR ==========
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

    // ========== PREVIEW ==========
    function actualizarPreview() {
        const titulo = document.getElementById('titulo').value || 'Título de la publicación';
        const contenido = document.getElementById('contenido').value || 'El contenido aparecerá aquí...';
        document.getElementById('previewTitulo').textContent = titulo;
        document.getElementById('previewDescripcion').textContent = contenido.length > 200 ? contenido.substring(0, 200) + '...' : contenido;
        document.getElementById('previewTitulo').className = 'preview-titulo' + (document.getElementById('titulo').value ? '' : ' vacio');
        document.getElementById('previewDescripcion').className = 'preview-descripcion' + (document.getElementById('contenido').value ? '' : ' vacio');
        if (imagenDataURL) {
            document.getElementById('previewImagen').innerHTML = '<img src="' + imagenDataURL + '" alt="Preview">';
        } else {
            document.getElementById('previewImagen').innerHTML = '<span>Sin imagen</span>';
        }
    }
    </script>
</body>
</html>