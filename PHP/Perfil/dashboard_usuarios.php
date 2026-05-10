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
$query = "SELECT username, foto_perfil, banner_perfil FROM usuarios WHERE id = $id_admin";
$resultado = $conn->query($query);

if ($resultado && $row = $resultado->fetch_assoc()) {
    $nombre = $row['username'];
    $foto = $row['foto_perfil'];
    $banner = $row['banner_perfil'];
} else {
    $nombre = "Admin";
    $foto = "default_avatar.png";
    $banner = "default_banner.jpg";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECOLIMA – Gestión de Usuarios</title>
    <link rel="stylesheet" href="../../CSS/Perfil/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Playfair+Display:ital,wght@0,700;1,600&display=swap" rel="stylesheet">
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
    background-color: #f0fdf4; /* Verde muy clarito */
    color: var(--green-dark);
    font-family: 'Nunito', sans-serif;
    font-weight: 700;
    font-size: 0.8rem;
    cursor: pointer;
    outline: none;
    transition: all 0.2s ease;
    appearance: none; /* Quita la flecha por defecto en algunos navegadores */
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
    background-color: #fee2e2; /* Rojo clarito */
    color: #ef4444; /* Rojo fuerte */
    transform: scale(1.1);
}
    </style>
</head>
<body>

<nav class="sidebar">
  <div style="height: 70px;">
    <img src="../../assets/Perfil/Logo.png" style="width: 50px; height: 50px; margin-left: 13px;"/>
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
        
      <input type="file" id="inputBanner" accept="image/*" style="display: none;">      <div class="profile-avatar-wrap" style="bottom: -35px;">
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

<script src="../../JavaScript/Dashboard/usuarios.js"></script>
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
