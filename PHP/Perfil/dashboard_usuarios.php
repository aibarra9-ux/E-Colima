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
    $nombre = "Admin";
    $foto = "default_avatar.png"; // Valor por defecto por seguridad
}
?>
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
                    <li><a href="dashboard_perfil.php"><i class="fa-solid fa-user" style="color: rgb(176, 171, 179);"></i> Perfil</a></li>
                    <li><a href="#"><i class="fa-solid fa-file-circle-check"></i> Validar Publicaciones</a></li>
                    <li><a href="dashboard_publicaciones.php"><i class="fa-regular fa-file"></i> Publicaciones</a></li>
                    <li class="active"><a href="dashboard_usuarios.php"><i class="fa-solid fa-users"></i> Usuarios</a></li>
                    <li><a href="dashboard_solicitudes.php"><i class="fa-solid fa-id-card-clip"></i> Solucitudes</a></li>
                    <li><a href="dashboard_estadisticas.php"><i class="fa-solid fa-chart-line"></i> Estadisticas</a></li>
                    <li><a href="#"><i class="fa-solid fa-gear"></i> Configuración</a></li>
                </ul>
            </nav>
            </aside>

        
        <main class="main-content">
            
        

           <div class="scrollable-area">
    <div class="card users-management-card">
        <div class="card-header-flex">
            <span>Gestión de Usuarios Registrados</span>
            <div class="table-actions">
            <input type="text" id="user-search" placeholder="Buscar usuario por nombre o correo...">            </div>
        </div>

        <table class="users-table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Correo Electrónico</th>
                    <th>Fecha Registro</th>
                    <th>Rol actual</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="users-table-body">
               
                </tbody>
        </table>
    </div>
</div>
        </main>
    </div>
<script src="../../JavaScript/Dashboard/usuarios.js"></script>
</body>
</html>
