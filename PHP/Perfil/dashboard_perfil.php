<?php 
require_once "verificar_sesion.php"; 
require_once "conexion.php";

// Obtenemos los datos completos del usuario logueado
$id_admin = $_SESSION['usuario_id'];
// 🔍 Recuperamos también el campo 'modo_oscuro' de la base de datos
$query = "SELECT username, email, fecha_registro, foto_perfil, banner_perfil, modo_oscuro FROM usuarios WHERE id = $id_admin";
$resultado = $conn->query($query);
$datos_admin = $resultado->fetch_assoc();

// Guardamos en variables
$nombre = $datos_admin['username'];
$correo = $datos_admin['email'];
$fecha = date("d M, Y", strtotime($datos_admin['fecha_registro']));
$foto = $datos_admin['foto_perfil'] ?? 'default_avatar.png';
$banner = $datos_admin['banner_perfil'] ?? 'default_banner.jpg';
$modo_oscuro = $datos_admin['modo_oscuro'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECOLIMA – Mi Perfil</title>
    <link rel="stylesheet" href="../../CSS/Perfil/dashboard.css">
    <link class="config-style" rel="stylesheet" href=""> 
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght=400;600;700;800;900&family=Playfair+Display:ital,wght=0,700;1,600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Pequeños ajustes para mantener las funciones de carga de imagen */
        .btn-change-media {
            position: absolute;
            right: 10px;
            bottom: 10px;
            background: rgba(0,0,0,0.5);
            color: white;
            border: none;
            padding: 8px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 5;
            transition: background 0.3s;
        }
        .btn-change-media:hover { background: var(--green-mid); }
        .profile-avatar-wrap { cursor: pointer; }

        /* ======================================================== */
        /* ✨ ESTILOS BASE ESTRUCTURALES PARA EL SWITCH (UIVERSE)    */
        /* ======================================================== */
        .switch {
            font-size: 17px;
            position: relative;
            display: inline-block;
            width: 3.5em;
            height: 2em;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #f4f4f5;
            transition: .4s;
            border-radius: 30px;
            overflow: hidden;
            border: 1px solid #e4e4e7;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 1.4em;
            width: 1.4em;
            border-radius: 20px;
            left: 0.3em;
            bottom: 0.25em;
            background: linear-gradient(40deg, #ff0080,#ff8c00 70%);
            transition: .4s;
            z-index: 2;
        }
        .clouds {
            position: absolute;
            width: 100%;
            height: 100%;
            fill: #ffffff;
            transition: 0.4s;
        }
        .stars {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: 0.4s;
            transform: translateY(-20px);
        }
        .star {
            position: absolute;
            fill: #fff;
        }
        #star-1 { width: 0.4em; top: 0.4em; left: 1.8em; }
        #star-2 { width: 0.3em; top: 0.8em; left: 2.5em; }
        #star-3 { width: 0.2em; top: 0.3em; left: 2.8em; }

        /* ── Estados Activos (Modo Oscuro checked) ── */
        input:checked + .slider {
            background-color: #1e293b;
            border-color: #334155;
        }
        input:checked + .slider:before {
            transform: translateX(1.5em);
            background: linear-gradient(40deg, #818cf8, #312e81 70%);
        }
        input:checked + .slider .clouds {
            transform: translateY(20px);
            opacity: 0;
        }
        input:checked + .slider .stars {
            transform: translateY(0);
            opacity: 1;
        }
    </style>
</head>
<body>

<nav class="sidebar">
  <div style="height: 70px; display: flex; align-items: center;">
    <a href="../Home/home.php" style="display: flex; align-items: center; text-decoration: none; gap: 10px;">
      <img src="../../assets/Perfil/Logo.png" style="width: 50px; height: 50px; margin-left: 13px;"/>
      <span style="font-family: sans-serif; font-weight: 700; font-size: 22px; color: #1a3a2a; letter-spacing: 2px;">ECOLIMA</span>
    </a>
  </div>

  <div class="sidebar-nav">
    <a href="dashboard_perfil.php" class="nav-item active">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Mi Perfil
    </a>
    <a href="dashboard_usuarios.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      Usuarios
    </a>
    <a href="dashboard_publicaciones.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
      Publicaciones
    </a>
    <a href="dashboard_solicitudes.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      Solicitudes
    </a>
    <a href="dashboard_estadisticas.php" class="nav-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
        Estadísticas
    </a>
  </div>

  <div class="sidebar-bottom">
    <button class="nav-item" onclick="openConfig()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2"/></svg>
      Configuración
    </button>
    <button class="nav-item danger" onclick="cerrarSesion()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      Cerrar sesión
    </button>
  </div>
</nav>

<main class="main">
  <div class="profile-card">
    <div class="profile-hero">
      <img class="cover" src="../../assets/Fotos_banner/<?php echo $banner; ?>" id="imgBanner">
      <button class="btn-change-media" onclick="document.getElementById('inputBanner').click();" title="Cambiar Portada">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="white"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
      </button>
      <input type="file" id="inputBanner" name="banner_perfil" accept="image/*" data-usuario-id="<?php echo $id_admin; ?>" style="display: none;">
      
      <div class="profile-avatar-wrap" onclick="document.getElementById('inputFoto').click();" title="Cambiar foto de perfil">
        <img class="profile-avatar" src="../../assets/Fotos_perfil/<?php echo $foto; ?>" id="imgPerfilGrande">
        <div class="camera-icon-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                <circle cx="12" cy="13" r="4"></circle>
            </svg>
        </div>
      </div>
      <input type="file" id="inputFoto" name="foto" accept="image/*" data-usuario-id="<?php echo $id_admin; ?>" style="display: none;">
    </div>

    <div class="profile-info">
      <div class="profile-name"><?php echo strtoupper($nombre); ?></div>
      <div class="profile-role">Super Administrador Ecolima</div>

      <div class="profile-stat">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
        <span class="profile-stat-text">Acceso de Nivel <?php echo $_SESSION['rol_id']; ?></span>
      </div>

      <div class="profile-bio">
        Correo registrado: <?php echo $correo; ?><br>
        Miembro desde: <?php echo $fecha; ?>
      </div>
      
      <button onclick="openConfig()" class="btn-save" style="width: 100%; margin-top: 20px;">Editar Perfil</button>
    </div>
  </div>

  <div class="feed-panel">
    <div class="feed-tabs">
        <div class="tab-btn active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            Estado del Sistema
        </div>
    </div>

    <div class="feed-list">
        <div class="feed-card">
            <div class="post-inner">
                <span class="post-tag">Security</span>
                <div class="post-title">Verificación de Identidad</div>
                <div class="post-body">Tu cuenta tiene privilegios de administrador para la gestión de ecosistemas en Colima. Asegúrate de mantener tus credenciales seguras.</div>
            </div>
        </div>
    </div>
  </div>
</main>

<div class="config-overlay" id="configOverlay" onclick="closeConfig()"></div>
<aside class="config-drawer" id="configDrawer">
  <div class="drawer-header">
    <h2>⚙️ Editar Perfil</h2>
    <button class="drawer-close" onclick="closeConfig()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>
  <div class="drawer-body">
    <form id="formEditarPerfil">
        <div class="config-group">
            <label>Nombre de Usuario</label>
            <input class="config-input" type="text" name="username" id="cfgName" value="<?php echo htmlspecialchars($nombre); ?>" required />
        </div>
        <div class="config-group">
            <label>Correo Electrónico</label>
            <input class="config-input" type="email" name="email" id="cfgEmail" value="<?php echo htmlspecialchars($correo); ?>" required />
        </div>
        
        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">

        <div class="config-group">
            <label>Apariencia del Sistema</label>
            <div class="theme-switch-wrapper" style="margin-top: 8px;">
                <label class="switch" for="switchModoOscuro" style="display: flex; align-items: center; width: 100%; height: 45px; background: rgba(0,0,0,0.05); border-radius: 12px; position: relative; cursor: pointer;">
                    <input type="checkbox" id="switchModoOscuro" style="display:none;" <?php echo ($modo_oscuro == 1) ? 'checked' : ''; ?>>
                    <div class="slider" style="position: absolute; top:0; left:0; right:0; bottom:0; border-radius: 12px; transition: 0.4s; display: flex; align-items: center; padding: 0 12px; overflow: hidden;">
                        
                        <div class="clouds" style="position: absolute; right: 15px; top: 0; bottom: 0; width: 50px; transition: 0.4s;">
                            <svg class="cloud" id="cloud-1" viewBox="0 0 24 24" fill="#ffffff" style="position:absolute; opacity:0.8;"><path d="M19.36 10.04A6 6 0 0 0 8 11a4 4 0 1 0-.6 7.87H19.4a4.38 4.38 0 0 0 .15-8.83z"/></svg>
                            <svg class="cloud" id="cloud-2" viewBox="0 0 24 24" fill="#ffffff" style="position:absolute; opacity:0.5;"><path d="M19.36 10.04A6 6 0 0 0 8 11a4 4 0 1 0-.6 7.87H19.4a4.38 4.38 0 0 0 .15-8.83z"/></svg>
                        </div>

                        <div class="stars" style="position: absolute; left: 15px; top: 0; bottom: 0; width: 50px; transition: 0.4s; opacity: 0; transform: translateY(-20px);">
                            <svg class="star" viewBox="0 0 24 24" fill="#ffffd0" style="position:absolute; width:10px;"><path d="M12 2l2.4 4.9 5.4.8-3.9 3.8.9 5.4-4.8-2.5-4.8 2.5.9-5.4-3.9-3.8 5.4-.8z"/></svg>
                            <svg class="star" viewBox="0 0 24 24" fill="#ffffff" style="position:absolute; width:6px; left:25px; top:25px;"><path d="M12 2l2.4 4.9 5.4.8-3.9 3.8.9 5.4-4.8-2.5-4.8 2.5.9-5.4-3.9-3.8 5.4-.8z"/></svg>
                        </div>

                        <div class="astro-icon" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; z-index: 2; transition: 0.4s; border-radius: 50%;">
                            <svg class="sun-svg" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5" style="width:20px; height:20px; transition: 0.4s;"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.22" x2="5.64" y2="17.78"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                            <svg class="moon-svg" viewBox="0 0 24 24" fill="none" stroke="#f1f5f9" stroke-width="2.2" style="width:18px; height:18px; transition: 0.4s; display:none;"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                        </div>
                        
                        <span class="lbl-modo" style="margin-left: 10px; font-family:'Nunito', sans-serif; font-size:0.88rem; font-weight:700; color: var(--green-dark); z-index:2; transition:0.4s;">Modo Claro</span>
                    </div>
                </label>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">

        <div class="config-group">
            <label style="color: #b91c1c; font-weight: 700;">Contraseña Actual</label>
            <input class="config-input" type="password" name="password_actual" id="cfgPassActual" placeholder="Requerida para confirmar cambios" />
        </div>
        <div class="config-group">
            <label>Contraseña Nueva</label>
            <input class="config-input" type="password" name="password_nueva" id="cfgPass" minlength="8" maxlength="32" placeholder="Dejar vacío para no cambiar" />
        </div>
        
        <p style="font-size: 0.7rem; color: #94a3b8; margin-top: 10px;">
            ID de Administrador: #<?php echo $id_admin; ?>
        </p>

        <button type="submit" class="btn-save" style="width: 100%; margin-top: 20px;">Guardar Cambios</button>
    </form>
  </div>
</aside>

<div class="toast" id="toast"></div>

<script>
  // 🌟 CONTROL DE MODO OSCURO CON PASO DE VALIDACIONES OBLIGATORIAS
    const switchModoOscuro = document.getElementById('switchModoOscuro');
    const configStyleLink = document.querySelector('.config-style');
    const labelModo = document.querySelector('.lbl-modo');
    const sunSvg = document.querySelector('.sun-svg');
    const moonSvg = document.querySelector('.moon-svg');
    const starsDiv = document.querySelector('.stars');
    const cloudsDiv = document.querySelector('.clouds');

    function aplicarModoOscuro(enforceDark) {
        if (enforceDark) {
            document.body.classList.add('dark-mode');
            if(configStyleLink) configStyleLink.setAttribute('href', '../../CSS/Perfil/Perfil_oscuro.css');
            if(labelModo) {
                labelModo.textContent = "Modo Oscuro";
                labelModo.style.color = "#f8fafc";
            }
            if(sunSvg) sunSvg.style.display = "none";
            if(moonSvg) moonSvg.style.display = "block";
            if(starsDiv) { starsDiv.style.opacity = "1"; starsDiv.style.transform = "translateY(0)"; }
            if(cloudsDiv) cloudsDiv.style.opacity = "0";
            if(switchModoOscuro) switchModoOscuro.checked = true;
        } else {
            document.body.classList.remove('dark-mode');
            if(configStyleLink) configStyleLink.setAttribute('href', '');
            if(labelModo) {
                labelModo.textContent = "Modo Claro";
                labelModo.style.color = "";
            }
            if(sunSvg) sunSvg.style.display = "block";
            if(moonSvg) moonSvg.style.display = "none";
            if(starsDiv) { starsDiv.style.opacity = "0"; starsDiv.style.transform = "translateY(-20px)"; }
            if(cloudsDiv) cloudsDiv.style.opacity = "1";
            if(switchModoOscuro) switchModoOscuro.checked = false;
        }
    }

    // Inicialización al cargar la página
    const preferedTheme = localStorage.getItem('theme');
    const phpTheme = <?php echo $modo_oscuro; ?>;
    if (preferedTheme === 'dark' || (preferedTheme === null && phpTheme === 1)) {
        aplicarModoOscuro(true);
    } else {
        aplicarModoOscuro(false);
    }

    // Enviar cambio asíncrono directo burlando la validación estricta
    if(switchModoOscuro) {
        switchModoOscuro.addEventListener('change', async function() {
            const hitoDark = this.checked;
            aplicarModoOscuro(hitoDark);
            localStorage.setItem('theme', hitoDark ? 'dark' : 'light');

            // 🔍 CAPTURAMOS LOS VALORES DE LOS INPUTS PARA SQUEEZEAR LA VALIDACIÓN DEL PHP
            const nombreActual = document.getElementById('cfgName') ? document.getElementById('cfgName').value : '';
            const correoActual = document.getElementById('cfgEmail') ? document.getElementById('cfgEmail').value : '';

            const datosSincronizacion = new URLSearchParams();
            datosSincronizacion.append('toggle_dark_mode', '1');
            datosSincronizacion.append('modo_oscuro', hitoDark ? '1' : '0');
            // Enviamos estos campos para que el backend pase el filtro "if (!username || !email)"
            datosSincronizacion.append('username', nombreActual);
            datosSincronizacion.append('email', correoActual);
            
            try {
                // Si estás dentro de la misma carpeta que el archivo usa 'actualizar_configuracion.php'
                // Si estás en otra subcarpeta usa '../Perfil/actualizar_configuracion.php'
                const response = await fetch('../Perfil/actualizar_configuracion.php', { 
                    method: 'POST', 
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: datosSincronizacion.toString() 
                });
                
                const textoRespuesta = await response.text();
                console.log("Respuesta del servidor para el modo oscuro:", textoRespuesta);
                
            } catch(e) { 
                console.error("Error de red al sincronizar con la base de datos: ", e); 
            }
        });
    }

    // Funciones de los Drawers y Modales
    function openConfig() {
        document.getElementById('configOverlay').classList.add('open');
        document.getElementById('configDrawer').classList.add('open');
    }
    function closeConfig() {
        document.getElementById('configOverlay').classList.remove('open');
        document.getElementById('configDrawer').classList.remove('open');
    }
    function cerrarSesion() {
        Swal.fire({
            title: '¿Cerrar sesión?',
            text: "Volverás a la pantalla de inicio",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2d6a4f',
            cancelButtonColor: '#b0b0b0',
            confirmButtonText: 'Sí, salir'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'cerrar_sesion.php';
            }
        })
    }

    // CONTROL DE ENVIÓ INTELIGENTE
    document.getElementById('formEditarPerfil').addEventListener('submit', async function(e) {
        e.preventDefault();

        const name = document.getElementById('cfgName').value.trim();
        const email = document.getElementById('cfgEmail').value.trim();
        const passActual = document.getElementById('cfgPassActual').value;
        const passNueva = document.getElementById('cfgPass').value;

        if (!name || !email) {
            Swal.fire('Campos obligatorios', 'El nombre y correo no pueden estar vacíos.', 'warning');
            return;
        }

        if (passNueva.length > 0) {
            if (passActual.length === 0) {
                Swal.fire('Seguridad', 'Debes escribir tu contraseña actual para poder asignar una nueva.', 'warning');
                return;
            }
            if (passNueva.length < 8 || passNueva.length > 32) {
                Swal.fire('Contraseña inválida', 'La nueva contraseña debe tener entre 8 y 32 caracteres.', 'warning');
                return;
            }
        }

        const formData = new FormData(this);

        try {
            Swal.fire({
                title: 'Procesando...',
                text: 'Espere un momento, por favor.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            let response = await fetch('../Perfil/actualizar_configuracion.php', {
                method: 'POST',
                body: formData
            });

            let data = await response.json();

            if (data.status === 'need_verification') {
                const { value: codigoIngresado } = await Swal.fire({
                    title: 'Confirma tu nuevo correo',
                    text: data.message,
                    input: 'text',
                    inputPlaceholder: '000000',
                    allowOutsideClick: false,
                    confirmButtonColor: '#2d6a4f',
                    confirmButtonText: 'Verificar y Guardar',
                    cancelButtonText: 'Cancelar',
                    showCancelButton: true,
                    inputAttributes: { 
                        maxlength: 6, 
                        style: 'text-align: center; letter-spacing: 5px; font-weight: bold; font-size: 24px;' 
                    },
                    inputValidator: (value) => {
                        if (!value || value.length !== 6 || isNaN(value)) {
                            return 'Debes introducir el código numérico de 6 dígitos.';
                        }
                    }
                });

                if (!codigoIngresado) {
                    Swal.close();
                    return;
                }

                formData.append('codigo_verificacion', codigoIngresado);

                Swal.fire({
                    title: 'Verificando código...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                response = await fetch('../Perfil/actualizar_configuracion.php', {
                    method: 'POST',
                    body: formData
                });
                data = await response.json();
            }

            if (data.status === 'success') {
                Swal.fire('¡Actualizado!', data.message, 'success').then(() => {
                    location.reload(); 
                });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
            
        } catch (error) {
            console.error('Error al actualizar la configuración:', error);
            Swal.fire('Error', 'No se pudo procesar la solicitud con el servidor.', 'error');
        }
    });
</script>
</body>
</html>