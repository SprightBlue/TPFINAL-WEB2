<?php

    class ProfileController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            $user = isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : "";
            $this->presenter->render("view/profileView.mustache", ["usuario"=>$user]);
        }

    }

?>