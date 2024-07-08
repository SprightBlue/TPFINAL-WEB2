<?php

    class ProfileController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            $this->verifyUserSession();
            $this->verifyEntorno();
            $username = $_SESSION["usuario"]["username"];
            $user = $this->model->getUser($username);
            if ($user) {
                $isOwnProfile = ($_SESSION["usuario"]["username"] == $user["username"]);
                $this->presenter->render("view/profileView.mustache", ["user"=>$user, "isOwnProfile"=>$isOwnProfile]);
            }
        }

        public function get() {
            $this->verifyUserSession();
            if (!isset($_GET["username"])) {Redirect::to("/login/read");}
            $this->verifyEntorno();
            $username = $_GET["username"];
            $data = $this->getData($username);
            if ($data["user"]["userRole"] != "player") {Redirect::to("/login/read");}
            $this->presenter->render("view/profileView.mustache", $data); 
        }

        private function getData($username) {
            $user = $this->model->getUser($username);
            $isOwnProfile = ($_SESSION["usuario"]["username"] == $user["username"]);
            $data = ["user" => $user, "qr" => "/public/qr/qr-" . $username . ".png", "isOwnProfile" => $isOwnProfile];
            return $data;
        }

        private function verifyUserSession() {
            if (!isset($_SESSION["usuario"])) {Redirect::to("/login/read");}  
        }

        private function verifyEntorno() {
            if (isset($_SESSION["entorno"])) {
                $idEmpresa = $_SESSION["entorno"]["idEmpresa"];
                $idUsuario = $_SESSION["usuario"]["id"];
                $currentTime = date("Y-m-d H:i:s");
                $result = $this->model->getEntorno($idEmpresa, $idUsuario, $currentTime);
                if (!$result) {$_SESSION["entorno"] = null;}
            } 
        }

    }
