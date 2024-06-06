<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {

    public static function send($email, $fullname, $verificationurl) {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        try {
            $mail->SMTPDebug = 0;
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'panchovilla2003x@gmail.com';
            $mail->Password = 'ykpk vnrl mtpl kdxi';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('panchovilla2003x@gmail.com', 'Q&A');
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
