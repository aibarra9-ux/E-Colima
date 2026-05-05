<?php 
// 1. Iniciamos sesión y verificamos permisos
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
    header("Location: ../Home/home.php?error=no_autorizado");
    exit();
}

// 2. Traemos los datos de la base de datos para el sidebar
require_once "../../PHP/Perfil/conexion.php"; // Ajusta la ruta si es necesario

$id_admin = $_SESSION['usuario_id'];
$query = "SELECT username, foto_perfil FROM usuarios WHERE id = $id_admin";
$resultado = $conn->query($query);

if ($resultado && $row = $resultado->fetch_assoc()) {
    $nombre = $row['username'];
    $foto = $row['foto_perfil']; // ¡Aquí es donde se obtiene!
} else {
    $nombre = "Admin"; // Por si algo falla
    $foto = "default_avatar.png"; // Valor por defecto por seguridad

}
// --- CONSULTAS PARA MÉTRICAS REALES ---

// 1. Comunidad Total (Usuarios activos)
$sql_comunidad = "SELECT COUNT(*) as total FROM usuarios WHERE activo = 1";
$res_comunidad = $conn->query($sql_comunidad);
$total_usuarios = $res_comunidad->fetch_assoc()['total'];

// 2. Nuevos Registros (Últimas 24 horas)
$sql_nuevos = "SELECT COUNT(*) as total FROM usuarios WHERE fecha_registro >= NOW() - INTERVAL 1 DAY";
$res_nuevos = $conn->query($sql_nuevos);
$nuevos_usuarios = $res_nuevos->fetch_assoc()['total'];

// 3. Actividad de Publicaciones (Total de posts publicados)
$sql_pubs = "SELECT COUNT(*) as total FROM publicaciones WHERE estado = 'publicado'";
$res_pubs = $conn->query($sql_pubs);
$total_pubs = $res_pubs->fetch_assoc()['total'];

// 4. Acervo de Contenido (Total de archivos multimedia)
$sql_multimedia = "SELECT COUNT(*) as total FROM recursos_multimedia";
$res_multimedia = $conn->query($sql_multimedia);
$total_recursos = $res_multimedia->fetch_assoc()['total'];

// 5. Índice de Engagement (Likes + Comentarios)
// Sumamos interacciones totales
$sql_interact = "SELECT 
    (SELECT COUNT(*) FROM likes) + 
    (SELECT COUNT(*) FROM comentarios) as interacciones";
$res_interact = $conn->query($sql_interact);
$total_interacciones = $res_interact->fetch_assoc()['interacciones'];

// --- DATOS PARA EL GRÁFICO DE BARRAS (Registros por mes) ---
// Consultamos los registros de los últimos 6 meses
$meses_data = [];
for ($i = 5; $i >= 0; $i--) {
    $mes_nombre = date('M', strtotime("-$i months")); // ENE, FEB, etc.
    $mes_num = date('m', strtotime("-$i months"));
    $anio_num = date('Y', strtotime("-$i months"));

    $sql_mes = "SELECT COUNT(*) as total FROM usuarios WHERE MONTH(fecha_registro) = '$mes_num' AND YEAR(fecha_registro) = '$anio_num'";
    $res_mes = $conn->query($sql_mes);
    $total = $res_mes->fetch_assoc()['total'];
    
    // Guardamos el nombre y calculamos una altura relativa (máximo 100 para el CSS)
    // Suponiendo que tu meta o máximo esperado son 100 registros para que se vea bien la barra
    $altura = min($total * 2, 100); 
    $meses_data[] = ['nombre' => strtoupper($mes_nombre), 'altura' => $altura, 'real' => $total];
}

// --- DATOS PARA EL GRÁFICO DE DONA (Distribución de Roles) ---
// Vamos a ver qué porcentaje son "Usuarios" (Rol 4) vs el resto
$sql_roles = "SELECT 
    SUM(CASE WHEN rol_id = 4 THEN 1 ELSE 0 END) as base,
    COUNT(*) as total 
    FROM usuarios";
