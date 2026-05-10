<?php 
require_once "verificar_sesion.php"; 
require_once "conexion.php";

// Obtenemos los datos completos del usuario logueado
$id_admin = $_SESSION['usuario_id'];
$query = "SELECT username, email, fecha_registro, foto_perfil, banner_perfil FROM usuarios WHERE id = $id_admin";
$resultado = $conn->query($query);
$datos_admin = $resultado->fetch_assoc();

// Guardamos en variables
$nombre = $datos_admin['username'];
$correo = $datos_admin['email'];
$fecha = date("d M, Y", strtotime($datos_admin['fecha_registro']));
$foto = $datos_admin['foto_perfil'] ?? 'default_avatar.png';
$banner = $datos_admin['banner_perfil'] ?? 'default_banner.jpg';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECOLIMA – Mi Perfil</title>
    <link rel="stylesheet" href="../../CSS/Perfil/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Playfair+Display:ital,wght@0,700;1,600&display=swap" rel="stylesheet">
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
    </style>
</head>
<body>

<nav class="sidebar">
  <div style="height: 70px;">
    <img src="../../assets/Perfil/Logo.png" style="width: 50px; height: 50px; margin-left: 13px;"/>
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
      <input type="file" id="inputBanner" accept="image/*" style="display: none;">

      <div class="profile-avatar-wrap" onclick="document.getElementById('inputFoto').click();" title="Cambiar foto de perfil">
    <img class="profile-avatar" src="../../assets/Fotos_perfil/<?php echo $foto; ?>" id="imgPerfilGrande">
    
    <div class="camera-icon-badge">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
            <circle cx="12" cy="13" r="4"></circle>
        </svg>
    </div>
    
    <input type="file" id="inputFoto" accept="image/*" style="display: none;">
</div>
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
                <span class="post-tag">Seguridad</span>
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
            <input class="config-input" type="text" name="username" value="<?php echo $nombre; ?>" required />
        </div>
        <div class="config-group">
            <label>Correo Electrónico</label>
            <input class="config-input" type="email" name="email" value="<?php echo $correo; ?>" required />
        </div>
        
        <p style="font-size: 0.7rem; color: var(--gray-400); margin-top: 10px;">
            ID de Administrador: #<?php echo $id_admin; ?><br>
            Registro: <?php echo $fecha; ?>
        </p>

        <button type="submit" class="btn-save" style="width: 100%; margin-top: 20px;">Guardar Cambios</button>
    </form>
  </div>
</aside>

<div class="toast" id="toast"></div>

<script src="../../JavaScript/Dashboard/perfil.js"></script>
<script>
    // Adaptación de funciones de UI
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

   
</script>
</body>
</html>
