<?php
session_start();

// 1. Limpiar todas las variables de sesión
session_unset();

// 2. Destruir la sesión en el servidor
session_destroy();

// 3. Forzar al navegador a que no guarde esta acción en el historial/caché
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// 4. Redirigir directamente al Login (es más seguro que ir al Home)
header("Location: ../Home/home.php");
exit();
?>