$res_roles = $conn->query($sql_roles);
$data_roles = $res_roles->fetch_assoc();
$porcentaje_base = ($data_roles['total'] > 0) ? round(($data_roles['base'] / $data_roles['total']) * 100) : 0;


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Estadísticas</title>
    <link rel="stylesheet" href="../../CSS/Perfil/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="dashboard-container">
        
        <aside class="sidebar">
            <div class="profile-section">
                <div class="avatar">
                    <img src="../../assets/Fotos_perfil/<?php echo $foto; ?>" alt="Perfil">
                </div>
                <h2><?php echo strtoupper($nombre); ?></h2>
                <p>Administrador</p>
            </div>

            <nav class="sidebar-menu">
                <ul>
                    <li><a href="dashboard_perfil.php"><i class="fa-solid fa-user"></i> Perfil</a></li>
                    <li><a href="#"><i class="fa-regular fa-file"></i> Publicaciones</a></li>
                    <li><a href="dashboard_usuarios.php"><i class="fa-solid fa-users"></i> Usuarios</a></li>
                    <li><a href="#"><i class="fa-solid fa-bell"></i> Notificaciones</a></li>
                    <li class="active"><a href="dashboard_estadisticas.php"><i class="fa-solid fa-chart-line"></i> Estadisticas</a></li>
                    <li><a href="#"><i class="fa-solid fa-gear"></i> Configuración</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            
            <header class="top-header">
                <h1>Panel de Estadísticas</h1>
            </header>

            <div class="scrollable-area">
                <div class="metrics-grid">
                    <div class="metric-card interactive">
                        <div class="card-top">
                            <span>Nuevos Registros (24h)</span>
                            <i class="fa-solid fa-person-circle-plus" style="color: rgb(57, 203, 57);"></i>
                        </div>
                        <h3><?php echo number_format($nuevos_usuarios); ?></h3>
                    </div>

                    <div class="metric-card interactive">
                        <div class="card-top">
                            <span>Comunidad Total</span>
                            <i class="fa-solid fa-people-group" style="color: rgb(57, 203, 57);"></i>
                        </div>
                        <h3><?php echo number_format($total_usuarios); ?></h3>
                    </div>

                    <div class="metric-card interactive">
                        <div class="card-top">
                            <span>Publicaciones Activas</span>
                            <i class="fa-solid fa-newspaper" style="color: rgb(57, 203, 57);"></i>
                        </div>
                        <h3><?php echo number_format($total_pubs); ?></h3>
                    </div>

                    <div class="metric-card interactive">
                        <div class="card-top">
                            <span>Recursos Multimedia</span>
                            <i class="fa-brands fa-buffer" style="color: rgb(57, 203, 57);"></i>
                        </div>
                        <h3><?php echo number_format($total_recursos); ?></h3>
                    </div>

                    <div class="metric-card interactive">
                        <div class="card-top">
                            <span>Interacciones Totales</span>
                            <i class="fa-solid fa-heart-pulse" style="color: #e63946;"></i>
                        </div>
                        <h3><?php echo number_format($total_interacciones); ?></h3>
                    </div>
                </div>

                <div class="middle-grid">
                    
                    <div class="card bar-chart-card interactive-soft">
                        <div class="card-header-flex">
                            <span>Registros de Usuarios (Últimos 6 meses)</span>
                        </div>
                        <div class="bar-chart-container">
                            <div class="y-axis">
                                <span>100</span><span>75</span><span>50</span><span>25</span><span>0</span>
                            </div>
                            <div class="bars">
                                <?php foreach($meses_data as $mes): ?>
                                    <div class="bar-group">
                                        <div class="bar green" style="height: <?php echo $mes['altura']; ?>%;">
                                            <div class="tooltip"><?php echo $mes['real']; ?> registros</div>
                                        </div>
                                        <span class="x-label"><?php echo $mes['nombre']; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="legend">
                                <div class="legend-item"><div class="box green"></div>Usuarios registrados</div>
                            </div>
                        </div>
                    </div>

                    <div class="card donut-card interactive-soft">
                        <div class="card-header-flex">
                            <span>Distribución de Roles</span>
                        </div>
                        <div class="donut-chart" style="background: conic-gradient(rgb(57, 203, 57) <?php echo $porcentaje_base; ?>%, #e1e1e1 0);">
                            <div class="donut-hole">
                                <span><?php echo $porcentaje_base; ?>%</span>
                            </div>
                        </div>
                        <div class="donut-title">Usuarios Base</div>
                        <p style="font-size: 12px; color: gray; text-align: center; margin-top: 10px;">
                            Porcentaje respecto al total de la comunidad
                        </p>
                    </div>
                </div> </div> </main>
    </div>
</body>
</html>
