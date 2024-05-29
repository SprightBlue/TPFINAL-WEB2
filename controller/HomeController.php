<?php

    class HomeController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            $usuario = isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : ""; 
            $this->presenter->render("view/homeView.mustache", ["usuario"=>$usuario]);
        }

    }

?>