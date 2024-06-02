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
            $errorImg = $this->addImg($img);
            if($errorImg) {
                $errors[] = $errorImg;
            }
            if(!empty($errors)) {
                $_SESSION["errorRegistro"] = $errors;
                throw new Exception(implode(" ", $errors));
            }
            $token = bin2hex(random_bytes(16));  
            $this->database->createUser($fullname, $yearOfBirth, $gender, $country, $city, $email, $pass, $username, $img["name"], $token);
            $verificationUrl = "http://localhost/registro/verify&token=$token";
            Mailer::send($email, $fullname, $verificationUrl);
        }

        public function verifyUser($token) {
            $this->database->activeUser($token);
        }

        private function addImg($img) {
            if($img) {
                return "No se ha subido ningún archivo.";
            }
            if($img['error'] !== UPLOAD_ERR_OK) {
                return "Error al subir el archivo.";
            }
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($img['tmp_name']);
            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
            if(!in_array($mime, $allowedMimes)) {
                return "El archivo no es una imagen válida.";
            }
            $ext = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
            if(!in_array($ext, $allowedExts)) {
                return "Extensión de archivo no permitida.";
            }
            $destination = 'public/img/' . uniqid('', true) . '.' . $ext;
            if(!move_uploaded_file($img['tmp_name'], $destination)) {
                return "Error al mover el archivo.";
            }
        }
    
    }

?>