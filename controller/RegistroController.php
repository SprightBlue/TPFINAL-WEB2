<?php

    class RegistroController{

        private $model;
        private $presenter;

        public function __construct($model, $presenter){
            $this->model = $model;
            $this->presenter = $presenter;
        }
        
        public function read() {
            $error = isset($_SESSION["errorRegistro"]) && is_array($_SESSION["errorRegistro"]) ? implode("<br>", $_SESSION["errorRegistro"]) : "";
            $_SESSION["errorRegistro"] = "";
            $this->presenter->render("view/registroView.mustache", ["error"=>$error]);
        }

        public function insert(){
            $fullname = $_POST["fullname"];
            $yearOfBirth = $_POST["yearOfBirth"];
            $gender = $_POST["gender"];
            $country = $_POST["country"];
            $city = $_POST["city"];
            $email = $_POST["email"];
            $pass = $_POST["pass"];
            $repeatPass = $_POST["repeatPass"];
            $username = $_POST["username"];
            $img = $_FILES["img"];
            try {
                $this->model->createUser($fullname, $yearOfBirth, $gender, $country, $city, $email, $pass, $repeatPass, $username, $img);
                Redirect::to("/login/read");
            } catch (Exception $e) {
                $_SESSION["error"] = $e->getMessage();
                Redirect::to("/registro/read");
            }
        }

        public function verify() {
            $token = $_GET['token'];
            $this->model->verifyUser($token);
            Redirect::to("/login/read");
        }

    }

?>