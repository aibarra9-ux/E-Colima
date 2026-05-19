<?php
session_start();
$sesion_activa = isset($_SESSION['usuario']);
$ruta_perfil = $sesion_activa ? ((isset($_SESSION['rol_id']) && (int)$_SESSION['rol_id'] === 1) ? '../Perfil/dashboard_perfil.php' : '../Perfil/perfil.php') : '../Login/login.php';
$query_busqueda = isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de búsqueda - E-COLIMA</title>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;600;700&family=Outfit:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../CSS/Home/style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        /* 🌟 VARIABLES PARA MODO CLARO (POR DEFECTO) */
        :root {
            --fondo-principal: #f4f4f9;
            --fondo-tarjeta: #ffffff;
            --texto-principal: #121212;
            --texto-secundario: #555555;
            --borde-tarjeta: rgba(0, 0, 0, 0.08);
            --color-acento: #2d5a27;
        }

        /* 🌟 VARIABLES PARA MODO OSCURO (SE ACTIVA CON LA CLASE DARK-MODE EN EL BODY) */
        body.dark-mode {
            --fondo-principal: #121212;
            --fondo-tarjeta: rgba(255, 255, 255, 0.05);
            --texto-principal: #ffffff;
            --texto-secundario: rgba(255, 255, 255, 0.7);
            --borde-tarjeta: rgba(255, 255, 255, 0.1);
            --color-acento: #9bedb7;
        }

        /* Corrección del espacio blanco inferior y herencia de fondo */
        html, body {
            background-color: var(--fondo-principal);
            color: var(--texto-principal);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .seccion-resultados { 
            padding: 40px 10%; 
            background-color: var(--fondo-principal); 
            min-height: calc(100vh - 80px); 
            box-sizing: border-box;
        }

        .titulo-busqueda { 
            color: var(--texto-principal); 
            font-family: 'League Spartan', sans-serif; 
            margin-bottom: 30px; 
            font-size: 2rem; 
        }
        
        .titulo-busqueda span { 
            color: #2d5a27; 
        }
        body.dark-mode .titulo-busqueda span {
            color: #9bedb7;
        }

        /* Contenedores y Grilla adaptables */
        .contenedor-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
        
        .tarjeta-grande { 
            grid-column: span 3; 
            display: flex; 
            background: var(--fondo-tarjeta); 
            border: 1px solid var(--borde-tarjeta);
            border-radius: 15px; 
            overflow: hidden; 
            margin-bottom: 20px; 
            opacity: 0; 
            transform: translateY(20px); 
            transition: opacity 0.5s, transform 0.5s, background-color 0.3s; 
        }
        .tarjeta-grande.mostrar { opacity: 1; transform: translateY(0); }
        
        .tarjeta-publicacion { 
            background: var(--fondo-tarjeta); 
            border: 1px solid var(--borde-tarjeta);
            border-radius: 12px; 
            overflow: hidden; 
            display: flex; 
            flex-direction: column; 
            padding: 15px; 
            opacity: 0; 
            transform: translateY(20px); 
            transition: opacity 0.5s, transform 0.5s, background-color 0.3s; 
        }
        .tarjeta-publicacion.mostrar { opacity: 1; transform: translateY(0); }
        
        .imagen-grande { width: 50%; max-height: 400px; object-fit: cover; }
        .info-grande { padding: 30px; display: flex; flex-direction: column; justify-content: center; width: 50%; color: var(--texto-principal); }
        .imagen-publicacion { width: 100%; height: 200px; object-fit: cover; border-radius: 8px; }
        
        .titulo-publicacion { color: var(--texto-principal); font-family: 'League Spartan'; margin: 15px 0 10px; }
        .descripcion-grande { color: var(--texto-secundario); }
        .descripcion-publicacion { color: var(--texto-secundario); font-size: 0.9rem; line-height: 1.4; margin-bottom: 15px; }
        
        .boton-ver-mas { background: #2d5a27; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; }
        
        /* Ajuste de íconos de corazones según el tema */
        .info-grande button i, .likes-container button i {
            color: var(--texto-secundario);
        }
    </style>
</head>
<body>

    <div class="top-bar">
        <div class="topbar-main-row">
            <div class="left-icons">
                <div class="logo-box" onclick="window.location.href='home.php'" style="cursor:pointer;">
                    <img src="../../assets/Home/Logo_Oficial.png" alt="Logo" class="logo-img">
                </div>
                <div class="perfil-box" onclick="window.location.href='<?php echo $ruta_perfil; ?>'">
                    <i class="fas fa-user"></i>
                    <?php if ($sesion_activa): ?> <span class="notif-dot"></span> <?php endif; ?>
                </div>
            </div>
            <div class="right-buttons">
                <form action="buscar.php" method="GET" class="search-box">
                    <input type="text" name="q" value="<?php echo $query_busqueda; ?>" placeholder="Buscar...">
                    <button type="submit" style="background:none; border:none; color:inherit; cursor:pointer;"><i class="fas fa-search"></i></button>
                </form>
                <div class="lang-box">ES / EN</div>
                <?php if($sesion_activa): ?>
                    <a href="../Login/logout.php" class="login-box">Cerrar sesión</a>
                <?php else: ?>
                    <a href="../Login/login.php" class="login-box">Iniciar sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <section class="seccion-resultados">
        <h2 class="titulo-busqueda">Resultados para: <span>"<?php echo $query_busqueda; ?>"</span></h2>
        
        <div class="contenedor-grid" id="contenedorPublicaciones"></div>
    </section>

    <script>
        const contenedor = document.getElementById("contenedorPublicaciones");
        const consultaOriginal = "<?php echo $query_busqueda; ?>";

        // Sincronizar el estado del modo oscuro con la configuración almacenada de tu Home
        if (localStorage.getItem('theme') === 'dark' || localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
        }

        function ejecutarBusqueda() {
            if(!consultaOriginal) return;

            fetch(`buscar_backend.php?q=${encodeURIComponent(consultaOriginal)}`)
                .then(res => res.json())
                .then(publicaciones => {
                    contenedor.innerHTML = "";

                    if (publicaciones.length === 0) {
                        contenedor.innerHTML = `
                            <div style="grid-column: 1 / -1; text-align: center; padding: 50px; color: var(--texto-principal);">
                                <i class="fas fa-search-minus" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
                                <p style="font-family: 'Outfit', sans-serif; font-size: 1.2rem;">No se encontraron publicaciones que coincidan con tu búsqueda.</p>
                            </div>`;
                        return;
                    }

                    publicaciones.forEach((post, i) => {
                        if (i === 0) {
                            const tarjetaGrande = document.createElement("div");
                            tarjetaGrande.classList.add("tarjeta-grande");
                            tarjetaGrande.innerHTML = `
                                <img class="imagen-grande" src="${post.imagen}" style="cursor: pointer;" onclick="window.location.href='../Publicacion/detalle_publicacion.php?id=${post.id}'">
                                <div class="info-grande">
                                    <h2 style="cursor: pointer;" onclick="window.location.href='../Publicacion/detalle_publicacion.php?id=${post.id}'">${post.titulo}</h2>
                                    <p class="descripcion-grande">${post.descripcion}</p>
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 15px;">
                                        <button class="boton-ver-mas" onclick="window.location.href='../Publicacion/detalle_publicacion.php?id=${post.id}'">Ver más</button>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <button onclick="interactuarLike(${post.id}, this)" style="background: none; border: none; cursor: pointer; font-size: 1.4rem;">
                                                <i class="${post.le_gusta ? 'fa-solid fa-heart' : 'fa-regular fa-heart'}" style="color: ${post.le_gusta ? '#e63946' : 'var(--texto-secundario)'};"></i>
                                            </button>
                                            <span style="color: var(--texto-principal);">${post.likes}</span>
                                        </div>
                                    </div>
                                </div>`;
                            contenedor.appendChild(tarjetaGrande);
                            setTimeout(() => tarjetaGrande.classList.add("mostrar"), 100);
                            return;
                        }

                        const tarjeta = document.createElement("div");
                        tarjeta.classList.add("tarjeta-publicacion");
                        tarjeta.style.cursor = "pointer";
                        
                        tarjeta.addEventListener("click", (event) => {
                            if (!event.target.closest('button')) {
                                window.location.href = `../Publicacion/detalle_publicacion.php?id=${post.id}`;
                            }
                        });
                        
                        tarjeta.innerHTML = `
                            <img class="imagen-publicacion" src="${post.imagen}">
                            <h3>${post.titulo}</h3>
                            <p class="descripcion-publicacion">${post.descripcion}</p>
                            <div class="likes-container" style="display: flex; align-items: center; gap: 8px; margin-top: auto; padding-top: 10px; border-top: 1px solid var(--borde-tarjeta);">
                                <button onclick="interactuarLike(${post.id}, this)" style="background: none; border: none; cursor: pointer; font-size: 1.2rem; padding: 0;">
                                    <i class="${post.le_gusta ? 'fa-solid fa-heart' : 'fa-regular fa-heart'}" style="color: ${post.le_gusta ? '#e63946' : 'var(--texto-secundario)'};"></i>
                                </button>
                                <span style="color: var(--texto-principal);">${post.likes}</span>
                            </div>`;
                        contenedor.appendChild(tarjeta);
                        setTimeout(() => tarjeta.classList.add("mostrar"), 100 * i);
                    });
                });
        }

        async function interactuarLike(postId, boton) {
            try {
                const response = await fetch(`../../PHP/Perfil/dar_like.php?publicacion_id=${postId}`);
                const data = await response.json();
                if (data.status === 'success') {
                    const icono = boton.querySelector('i');
                    const contador = boton.nextElementSibling;
                    if (data.accion === 'liked') {
                        icono.className = 'fa-solid fa-heart';
                        icono.style.color = '#e63946';
                    } else {
                        icono.className = 'fa-regular fa-heart';
                        icono.style.color = 'var(--texto-secundario)';
                    }
                    contador.textContent = data.total_likes;
                } else if (data.message === 'No autorizado') {
                    alert('Necesitas iniciar sesión para dar me gusta.');
                }
            } catch (err) { console.error(err); }
        }

        document.addEventListener("DOMContentLoaded", ejecutarBusqueda);
    </script>
</body>
</html>