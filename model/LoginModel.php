<?php

    class LoginModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function loginUser($username, $pass) {
            $errors = [];
            $user = $this->database->getUser($username, $pass);
            if($user == false) {
                $errors[] = "El usuario y/o contraseña incorrectos.";
            } else if ($user["active"] == 0) {
                $errors[] = "Debes verificar tu correo electrónico antes de poder iniciar sesión.";
            }
            if(!empty($errors)) {
                $_SESSION["errorLogin"] = $errors;
                throw new Exception(implode(" ", $errors));
            }
            return $user;
        }

    }

