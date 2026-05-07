<?php 
require_once "verificar_sesion.php"; 
require_once "conexion.php";

$id_admin = $_SESSION['usuario_id'];
// Reutilizamos la lógica del sidebar que ya tienes
$query = "SELECT username, foto_perfil FROM usuarios WHERE id = $id_admin";
$res = $conn->query($query);
$row = $res->fetch_assoc();
$nombre = $row['username'];
$foto = $row['foto_perfil'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Publicaciones</title>
    <link rel="stylesheet" href="../../CSS/Perfil/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

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
                   <li><a href="dashboard_perfil.php"><i class="fa-solid fa-user" style="color: rgb(176, 171, 179);"></i> Perfil</a></li>
                    <li><a href="#"><i class="fa-solid fa-file-circle-check"></i> Validar Publicaciones</a></li>
                    <li class="active"><a href="dashboard_publicaciones.php"><i class="fa-regular fa-file"></i> Publicaciones</a></li>
                    <li><a href="dashboard_usuarios.php"><i class="fa-solid fa-users"></i> Usuarios</a></li>
                    <li><a href="dashboard_solicitudes.php"><i class="fa-solid fa-id-card-clip"></i> Solucitudes</a></li>
                    <li><a href="dashboard_estadisticas.php"><i class="fa-solid fa-chart-line"></i> Estadisticas</a></li>
                    <li><a href="#"><i class="fa-solid fa-gear"></i> Configuración</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <h1>Gestión de Publicaciones</h1>
                <div class="search-container">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchPost" placeholder="Buscar por título o autor...">
                </div>
            </header>

            <div class="scrollable-area">
                <div class="posts-grid" id="posts-container">
                    </div>
            </div>
        </main>
    </div>

    <script src="../../JavaScript/Dashboard/publicaciones.js"></script>
</body>
</html>