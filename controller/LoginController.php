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
            try {
                $user = $this->model->loginUser($_POST["username"], $_POST["pass"]);
                $_SESSION["usuario"] = $user;
                Redirect::to("/lobby/read");                
            } catch(Exception $e) {
                $_SESSION["error"] = $e->getMessage();
                Redirect::to("/login/read");
            }
        }

    }

