
<?php 
require_once "verificar_sesion.php"; 
require_once "conexion.php";

// Obtenemos los datos completos del usuario logueado
$id_admin = $_SESSION['usuario_id'];
$query = "SELECT username, email, fecha_registro, foto_perfil, banner_perfil FROM usuarios WHERE id = $id_admin";
$resultado = $conn->query($query);
$datos_admin = $resultado->fetch_assoc();

// Guardamos en variables para usarlas en el HTML
$nombre = $datos_admin['username'];
$correo = $datos_admin['email'];
$fecha = date("d M, Y", strtotime($datos_admin['fecha_registro'])); // Formato: 25 Abr, 2024
$foto = $datos_admin['foto_perfil'];
$banner = $datos_admin['banner_perfil'] ?? 'default_banner.jpg'; // Imagen por defecto
// ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../../CSS/Perfil/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="dashboard-container">
        
        <aside class="sidebar">
            <div class="profile-section">
                <div class="avatar">
                    <img src="../../assets/Fotos_perfil/<?php echo $foto; ?>" alt="Perfil" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">                </div>
                <h2><?php echo strtoupper($nombre); ?></h2>
                <p>Administrador</p>
            </div>

            <nav class="sidebar-menu">
                <ul>
                    <li class="active"><a href="dashboard_perfil.php"><i class="fa-solid fa-user" style="color: rgb(176, 171, 179);"></i> Perfil</a></li>
                    <li><a href="#"><i class="fa-regular fa-file"></i> Publicaciones</a></li>
                    <li><a href="dashboard_usuarios.php"><i class="fa-solid fa-users"></i> Usuarios</a></li>
                    <li><a href="#"><i class="fa-solid fa-bell"></i> Notificaciones</a></li>
                    <li><a href="dashboard_estadisticas.php"><i class="fa-solid fa-chart-line"></i> Estadisticas</a></li>
                    <li><a href="#"><i class="fa-solid fa-gear"></i> Configuración</a></li>
                </ul>
            </nav>
            </aside>

        <main class="main-content">
           <div class="scrollable-area">
    
                <div class="profile-main-card">
                    <div class="profile-cover" style="background-image: url('../../assets/Fotos_banner/<?php echo $banner; ?>'); background-size: cover; background-position: center;">
                        <button type="button" class="change-banner-btn" onclick="document.getElementById('inputBanner').click();">
                            <i class="fa-solid fa-camera"></i> Cambiar Portada
                        </button>
                        <input type="file" id="inputBanner" accept="image/*" style="display: none;">
                    </div>
                    <div class="profile-content">
                        
                        <div class="profile-avatar-wrapper">
                            
                            <div class="avatar-large">
                                <?php 
                                    $foto_mostrar = $datos_admin['foto_perfil'] ?? 'default_avatar.png';
                                ?>
                                <img src="../../assets/Fotos_perfil/<?php echo $foto_mostrar; ?>" alt="Foto de Perfil" id="imgPerfilGrande">
                            </div>
    
                            <button type="button" class="change-photo-btn" onclick="document.getElementById('inputFoto').click();">
                                <i class="fa-solid fa-camera"></i>
                            </button>
    
                            <input type="file" id="inputFoto" accept="image/*" style="display: none;">
                        </div>
                        <div class="profile-header-text">
                            <div class="name-row">
                                <h2><?php echo strtoupper($nombre); ?></h2>
                                <span class="badge-admin">Admin Verificado</span>
                            </div>
                            <p class="role-text">Super Administrador de Ecolima</p>
                            <div class="profile-stats-row">
                                <span><strong>Online</strong></span>
                            </div>
                        </div>
                        <button id="btnEditarPerfil" class="btn-edit-main">Editar Información</button>
                    </div>
                </div>

                <div class="middle-grid">
                    <div class="card info-card">
                        <div class="card-header-flex">
                            <span>Información Personal</span>
                            <i class="fa-solid fa-address-card"></i>
                        </div>
                        <div class="info-list">
                            <div class="info-item"><span>Correo:</span> <strong><?php echo $correo; ?></strong></div>
                            <div class="info-item"><span>Miembro desde:</span> <strong><?php echo $fecha; ?></strong></div>
                        </div>
                    </div>

                    <div class="card info-card">
                        <div class="card-header-flex">
                            <span>Seguridad del Sistema</span>
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div class="security-list">
                            <div class="security-item">
                                <div class="sec-text">
                                    <p>Nivel de Acceso</p>
                                    <small>Control Total (ID: <?php echo $_SESSION['rol_id']; ?>)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="modalEdit" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Editar Perfil de Administrador</h3>
            <button id="closeModal" class="btn-close">&times;</button>
        </div>
        <form id="formEditarPerfil">
            <div class="input-group">
        <label>Nombre de Usuario</label>
        <input type="text" name="username" 
               value="<?php echo $nombre; ?>" 
               minlength="4" maxlength="20" 
               pattern="[A-Za-z0-9_.]+" 
               title="Solo letras, números, _ o ." 
               placeholder="Nombre de usuario" required>
    </div>
    <div class="input-group">
        <label>Correo Electrónico</label>
        <input type="email" name="email" 
               value="<?php echo $correo; ?>" 
               placeholder="Correo electrónico" required>
    </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" id="btnCancel">Cancelar</button>
                <button type="submit" class="btn-save">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
<script src="../../JavaScript/Dashboard/perfil.js"></script>
</body>
</html>
