<?php

    class LoginController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            $error = isset($_SESSION["error"]) ? $_SESSION["error"] : "";
            $this->presenter->render("view/loginView.mustache", ["error"=>$error]);
        }

        public function get() {
            $user = $this->model->loginUser($_POST["username"], $_POST["pass"]);
            if($user == false) {
                $_SESSION["error"] = "El usuario y/o contraña incorrectos.";
                Redirect::to("/login/read");
            }else {
                $_SESSION["error"] = "";
            }
            $_SESSION["usuario"] = $user;
            Redirect::to("/home/read");
        }

    }

?>