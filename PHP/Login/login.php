<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title data-translate="true">Login</title>
    <link rel="stylesheet" href="../../CSS/Login/estilos.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>

<!-- 🔥 Detecta error -->
<body <?php if(isset($_GET['error'])) echo 'data-error="true"'; ?>>

<div class="contenedor">

    <!-- LADO IZQUIERDO -->
    <div class="login-section">

        <a href="../Home/home.php" class="back-home" data-translate="true">← Volver</a>

        <div class="card">
            <h3 data-translate="true">Bienvenido de nuevo 👋</h3>

            <!-- ERROR -->
           <?php if(isset($_GET['error'])): ?>
    <div class="login-error mostrar-error">
        <span>⚠</span>
        <span>
            <?php 
                switch($_GET['error']) {
                    case 'acceso_denegado':
                        echo "No tienes permisos de administrador.";
                        break;
                    case 'no_autorizado':
                        echo "Inicia sesión para acceder a esta área.";
                        break;
                    case 'password':
                    case 'usuario':
                        echo "Credenciales incorrectas.";
                        break;
                    default:
                        echo "Ha ocurrido un error inesperado.";
                }
            ?>
        </span>
    </div>
<?php endif; ?>

            <form action="procesar_login.php" method="POST" id="loginForm">

                <div class="input-group">
                    <input type="text" name="username"
                        minlength="4" maxlength="20"
                        pattern="[A-Za-z0-9_.]+"
                        placeholder="Nombre de usuario"
                        data-translate-placeholder="true"
                        autocomplete="username"
                        required>
                </div>

                <div class="input-group password">
                    <input type="password" name="password" id="password"
                        placeholder="Ingresar contraseña"
                        data-translate-placeholder="true"
                        autocomplete="current-password"
                        required>

                    <span class="eye">
                        <img src="../../assets/Login/eye-off-fill.png"
                             class="togglePassword"
                             alt="mostrar contraseña">
                    </span>
                </div>

                <div class="extras">
                    <label class="switch">
                        <input type="checkbox" name="remember">
                        <span class="slider"></span>
                        <span data-translate="true">Recordar usuario</span>
                    </label>

                    <a href="javascript:void(0);" id="forgotPassword" class="forgot" data-translate="true">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit" class="btn-login" data-translate="true">Entrar</button>

                <div class="divider"></div>

                <div id="g_id_onload"
     data-client_id="551526400527-d7btgc4vcgpfmqsuieooo6naols4g2la.apps.googleusercontent.com"
     data-context="signin"
     data-ux_mode="redirect"
     data-login_uri="http://localhost/e-Colima/PHP/Login/callback_google.php"
     data-auto_prompt="false">
</div>

<div class="g_id_signin"
     data-type="standard"
     data-shape="rectangular"
     data-theme="dark"
     data-text="signin_with"
     data-size="large"
     data-logo_alignment="left">
</div>

                <p class="register">
                    <span data-translate="true">No tienes una cuenta aún?</span>
                    <a href="registro.php">Regístrate</a>
                </p>

            </form>
        </div>

    </div>

    <!-- LADO DERECHO -->
    <div class="image-section">
        <img src="../../assets/Login/Imagen Onza.png" class="jaguar" alt="Jaguar">
    </div>

</div>

<script src="../../JavaScript/Login/login.js"></script>
<script src="../../JavaScript/Traduccion/traduccion.js"></script>
</body>
</html>