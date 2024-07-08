<?php

    class LoginController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            $this->verifyUser();
            $this->presenter->render("view/loginView.mustache");
        }

        public function get() {
            $this->verifyUser();
            $errors = [];
            $username = $_POST["username"];
            $pass = $_POST["pass"];
            $user = $this->model->loginUser($username, $pass, $errors);
            if(!empty($errors)) {$this->presenter->render("view/loginView.mustache", ["errors"=>$errors]);}
            $_SESSION["usuario"] = $user;
            Redirect::to("/lobby/read");       
        }

        public function active() {
            $this->verifyUserSession();
            $idEmpresa = $_POST["entorno"];
            $idUsuario = $_SESSION["Usuario"]["id"];
            $startTime = date("Y-m-d H:i:s");
            $endTime = date("Y-m-d H:i:s", strtotime("+1 hour"));
            $entorno = $this->model->getEntorno($idEmpresa, $idUsuario, $startTime);
            if ($entorno == false) {$entorno = $this->model->createEntorno($idEmpresa, $idUsuario, $startTime, $endTime);}
            $_SESSION["entorno"] = $entorno;
        }

        private function verifyUser() {
            if (isset($_SESSION["usuario"])) {Redirect::to("/lobby/read");}
        }

        private function verifyUserSession() {
            if (!isset($_SESSION["usuario"])) {Redirect::to("/login/read");}  
        }

    }
