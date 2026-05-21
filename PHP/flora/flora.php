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
$foto_perfil = ''; // Variable para almacenar la imagen del usuario

if (isset($_SESSION['usuario_id'])) {
    $id_user = (int)$_SESSION['usuario_id'];
    // Extraemos modo_oscuro y foto_perfil en la misma consulta
    $query_theme = "SELECT modo_oscuro, foto_perfil FROM usuarios WHERE id = $id_user";
    $result_theme = $conn->query($query_theme);
    if ($result_theme && $row_theme = $result_theme->fetch_assoc()) {
        $modo_oscuro = (int)$row_theme['modo_oscuro'];
        $foto_perfil = $row_theme['foto_perfil'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="true">Flora - ECOLIMA</title>

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
    /* ==========================================================================
       SOLUCIÓN: HEADER FIJO AL HACER SCROLL (COMPORTAMIENTO GLOBAL UNIFICADO)
       ========================================================================== */
    .barra-superior {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
        box-sizing: border-box;
    }

    /* Margen de compensación para evitar superposición con el contenido */
    .seccion-hero {
        margin-top: 70px; 
    }

    /* ==========================================================================
       NUEVOS ESTILOS PARA EL AVATAR Y EFECTO HOVER DE LA CAJA DE PERFIL
       ========================================================================== */
    .perfil-box {
        cursor: pointer;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s ease;
    }

    /* Efecto hover interactivo */
    .perfil-box:hover {
        transform: scale(1.12);
        box-shadow: 0 0 12px rgba(155, 237, 183, 0.5);
    }

    /* Estilos para la foto de perfil real */
    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #9bedb7;
    }

    /* Icono si no hay foto */
    .perfil-box i {
        font-size: 1.4rem;
        transition: color 0.3s;
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

    /* ==========================================================================
       DISEÑO DE LOS ENLACES DE SESIÓN (IGUALACIÓN VISUAL)
       ========================================================================== */
    .caja-login {
        text-decoration: none;
        padding: 8px 16px;
        border-radius: 20px;
        transition: background 0.3s, color 0.3s;
        font-weight: 600;
    }

    /* ==========================================================================
       MODALES CON CRISTALERÍA (GLASSMORPHISM)
       ========================================================================== */
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

    /* Botones de acción dentro de modales */
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

    .btn-confirmar, .btn-login-modal {
        background: #2d5a27;
        text-decoration: none;
        padding: 12px 24px;
        border-radius: 10px;
        color: white;
        font-weight: 700;
        transition: 0.3s;
        display: inline-block;
    }

    .btn-confirmar:hover, .btn-login-modal:hover { background: #1e3d1a; transform: scale(1.05); }
    .btn-cancelar:hover { background: rgba(144, 238, 144, 0.5); }
    </style>
</head>

<body>

    <div class="barra-superior">

        <div class="iconos-izquierda" style="display: flex; align-items: center; gap: 15px;">

            <div class="caja-logo" onclick="window.location.href='../Home/home.php'" style="cursor: pointer;">
                <img src="../../assets/Home/logomini.png" alt="Logo" class="imagen-logo">
            </div>

            <div class="perfil-box" id="perfilBox" data-sesion="<?php echo $sesion_activa ? 'true' : 'false'; ?>" data-url="<?php echo $ruta_perfil; ?>">
                <?php if ($sesion_activa): ?>
                    <?php if (!empty($foto_perfil)): ?>
                        <img src="../../assets/Fotos_perfil/<?php echo htmlspecialchars($foto_perfil); ?>" alt="Avatar" class="avatar-img">
                    <?php else: ?>
                        <i class="fas fa-user-circle" style="color: #9bedb7;"></i>
                    <?php endif; ?>
                    <span class="notif-dot"></span> 
                <?php else: ?>
                    <i class="fas fa-user"></i>
                <?php endif; ?>
            </div>

        </div>

        <div class="botones-derecha">

            <div class="caja-buscador">
                <input type="text" id="inputBuscador" data-categoria-id="1" placeholder="Buscar en flora..." data-translate-placeholder="true">
                <i class="fas fa-search"></i>
            </div>

            <div class="lang-box">ES / EN</div>
                <?php if(isset($_SESSION['usuario'])): ?>
                <a href="#" class="caja-login" onclick="mostrarModal(event)" data-translate>Cerrar sesión</a>
                <?php else: ?>
                    <a href="../Login/login.php" class="caja-login" data-translate>Iniciar sesión</a>
                <?php endif; ?>

        </div>

    </div>


    <div class="seccion-hero">
        <h1 class="titulo-hero" data-translate="true">FLORA</h1>
        <p class="texto-hero" data-translate="true">Una categoría centrada en mostrar la diversidad de plantas del estado de Colima</p>

        <div class="barra-botones">
            <button class="boton-filtro" data-bg="../../assets/flora/subcategorias_bg/flora_todos.jpg" data-translate="true">Todos</button>
            <button class="boton-filtro" data-subcategoria="7" data-bg="../../assets/flora/subcategorias_bg/flora_arboles.jpg" data-translate="true">Arboles</button>
            <button class="boton-filtro" data-subcategoria="8" data-bg="../../assets/flora/subcategorias_bg/flora_arbustos.jpg" data-translate="true">Arbustos</button>
            <button class="boton-filtro" data-subcategoria="9" data-bg="../../assets/flora/subcategorias_bg/flora_cactacea.jpg" data-translate="true">Cactaceas</button>
            <button class="boton-filtro" data-subcategoria="10" data-bg="../../assets/flora/subcategorias_bg/flora_endemica.jpg" data-translate="true">Plantas Endémicas</button>
            <button class="boton-filtro" data-subcategoria="11" data-bg="../../assets/flora/subcategorias_bg/flora_extintas.jpg" data-translate="true">Plantas en riesgo</button>
            <button class="boton-filtro" data-subcategoria="12" data-bg="../../assets/flora/subcategorias_bg/flora_fungi.jpg" data-translate="true">Reino Fungi</button>
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
                <h2 data-translate="true">¿CERRAR SESIÓN?</h2>
                <p style="color: #ffffff; font-weight: 600; font-size: 0.95rem; margin-bottom: 20px; font-family: 'League Spartan', sans-serif; line-height: 1.4; text-shadow: 0px 1px 2px rgba(0,0,0,0.2);" data-translate="true">
                    Si cierras sesión, tendrás que volver a ingresar para ver tu perfil e interactuar en las publicaciones.
                </p>
            </div>
            <div class="modal-buttons">
                <button id="btnCancelar" class="btn-cancelar" data-translate="true">Cancelar</button>
                <a href="../Login/logout.php" class="btn-confirmar" data-translate="true">Aceptar</a>
            </div>
        </div>
    </div>

    <div id="modalRequRequiresLogin" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <i class="fas fa-lock" style="color: #ff4d4d; font-size: 2.2rem; margin-bottom: 12px;"></i>
                <h2 data-translate="true">INICIO REQUERIDO</h2>
                <p style="color: #ffffff; font-weight: 600; font-size: 0.95rem; margin-bottom: 20px; font-family: 'League Spartan', sans-serif; line-height: 1.4;" data-translate="true">
                    Para explorar tu perfil, personalizar tu interfaz e interactuar en la comunidad de E-COLIMA, necesitas iniciar sesión.
                </p>
            </div>
            <div class="modal-buttons">
                <button id="btnCancelarLogin" class="btn-cancelar" data-translate="true">Quedarme aquí</button>
                <a href="../Login/login.php" class="btn-login-modal" data-translate="true">Iniciar Sesión</a>
            </div>
        </div>
    </div>


    <script src="../../JavaScript/Flora/script.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalCerrar = document.getElementById('modalCerrarSesion');
        const btnCancelarCerrar = document.getElementById('btnCancelar');

        const perfilBox = document.getElementById('perfilBox');
        const modalLogin = document.getElementById('modalRequRequiresLogin');
        const btnCancelarLogin = document.getElementById('btnCancelarLogin');

        // Disparar modal de logout desde el navbar
        window.mostrarModal = function(event) {
            event.preventDefault(); 
            if(modalCerrar) modalCerrar.style.display = 'flex';
        }

        if (btnCancelarCerrar) {
            btnCancelarCerrar.onclick = function() {
                modalCerrar.style.display = 'none';
            }
        }

        // Control dinámico del clic en el Avatar
        if (perfilBox) {
            perfilBox.addEventListener('click', function() {
                const tieneSesion = perfilBox.getAttribute('data-sesion') === 'true';
                const urlDestino = perfilBox.getAttribute('data-url');

                if (tieneSesion) {
                    window.location.href = urlDestino;
                } else {
                    if (modalLogin) modalLogin.style.display = 'flex';
                }
            });
        }

        if (btnCancelarLogin) {
            btnCancelarLogin.onclick = function() {
                modalLogin.style.display = 'none';
            }
        }

        // Cerrar modales haciendo clic en las capas traseras opacas
        window.addEventListener('click', function(event) {
            if (event.target == modalCerrar) {
                modalCerrar.style.display = 'none';
            }
            if (event.target == modalLogin) {
                modalLogin.style.display = 'none';
            }
        });
    });

    // Evitar bugs visuales al regresar con las flechas nativas del navegador
    window.addEventListener('pageshow', function(event) {
        if (event.persisted || (typeof window.performance != "undefined" && window.performance.navigation.type === 2)) {
            window.location.reload();
        }
    });
    </script>

    <script src="../../JavaScript/Traduccion/traduccion.js"></script>
</body>
</html>