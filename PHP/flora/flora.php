<?php
session_start();
include('../Perfil/conexion.php');

// 2. Verificamos si hay sesión activa
$sesion_activa = isset($_SESSION['usuario']);

// 3. Definimos la ruta del perfil con seguridad
if ($sesion_activa) {
    // Convertimos a entero (int) para asegurar que la comparación sea numérica
    $rol = isset($_SESSION['rol_id']) ? (int)$_SESSION['rol_id'] : 0; 
    
    if ($rol === 1) {
        $ruta_perfil = '../Perfil/dashboard_perfil.php';
    } else {
        $ruta_perfil = '../Perfil/perfil.php';
    }
} else {
    $ruta_perfil = '../Login/login.php'; 
}

$modo_oscuro = 0;
// 🌟 CORREGIDO: Removimos la variable inexistente '$sesion_activa'
if (isset($_SESSION['usuario_id'])) {
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
    <title>Flora - ECOLIMA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <?php if ($modo_oscuro === 1): ?>
        <link rel="stylesheet" href="../../CSS/Flora/styles_oscuro.css">
    <?php else: ?>
        <link rel="stylesheet" href="../../CSS/Flora/styles.css">
    <?php endif; ?>
    <style>
    /* Caja de perfil e indicador de notificaciones activas */
    .perfil-box {
        cursor: pointer;
        font-size: 1.2rem;
        color: #1a3a2a; 
        position: relative;
        display: inline-block;
        padding: 5px;
    }
    .notif-dot {
        position: absolute;
        top: 2px;
        right: 2px;
        width: 8px;
        height: 8px;
        background-color: #ff4d4d;
        border-radius: 50%;
    }

    /* Fondo oscuro desenfocado del modal */
    .modal-overlay {
        display: none; 
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.4); 
        backdrop-filter: blur(8px);
        z-index: 9999; 
        justify-content: center;
        align-items: center;
    }

    /* Tarjeta interna (Glassmorphism) */
    .modal-content {
        background: rgba(255, 255, 255, 0.15);
        padding: 2.5rem;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        text-align: center;
        width: 380px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    .modal-content h2 {
        color: white;
        margin: 15px 0;
        font-family: 'League Spartan', sans-serif;
        font-size: 1.8rem;
    }

    .modal-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 25px;
    }

    /* Botón Cancelar - Verde translúcido */
    .btn-cancelar {
        background: rgba(144, 238, 144, 0.3);
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        color: white;
        cursor: pointer;
        font-weight: 600;
        transition: 0.3s;
    }

    /* Botón Confirmar - Verde Sólido */
    .btn-confirmar {
        background: #2d5a27;
        text-decoration: none;
        padding: 12px 24px;
        border-radius: 10px;
        color: white;
        font-weight: 700;
        transition: 0.3s;
    }

    .btn-confirmar:hover { background: #1e3d1a; transform: scale(1.05); }
    .btn-cancelar:hover { background: rgba(144, 238, 144, 0.5); }
    </style>
</head>

<body>

    <div class="barra-superior">

        <div class="iconos-izquierda" style="display: flex; align-items: center; gap: 15px;">

            <div class="caja-logo" onclick="window.location.href='../Home/home.php'" style="cursor: pointer;">
                <img src="../../assets/Home/logomini.png" alt="Logo" class="imagen-logo">
            </div>

            <div class="perfil-box" onclick="window.location.href='<?php echo $ruta_perfil; ?>'">
                <i class="fas fa-user"></i>
                <?php if ($sesion_activa): ?>
                    <span class="notif-dot"></span> 
                <?php endif; ?>
            </div>

        </div>

        <div class="botones-derecha">

            <div class="caja-buscador">
                <input type="text" id="inputBuscador" data-categoria-id="1" placeholder="Buscar en flora...">
                <i class="fas fa-search"></i>
            </div>

            <div class="caja-idioma">ES / EN</div>

            <?php if($sesion_activa): ?>
                <a href="#" class="caja-login" onclick="mostrarModal(event)">Cerrar sesión</a>
            <?php else: ?>
                <a href="../Login/login.php" class="caja-login">Iniciar sesión</a>
            <?php endif; ?>

        </div>

    </div>


    <div class="seccion-hero">
        <h1 class="titulo-hero">FLORA</h1>
        <p class="texto-hero">Una categoría centrada en mostrar la diversidad de plantas del estado de Colima</p>

        <div class="barra-botones">
            <button class="boton-filtro"  data-bg="../../assets/flora/subcategorias_bg/flora_todos.jpg">Todos</button>

            <button class="boton-filtro" data-subcategoria="7" data-bg="../../assets/flora/subcategorias_bg/flora_arboles.jpg">Arboles</button>
            <button class="boton-filtro" data-subcategoria="8" data-bg="../../assets/flora/subcategorias_bg/flora_arbustos.jpg">Arbustos</button>
            <button class="boton-filtro" data-subcategoria="9" data-bg="../../assets/flora/subcategorias_bg/flora_cactacea.jpg">Cactaceas</button>
            <button class="boton-filtro" data-subcategoria="10" data-bg="../../assets/flora/subcategorias_bg/flora_endemica.jpg">Plantas Endémicas</button>
            <button class="boton-filtro" data-subcategoria="11" data-bg="../../assets/flora/subcategorias_bg/flora_extintas.jpg">Plantas en riesgo</button>
            <button class="boton-filtro" data-subcategoria="12" data-bg="../../assets/flora/subcategorias_bg/flora_fungi.jpg">Reino Fungi</button>
        </div>

    </div>


    <div class="contenido-principal" id="contenedorPrincipal">
        <div class="grid-publicaciones" id="contenedorPublicaciones"></div>
    </div>

    <button class="btn-scroll-top" id="btnScrollTop" aria-label="Volver arriba">
        <img src="../../assets/flora/iconoBtnTopScroll.png" alt="Volver arriba">
    </button>


    <div id="modalCerrarSesion" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <i class="fas fa-paw" style="color: #9bedb7; font-size: 2rem; margin-bottom: 10px; transform: rotate(-20deg);"></i>
                <h2>¿CERRAR SESIÓN?</h2>
                <p style="color: #ffffff; font-weight: 600; font-size: 0.95rem; margin-bottom: 20px; font-family: 'League Spartan', sans-serif; line-height: 1.4; text-shadow: 0px 1px 2px rgba(0,0,0,0.2);">
                    Si cierras sesión, tendrás que volver a ingresar para ver tu perfil e interactuar en las publicaciones.
                </p>
            </div>
            <div class="modal-buttons">
                <button id="btnCancelar" class="btn-cancelar">Cancelar</button>
                <a href="../Login/logout.php" class="btn-confirmar">Aceptar</a>
            </div>
        </div>
    </div>


    <script src="../../JavaScript/Flora/script.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modalCerrarSesion');
        const btnCancelar = document.getElementById('btnCancelar');

        // Muestra el modal interceptando el clic del enlace
        window.mostrarModal = function(event) {
            event.preventDefault(); 
            if(modal) modal.style.display = 'flex';
        }

        if (btnCancelar) {
            btnCancelar.onclick = function() {
                modal.style.display = 'none';
            }
        }

        // Cierra el modal al pulsar en las zonas externas borrosas
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    });

    // Forzar renderizado fresco al regresar usando el historial del cliente
    window.addEventListener('pageshow', function(event) {
        if (event.persisted || (typeof window.performance != "undefined" && window.performance.navigation.type === 2)) {
            window.location.reload();
        }
    });
    </script>

</body>
</html>
