<?php
session_start();
$error_registro = $_SESSION['error_registro'] ?? null;
unset($_SESSION['error_registro']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title data-translate="true">Registro - ECOLIMA</title>
    <link rel="stylesheet" href="../../CSS/Login/estilos_registro.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* Solo estilos adicionales que no están en el CSS principal */
        .reenviar-codigo {
            text-align: center;
            margin-top: 10px;
            font-size: 0.85rem;
        }
        .reenviar-codigo a {
            color: #a8d5b5;
            text-decoration: none;
            cursor: pointer;
            transition: color 0.3s;
        }
        .reenviar-codigo a:hover {
            color: #4a7c5c;
            text-decoration: underline;
        }
        .reenviar-mensaje {
            font-size: 0.8rem;
            color: #a8d5b5;
            margin-top: 5px;
            text-align: center;
        }
        .reenviar-mensaje i {
            margin-right: 5px;
        }
        .timer {
            color: #f39c12;
            font-weight: bold;
        }

        /* 🔥 CORRECCIÓN: Eliminamos overflow:hidden que corta la imagen */
        .image-section {
            overflow: visible !important;
        }
        
        .jaguar {
            max-height: 100vh !important;
            width: auto !important;
            right: 0 !important;
        }
        
        .contenedor {
            overflow: visible !important;
        }
    </style>
</head>
<body>

<div class="contenedor">

    <div class="login-section">

        <a href="../Home/home.php" class="back-home" data-translate="true">← Volver</a>

        <div class="card">
            <h3 data-translate="true">Crea tu cuenta</h3>

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
                <span data-translate="true">Las contraseñas no coinciden</span>
            </div>

<form action="procesar_registro.php" method="POST" id="registroForm" onkeydown="return event.key != 'Enter';">
                <div class="input-group">
                    <input type="text" name="username"
                        minlength="4" maxlength="20"
                        pattern="[A-Za-z0-9_.]+"
                        title="Solo letras, números, _ o ."
                        placeholder="Nombre de usuario"
                        data-translate-placeholder="true"
                        required>
                </div>

                <div class="input-group">
                    <input type="email" name="correo" id="correoRegistro"
                        placeholder="Correo electrónico"
                        data-translate-placeholder="true"
                        required>
                </div>

                <div id="strengthMessage"></div>

                <div class="input-group password">
                    <input type="password" name="password" id="password"
                        minlength="8" maxlength="32"
                        placeholder="Crear contraseña"
                        data-translate-placeholder="true"
                        required>
                    <span class="eye">
                        <img src="../../assets/Login/eye-off-fill.png"
                             class="togglePassword"
                             alt="mostrar contraseña">
                    </span>
                </div>

                <div class="input-group password">
                    <input type="password" name="confirmar" id="confirmar"
                        minlength="8" maxlength="32"
                        placeholder="Confirmar contraseña"
                        data-translate-placeholder="true"
                        required>
                    <span class="eye">
                        <img src="../../assets/Login/eye-off-fill.png"
                             class="togglePassword"
                             alt="mostrar contraseña">
                    </span>
                </div>

                <div id="seccionCodigo" style="display: none; margin-bottom: 20px; border-top: 1px solid rgba(255,255,255,0.1); pt-3">
                    <p style="color: #a8d5b5; font-size: 0.85rem; margin: 15px 0 10px 0; font-family: 'Segoe UI', sans-serif;" data-translate="true">
                        Te hemos enviado un código a tu correo:
                    </p>
                    <div class="input-group">
                        <input type="text" id="codigoVerificacion" name="codigo_ingresado" 
                               placeholder="Código de 6 dígitos" 
                               data-translate-placeholder="true"
                               maxlength="6" 
                               style="text-align: center; letter-spacing: 3px; font-weight: bold;">
                    </div>
                    
                    <div class="reenviar-codigo">
                        <a id="btnReenviarCodigo"><span data-translate="true">📧 ¿No recibiste el código? Reenviar</span></a>
                    </div>
                    <div id="mensajeReenvio" class="reenviar-mensaje" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> <span data-translate="true">Reenviando...</span>
                    </div>
                    <div id="timerReenvio" class="reenviar-mensaje" style="display: none;">
                        <i class="fas fa-clock"></i> <span data-translate="true">Puedes reenviar en</span> <span id="segundosRestantes" class="timer">60</span> <span data-translate="true">segundos</span>
                    </div>
                </div>

                <button type="button" id="btnAccion" class="btn-login" data-translate="true">Enviar código de verificación</button>

                <button type="submit" id="btnSubmitReal" style="display:none;"></button>

                <p class="register">
                    <span data-translate="true">¿Ya tienes una cuenta?</span>
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
<script src="../../JavaScript/Traduccion/traduccion.js"></script>

<script>
document.getElementById('btnReenviarCodigo')?.addEventListener('click', function(e) {
    e.preventDefault();
    
    const correo = document.getElementById('correoRegistro').value;
    const btnReenviar = document.getElementById('btnReenviarCodigo');
    const mensajeReenvio = document.getElementById('mensajeReenvio');
    const timerReenvio = document.getElementById('timerReenvio');
    
    if (!correo) {
        alert('Por favor, ingresa tu correo electrónico primero.');
        return;
    }
    
    btnReenviar.style.pointerEvents = 'none';
    btnReenviar.style.opacity = '0.5';
    mensajeReenvio.style.display = 'block';
    
    fetch('verificar.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'correo=' + encodeURIComponent(correo)
    })
    .then(response => response.json())
    .then(data => {
        mensajeReenvio.style.display = 'none';
        
        if (data.status === 'success') {
            timerReenvio.style.display = 'block';
            let segundos = 60;
            const segundosSpan = document.getElementById('segundosRestantes');
            
            const intervalo = setInterval(() => {
                segundos--;
                segundosSpan.textContent = segundos;
                
                if (segundos <= 0) {
                    clearInterval(intervalo);
                    timerReenvio.style.display = 'none';
                    btnReenviar.style.pointerEvents = 'auto';
                    btnReenviar.style.opacity = '1';
                }
            }, 1000);
            
            const exitoMsg = document.createElement('div');
            exitoMsg.className = 'reenviar-mensaje';
            exitoMsg.style.color = '#4caf50';
            exitoMsg.innerHTML = '<i class="fas fa-check-circle"></i> <span data-translate="true">¡Código reenviado! Revisa tu correo.</span>';
            exitoMsg.style.marginTop = '5px';
            document.getElementById('seccionCodigo').appendChild(exitoMsg);
            
            setTimeout(() => {
                exitoMsg.remove();
            }, 5000);
        } else {
            alert('Error al reenviar: ' + data.message);
            btnReenviar.style.pointerEvents = 'auto';
            btnReenviar.style.opacity = '1';
        }
    })
    .catch(error => {
        mensajeReenvio.style.display = 'none';
        alert('Error de conexión. Intenta nuevamente.');
        btnReenviar.style.pointerEvents = 'auto';
        btnReenviar.style.opacity = '1';
    });
});
</script>

</body>
</html>