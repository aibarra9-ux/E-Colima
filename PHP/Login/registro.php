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

        <div class="login-section">

             <a href="../Home/home.php" class="back-home" data-section="login" data-value="volver">← Volver</a>

            <div class="card">
                <h3 data-section="registro" data-value="titulo">Crea tu cuenta</h3>

                <form action="procesar_registro.php" method="POST" id="registroForm">

                    <div class="input-group">
                        <input type="text" name="nombre" placeholder="Nombre completo" data-section="registro" data-value="placeholder_nombre" required>
                    </div>

                    <div class="input-group">
                        <input type="email" name="correo" placeholder="Correo electrónico" data-section="registro" data-value="placeholder_correo" required>
                    </div>

                    <div class="input-group password">
                        <input type="password" name="password" id="password" placeholder="Crear contraseña" data-section="registro" data-value="placeholder_crear" required>
                        <span class="eye">
                            <img src="../../assets/Login/eye-off-fill.png" class="togglePassword" alt="mostrar contraseña">
                        </span>
                    </div>

                    <div class="input-group password">
                        <input type="password" name="confirmar" id="confirmar" placeholder="Confirmar contraseña" data-section="registro" data-value="placeholder_confirmar" required>
                        <span class="eye">
                            <img src="../../assets/Login/eye-off-fill.png" class="togglePassword" alt="mostrar contraseña">
                        </span>
                    </div>

                    <button type="submit" class="btn-login" data-section="registro" data-value="btn_crear">Crear cuenta</button>

                    <p id="errorPassword"></p>

                    <p class="register">
                        <span data-section="registro" data-value="ya_cuenta">¿Ya tienes una cuenta?</span>
                        <a href="login.php" data-section="registro" data-value="logearse">Logeate</a>
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