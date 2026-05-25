<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';

session_start();
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correoDestino = $_POST['correo'] ?? '';

    if (empty($correoDestino)) {
        echo json_encode(["status" => "error", "message" => "Correo vacío"]);
        exit();
    }

    $codigo = rand(100000, 999999);
    $_SESSION['codigo_verificacion'] = $codigo;

    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = ''; // 👈 Tu correo de Gmail
        $mail->Password   = ''; // 👈 La clave que generaste
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Destinatarios
        $mail->setFrom('aibarra9@ucol.mx', 'E-COLIMA Registro');
        $mail->addAddress($correoDestino);

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = 'Codigo de verificacion - E-COLIMA';
        $mail->Body    = "
            <div style='font-family: Arial; border: 1px solid #4a7c5c; padding: 20px; border-radius: 10px;'>
                <h2 style='color: #4a7c5c;'>¡Hola!</h2>
                <p>Tu código de verificación para registrarte en E-COLIMA es:</p>
                <h1 style='letter-spacing: 5px; color: #f39c12;'>$codigo</h1>
                <p>Este código expirará pronto. Si no solicitaste este registro, ignora este correo.</p>
            </div>
        ";

        $mail->send();
        echo json_encode(["status" => "success", "message" => "Enviado con éxito"]);

    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "No se pudo enviar: {$mail->ErrorInfo}"]);
    }
    exit();
}
