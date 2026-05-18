<?php
session_start();
include "../Perfil/conexion.php"; 

// 🛡️ Filtro de seguridad
if (!isset($_SESSION['usuario'])) {
    header("Location: ../Login/login.php");
    exit();
}

$user_session = $_SESSION['usuario'];

// 🔍 1. CORRECCIÓN: Traemos u.modo_oscuro de la tabla usuarios
$query = "SELECT u.id, u.username, u.email, u.biografia, u.foto_perfil, u.banner_perfil, u.rol_id, u.modo_oscuro, r.nombre AS nombre_rol 
          FROM usuarios u 
          INNER JOIN roles r ON u.rol_id = r.id 
          WHERE u.username = ?";
          
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_session);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $user = $resultado->fetch_assoc();
    
    $_SESSION['usuario_id'] = $user['id'];
    // Guardamos en sesión el estado por si lo necesitas usar en barras de navegación globales
    $_SESSION['modo_oscuro'] = $user['modo_oscuro'];

    if (!empty($user['foto_perfil']) && $user['foto_perfil'] !== 'default_avatar.png' && file_exists('../../assets/Fotos_perfil/' . $user['foto_perfil'])) {
        $avatar = '../../assets/Fotos_perfil/' . $user['foto_perfil'];
    } else {
        $avatar = 'https://cdn-icons-png.flaticon.com/512/847/847969.png'; 
    }

    if (!empty($user['banner_perfil']) && $user['banner_perfil'] !== 'default_banner.jpg' && file_exists('../../assets/Fotos_banner/' . $user['banner_perfil'])) {
        $banner = '../../assets/Fotos_banner/' . $user['banner_perfil'];
    } else {
        $banner = 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&q=80';
    }

    $biografia = !empty($user['biografia']) ? $user['biografia'] : '¡Hola! Estoy usando E-COLIMA.';
} else {
    echo "Error al recuperar los datos del perfil.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ECOLIMA – Perfil</title>
  
  <?php if (isset($user['modo_oscuro']) && $user['modo_oscuro'] == 1): ?>
      <link rel="stylesheet" href="../../CSS/Perfil/Perfil_oscuro.css"/>
  <?php else: ?>
      <link rel="stylesheet" href="../../CSS/Perfil/Perfil.css"/>
  <?php endif; ?>
  
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../../JavaScript/Perfil/perfil.js" defer></script>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Playfair+Display:ital,wght@0,700;1,600&display=swap" rel="stylesheet"/>
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
    <button class="nav-item active" onclick="switchSection('panel-perfil')">Panel de perfil</button>
    <button class="nav-item" onclick="switchSection('panel-permisos')">Solicitar cambio de Permisos</button>
  </div>

  <div class="sidebar-bottom">
    <button class="nav-item" onclick="openConfig()">Configuración</button>
    <button type="button" class="nav-item danger" onclick="cerrarSesion()" style="background: none; border: none; width: 100%; text-align: left; cursor: pointer; font-family: inherit;">Cerrar sesión</button>
  </div>
</nav>

<main class="main">
  <div id="section-perfil" style="display: contents;">
    <div class="profile-card">
      <div class="profile-hero">
        <div class="cover-clickable" onclick="document.getElementById('input-banner').click();" title="Haga clic para cambiar la foto de portada" style="cursor: pointer; width: 100%; height: 100%;">
          <img class="cover" src="<?php echo htmlspecialchars($banner); ?>" alt="Portada"/>
        </div>
        
        <div class="profile-avatar-wrap" onclick="document.getElementById('input-avatar').click();" title="Haga clic para cambiar su foto de perfil" style="cursor: pointer;">
          <img class="profile-avatar" src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar"/>
        </div>
      </div>

      <form id="form-cambiar-fotos" style="display: none;" enctype="multipart/form-data">
        <input type="file" id="input-avatar" name="foto" accept="image/*" data-usuario-id="<?php echo $user['id']; ?>" onchange="subirImagenAutomatica('avatar')">
        <input type="file" id="input-banner" name="banner_perfil" accept="image/*" data-usuario-id="<?php echo $user['id']; ?>" onchange="subirImagenAutomatica('banner')">
      </form>

      <div class="profile-info">
        <div class="profile-name"><?php echo htmlspecialchars($user['username']); ?></div>
        <div class="profile-role"><?php echo htmlspecialchars($user['nombre_rol']); ?></div>
        <div class="profile-bio" contenteditable="true" id="bioElement" title="Haz clic para editar tu biografía" onblur="guardarBiografia(this.innerText)">
          <?php echo htmlspecialchars($biografia); ?>
        </div>
      </div>
    </div>

    <div class="feed-panel" id="panel-feed-izquierdo">
      <div class="feed-tabs" style="display: flex; gap: 10px;">
        <button class="tab-btn active" data-tab="megusta" onclick="switchTab('megusta', this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          Me gusta
        </button>

        <?php if ($user['rol_id'] == 1 || $user['rol_id'] == 3): ?>
        <button class="tab-btn" data-tab="mispublicaciones" onclick="switchTab('mispublicaciones', this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
          Mis Publicaciones
        </button>
        <?php endif; ?>
      </div>
      
      <div class="feed-list" id="feedList"></div>
    </div>
  </div> 

  <div id="section-permisos" style="display: none;">
    <div class="solicitud-container" style="max-width: 500px; margin: 40px auto; padding: 30px; background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
        <form id="formSolicitudRol">
            <input type="hidden" name="usuario_id" value="<?php echo $user['id']; ?>">
            
            <h2 style="font-family: 'Playfair Display', serif; color: #1a3a2a; margin-bottom: 10px;">Solicitud de rango avanzado</h2>
            <p style="font-size: 0.9rem; color: #666; margin-bottom: 20px;">
                Envía una propuesta detallando por qué deseas cambiar de rol (ej. Escritor o Editor). Tu caso será evaluado por los administradores.
            </p>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 700; margin-bottom: 5px; color: #1a3a2a;">Rango solicitado:</label>
                <select name="rol_solicitado" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; font-family: inherit;" required>
                    <option value="2">Editor (Modificar y validar contenido)</option>
                    <option value="3">Autor / Escritor (Subir flora, fauna, etc.)</option>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; margin-bottom: 5px; color: #1a3a2a;">Motivo detallado:</label>
                <textarea name="motivo" placeholder="Escribe aquí tu experiencia o razones para el cambio..." style="width: 100%; height: 120px; padding: 10px; border: 1px solid #ccc; border-radius: 8px; font-family: inherit; resize: none;" required></textarea>
            </div>

            <button type="submit" style="width: 100%; background: #2d6a4f; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: background 0.2s;">
                Enviar Solicitud
            </button>
        </form>
    </div>
  </div>
</main>

<aside class="config-drawer" id="configDrawer">
  <div class="drawer-header">
    <h2>⚙️ Configuración</h2>
    <button class="drawer-close" onclick="closeConfig()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>
  <div class="drawer-body">
    <div class="config-group">
      <label>Nombre de usuario</label>
      <input class="config-input" type="text" id="cfgName" value="<?php echo htmlspecialchars($user['username']); ?>"/>
    </div>
    <div class="config-group">
      <label>Correo electrónico</label>
      <input class="config-input" type="email" id="cfgEmail" value="<?php echo htmlspecialchars($user['email']); ?>"/>
    </div>
    
    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">
    
    <div class="config-group">
      <label style="color: #b91c1c; font-weight: 700;">Contraseña Actual</label>
      <input class="config-input" type="password" id="cfgPassActual" placeholder="Requerida para confirmar cambios"/>
    </div>
    <div class="config-group">
      <label>Contraseña Nueva</label>
      <input class="config-input" type="password" id="cfgPass" minlength="8" maxlength="32" placeholder="Dejar vacío para no cambiar"/>
    </div>

    <label class="switch">
      <input id="switchModoOscuro" type="checkbox" <?php echo (isset($user['modo_oscuro']) && $user['modo_oscuro'] == 1) ? 'checked' : ''; ?> />
      <div class="slider round">
        <div class="sun-moon">
          <svg id="moon-dot-1" class="moon-dot" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
          <svg id="moon-dot-2" class="moon-dot" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
          <svg id="moon-dot-3" class="moon-dot" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
          <svg id="light-ray-1" class="light-ray" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
          <svg id="light-ray-2" class="light-ray" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
          <svg id="light-ray-3" class="light-ray" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
          <svg id="cloud-1" class="cloud-dark" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
          <svg id="cloud-2" class="cloud-dark" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
          <svg id="cloud-3" class="cloud-dark" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
          <svg id="cloud-4" class="cloud-light" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
          <svg id="cloud-5" class="cloud-light" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
          <svg id="cloud-6" class="cloud-light" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
        </div>
        <div class="stars">
          <svg id="star-1" class="star" viewBox="0 0 20 20"><path d="M 0 10 C 10 10,10 10 ,0 10 C 10 10 , 10 10 , 10 20 C 10 10 , 10 10 , 20 10 C 10 10 , 10 10 , 10 0 C 10 10,10 10 ,0 10 Z"></path></svg>
          <svg id="star-2" class="star" viewBox="0 0 20 20"><path d="M 0 10 C 10 10,10 10 ,0 10 C 10 10 , 10 10 , 10 20 C 10 10 , 10 10 , 20 10 C 10 10 , 10 10 , 10 0 C 10 10,10 10 ,0 10 Z"></path></svg>
          <svg id="star-3" class="star" viewBox="0 0 20 20"><path d="M 0 10 C 10 10,10 10 ,0 10 C 10 10 , 10 10 , 10 20 C 10 10 , 10 10 , 20 10 C 10 10 , 10 10 , 10 0 C 10 10,10 10 ,0 10 Z"></path></svg>
          <svg id="star-4" class="star" viewBox="0 0 20 20"><path d="M 0 10 C 10 10,10 10 ,0 10 C 10 10 , 10 10 , 10 20 C 10 10 , 10 10 , 20 10 C 10 10 , 10 10 , 10 0 C 10 10,10 10 ,0 10 Z"></path></svg>
        </div>
      </div>
    </label>
        
    <button class="btn-save" onclick="guardarConfig()">Guardar cambios</button>
  </div>
</aside>

<div class="config-overlay" id="configOverlay" onclick="closeConfig()"></div>
<div class="toast" id="toast"></div>

</body>
</html>
