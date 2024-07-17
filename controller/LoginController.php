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
            if (isset($_POST["ingresar"])) {
                $errors = [];
                $user = $this->model->loginUser($_POST["username"], $_POST["pass"], $errors);
                if (empty($errors)) {
                    $_SESSION["usuario"] = $user;
                    Redirect::to("/lobby/read");                    
                } else {
                    $this->presenter->render("view/loginView.mustache", ["errors"=>$errors]);
                }                    
            } else {
                Redirect::to("/login/read");   
            }
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

        
        public function active() {
            $this->verifyUserSession();
            if (isset($_GET["idThirdParties"]) && $this->model->getQuestionsCount($_GET["idThirdParties"]) >= 10 ) {
                $startDate = date("Y-m-d H:i:s");
                $endDate = date("Y-m-d H:i:s", strtotime("+1 hour"));   
                if ($this->model->sessionThirdPartiesExists($_GET["idThirdParties"], $_SESSION["usuario"]["id"])) {
                    $this->model->updateSessionThirdParties($_GET["idThirdParties"], $_SESSION["usuario"]["id"], $startDate, $endDate);
                } else {
                    $this->model->createSessionThirdParties($_GET["idThirdParties"], $_SESSION["usuario"]["id"], $startDate, $endDate);
                }
                $_SESSION["modoTerceros"] = $this->model->getSessionThirdParties($_GET["idThirdParties"], $_SESSION["usuario"]["id"], $startDate);
            } 
            Redirect::to("/lobby/read"); 
        }
        

    }
