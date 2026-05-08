<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-COLIMA</title>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../CSS/Home/style.css">

    <!--- ICONOS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">


    <style>
/* Capa de fondo con desenfoque */
.modal-overlay {
    display: none; /* Se activa con JS */
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.4); 
    backdrop-filter: blur(8px);
    z-index: 9999; /* Por encima de todo */
    justify-content: center;
    align-items: center;
}

/* El rectángulo de cristal (Glassmorphism) */
.modal-content {
    background: rgba(255, 255, 255, 0.15);
    padding: 2.5rem;
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    text-align: center;
    width: 380px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.modal-content h2 {
    color: white;
    margin: 15px 0;
    font-family: 'League Spartan', sans-serif;
    font-size: 1.8rem;
}

.modal-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 25px;
}

/* Botón Cancelar - Verde claro semi-transparente */
.btn-cancelar {
    background: rgba(144, 238, 144, 0.3);
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    color: white;
    cursor: pointer;
    font-weight: 600;
    transition: 0.3s;
}

/* Botón Confirmar - Verde bosque oscuro */
.btn-confirmar {
    background: #2d5a27;
    text-decoration: none;
    padding: 12px 24px;
    border-radius: 10px;
    color: white;
    font-weight: 700;
    transition: 0.3s;
}

.btn-confirmar:hover { background: #1e3d1a; transform: scale(1.05); }
.btn-cancelar:hover { background: rgba(144, 238, 144, 0.5); }
</style>

</head>
<body>

    <div class="top-bar">
        <!-- Izquierda: logo + perfil -->
        <div class="left-icons">
            <div class="logo-box">
                <img src="../../assets/Home/logomini.png" alt="Logo" class="logo-img">
            </div>
            
        </div>
        
        <!-- Derecha: buscador + idioma + login -->
        <div class="right-buttons">
            <div class="search-box">
                <input type="text" placeholder="Buscar...">
                <i class="fas fa-search"></i>
            </div>
            <div class="lang-box">ES / EN</div>
            <?php if(isset($_SESSION['usuario'])): ?>

                <a href="#" class="login-box" onclick="mostrarModal(event)">Cerrar sesión</a>

            <?php else: ?>

                <a href="../Login/login.php" class="login-box">Iniciar sesión</a>

            <?php endif; ?>        
        </div>
    </div>

    <?php if(isset($_SESSION['mensaje'])): ?>
        <div class="alerta <?php echo $_SESSION['tipo'] ?? ''; ?>">
            <?php 
                echo $_SESSION['mensaje']; 
                unset($_SESSION['mensaje']);
            ?>
        </div>
    <?php endif; ?>

    <div class="home">
        
        <?php if(isset($_GET['success']) && isset($_SESSION['usuario'])): ?>
            <p class="bienvenida">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?></p>
        <?php endif; ?>
        
        <h1 class="logo">
            E<span class="glass">CO</span>LIMA
        </h1>
        
    </div>

    <div class="slogan">
        Colima es un museo vivo de biodiversidad, donde cada especie y cada paisaje cuentan una historia; explora, aprende y descubre cómo conservar estos tesoros naturales con información completa y confiable.
    </div>

    <!-- CATEGORÍAS - SECCIÓN FLORA -->
<section class="categorias">
    <h2 class="categorias-titulo">Adéntrate en la red de la vida.</h2>
    <div class="categorias-grid">
        <!-- Tarjeta Flora -->
        <div class="categoria-card">
            <div class="card-image" style="background-image: url('../../assets/Home/ipomoea2.jpg');">
                <div class="card-overlay">
                    <h3 class="card-titulo">Flora</h3>
                    <p class="card-descripcion">Descubre la increíble variedad de plantas y vegetación que hacen único a Colima</p>
                    <button class="card-boton">Leer más <i class="fas fa-arrow-right"></i></button>
                </div>
            </div>
        </div>
        <!-- Aquí puedes agregar más tarjetas (Fauna, Hongos, etc.) en el futuro -->
        <!-- Tarjeta Fauna -->
        <div class="categoria-card">
            <div class="card-image" style="background-image: url('../../assets/Home/AVIARIO-3.webp');">
                <div class="card-overlay">
                    <h3 class="card-titulo">Fauna</h3>
                    <p class="card-descripcion">Conoce las fascinantes especies animales que habitan en los diversos ecosistemas de Colima</p>
                    <button class="card-boton">Leer más <i class="fas fa-arrow-right"></i></button>
                </div>
            </div>
        </div>

        <div class="categoria-card">
            <div class="card-image" style="background-image: url('../../assets/Ecosistemas/Ecosistemas categoria.png');">
                <div class="card-overlay">
                    <h3 class="card-titulo">Ecosistemas</h3>
                    <p class="card-descripcion">Conoce los fascinantes ecosistemas terrestres del estado de Colima</p>
                    <a class="card-boton" href="../Ecosistemas/ecosistemas.php">
                        Leer más <i class="fas fa-arrow-right"></i>
                    </a>                
                </div>
            </div>
        </div>
    </div>
</section>

    <section class="noticias">
        <h2>Noiticas de los ecosistemas de Colima</h2>
        <p>
            Noticia:
            Noticia:
            Noticia:
        </p>
    </section>

    <script>
    setTimeout(() => {
        const alerta = document.querySelector('.alerta');
        if(alerta){
            alerta.style.display = 'none';
        }
    }, 3000); // 3 segundos
    </script>

    <!-- ===== FOOTER CRÉDITOS ===== -->
    <footer class="footer-creditos">
        <div class="footer-contenido">
            <!-- Lado izquierdo: Créditos con nombres -->
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

            <!-- NUEVO: Centro - Ubicación y fecha -->
            <div class="ubicacion-centro">
                <p class="ciudad">Manzanillo, Colima.</p>
                <p class="fecha">Febrero 2026</p>
            </div>

            <!-- Lado derecho: Universidad -->
            <div class="universidad-info">
                <h3>UNIVERSIDAD DE COLIMA</h3>
                <p>Facultad de Ingeniería Electromecánica</p>
            </div>
        </div>
    </footer>
    <script>
    setTimeout(() => {
        const alerta = document.querySelector('.alerta');
        if(alerta){
            alerta.style.animation = "salir 0.4s ease forwards";

            setTimeout(() => {
                alerta.remove();
            }, 400);
        }
    }, 1500); // ⏱ dura 2.5 segundos
    </script>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Ahora sí, buscamos los elementos cuando el HTML ya está listo
    const modal = document.getElementById('modalCerrarSesion');
    const btnCancelar = document.getElementById('btnCancelar');

    // Función para abrir el modal (esta puede quedar fuera o dentro)
    window.mostrarModal = function(event) {
        event.preventDefault(); 
        if(modal) modal.style.display = 'flex';
    }

    // Solo asignamos el clic si el botón realmente existe
    if (btnCancelar) {
        btnCancelar.onclick = function() {
            modal.style.display = 'none';
        }
    }

    // Cerrar al hacer clic fuera del cuadro
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
});
</script>

    <div id="modalCerrarSesion" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <i class="fas fa-paw" style="color: #9bedb7; font-size: 2rem; margin-bottom: 10px; transform: rotate(-20px);"></i>
            <h2>¿CERRAR SESIÓN?</h2>
            <p style="color: #ffffff; font-weight: 600; font-size: 0.95rem; margin-bottom: 20px; font-family: 'League Spartan', sans-serif; line-height: 1.4; text-shadow: 0px 1px 2px rgba(0,0,0,0.2);">
            Si cierras sesión, tendrás que volver a ingresar para ver tu perfil e interactuar en las publicaciones.
        </p>
        </div>
        <div class="modal-buttons">
            <button id="btnCancelar" class="btn-cancelar">Cancelar</button>
            <a href="../Login/logout.php" class="btn-confirmar">Aceptar</a>
        </div>
    </div>
</div>

</body>
</html>
