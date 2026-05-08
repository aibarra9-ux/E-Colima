<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../../CSS/Login/estilos.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<!-- 🔥 Detecta error -->
<body <?php if(isset($_GET['error'])) echo 'data-error="true"'; ?>>

<div class="contenedor">

    <!-- LADO IZQUIERDO -->
    <div class="login-section">

        <a href="../Home/home.php" class="back-home">← Volver</a>

        <div class="card">
            <h3>Bienvenido de nuevo 👋</h3>

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
                        autocomplete="username"
                        required>
                </div>

                <div class="input-group password">
                    <input type="password" name="password" id="password"
                        placeholder="Ingresar contraseña"
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
                        Recordar usuario
                    </label>

                    <a href="#" class="forgot">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit" class="btn-login">Entrar</button>

                <div class="divider"></div>

                <button type="button" class="btn-google">
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg">
                    O únete con Google
                </button>

                <p class="register">
                    No tienes una cuenta aún?
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
</body>
</html>
