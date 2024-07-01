<?php

    class BuyController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            if(isset($_SESSION["usuario"])) {
                $user = $_SESSION["usuario"];
                $this->presenter->render("view/buyView.mustache", ["user" => $user]);
            }else {
                Redirect::to("/login/read");
            }
        }

        public function update() {
            if(isset($_SESSION["usuario"]) && isset($_POST["buy"])) {
                $idUsuario = $_POST["idUsuario"];
                $cantidad = $_POST["cantidad"];
                $precioTotal = $_POST["precioTotal"];
                $this->model->buyTrampitas($idUsuario, $cantidad, $precioTotal);
                $this->model->updateCantidadTrampitasUsuario($idUsuario, $cantidad);
                $_SESSION["usuario"] = $this->model->getUser($idUsuario);
                Redirect::to("/lobby/read");
            }else {
                Redirect::to("/login/read");
            }
        }

    }

?>