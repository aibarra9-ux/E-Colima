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

        <!-- Lado izquierdo -->
        <div class="login-section">
            <a href="../Home/home.php" class="back-home">← Volver</a>

            <div class="card">
                <h3>Hola de nuevo, ¿Qué tal?</h3>
                <?php if(isset($_GET['error'])): ?>
                    <div class="login-error" style="color: #ff4d4f !important;">
                        <span style="color: #ff4d4f !important;">⚠</span>
                        <span style="color: #ff4d4f !important;">
                            <?php
                                if($_GET['error'] == "usuario"){
                                    echo "Usuario no encontrado";
                                }
                                if($_GET['error'] == "password"){
                                    echo "Contraseña incorrecta";
                                }
                            ?>
                        </span>
                    </div>
                <?php endif; ?>

                <form action="procesar_login.php" method="POST">
                    <div class="input-group">
                        <input type="text" name="username" placeholder="Nombre de usuario" required>
                    </div>

                    <div class="input-group password">
                        <input type="password" name = "password" id="password"placeholder="Ingresar contraseña" required>
                        <span class="eye">
                            <img src="../../assets/Login/eye-off-fill.png" id="togglePassword" alt="mostrar contraseña">
                        </span>
                    </div>

                    <div class="extras">
                        <label class="switch">
                            <input type="checkbox">
                            <span class="slider"></span>
                                Recordar usuario
                        </label>
                        <a href="#" class="forgot">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" class="btn-login">Entrar</button>

                    <div class="divider"></div>

                    <button type="button" class="btn-google">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google">
                        O únete con Google
                    </button>

                    <p class="register">
                        No tienes una cuenta aún?
                        <a href="registro.php">Regístrate</a>
                    </p>

                </form>
            </div>

        </div>

        <!-- Lado derecho -->
        <div class="image-section">
            <img src="../../assets/Login/imagen_gajuar.png" class="jaguar" alt="Jaguar">

        </div>

    </div>
   <script src="../../JavaScript/Login/login.js"></script>
</body>
</html>
