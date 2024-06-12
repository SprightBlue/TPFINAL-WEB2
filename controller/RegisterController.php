<?php

    class RegisterController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter){
            $this->model = $model;
            $this->presenter = $presenter;
        }
        
        public function read() {
            if(isset($_SESSION["usuario"])) {
                Redirect::to("/lobby/read");
            }else {
                $this->presenter->render("view/registerView.mustache");
            }  
        }

        public function insert(){
            $erros = [];
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
            $this->model->createUser($fullname, $yearOfBirth, $gender, $country, $city, $email, $pass, $repeatPass, $username, $img, $errors);
            if(empty($errors)) {     
                Redirect::to("/login/read");
            }else {
                $this->presenter->render("view/registerView.mustache", ["errors"=>$errors]);
            }
        }

        public function active() {
            $token = $_GET["token"];
            $this->model->activeUser($token);
            Redirect::to("/login/read");
        }

    }

?>
