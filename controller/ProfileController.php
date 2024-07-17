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
            $this->verifySessionThirdParties();
            $data = $this->getData($_SESSION["usuario"]["id"]);
            $this->presenter->render("view/profileView.mustache", $data);
        }

        public function get() {
            $this->verifyUserSession();
            $this->verifySessionThirdParties();
            if (isset($_GET["idUser"])) {
                $data = $this->getData($_GET["idUser"]);
                if ($data["user"]["idRole"] == 1) {
                    $this->presenter->render("view/profileView.mustache", $data); 
                } else {
                    Redirect::to("/login/read");
                }
            } else {
                Redirect::to("/login/read");
            }
        }

        private function getData($id) {
            $user = $this->model->getUser($id);
            if ($user != false) {
                $isOwnProfile = ($_SESSION["usuario"]["id"] == $user["id"]);
                $this->createQR($id);
                $data = ["user" => $user, "qr" => "/public/qr/qr-" . $id . ".png", "isOwnProfile" => $isOwnProfile];
                return $data;                
            }
        }
        
        private function createQR($id) {
            if (!file_exists("public/qr/qr-" . $id . ".png")) {
                $profileUrl = "http:/localhost/profile/get?idUser=$id";
                $pathImg = "public/qr/qr-". $id . ".png";
                GeneratorQR::generate($profileUrl, $pathImg);             
            } 
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
