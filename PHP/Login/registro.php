<?php
session_start();
$error_registro = $_SESSION['error_registro'] ?? null;
unset($_SESSION['error_registro']);
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

    <!-- IZQUIERDA -->
    <div class="login-section">

        <a href="../Home/home.php" class="back-home">← Volver</a>

        <div class="card">
            <h3>Crea tu cuenta</h3>

            <!-- ERROR PHP -->
            <?php if($error_registro): ?>
                <div class="login-error mostrar-error" id="errorPHP">
                    <span>⚠</span>
                    <span>
                        <?php
                            if($error_registro == "duplicado"){
                                echo "El usuario o correo ya existe";
                            } elseif($error_registro == "password"){
                                echo "Las contraseñas no coinciden";
                            } else {
                                echo "Error al registrar, intenta de nuevo";
                            }
                        ?>
                    </span>
                </div>
            <?php endif; ?>

            <!-- ERROR JS -->
            <div class="login-error" id="errorJS">
                <span>⚠</span>
                <span>Las contraseñas no coinciden</span>
            </div>

            <!-- FORMULARIO -->
            <form action="procesar_registro.php" method="POST" id="registroForm">

                <div class="input-group">
                    <input type="text" name="username"
                        minlength="4" maxlength="20"
                        pattern="[A-Za-z0-9_.]+"
                        title="Solo letras, números, _ o ."
                        placeholder="Nombre de usuario" required>
                </div>

                <div class="input-group">
                    <input type="email" name="correo"
                        placeholder="Correo electrónico" required>
                </div>

                <!-- FUERZA CONTRASEÑA -->
                <div id="strengthMessage"></div>

                <!-- PASSWORD -->
                <div class="input-group password">
                    <input type="password" name="password" id="password"
                        minlength="8" maxlength="32"
                        placeholder="Crear contraseña" required>
                    <span class="eye">
                        <img src="../../assets/Login/eye-off-fill.png"
                             class="togglePassword"
                             alt="mostrar contraseña">
                    </span>
                </div>

                <!-- CONFIRMAR -->
                <div class="input-group password">
                    <input type="password" name="confirmar" id="confirmar"
                        minlength="8" maxlength="32"
                        placeholder="Confirmar contraseña" required>
                    <span class="eye">
                        <img src="../../assets/Login/eye-off-fill.png"
                             class="togglePassword"
                             alt="mostrar contraseña">
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

    <!-- DERECHA -->
    <div class="image-section">
        <img src="../../assets/Login/Imagen Onza.png" class="jaguar" alt="Jaguar">
    </div>

</div>

<script src="../../JavaScript/Login/login.js"></script>

</body>
</html>