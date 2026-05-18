<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
    header("Location: ../Home/home.php?error=no_autorizado");
    exit();
}

require_once "../../PHP/Perfil/conexion.php";

$id_admin = $_SESSION['usuario_id'];

// 🔍 Traemos los datos para renderizar la interfaz incluyendo el modo_oscuro
$query = "SELECT username, email, foto_perfil, banner_perfil, modo_oscuro FROM usuarios WHERE id = $id_admin";
$resultado = $conn->query($query);

if ($resultado && $row = $resultado->fetch_assoc()) {
    $nombre = $row['username'];
    $correo = $row['email'];
    $foto = $row['foto_perfil'] ?? 'default_avatar.png';
    $banner = $row['banner_perfil'] ?? 'default_banner.jpg';
    $modo_oscuro = $row['modo_oscuro'] ?? 0;
    
    // 🌟 LA CLAVE DE LA SEGURIDAD: Sincronizamos las variables que el backend unificado espera recibir
    $_SESSION['usuario'] = $row['username']; 
    $_SESSION['usuario_id'] = $id_admin;

} else {
    $nombre = "Admin";
    $correo = "";
    $foto = "default_avatar.png";
    $banner = "default_banner.jpg";
    $modo_oscuro = 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECOLIMA – Gestión de Usuarios</title>
    <link rel="stylesheet" href="../../CSS/Perfil/dashboard.css">
    <link class="config-style" rel="stylesheet" href="">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght=400;600;700;800;900&family=Playfair+Display:ital,wght=0,700;1,600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Estilos para la tabla adaptados al nuevo diseño */
        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            padding: 10px;
        }
        .users-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        .users-table th {
            padding: 16px;
            color: var(--green-dark);
            font-weight: 800;
            border-bottom: 2px solid var(--green-pale);
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        .users-table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--gray-100);
            font-size: 0.9rem;
            color: var(--text-mid);
            font-weight: 600;
        }
        .user-info-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-mini-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }
        .search-container {
            flex: 1;
            display: flex;
            justify-content: flex-end;
        }
        .search-input {
            padding: 8px 16px;
            border-radius: 12px;
            border: 2px solid var(--green-pale);
            outline: none;
            font-family: 'Nunito', sans-serif;
            width: 100%;
            max-width: 300px;
            transition: border-color 0.3s;
        }
        .search-input:focus { border-color: var(--green-mid); }
        
        /* Badges de rol */
        .role-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 800;
        }
        .role-admin { background: #ffe8cc; color: #d97706; }
        .role-user { background: var(--green-pale); color: var(--green-dark); }

        /* Estilo del Selector de Rol */
        .role-select {
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid var(--green-pale);
            background-color: #f0fdf4; 
            color: var(--green-dark);
            font-family: 'Nunito', sans-serif;
            font-weight: 700;
            font-size: 0.8rem;
            cursor: pointer;
            outline: none;
            transition: all 0.2s ease;
            appearance: none; 
        }

        .role-select:hover {
            border-color: var(--green-mid);
            background-color: var(--green-pale);
        }

        /* Estilo del Botón Eliminar */
        .btn-action-delete {
            background: none;
            border: none;
            color: var(--gray-400);
            padding: 8px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-action-delete:hover {
            background-color: #fee2e2; 
            color: #ef4444; 
            transform: scale(1.1);
        }

        /* ── Estilos Estructurales del Switch de Uiverse ── */
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
    <a href="dashboard_perfil.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Mi Perfil
    </a>
    <a href="dashboard_usuarios.php" class="nav-item active">
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
    <div class="profile-hero" style="height: 120px;">
      <img class="cover" src="../../assets/Fotos_banner/<?php echo $banner; ?>" id="imgBanner">
        
      <input type="file" id="inputBanner" accept="image/*" style="display: none;">      
      <div class="profile-avatar-wrap" style="bottom: -35px;">
        <img class="profile-avatar" style="width: 70px; height: 70px;" src="../../assets/Fotos_perfil/<?php echo $foto; ?>">
      </div>
    </div>
    <div class="profile-info" style="padding-top: 45px;">
      <div class="profile-name" style="font-size: 0.9rem;"><?php echo strtoupper($nombre); ?></div>
      <div class="profile-role">Gestión de Comunidad</div>
    </div>
  </div>

  <div class="feed-panel">
    <div class="feed-tabs" style="width: 100%; display: flex; align-items: center; justify-content: space-between;">
      <div class="tab-btn active">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        Lista de Usuarios
      </div>
      <div class="search-container">
          <input type="text" id="user-search" class="search-input" placeholder="🔍 Buscar nombre o correo...">
      </div>
    </div>

    <div class="feed-list">
        <div class="feed-card" style="display: block;">
            <div class="table-wrapper">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Correo Electrónico</th>
                            <th>Registro</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="users-table-body">
                         </tbody>
                </table>
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
    const miIdDeSesion = <?php echo $_SESSION['usuario_id']; ?>;
</script>
<script src="../../JavaScript/Dashboard/usuarios.js"></script>
<script>
    // LÓGICA DE CONTROL DE MODO OSCURO SIN CONFLICTOS 
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

    // Ejecución inmediata leyendo de localStorage o de la base de datos
    const preferedTheme = localStorage.getItem('theme');
    const phpTheme = <?php echo $modo_oscuro; ?>;
    if (preferedTheme === 'dark' || (preferedTheme === null && phpTheme === 1)) {
        aplicarModoOscuro(true);
    } else {
        aplicarModoOscuro(false);
    }

    // Evento manual del interruptor
    if(switchModoOscuro) {
        switchModoOscuro.addEventListener('change', async function() {
            const hitoDark = this.checked;
            aplicarModoOscuro(hitoDark);
            localStorage.setItem('theme', hitoDark ? 'dark' : 'light');

            const syncForm = new FormData();
            syncForm.append('toggle_dark_mode', '1');
            syncForm.append('modo_oscuro', hitoDark ? '1' : '0');
            try {
                await fetch('../Perfil/actualizar_configuracion.php', { method: 'POST', body: syncForm });
            } catch(e) { console.error("Error sincronizando tema: ", e); }
        });
    }

    // Funciones de control de UI de los drawers
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

    // 🌟 CONTROL DE ENVIÓ INTELIGENTE: Intercepción con Modal de Verificación de Email
    document.getElementById('formEditarPerfil').addEventListener('submit', async function(e) {
        e.preventDefault(); // Detiene la recarga nativa

        const name = document.getElementById('cfgName').value.trim();
        const email = document.getElementById('cfgEmail').value.trim();
        const passActual = document.getElementById('cfgPassActual').value;
        const passNueva = document.getElementById('cfgPass').value;

        if (!name || !email) {
            Swal.fire('Campos obligatorios', 'El nombre y correo no pueden estar vacíos.', 'warning');
            return;
        }

        // Validación de estándares mínimos de contraseñas
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

        // Se recopilan los datos del formulario de manera nativa
        const formData = new FormData(this);

        try {
            // Mostramos pantalla de carga inicial
            Swal.fire({
                title: 'Procesando...',
                text: 'Espere un momento, por favor.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            // 1. Primer intento de envío al backend unificado
            let response = await fetch('../Perfil/actualizar_configuracion.php', {
                method: 'POST',
                body: formData
            });

            let data = await response.json();

            // 2. 📧 SI EL BACKEND DE CORREO RESPONDE QUE EL EMAIL CAMBIÓ:
            if (data.status === 'need_verification') {
                
                // Abrimos el prompt interactivo de 6 números en SweetAlert2
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

                // Si el administrador cancela o cierra la alerta, abortamos el proceso
                if (!codigoIngresado) {
                    Swal.close();
                    return;
                }

                // Agregamos el token ingresado al set de datos del formulario
                formData.append('codigo_verificacion', codigoIngresado);

                // Mostramos cargando definitivo para la verificación del token
                Swal.fire({
                    title: 'Verificando código...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                // Re-enviamos el paquete completo de datos con el código incrustado
                response = await fetch('../Perfil/actualizar_configuracion.php', {
                    method: 'POST',
                    body: formData
                });
                data = await response.json();
            }

            // 3. Evaluar respuesta del Servidor (Éxito o Error definitivo)
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