<?php

    class RegistroModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function createUser($fullname, $yearOfBirth, $gender, $country, $city, $email, $pass, $repeatPass, $username, $img) {
            $errors = [];
            if($this->database->emailExists($email)) {
                $errors[] = "El correo electrónico ya está en uso.";
            }
            if($pass != $repeatPass) {
                $errors[] = "Las contraseñas ingresadas no coinciden.";
            }
            if($this->database->usernameExists($username)) {
                $errors[] = "El nombre de usuario ya está en uso.";
            }
            $destination = null;
            if(empty($img['name'])) {
                $errors[] = "No se ha subido ningún archivo.";
            } else {
                $destination = $this->addImg($img, $errors);
            }
            if(!empty($errors)) {
                $_SESSION["errorRegistro"] = $errors;
                throw new Exception(implode(" ", $errors));
            } 
            $token = bin2hex(random_bytes(16));           
            $profileUrl = "http:/localhost/profile/get?username=$username";
            PHPQRCode::generate($profileUrl, $username);           
            $this->database->createUser($fullname, $yearOfBirth, $gender, $country, $city, $email, $pass, $username, $destination, $token, 0); // Añade un 0 al final para inicializar el puntaje en 0 
            $verificationUrl = "http://localhost/registro/verify&token=$token";
            Mailer::send($email, $fullname, $verificationUrl);
        }

        public function verifyUser($token) {
            $this->database->activeUser($token);
        }

        private function addImg($img, &$errors) {
            if($img['error'] !== UPLOAD_ERR_OK) {
                $errors[] = "Error al subir el archivo.";
                return null;
            }
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($img['tmp_name']);
            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
            if(!in_array($mime, $allowedMimes)) {
                $errors[] = "El archivo no es una imagen válida.";
                return null;
            }
            $ext = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
            if(!in_array($ext, $allowedExts)) {
                $errors[] = "Extensión de archivo no permitida.";
                return null;
            }
            $destination = 'public/img/' . bin2hex(random_bytes(16)) . '.' . $ext;
            if(!move_uploaded_file($img['tmp_name'], $destination)) {
                $errors[] = "Error al mover el archivo.";
                return null;
            }
            return $destination;
        }
    
    }

?>