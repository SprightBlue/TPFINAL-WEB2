<?php

    class RegistroModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function createUser($fullname, $yearOfBirth, $gender, $country, $city, $email, $pass, $username, $img_name, $tmp_name) {
            $errors = [];
            if ($this->database->emailExists($email)) {
                $errors[] = "El correo electrónico ya está en uso.";
            }
            if ($this->database->usernameExists($username)) {
                $errors[] = "El nombre de usuario ya está en uso.";
            }
            if (!empty($errors)) {
                $_SESSION["errorRegistro"] = $errors;
                throw new Exception(implode(" ", $errors));
            }
            $this->addImg($img_name, $tmp_name);
            $token = bin2hex(random_bytes(16));  
            $this->database->createUser($fullname, $yearOfBirth, $gender, $country, $city, $email, $pass, $username, $img_name, $token, 0);
            //Tengo el puerto en 8089
            $verificationUrl = "http://localhost:8089/registro/verify&token=$token";
            Mailer::send($email, $fullname, $verificationUrl);
        }
        private function addImg($img_name, $tmp_name) {
            if (!file_exists("public/img/" . $img_name)) {move_uploaded_file($tmp_name, "public/img/" . $img_name);}
        }
        public function verifyUser($token) {
            $this->database->activeUser($token);
        }
    }

