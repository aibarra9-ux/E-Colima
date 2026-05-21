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
// 🔍 Optimizado: Traemos también el modo_oscuro de la base de datos
$query = "SELECT username, email, foto_perfil, modo_oscuro FROM usuarios WHERE id = $id_admin";
$resultado = $conn->query($query);

if ($resultado && $row = $resultado->fetch_assoc()) {
    $nombre = $row['username'];
    $correo = $row['email'];
    $foto = $row['foto_perfil'];
    $modo_oscuro = $row['modo_oscuro'] ?? 0;
    
    $_SESSION['usuario'] = $row['username']; 
    $_SESSION['usuario_id'] = $id_admin;
} else {
    $nombre = "Admin";
    $correo = "";
    $foto = "default_avatar.png";
    $modo_oscuro = 0;
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
    <title data-translate="true">ECOLIMA – Estadísticas</title>
    <link rel="stylesheet" href="../../CSS/Perfil/dashboard.css">
    <link class="config-style" rel="stylesheet" href=""> <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
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
            grid-template-columns: 1.5fr 1fr;
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
            height: 250px;
            padding: 20px 10px;
            border-bottom: 2px solid #f1f5f9;
        }

        /* Ajuste Gráfico de Dona (Círculo) */
        .donut-chart {
            width: 180px;
            height: 180px;
            margin: 30px auto;
            border-radius: 50%;
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

        .bar {
            width: 45px;
            background: linear-gradient(to top, #2d6a4f, #52b788);
            border-radius: 8px 8px 4px 4px;
            position: relative;
            transition: 0.3s;
            min-height: 5px;
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
  <div style="height: 70px; display: flex; align-items: center;">
    <a href="../Home/home.php" style="display: flex; align-items: center; text-decoration: none; gap: 10px;">
      <img src="../../assets/Perfil/Logo.png" style="width: 50px; height: 50px; margin-left: 13px;"/>
      <span style="font-family: sans-serif; font-weight: 700; font-size: 22px; color: #1a3a2a; letter-spacing: 2px;">ECOLIMA</span>
    </a>
  </div>
  <div class="sidebar-nav">
    <a href="dashboard_perfil.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      <span data-translate="true">Mi Perfil</span>
    </a>
    <a href="dashboard_usuarios.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      <span data-translate="true">Usuarios</span>
    </a>
    <a href="dashboard_publicaciones.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
      <span data-translate="true">Publicaciones</span>
    </a>
    <a href="dashboard_solicitudes.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      <span data-translate="true">Solicitudes</span>
    </a>
    <a href="dashboard_estadisticas.php" class="nav-item active">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
        <span data-translate="true">Estadísticas</span>
    </a>
  </div>

  <div class="sidebar-bottom">
    <button class="nav-item" onclick="openConfig()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2"/></svg>
      <span data-translate="true">Configuración</span>
    </button>
    <button class="nav-item danger" onclick="cerrarSesion()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      <span data-translate="true">Cerrar sesión</span>
    </button>
  </div>
</nav>

<main class="main">
    <div class="stats-header-grid">
        <div class="stat-mini-card">
            <div class="stat-label" data-translate="true">Nuevos Hoy</div>
            <div class="stat-value" style="color: #52b788;">+<?php echo $nuevos_usuarios; ?></div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-label" data-translate="true">Publicaciones</div>
            <div class="stat-value"><?php echo $total_pubs; ?></div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-label" data-translate="true">Multimedia</div>
            <div class="stat-value"><?php echo $total_recursos; ?></div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-label" data-translate="true">Interacciones</div>
            <div class="stat-value" style="color: #e63946;"><?php echo $total_interacciones; ?></div>
        </div>
    </div>

    <div class="charts-container">
        <div class="chart-card">
            <h3 style="color: #1e293b; margin-bottom: 25px;" data-translate="true">Registros de Usuarios (6 meses)</h3>
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
            <h3 style="color: #1e293b;" data-translate="true">Distribución de Comunidad</h3>
            <div class="donut-chart" style="background: conic-gradient(#409167 <?php echo $porcentaje_base; ?>%, #d8f3dc 0);">
                <div class="donut-hole">
                    <?php echo $porcentaje_base; ?>%
                    <span style="font-size: 0.7rem; color: #94a3b8;" data-translate="true">BASE</span>
                </div>
            </div>
            <p style="font-size: 0.85rem; color: #64748b; padding: 0 20px;">
                <span data-translate="true">El</span> <strong><?php echo $porcentaje_base; ?>%</strong> <span data-translate="true">de tus usuarios son nivel Base.</span>
            </p>
        </div>
    </div>
</main>

<div class="config-overlay" id="configOverlay" onclick="closeConfig()"></div>
<aside class="config-drawer" id="configDrawer">
  <div class="drawer-header">
    <h2 data-translate="true">⚙️ Editar Perfil</h2>
    <button class="drawer-close" onclick="closeConfig()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>
  <div class="drawer-body">
    <form id="formEditarPerfil">
        <div class="config-group">
            <label data-translate="true">Nombre de Usuario</label>
            <input class="config-input" type="text" name="username" id="cfgName" value="<?php echo htmlspecialchars($nombre); ?>" data-translate-placeholder="true" required />
        </div>
        <div class="config-group">
            <label data-translate="true">Correo Electrónico</label>
            <input class="config-input" type="email" name="email" id="cfgEmail" value="<?php echo htmlspecialchars($correo); ?>" data-translate-placeholder="true" required />
        </div>
        
        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">

        <div class="config-group">
            <label data-translate="true">Apariencia del Sistema</label>
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
                        
                        <span class="lbl-modo" style="margin-left: 10px; font-family:'Nunito', sans-serif; font-size:0.88rem; font-weight:700; color: var(--green-dark); z-index:2; transition:0.4s;" data-translate="true">Modo Claro</span>
                    </div>
                </label>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">

        <div class="config-group">
            <label style="color: #b91c1c; font-weight: 700;" data-translate="true">Contraseña Actual</label>
            <input class="config-input" type="password" name="password_actual" id="cfgPassActual" placeholder="Requerida para confirmar cambios" data-translate-placeholder="true" />
        </div>
        <div class="config-group">
            <label data-translate="true">Contraseña Nueva</label>
            <input class="config-input" type="password" name="password_nueva" id="cfgPass" minlength="8" maxlength="32" placeholder="Dejar vacío para no cambiar" data-translate-placeholder="true" />
        </div>
        
        <p style="font-size: 0.7rem; color: #94a3b8; margin-top: 10px;">
            <span data-translate="true">ID de Administrador:</span> #<?php echo $id_admin; ?>
        </p>

        <button type="submit" class="btn-save" style="width: 100%; margin-top: 20px;" data-translate="true">Guardar Cambios</button>
    </form>
  </div>
</aside>

<div class="toast" id="toast"></div>

<script>
    // 🌟 LÓGICA DE CONTROL DE MODO OSCURO (TRAÍDA DE SOLICITUDES)
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
            document.documentElement.classList.add('dark-theme'); 
            configStyleLink.setAttribute('href', '../../CSS/Perfil/Perfil_oscuro.css');
            labelModo.textContent = "Modo Oscuro";
            labelModo.style.color = "#f8fafc";
            sunSvg.style.display = "none";
            moonSvg.style.display = "block";
            if(starsDiv) { starsDiv.style.opacity = "1"; starsDiv.style.transform = "translateY(0)"; }
            if(cloudsDiv) cloudsDiv.style.opacity = "0";
            switchModoOscuro.checked = true;
        } else {
            document.body.classList.remove('dark-mode');
            document.documentElement.classList.remove('dark-theme');
            configStyleLink.setAttribute('href', '');
            labelModo.textContent = "Modo Claro";
            labelModo.style.color = "";
            sunSvg.style.display = "block";
            moonSvg.style.display = "none";
            if(starsDiv) { starsDiv.style.opacity = "0"; starsDiv.style.transform = "translateY(-20px)"; }
            if(cloudsDiv) cloudsDiv.style.opacity = "1";
            switchModoOscuro.checked = false;
        }
    }

    // Ejecución inmediata heredando preferencias locales y el callback síncrono de base de datos
    const preferedTheme = localStorage.getItem('theme');
    const phpTheme = <?php echo $modo_oscuro; ?>;
    if (preferedTheme === 'dark' || (preferedTheme === null && phpTheme === 1)) {
        aplicarModoOscuro(true);
    } else {
        aplicarModoOscuro(false);
    }

    // Evento de cambio manual por el Administrador con fetch asíncrono en caliente
    switchModoOscuro.addEventListener('change', async function() {
        const hitoDark = this.checked;
        aplicarModoOscuro(hitoDark);
        localStorage.setItem('theme', hitoDark ? 'dark' : 'light');

        // Sincronizar estado en caliente con el servidor
        const syncForm = new FormData();
        syncForm.append('toggle_dark_mode', '1');
        syncForm.append('modo_oscuro', hitoDark ? '1' : '0');
        try {
            await fetch('../Perfil/actualizar_configuracion.php', { method: 'POST', body: syncForm });
        } catch(e) { console.error("Error sincronizando tema: ", e); }
    });

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

    // 🌟 CONTROL DE ENVIÓ INTELIGENTE
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
<script src="../../JavaScript/Traduccion/traduccion.js"></script>
</body>
</html>