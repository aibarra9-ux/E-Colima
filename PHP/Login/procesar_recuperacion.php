<?php
// Evitamos salidas accidentales de texto que rompan el JSON
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// 🔍 RUTAS CORREGIDAS AL 100% SEGÚN TU ESTRUCTURA DE CARPETAS
// Como estás en PHP/Login/, subes un nivel (../) para ir a PHP/ y luego buscas Perfil o PHPMailer
$ruta_conexion = "../Perfil/conexion.php"; 
$base_phpmailer = "../PHPMailer";

if (!file_exists($ruta_conexion)) {
    ob_clean();
    echo json_encode(["status" => "error", "message" => "No se encontró conexion.php en la ruta: " . $ruta_conexion]);
    exit;
}
require_once $ruta_conexion;

if (!file_exists("$base_phpmailer/Exception.php")) {
    ob_clean();
    echo json_encode(["status" => "error", "message" => "No se encontró PHPMailer en la ruta: " . $base_phpmailer]);
    exit;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "$base_phpmailer/Exception.php";
require "$base_phpmailer/PHPMailer.php";
require "$base_phpmailer/SMTP.php";

// Requerimos las librerías oficiales de PHPMailer validando su existencia física
if (!file_exists("$base_phpmailer/Exception.php") || !file_exists("$base_phpmailer/PHPMailer.php") || !file_exists("$base_phpmailer/SMTP.php")) {
    echo json_encode(["status" => "error", "message" => "Error crítico de sistema: No se encontraron los archivos de PHPMailer."]);
    exit;
}

$accion = $_POST['accion'] ?? '';
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);

if (!$email) {
    echo json_encode(["status" => "error", "message" => "Correo electrónico no válido."]);
    exit;
}

// Accion 1: Solicitar y Despachar el Código por Correo
if ($accion === 'solicitar_codigo') {
    // Verificar si el correo existe en tu sistema
    $stmt = $conn->prepare("SELECT id, username FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "El correo electrónico no está registrado en el sistema."]);
        exit;
    }
    
    $user = $res->fetch_assoc();
    
    // Generar Token numérico aleatorio
    $token = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    
    // Guardamos de forma temporal el estado en las variables de sesión del servidor
    $_SESSION['recuperacion_email'] = $email;
    $_SESSION['recuperacion_token'] = $token;
    $_SESSION['recuperacion_vence'] = time() + 600; // Vence en 10 minutos
    
    // Configuración y envío de PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = '';               // Correo institucional
        $mail->Password   = '';              // Contraseña de aplicación
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom('aibarra9@ucol.mx', 'ECOLIMA');
        $mail->addAddress($email, $user['username']);

        $mail->isHTML(true);
        $mail->Subject = '🔑 Recuperación de tu contraseña - ECOLIMA';
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; padding: 20px; max-width: 600px; border: 1px solid #e2e8f0;'>
                <h2 style='color: #2d6a4f;'>Control de Accesos ECOLIMA</h2>
                <p>Hola <strong>" . htmlspecialchars($user['username']) . "</strong>,</p>
                <p>Has solicitado restablecer tus credenciales de acceso. Utiliza el siguiente PIN de verificación para proceder:</p>
                <div style='background-color: #f0fdf4; padding: 15px; text-align: center; font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #1a3a2a; border-radius: 8px; margin: 20px 0;'>
                    $token
                </div>
                <p style='font-size: 12px; color: #94a3b8;'>Este código de un solo uso expirará en 10 minutos por razones de seguridad.</p>
            </div>";

        // Limpiamos cualquier buffer residual antes de imprimir el JSON final de éxito
        ob_clean();
        $mail->send();
        echo json_encode(["status" => "success", "message" => "Código enviado con éxito."]);
    } catch (Exception $e) {
        ob_clean();
        echo json_encode(["status" => "error", "message" => "No se pudo enviar el correo. Mailer Error: {$mail->ErrorInfo}"]);
    }
    exit;
}

// Accion 2: Validar el Token provisto por el usuario
if ($accion === 'validar_codigo') {
    $token_ingresado = $_POST['token'] ?? '';
    
    ob_clean();
    if (
        isset($_SESSION['recuperacion_email']) && $_SESSION['recuperacion_email'] === $email &&
        isset($_SESSION['recuperacion_token']) && $_SESSION['recuperacion_token'] === $token_ingresado &&
        time() <= $_SESSION['recuperacion_vence']
    ) {
        echo json_encode(["status" => "success", "message" => "Código verificado con éxito."]);
    } else {
        echo json_encode(["status" => "error", "message" => "El código es incorrecto, ya fue usado o ha expirado."]);
    }
    exit;
}

// Accion 3: Procesar cambio físico en la base de datos
if ($accion === 'cambiar_password') {
    $token_ingresado = $_POST['token'] ?? '';
    $nueva_pass = $_POST['password'] ?? '';
    
    if (
        !isset($_SESSION['recuperacion_email']) || $_SESSION['recuperacion_email'] !== $email ||
        !isset($_SESSION['recuperacion_token']) || $_SESSION['recuperacion_token'] !== $token_ingresado ||
        time() > $_SESSION['recuperacion_vence']
    ) {
        ob_clean();
        echo json_encode(["status" => "error", "message" => "Sesión de seguridad inválida o expirada."]);
        exit;
    }
    
    if (strlen($nueva_pass) < 8 || strlen($nueva_pass) > 32) {
        ob_clean();
        echo json_encode(["status" => "error", "message" => "La contraseña no cumple con los estándares exigidos."]);
        exit;
    }
    
    $pass_hash = password_hash($nueva_pass, PASSWORD_BCRYPT);
    
    $stmt = $conn->prepare("UPDATE usuarios SET password_hash = ? WHERE email = ?");
    $stmt->bind_param("ss", $pass_hash, $email);
    
    ob_clean();
    if ($stmt->execute()) {
        unset($_SESSION['recuperacion_email']);
        unset($_SESSION['recuperacion_token']);
        unset($_SESSION['recuperacion_vence']);
        
        echo json_encode(["status" => "success", "message" => "Tu contraseña ha sido restablecida de manera exitosa. Ya puedes iniciar sesión con tus nuevas credenciales."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Hubo un problema interno al procesar el cambio en la base de datos."]);
    }
    exit;
}

ob_clean();
echo json_encode(["status" => "error", "message" => "Acción inválida o denegada."]);
exit;