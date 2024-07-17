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
            $this->verifySessionThirdParties();
            $this->presenter->render("view/buyView.mustache", ["user" => $_SESSION["usuario"]]);
        }

        public function update() {
            $this->verifyUserSession();
            $this->verifySessionThirdParties();
            if (isset($_POST["comprar"])) {
                $this->model->buyBonus($_POST["idUser"], $_POST["amount"], $_POST["totalPrice"]);
                $this->model->updateUserBonus($_POST["idUser"], $_POST["amount"]);
                $_SESSION["usuario"] = $this->model->getUser($_POST["idUser"]);             
            } 
            Redirect::to("/lobby/read"); 
        }

        private function verifyUserSession() {
            if (!isset($_SESSION["usuario"])) {
                Redirect::to("/login/read");
            }  
        }

        
        private function verifySessionThirdParties() {
            if (isset($_SESSION["modoTerceros"])) {
                $currentTime = date("Y-m-d H:i:s");
                $session = $this->model->getSessionThirdParties($_SESSION["modoTerceros"]["idEnterprise"], $_SESSION["usuario"]["id"], $currentTime);
                if ($session == false) {
                    $_SESSION["modoTerceros"] = null;
                }
            } 
        }
        

    }
