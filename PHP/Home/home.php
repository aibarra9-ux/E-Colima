<?php
session_start();

// 2. Verificamos si hay sesión activa
$sesion_activa = isset($_SESSION['usuario']);

// --- NUEVA LÓGICA DE CONFIGURACIÓN DE APARIENCIA Y PERFIL EN BASE DE DATOS ---
$modo_oscuro = 0;
$foto_perfil = ''; // Variable para almacenar la imagen del usuario

if ($sesion_activa && isset($_SESSION['usuario_id'])) {
    require_once "../../PHP/Perfil/conexion.php"; // Asegúrate de que esta ruta a tu archivo de conexión sea la correcta
    $id_user = (int)$_SESSION['usuario_id'];
    
    // Traemos modo_oscuro y foto_perfil en la misma consulta
    $query_theme = "SELECT modo_oscuro, foto_perfil FROM usuarios WHERE id = $id_user";
    $result_theme = $conn->query($query_theme);
    if ($result_theme && $row_theme = $result_theme->fetch_assoc()) {
        $modo_oscuro = (int)$row_theme['modo_oscuro'];
        $foto_perfil = $row_theme['foto_perfil'];
    }
}

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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate>E-COLIMA</title>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;600;700&display=swap" rel="stylesheet">
    
    <?php if ($modo_oscuro === 1): ?>
        <link rel="stylesheet" href="../../CSS/Home/style_oscuro.css">
    <?php else: ?>
        <link rel="stylesheet" href="../../CSS/Home/style.css">
    <?php endif; ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
/* Capa de fondo con desenfoque */
.modal-overlay {
    display: none; /* Se activa con JS */
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.4); 
    backdrop-filter: blur(8px);
    z-index: 9999; /* Por encima de todo */
    justify-content: center;
    align-items: center;
}

/* El rectángulo de cristal (Glassmorphism) */
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

/* Botón Cancelar - Verde claro semi-transparente */
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

/* Botón Confirmar / Aceptar - Verde bosque oscuro */
.btn-confirmar {
    background: #2d5a27;
    text-decoration: none;
    padding: 12px 24px;
    border-radius: 10px;
    color: white;
    font-weight: 700;
    transition: 0.3s;
}

.btn-confirmar:hover { background: #2d5a27; transform: scale(1.05); }
.btn-cancelar:hover { background: rgba(144, 238, 144, 0.5); }

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

/* Estilos de la foto de perfil */
.avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #9bedb7; /* Borde sutil verde Ecolima */
}

/* Icono si no hay foto */
.perfil-box i {
    font-size: 1.4rem;
    transition: color 0.3s;
}

