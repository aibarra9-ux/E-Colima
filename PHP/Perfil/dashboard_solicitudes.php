<?php 
require_once "verificar_sesion.php"; 
require_once "conexion.php";

$id_admin = $_SESSION['usuario_id'];
$query = "SELECT username, foto_perfil, banner_perfil FROM usuarios WHERE id = $id_admin";
$res = $conn->query($query);
$datos_admin = $res->fetch_assoc();
$foto = $datos_admin['foto_perfil'] ?? 'default_avatar.png';
$banner = $datos_admin['banner_perfil'] ?? 'default_banner.jpg'
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECOLIMA – Solicitudes de Rol</title>
    <link rel="stylesheet" href="../../CSS/Perfil/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Estilos específicos para la gestión de solicitudes */
        .table-container {
            padding: 10px;
            overflow-x: auto;
        }
        .requests-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }
        .requests-table th {
            text-align: left;
            padding: 15px;
            font-size: 0.8rem;
            text-transform: uppercase;
            color: var(--green-dark);
            border-bottom: 2px solid var(--green-pale);
            font-weight: 800;
        }
        .requests-table td {
            padding: 15px;
            border-bottom: 1px solid var(--gray-100);
            font-size: 0.9rem;
            color: var(--text-mid);
            vertical-align: middle;
        }
        .reason-cell {
            max-width: 250px;
            font-style: italic;
            color: var(--gray-400);
            line-height: 1.4;
        }
        .status-badge {
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            background: var(--green-pale);
            color: var(--green-dark);
        }
       .action-buttons {
    display: flex;
    gap: 10px;
}

.btn-approve, .btn-reject {
    border: none;
    padding: 8px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: 0.2s;
}

.btn-approve { background: #dcfce7; color: #166534; }
.btn-reject { background: #fee2e2; color: #991b1b; }

.btn-approve:hover { background: #166534; color: white; }
.btn-reject:hover { background: #991b1b; color: white; }
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
    <a href="dashboard_usuarios.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      Usuarios
    </a>
    <a href="dashboard_publicaciones.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
      Publicaciones
    </a>
    <a href="dashboard_solicitudes.php" class="nav-item active">
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
  </div>
</nav>

<main class="main">
    <div class="profile-card">
        <div class="profile-hero" style="height: 110px;">
      <img class="cover" src="../../assets/Fotos_banner/<?php echo $banner; ?>" id="imgBanner">
            <div class="profile-avatar-wrap" style="bottom: -35px;">
                <img class="profile-avatar" style="width: 70px; height: 70px;" src="../../assets/Fotos_perfil/<?php echo $foto; ?>">
            </div>
        </div>
        <div class="profile-info" style="padding-top: 45px;">
            <div class="profile-name"><?php echo strtoupper($datos_admin['username']); ?></div>
            <div class="profile-role">Moderador de Credenciales</div>
        </div>
    </div>

    <div class="feed-panel">
        <div class="feed-tabs" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="tab-btn active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
                Solicitudes Pendientes
            </div>

            <div class="search-box" style="display: flex; align-items: center; background: var(--white); border: 2px solid var(--green-pale); border-radius: 12px; padding: 5px 15px; width: 280px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--green-mid);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="searchSolicitud" placeholder="Buscar usuario..." style="border: none; outline: none; padding: 8px; width: 100%; font-family: 'Nunito'; font-size: 0.85rem;">
            </div>
        </div>

        <div class="feed-list">
            <div class="feed-card" style="display: block; padding: 0;">
                <div class="table-container">
                    <table class="requests-table">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Rol Solicitado</th>
                                <th>Motivo</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="lista-solicitudes">
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="../../JavaScript/Dashboard/solicitudes.js"></script>
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