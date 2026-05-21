<!DOCTYPE html>
<html lang="es">

<head>
    <!-- ===================== CONFIGURACIÓN ===================== -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fauna</title>

    <!-- ===================== TIPOGRAFÍA ===================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    <!-- ===================== ESTILOS ===================== -->
    <link rel="stylesheet" href="../../CSS/Consejos/styles.css">
</head>

<body>

    <!-- ========================================================= -->
    <!-- ======================= TOP BAR ========================== -->
    <!-- ========================================================= -->

    <div class="barra-superior">

        <!-- IZQUIERDA: logo + perfil -->
        <div class="iconos-izquierda">

            <div class="caja-logo">
                <img src="../../assets/Home/logomini.png" alt="Logo" class="imagen-logo">
            </div>


        </div>

        <!-- DERECHA: buscador + idioma + login -->
        <div class="botones-derecha">

            <div class="caja-buscador">
                <input type="text" placeholder="Buscar...">
                <i class="fas fa-search"></i>
            </div>

            <div class="caja-idioma">ES / EN</div>

            <a href="../Login/login.php" class="caja-login">Iniciar sesión</a>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- ======================= HERO ============================= -->
    <!-- ========================================================= -->

    <div class="seccion-hero">
        <h1 class="titulo-hero">Consejos</h1>
        <p class="texto-hero">Una categoria centrada en mostrar consejos para cuidar la vida terrestre</p>

        <!-- -------- BOTONES DE FILTRO -------- -->
        <div class="barra-botones">

            <button class="boton-filtro">Todos</button>
            <button class="boton-filtro">Acciones individuales</button>
            <button class="boton-filtro">Acciones escolares</button>
            <button class="boton-filtro">Acciones comunitarias</button>
            <button class="boton-filtro">Consumo responsable</button>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- ================= CONTENIDO PRINCIPAL ==================== -->
    <!-- ========================================================= -->

    <div class="contenido-principal" id="contenedorPrincipal">

        <!-- -------- PUBLICACIONES -------- -->
        <div class="grid-publicaciones" id="contenedorPublicaciones"></div>

    </div>

    <!-- ========================================================= -->
    <!-- ================= BOTON SCROLL TOP ====================== -->
    <!-- ========================================================= -->

    <button class="btn-scroll-top" id="btnScrollTop" aria-label="Volver arriba">
        <img src="../../assets/Consejos/iconoBtnTopScroll.png" alt="Volver arriba">
    </button>

    <!-- ===================== SCRIPT ===================== -->
    <script src="../../JavaScript/Consejos/script.js"></script>

</body>
</html>
