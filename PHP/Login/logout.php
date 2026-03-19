<?php

session_start();

// borrar todas las variables de sesión
session_unset();

// destruir la sesión
session_destroy();

// regresar al home
header("Location: ../Home/home.php");
exit();

?>