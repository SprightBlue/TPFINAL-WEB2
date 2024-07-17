<?php

    class RegisterController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter){
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            $this->verifyUser();
            $this->presenter->render("view/registerView.mustache");
        }

        public function create(){
            $this->verifyUser();
            if (isset($_POST["registrarse"])) {
                if ($_POST["country"] != "" && $_POST["city"] != "") {
                    $errors = [];
                    $token = bin2hex(random_bytes(16)); 
                    $this->model->createUser($_POST["fullname"], $_POST["yearOfBirth"], $_POST["gender"], $_POST["country"], $_POST["city"], $_POST["email"], $_POST["pass"], $_POST["repeatPass"], $_POST["username"], $_FILES["img"], $token, $errors);
                    if (empty($errors)) {
                        $verificationUrl = "http://localhost/register/active?token=$token";
                        Mailer::send($_POST["email"], $_POST["fullname"], $verificationUrl);
                        $this->presenter->render("view/loginView.mustache", ["verifyMail"=>"Se envio un mensaje a su email para activar su cuenta."]);                           
                    } else {
                        $this->presenter->render("view/registerView.mustache", ["errors"=>$errors]);
                    }                    
                } else {
                    $this->presenter->render("view/registerView.mustache", ["ubicacion"=>"Seleccionar una ubicación valida."]);
                }
            } else {
                Redirect::to("/lobby/read");
            }
        }

        public function active() {
            $token = $_GET["token"];
            $this->model->activeUser($token);
            $this->presenter->render("view/loginView.mustache", ["activeACC"=>"Su cuenta se ah activado exitosamente."]);
        }

        public function update() {
            $this->verifyUserSession();
            //$this->verifySessionThirdParties();
            $data = $this->getData($_GET["id"]);
            $this->presenter->render("view/registerView.mustache", $data);
        }

        public function set() { 
            $this->verifyUserSession();
            //$this->verifySessionThirdParties();
            if (isset($_POST["actualizar"])) {
                if ($_POST["country"] != "" && $_POST["city"] != "") {
                    $errors = [];
                    $this->model->updateUser($_POST["id"], $_POST["fullname"], $_POST["yearOfBirth"], $_POST["gender"], $_POST["country"], $_POST["city"], $_POST["email"], $_POST["pass"], $_POST["repeatPass"], $_POST["username"], $_FILES["img"], $errors);
                    if (empty($errors)) {
                        $_SESSION["usuario"] = $this->model->getUser($_POST["id"]);
                        Redirect::to("/profile/read");                     
                    } else {
                        $this->presenter->render("view/registerView.mustache", ["user"=>$_SESSION["usuario"], "errors"=>$errors]);
                    }                    
                } else {
                    $this->presenter->render("view/loginView.mustache", ["user"=>$_SESSION["usuario"], "ubicacion"=>"Seleccionar una ubicación valida."]);
                }
            } else {
                Redirect::to("/lobby/read");
            }
        }

        private function getData($id) {
            $user = $this->model->getUser($id);
            $data = ["user" => $user];
            $data["genderMasculino"] = ($user["gender"] == "Masculino");
            $data["genderFemenino"] = ($user["gender"] == "Femenino");
            $data["noGender"] = ($user["gender"] == "Prefiero no cargarlo");
            return $data;
        }

        private function verifyUser() {
            if (isset($_SESSION["usuario"])) {
                Redirect::to("/lobby/read");
            }
        }

        private function verifyUserSession() {
            if (!isset($_SESSION["usuario"])) {
                Redirect::to("/login/read");
            }  
        }

        /*
        private function verifySessionThirdParties() {
            if (isset($_SESSION["modoTerceros"])) {
                $currentTime = date("Y-m-d H:i:s");
                $session = $this->model->getSessionThirdParties($_SESSION["modoTerceros"]["idEnterprise"], $_SESSION["usuario"]["id"], $currentTime);
                if ($session == false) {
                    $_SESSION["modoTerceros"] = null;
                }
            } 
        }
        */

    }
