<?php

    class ProfileController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            if(isset($_SESSION["usuario"])) {
                $user = $_SESSION["usuario"];
                $this->presenter->render("view/profileView.mustache", ["user"=>$user]);
            }else {
                Redirect::to("/login/read");
            }
        }

        public function get() {
            if(isset($_SESSION["usuario"])) {
                $username = isset($_GET["nombre"]) ? $_GET["nombre"] : false;
                $data = $this->getData($username);
                $this->presenter->render("view/profileView.mustache", $data);                
            }else {
                Redirect::to("/login/read");
            }
        }

        private function getData($username) {
            $user = $this->model->getUser($username);
            $data = ["user"=>$user];
            return $data;
        }

    }

?>