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
$query = "SELECT username, foto_perfil FROM usuarios WHERE id = $id_admin";
$resultado = $conn->query($query);

if ($resultado && $row = $resultado->fetch_assoc()) {
    $nombre = $row['username'];
    $foto = $row['foto_perfil'];
} else {
    $nombre = "Admin";
    $foto = "default_avatar.png";
}

// --- CONSULTAS PARA MÉTRICAS REALES ---
$total_usuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE activo = 1")->fetch_assoc()['total'];
$nuevos_usuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE fecha_registro >= NOW() - INTERVAL 1 DAY")->fetch_assoc()['total'];
$total_pubs = $conn->query("SELECT COUNT(*) as total FROM publicaciones WHERE estado = 'publicado'")->fetch_assoc()['total'];
$total_recursos = $conn->query("SELECT COUNT(*) as total FROM recursos_multimedia")->fetch_assoc()['total'];
$total_interacciones = $conn->query("SELECT (SELECT COUNT(*) FROM likes) + (SELECT COUNT(*) FROM comentarios) as interacciones")->fetch_assoc()['interacciones'];

// --- DATOS PARA EL GRÁFICO DE BARRAS (Últimos 6 meses) ---
$meses_data = [];
for ($i = 5; $i >= 0; $i--) {
    $mes_nombre = date('M', strtotime("-$i months"));
    $mes_num = date('m', strtotime("-$i months"));
    $anio_num = date('Y', strtotime("-$i months"));
    $total = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE MONTH(fecha_registro) = '$mes_num' AND YEAR(fecha_registro) = '$anio_num'")->fetch_assoc()['total'];
    $altura = ($total > 0) ? min($total * 15, 100) : 5;
    $meses_data[] = ['nombre' => strtoupper($mes_nombre), 'altura' => $altura, 'real' => $total];
}

// --- DATOS PARA EL GRÁFICO DE DONA ---
$res_roles = $conn->query("SELECT SUM(CASE WHEN rol_id = 4 THEN 1 ELSE 0 END) as base, COUNT(*) as total FROM usuarios")->fetch_assoc();
$porcentaje_base = ($res_roles['total'] > 0) ? round(($res_roles['base'] / $res_roles['total']) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECOLIMA – Estadísticas</title>
    <link rel="stylesheet" href="../../CSS/Perfil/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        /* Adaptación de los gráficos al nuevo estilo */
        /* Layout Principal */
.main {
    padding: 30px;
    display: flex;
    flex-direction: column;
    gap: 25px;
}

/* Fila de Mini Cards Superiores */
.stats-header-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.stat-mini-card {
    background: white;
    padding: 24px;
    border-radius: 24px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
    align-items: center;
    border: 1px solid #f1f5f9;
}

/* Contenedor de Gráficos Reorganizado */
.charts-container {
    display: grid;
    grid-template-columns: 1.5fr 1fr; /* Más espacio para las barras */
    gap: 25px;
}

.chart-card {
    background: white;
    border-radius: 30px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.03);
}

/* Ajuste Gráfico de Barras */
.bar-container {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    height: 250px; /* Más alto para que se note el crecimiento */
    padding: 20px 10px;
    border-bottom: 2px solid #f1f5f9;
}

.bar {
    width: 45px; /* Más anchas */
    background: linear-gradient(to top, #2d6a4f, #52b788);
    border-radius: 12px 12px 4px 4px;
    position: relative;
    transition: 0.3s;
}

/* Ajuste Gráfico de Dona (Círculo) */
.donut-chart {
    width: 180px;
    height: 180px;
    margin: 30px auto;
    border-radius: 50%; /* Obligatorio para que sea círculo */
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.donut-hole {
    width: 110px;
    height: 110px;
    background: white;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: 1.4rem;
    color: #1b4332;
    box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);
    z-index: 2;
}

/* Ajuste de Barras para que se vean */
.bar {
    width: 45px;
    background: linear-gradient(to top, #2d6a4f, #52b788);
    border-radius: 8px 8px 4px 4px;
    position: relative;
    transition: 0.3s;
    min-height: 5px; /* Evita que desaparezca si es 0 */
}

.bar::after {
    content: attr(data-value);
    position: absolute;
    top: -25px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 0.75rem;
    font-weight: 800;
    color: #1e293b;
}
    </style>
</head>
<body>

<nav class="sidebar">
  <div style="height: 70px;"><img src="../../assets/Perfil/Logo.png" style="width: 50px; margin-left: 13px;"/></div>
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
    <a href="dashboard_solicitudes.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      Solicitudes
    </a>
    <a href="dashboard_estadisticas.php" class="nav-item active">
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
    <div class="stats-header-grid">
        <div class="stat-mini-card">
            <div class="stat-label">Nuevos Hoy</div>
            <div class="stat-value" style="color: #52b788;">+<?php echo $nuevos_usuarios; ?></div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-label">Publicaciones</div>
            <div class="stat-value"><?php echo $total_pubs; ?></div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-label">Multimedia</div>
            <div class="stat-value"><?php echo $total_recursos; ?></div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-label">Interacciones</div>
            <div class="stat-value" style="color: #e63946;"><?php echo $total_interacciones; ?></div>
        </div>
    </div>

    <div class="charts-container">
        <div class="chart-card">
            <h3 style="color: #1e293b; margin-bottom: 25px;">Registros de Usuarios (6 meses)</h3>
            <div class="bar-container">
                <?php foreach($meses_data as $mes): ?>
                    <div style="display: flex; flex-direction: column; align-items: center; width: 100%;">
                        <?php $altura_visual = max($mes['real'] * 30, 10); ?> 
                        <div class="bar" style="height: <?php echo min($altura_visual, 100); ?>%;" data-value="<?php echo $mes['real']; ?>"></div>
                        <span class="bar-label" style="margin-top: 10px;"><?php echo $mes['nombre']; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="chart-card" style="text-align: center;">
            <h3 style="color: #1e293b;">Distribución de Comunidad</h3>
            <div class="donut-chart" style="background: conic-gradient(#409167 <?php echo $porcentaje_base; ?>%, #d8f3dc 0);">
                <div class="donut-hole">
                    <?php echo $porcentaje_base; ?>%
                    <span style="font-size: 0.7rem; color: #94a3b8;">BASE</span>
                </div>
            </div>
            <p style="font-size: 0.85rem; color: #64748b; padding: 0 20px;">
                El <strong><?php echo $porcentaje_base; ?>%</strong> de tus usuarios son nivel Base.
            </p>
        </div>
    </div>
</main>
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
