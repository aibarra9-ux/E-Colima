<?php
session_start();
// --- AGREGADO: Conexión y lógica de detección ---
include "../Login/conexion.php"; 
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : null;
if ($tipo) {
    // MODIFICADO: Agregamos ORDER BY RAND() para aleatoriedad
    $query = "SELECT * FROM consejos WHERE tipo = '$tipo' ORDER BY RAND()";
    $resultado_consejos = $conn->query($query);
}
// -----------------------------------------------
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-COLIMA</title>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../CSS/Home/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="top-bar">
        <div class="left-icons">
            <div class="logo-box">
                <img src="../../assets/Home/logomini.png" alt="Logo" class="logo-img">
            </div>
            <div class="perfil-box">
                <i class="fas fa-user"></i>
                <span class="notif-dot"></span>
            </div>
        </div>

        <div class="right-buttons">
            <div class="search-box">
                <input type="text" placeholder="Buscar...">
                <i class="fas fa-search"></i>
            </div>
            <div class="lang-box">ES / EN</div>
            <?php if(isset($_SESSION['usuario'])): ?>
                <a href="../Login/logout.php" class="login-box">Cerrar sesión</a>
            <?php else: ?>
                <a href="../Login/login.php" class="login-box">Iniciar sesión</a>
            <?php endif; ?>        
        </div>
    </div>

    <?php if (!$tipo): ?>
        <div class="home">
            <h1 class="logo">
                E<span class="glass">CO</span>LIMA
            </h1>
        </div>  

        <div class="slogan">
            Colima es un museo vivo de biodiversidad, donde cada especie y cada paisaje cuentan una historia; explora, aprende y descubre cómo conservar estos tesoros naturales con información completa y confiable.
        </div>

        <section class="categorias">
            <h2 class="categorias-titulo">Adéntrate en la red de la vida.</h2>
            <div class="categorias-grid">
                
                <div class="categoria-card">
                    <div class="card-image" style="background-image: url('../../assets/Home/ipomoea2.jpg');">
                        <div class="card-overlay">
                            <h3 class="card-titulo">Flora</h3>
                            <p class="card-descripcion">Descubre la increíble variedad de plantas y vegetación que hacen único a Colima</p>
                            <a href="home.php?tipo=flora" class="card-boton" style="text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px;">
                                Leer más <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="categoria-card">
                    <div class="card-image" style="background-image: url('../../assets/Home/AVIARIO-3.webp');">
                        <div class="card-overlay">
                            <h3 class="card-titulo">Fauna</h3>
                            <p class="card-descripcion">Conoce las fascinantes especies animales que habitan en los diversos ecosistemas de Colima</p>
                            <a href="home.php?tipo=fauna" class="card-boton" style="text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px;">
                                Leer más <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <section class="noticias">
            <h2>Noticias de los ecosistemas de Colima</h2>
            <p>Noticia: ...</p>
        </section>

    <?php else: ?>
        <?php include "detalle_categoria.php"; ?>
    <?php endif; ?>

    <footer class="footer-creditos">
        <div class="footer-contenido">
            <div class="creditos-lista">
                <h3>CRÉDITOS</h3>
                <ul>
                    <li>Alan Ibarra</li>
                    <li>Dana Nava</li>
                    <li>Miranda Montiel</li>
                    <li>Carolina Zuñiga</li>
                    <li>Ricardo Barba</li>
                </ul>
            </div>
            <div class="ubicacion-centro">
                <p class="ciudad">Manzanillo, Colima.</p>
                <p class="fecha">Marzo 2026</p>
            </div>
            <div class="universidad-info">
                <h3>UNIVERSIDAD DE COLIMA</h3>
                <p>Facultad de Ingeniería Electromecánica</p>
            </div>
        </div>
    </footer>
</body>
</html>