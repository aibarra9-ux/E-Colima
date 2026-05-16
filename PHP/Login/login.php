<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../../CSS/Login/estilos.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
</head>
<body>

    <div class="contenedor">

        <div class="login-section">
            <a href="../Home/home.php" class="back-home" data-section="login" data-value="volver">← Volver</a>

            <div class="card">
                <h3 data-section="login" data-value="titulo">Hola de nuevo, ¿Qué tal?</h3>
                <?php if(isset($_GET['error'])): ?>
                    <div class="login-error" style="color: #ff4d4f !important;">
                        <span style="color: #ff4d4f !important;">⚠</span>
                        <span style="color: #ff4d4f !important;">
                            <?php
                                if($_GET['error'] == "usuario"){
                                    // AGREGADO: Estructura span con data attributes manteniendo tu texto
                                    echo '<span data-section="login" data-value="err_usuario">Usuario no encontrado</span>';
                                }
                                if($_GET['error'] == "password"){
                                    // AGREGADO: Estructura span con data attributes manteniendo tu texto
                                    echo '<span data-section="login" data-value="err_password">Contraseña incorrecta</span>';
                                }
                            ?>
                        </span>
                    </div>
                <?php endif; ?>

                <form action="procesar_login.php" method="POST">
                    <div class="input-group">
                        <input type="text" name="username" placeholder="Nombre de usuario" data-section="login" data-value="placeholder_user" required>
                    </div>

                    <div class="input-group password">
                        <input type="password" name = "password" id="password" placeholder="Ingresar contraseña" data-section="login" data-value="placeholder_pass" required>
                        <span class="eye">
                            <img src="../../assets/Login/eye-off-fill.png" id="togglePassword" alt="mostrar contraseña">
                        </span>
                    </div>

                    <div class="extras">
                        <label class="switch">
                            <input type="checkbox">
                            <span class="slider"></span>
                            <span data-section="login" data-value="recordar">Recordar usuario</span>
                        </label>
                        <a href="#" class="forgot" data-section="login" data-value="olvidaste">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" class="btn-login" data-section="login" data-value="btn_entrar">Entrar</button>

                    <div class="divider"></div>

                    <button type="button" class="btn-google">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google">
                        <span data-section="login" data-value="btn_google">O únete con Google</span>
                    </button>

                    <p class="register">
                        <span data-section="login" data-value="no_cuenta">No tienes una cuenta aún?</span>
                        <a href="registro.php" data-section="login" data-value="registrate">Regístrate</a>
                    </p>

                </form>
            </div>

        </div>

        <div class="image-section">
            <img src="../../assets/Login/imagen_gajuar.png" class="jaguar" alt="Jaguar">

        </div>

    </div>
    
    <script src="../../JavaScript/script.js"></script>
    
    <script src="../../JavaScript/Login/login.js"></script>
</body>
</html>