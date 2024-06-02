<?php

    class LoginController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            $error = isset($_SESSION["errorLogin"]) && is_array($_SESSION["errorLogin"]) ? implode("<br>", $_SESSION["errorLogin"]) : "";
            $_SESSION["errorLogin"] = "";
            $this->presenter->render("view/loginView.mustache", ["error"=>$error]);
        }
        
        public function get() {
            $user = $this->model->loginUser($_POST["username"], $_POST["pass"]);
            if($user == false) {
                $_SESSION["errorLogin"] = ["El usuario y/o contraseña incorrectos."];
                Redirect::to("/login/read");
            } else if ($user["active"] == 0) {
                $_SESSION["errorLogin"] = ["Debes verificar tu correo electrónico antes de poder iniciar sesión."];
                Redirect::to("/login/read");
            } else {
                $_SESSION["errorLogin"] = [];
            }
            $_SESSION["usuario"] = $user;
            Redirect::to("/home/read");
        }
    }

