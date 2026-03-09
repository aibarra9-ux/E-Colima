<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proyecto Integrador - Login</title>
    <link rel="stylesheet" href="../../CSS/Login/estilos.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

    <div class="contenedor">

        <!-- Lado izquierdo -->
        <div class="login-section">

            <div class="logo">
                <h2>Proyecto<br>Integrador</h2>
                <div class="circulo"></div>
            </div>

            <div class="card">
                <h3>Crea tu cuenta</h3>

                <form action="procesar_registro.php" method="POST">

                    <div class="input-group">
                        <input type="text" name="nombre" placeholder="Nombre completo" required>
                    </div>

                    <div class="input-group">
                        <input type="email" name="correo" placeholder="Correo electrónico" required>
                    </div>

                    <div class="input-group password">
                        <input type="password" name="password" placeholder="Crear contraseña" required>
                    </div>

                    <div class="input-group password">
                        <input type="password" name="confirmar" placeholder="Confirmar contraseña" required>
                    </div>

                    <button type="submit" class="btn-login">Crear cuenta</button>

                    <p class="register">
                        ¿Ya tienes una cuenta?
                        <a href="login.html">Logeate</a>
                    </p>

                </form>
            </div>

        </div>

        <!-- Lado derecho -->
        <div class="image-section">
            <img src="../../assets/Login/imagen_gajuar.png" class="jaguar" alt="Jaguar">

        </div>

    </div>
   
</body>
</html>
