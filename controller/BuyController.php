<?php

    class BuyController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            $this->verifyUserSession();
            $this->verifyEntorno();
            $user = $_SESSION["usuario"];
            $this->presenter->render("view/buyView.mustache", ["user" => $user]);
        }

        public function update() {
            $this->verifyUserSession();
            $this->verifyEntorno();
            $idUsuario = $_POST["idUsuario"];
            $cantidad = $_POST["cantidad"];
            $precioTotal = $_POST["precioTotal"];
            $this->model->buyTrampitas($idUsuario, $cantidad, $precioTotal);
            $this->model->updateCantidadTrampitasUsuario($idUsuario, $cantidad);
            $_SESSION["usuario"] = $this->model->getUser($idUsuario);
            Redirect::to("/lobby/read");
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