/* Estilo para el botón de login en el modal de advertencia */
.btn-login-modal {
    background: #2d5a27;
    text-decoration: none;
    padding: 12px 24px;
    border-radius: 10px;
    color: white;
    font-weight: 700;
    transition: 0.3s;
}
.btn-login-modal:hover { background: #1e3d1a; transform: scale(1.05); }
</style>

</head>
<body>

    <div class="top-bar">
        <div class="topbar-main-row">
            <div class="left-icons">
                <div class="logo-box">
                    <img src="../../assets/Home/Logo_Oficial.png" alt="Logo" class="logo-img">
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
        
            <div class="right-buttons">
                <form action="buscar.php" method="GET" class="search-box">
                    <input type="text" name="q" placeholder="Buscar publicaciones..." data-translate-placeholder required>
                    <button type="submit" style="background: none; border: none; color: inherit; cursor: pointer; padding: 0;">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <div class="lang-box">ES / EN</div>
                <?php if(isset($_SESSION['usuario'])): ?>
                    <a href="#" class="login-box" onclick="mostrarModal(event)" data-translate>Cerrar sesión</a>
                <?php else: ?>
                    <a href="../Login/login.php" class="login-box" data-translate>Iniciar sesión</a>
                <?php endif; ?>        
            </div>
                  
        </div>

        <nav class="topbar-categorias">
            <?php 
            // 🌟 CORRECCIÓN DE LA LÓGICA DE REVISIONES (Híbrida para login normal y Google Login)
            $rol_actual = $_SESSION['rol'] ?? '';
            $rol_numerico = isset($_SESSION['rol_id']) ? (int)$_SESSION['rol_id'] : 0;
            
            if (isset($_SESSION['usuario']) && ($rol_actual === 'admin' || $rol_actual === 'editor' || $rol_numerico === 1 || $rol_numerico === 3)): 
            ?>
                <a href="../Editor/administrar_solicitudes.php" class="topbar-link" data-translate>Solicitudes</a>
            <?php endif; ?>
            <a href="../flora/flora.php" class="topbar-link" data-translate>Flora</a>
            <a href="../Fauna/Fauna.php" class="topbar-link" data-translate>Fauna</a>
            <a href="../Ecosistemas/ecosistemas.php" class="topbar-link" data-translate>Ecosistemas</a>
            <a href="../Consejos/consejos.php" class="topbar-link" data-translate>Consejos</a>
            <a href="../Noticias/noticias.php" class="topbar-link" data-translate>Noticias</a>
        </nav>
    </div>

    <?php if(isset($_SESSION['mensaje'])): ?>
        <div class="alerta <?php echo $_SESSION['tipo'] ?? ''; ?>">
            <?php 
                echo $_SESSION['mensaje']; 
                unset($_SESSION['mensaje']);
            ?>
        </div>
    <?php endif; ?>

    <div class="home">
        <?php if(isset($_GET['success']) && isset($_SESSION['usuario'])): ?>
            <p class="bienvenida" data-translate>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?></p>
        <?php endif; ?>
        
        <h1 class="logo">
            E<span class="glass">CO</span>LIMA
        </h1>
    </div>

    <div class="slogan" data-translate>
        Colima es un museo vivo de biodiversidad, donde cada especie y cada paisaje cuentan una historia; explora, aprende y descubre cómo conservar estos tesoros naturales con información completa y confiable.
    </div>

    <section class="categorias">
        <h2 class="categorias-titulo" data-translate>Adéntrate en la red de la vida.</h2>
        <div class="categorias-grid">
            <div class="categoria-card">
                <div class="card-image" style="background-image: url('../../assets/Home/ipomoea2.jpg');">
                    <div class="card-overlay">
                        <h3 class="card-titulo" data-translate>Flora</h3>
                        <p class="card-descripcion" data-translate>Descubre la increíble variedad de plantas y vegetación que hacen único a Colima</p>
                        <a class="card-boton" href="../flora/flora.php" data-translate>
                            Leer más <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="categoria-card">
                <div class="card-image" style="background-image: url('../../assets/Fauna/Fauna categoria.webp');">
                    <div class="card-overlay">
                        <h3 class="card-titulo" data-translate>Fauna</h3>
                        <p class="card-descripcion" data-translate>Conoce las fascinantes especies animales que habitan en los diversos ecosistemas de Colima</p>
                        <a class="card-boton" href="../Fauna/Fauna.php" data-translate>
                            Leer más <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="categoria-card">
                <div class="card-image" style="background-image: url('../../assets/Ecosistemas/Ecosistemas categoria.png');">
                    <div class="card-overlay">
                        <h3 class="card-titulo" data-translate>Ecosistemas</h3>
                        <p class="card-descripcion" data-translate>Conoce los fascinantes ecosistemas terrestres del estado de Colima</p>
                        <a class="card-boton" href="../Ecosistemas/ecosistemas.php" data-translate>
                            Leer más <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="categoria-card">
                <div class="card-image" style="background-image: url('../../assets/Consejos/Ecosistemas categoria.jpg');">
                    <div class="card-overlay">
                        <h3 class="card-titulo" data-translate>Consejos</h3>
                        <p class="card-descripcion" data-translate>Conoce educativos consejos para cuidar la vida terrestre</p>
                        <a class="card-boton" href="../Consejos/consejos.php" data-translate>
                            Leer más <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="categoria-card">
                <div class="card-image" style="background-image: url('../../assets/Noticias/Ecosistemas categoria.jpg');">
                    <div class="card-overlay">
                        <h3 class="card-titulo" data-translate>Noticias</h3>
                        <p class="card-descripcion" data-translate>Conoce las mas recientes noticias acerca de la vida terrestre</p>
                        <a class="card-boton" href="../Noticias/noticias.php" data-translate>
                            Leer más <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

             <div class="categoria-card">
                <div class="card-image" style="background-image: url('../../assets/Noticias/por defecto.jpg');">
                    <div class="card-overlay">
                        <h3 class="card-titulo" data-translate>PROXIMAMENTE...</h3>
                        <a class="card-boton" href="#" data-translate>
                            Leer más <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer-nuevo">
        <div class="footer-container">
            <div class="footer-column branding">
                <div class="logo-eco-lima">
                    <img src="../../assets/Home/letraslogo_sinfondo.png" alt="Logo Ecolima" class="img-principal">
                </div>
                
                <a href="https://github.com/aibarra9-ux/E-Colima" target="_blank" class="github-btn" data-translate>
                    <i class="fab fa-github"></i> View with GitHub
                </a>

                <div class="facultad-tag" data-translate>
                    2026 FACULTAD DE INGENIERÍA ELECTROMECÁNICA
                </div>
            </div>

            <div class="footer-column">
                <h3 class="footer-title" data-translate>LICENCIA</h3>
                <div class="box-text">
                    <p data-translate>Este proyecto está bajo la Licencia MIT. Eres libre de usar, copiar y modificar el software para fines personales, educativos o comerciales, manteniendo siempre el aviso de autoría original.</p>
                </div>
            </div>

            <div class="footer-column">
                <h3 class="footer-title" data-translate>RECURSOS</h3>
                <ul class="lista-footer">
                    <li>MY SQL</li><li>PHP</li><li>HTML</li><li>CSS</li><li>JAVA SCRIPT</li><li>GITHUB</li><li>VISUAL STUDIO CODE</li><li>XAMPP</li>
                </ul>
            </div>

            <div class="footer-column">
                <h3 class="footer-title" data-translate>EQUIPO</h3>
                <ul class="lista-footer nombres-equipo">
                    <li>ALAN IBARRA</li><li>CAROLINA ZÚÑIGA</li><li>DANA NAVA</li><li>MIRANDA NAVA</li><li>RICARDO BARBA</li>
                </ul>
            </div>

            <img src="../../assets/Home/iconologo_sinfondo.png" alt="Icono Decorativo" class="footer-decor-icon">
        </div>
    </footer>

    <script>
    setTimeout(() => {
        const alerta = document.querySelector('.alerta');
        if(alerta){
            alerta.style.animation = "salir 0.4s ease forwards";
            setTimeout(() => { alerta.remove(); }, 400);
        }
    }, 1500);
    </script>

    <?php 
    $rol_string = $_SESSION['rol'] ?? '';
    $rol_numerico = isset($_SESSION['rol_id']) ? (int)$_SESSION['rol_id'] : 0;

    if (isset($_SESSION['usuario']) && ($rol_string === 'escritor' || $rol_string === 'admin' || $rol_numerico === 1 || $rol_numerico === 2)): 
    ?>
    <div class="fab-container">
        <button class="fab-btn" id="fabBtn" aria-label="Crear publicación">
            <i class="fas fa-plus"></i>
        </button>
        <div class="fab-menu" id="fabMenu">
            <a href="../Publicar/publicar.php?categoria=flora" class="fab-item"><i class="fas fa-leaf"></i><span data-translate>Flora</span></a>
            <a href="../Publicar/publicar.php?categoria=fauna" class="fab-item"><i class="fas fa-paw"></i><span data-translate>Fauna</span></a>
            <a href="../Publicar/publicar.php?categoria=ecosistemas" class="fab-item"><i class="fas fa-mountain"></i><span data-translate>Ecosistemas</span></a> 
            <a href="../Publicar/publicar.php?categoria=noticias" class="fab-item"><i class="fas fa-newspaper"></i><span data-translate>Noticias</span></a>            
            <a href="../Publicar/publicar.php?categoria=consejos" class="fab-item"><i class="fas fa-lightbulb"></i><span data-translate>Consejos</span></a>            
        </div>
    </div>

    <script>
        const fabBtn = document.getElementById('fabBtn');
        const fabMenu = document.getElementById('fabMenu');

        fabBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            fabMenu.classList.toggle('active');
            fabBtn.classList.toggle('active');
        });

        document.addEventListener('click', (e) => {
            if (!fabBtn.contains(e.target) && !fabMenu.contains(e.target)) {
                fabMenu.classList.remove('active');
                fabBtn.classList.remove('active');
            }
        });
    </script>
    <?php endif; ?>

    <div id="modalCerrarSesion" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <i class="fas fa-paw" style="color: #9bedb7; font-size: 2rem; margin-bottom: 10px;"></i>
                <h2 data-translate>¿CERRAR SESIÓN?</h2>
                <p style="color: #ffffff; font-weight: 600; font-size: 0.95rem; margin-bottom: 20px; font-family: 'League Spartan', sans-serif; line-height: 1.4;" data-translate>
                    Si cierras sesión, tendrás que volver a ingresar para ver tu perfil e interactuar en las publicaciones.
                </p>
            </div>
            <div class="modal-buttons">
                <button id="btnCancelar" class="btn-cancelar" data-translate>Cancelar</button>
                <a href="../Login/logout.php" class="btn-confirmar" data-translate>Aceptar</a>
            </div>
        </div>
    </div>

    <div id="modalRequRequiresLogin" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <i class="fas fa-lock" style="color: #ff4d4d; font-size: 2.2rem; margin-bottom: 12px;"></i>
                <h2 data-translate>INICIO REQUERIDO</h2>
                <p style="color: #ffffff; font-weight: 600; font-size: 0.95rem; margin-bottom: 20px; font-family: 'League Spartan', sans-serif; line-height: 1.4;" data-translate>
                    Para explorar tu perfil, personalizar tu interfaz e interactuar en la comunidad de E-COLIMA, necesitas iniciar sesión.
                </p>
            </div>
            <div class="modal-buttons">
                <button id="btnCancelarLogin" class="btn-cancelar" data-translate>Quedarme aquí</button>
                <a href="../Login/login.php" class="btn-login-modal" data-translate>Iniciar Sesión</a>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalCerrar = document.getElementById('modalCerrarSesion');
        const btnCancelarCerrar = document.getElementById('btnCancelar');
        
        const perfilBox = document.getElementById('perfilBox');
        const modalLogin = document.getElementById('modalRequRequiresLogin');
        const btnCancelarLogin = document.getElementById('btnCancelarLogin');

        // Control del clic en Cerrar Sesión (desde el navbar)
        window.mostrarModal = function(event) {
            event.preventDefault(); 
            if(modalCerrar) modalCerrar.style.display = 'flex';
        }

        if (btnCancelarCerrar) {
            btnCancelarCerrar.onclick = function() {
                modalCerrar.style.display = 'none';
            }
        }

        // Control inteligente del clic en el Avatar de Perfil
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

        // Evento para cerrar ventanas haciendo clic fuera del contenido del modal
        window.addEventListener('click', function(event) {
            if (event.target == modalCerrar) {
                modalCerrar.style.display = 'none';
            }
            if (event.target == modalLogin) {
                modalLogin.style.display = 'none';
            }
        });
    });
    </script>

    <script>
    window.addEventListener('pageshow', function(event) {
        if (event.persisted || (typeof window.performance != "undefined" && window.performance.navigation.type === 2)) {
            window.location.reload();
        }
    });
    </script>

    <script>
    (function() {
        const cards = document.querySelectorAll('.categoria-card');
        if (!cards.length) return;

        cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(60px)';
            card.style.transition = 'opacity 1.4s ease, transform 1.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        });

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        cards.forEach(function(card) { observer.observe(card); });
    })();
    </script>

    <script src="../../JavaScript/Traduccion/traduccion.js"></script>
</body>
</html>
