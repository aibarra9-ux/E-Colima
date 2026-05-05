<?php 
require_once "verificar_sesion.php"; // Esto bloquea a los no-admins
?>
<?php
session_start();

// Si no hay sesión o el rol no es 1 (Administrador)
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1) {
    // Redirigir al login o a una página de "Acceso Denegado"
    header("Location: ../../PHP/Login/login.php?error=acceso_denegado");
    exit();
}
?>