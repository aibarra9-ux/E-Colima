<?php
session_start();
$error_registro = $_SESSION['error_registro'] ?? null;
unset($_SESSION['error_registro']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - ECOLIMA</title>
    <link rel="stylesheet" href="../../CSS/Login/estilos.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="contenedor">

    <div class="login-section">

        <a href="../Home/home.php" class="back-home">← Volver</a>

        <div class="card">
            <h3>Crea tu cuenta</h3>

            <?php if($error_registro): ?>
                <div class="login-error mostrar-error" id="errorPHP">
                    <span>⚠</span>
                    <span>
                        <?php
                            if($error_registro == "duplicado"){
                                echo "El usuario o correo ya existe";
                            } elseif($error_registro == "password"){
                                echo "Las contraseñas no coinciden";
                            } elseif($error_registro == "codigo_incorrecto"){
                                echo "El código de verificación es inválido";
                            } else {
                                echo "Error al registrar, intenta de nuevo";
                            }
                        ?>
                    </span>
                </div>
            <?php endif; ?>

            <div class="login-error" id="errorJS">
                <span>⚠</span>
                <span>Las contraseñas no coinciden</span>
            </div>

<form action="procesar_registro.php" method="POST" id="registroForm" onkeydown="return event.key != 'Enter';">
                <div class="input-group">
                    <input type="text" name="username"
                        minlength="4" maxlength="20"
                        pattern="[A-Za-z0-9_.]+"
                        title="Solo letras, números, _ o ."
                        placeholder="Nombre de usuario" required>
                </div>

                <div class="input-group">
                    <input type="email" name="correo" id="correoRegistro"
                        placeholder="Correo electrónico" required>
                </div>

                <div id="strengthMessage"></div>

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

                <div id="seccionCodigo" style="display: none; margin-bottom: 20px; border-top: 1px solid rgba(255,255,255,0.1); pt-3">
                    <p style="color: #a8d5b5; font-size: 0.85rem; margin: 15px 0 10px 0; font-family: 'Segoe UI', sans-serif;">
                        Te hemos enviado un código a tu correo:
                    </p>
                    <div class="input-group">
                        <input type="text" id="codigoVerificacion" name="codigo_ingresado" 
                               placeholder="Código de 6 dígitos" maxlength="6" 
                               style="text-align: center; letter-spacing: 3px; font-weight: bold;">
                    </div>
                </div>

                <button type="button" id="btnAccion" class="btn-login">Enviar código de verificación</button>

                <button type="submit" id="btnSubmitReal" style="display:none;"></button>

                <p class="register">
                    ¿Ya tienes una cuenta?
                    <a href="login.php">Logeate</a>
                </p>

            </form>
        </div>
    </div>

    <div class="image-section">
        <img src="../../assets/Login/Imagen Onza.png" class="jaguar" alt="Jaguar">
    </div>

</div>

<script src="../../JavaScript/Login/login.js"></script>

</body>
</html>
