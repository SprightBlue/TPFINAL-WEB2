<?php

    class RegistroModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function createUser($fullname, $yearOfBirth, $gender, $country, $city, $email, $pass, $username, $img_name, $tmp_name) {
            $this->addImg($img_name, $tmp_name);
            $token = bin2hex(random_bytes(16));  
            $this->database->createUser($fullname, $yearOfBirth, $gender, $country, $city, $email, $pass, $username, $img_name, $token, 0);
            $verificationUrl = "http://localhost/registro/verify&token=$token";
            Mailer::send($email, $fullname, $verificationUrl);
        }

        private function addImg($img_name, $tmp_name) {
            if (!file_exists("public/img/" . $img_name)) {move_uploaded_file($tmp_name, "public/img/" . $img_name);}
        }

    }

?>