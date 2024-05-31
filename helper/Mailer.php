<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {

    public static function send($email, $fullname, $verificationurl) {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        try {
            // Configura los parámetros del servidor de correo
            $mail->SMTPDebug = 0; // Habilita la salida de depuración detallada
            $mail->Host = 'smtp.gmail.com'; // Especifica el servidor SMTP principal
            $mail->SMTPAuth = true; // Habilita la autenticación SMTP
            $mail->Username = 'panchovilla2003x@gmail.com'; // Nombre de usuario SMTP
            $mail->Password = 'ykpk vnrl mtpl kdxi'; // Contraseña SMTP
            $mail->SMTPSecure = 'tls'; // Habilita el cifrado TLS; `PHPMailer::ENCRYPTION_SMTPS` también aceptado
            $mail->Port = 587; // Puerto TCP para conectarse, usa 465 para `PHPMailer::ENCRYPTION_SMTPS` arriba

            $mail->setFrom('panchovilla2003x@gmail.com', 'Mailer');
            $mail->addAddress($email, $fullname);

            $mail->isHTML(true);
            $mail->Subject = 'Verifica tu cuenta';
            $mail->Body    = "Hola,<br><br>Por favor, haz clic en el siguiente enlace para verificar tu cuenta:<br><a href='$verificationurl'>$verificationurl</a><br><br>Gracias!";
            $mail->AltBody = "Hola,\n\nPor favor, haz clic en el siguiente enlace para verificar tu cuenta:\n$verificationurl\n\nGracias!";

            $mail->send();
        } catch (Exception $e) {
            die("El mensaje no se pudo enviar. Error: {$mail->ErrorInfo}");
        }
    }

}

