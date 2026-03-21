<?php
session_start();
$error_registro = $_SESSION['error_registro'] ?? null;
unset($_SESSION['error_registro']); // Se borra para que no reaparezca al recargar
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="../../CSS/Login/estilos.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="contenedor">

    <!-- Lado izquierdo -->
    <div class="login-section">

        <a href="../Home/home.php" class="back-home">← Volver</a>

        <div class="card">
            <h3>Crea tu cuenta</h3>

            <!-- ERROR PHP (desde sesión) -->
            <?php if($error_registro): ?>
                <div class="login-error mostrar-error" id="errorPHP">
                    <span>⚠</span>
                    <span>
                        <?php
                            if($error_registro == "duplicado"){
                                echo "El usuario o correo ya existe";
                            }
                            if($error_registro == "password"){
                                echo "Las contraseñas no coinciden";
                            }
                            if($error_registro == "general"){
                                echo "Error al registrar, intenta de nuevo";
                            }
                        ?>
                    </span>
                </div>
            <?php endif; ?>

            <!-- ERROR JS (contraseñas en tiempo real) -->
            <div class="login-error" id="errorJS">
                <span>⚠</span>
                <span>Las contraseñas no coinciden</span>
            </div>

            <form action="procesar_registro.php" method="POST" id="registroForm">

                <div class="input-group">
                    <input type="text" name="nombre" placeholder="Nombre completo" required>
                </div>

                <div class="input-group">
                    <input type="email" name="correo" placeholder="Correo electrónico" required>
                </div>

                <div class="input-group password">
                    <input type="password" name="password" id="password" placeholder="Crear contraseña" required>
                    <span class="eye">
                        <img src="../../assets/Login/eye-off-fill.png" class="togglePassword">
                    </span>
                </div>

                <div class="input-group password">
                    <input type="password" name="confirmar" id="confirmar" placeholder="Confirmar contraseña" required>
                    <span class="eye">
                        <img src="../../assets/Login/eye-off-fill.png" class="togglePassword">
                    </span>
                </div>

                <button type="submit" class="btn-login">Crear cuenta</button>

                <p class="register">
                    ¿Ya tienes una cuenta?
                    <a href="login.php">Logeate</a>
                </p>

            </form>
        </div>

    </div>

    <!-- Lado derecho -->
    <div class="image-section">
        <img src="../../assets/Login/imagen_gajuar.png" class="jaguar">
    </div>

</div>

<script src="../../JavaScript/Login/login.js"></script>

</body>
</html>