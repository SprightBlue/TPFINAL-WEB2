<?php

    class LoginController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            if(isset($_SESSION["usuario"])) {
                Redirect::to("/lobby/read");
            }else {
                $this->presenter->render("view/loginView.mustache");
            }
        }

        public function get() {
            $errors = [];
            $username = isset($_POST["username"]) ? $_POST["username"] : "";
            $pass = isset($_POST["pass"]) ? $_POST["pass"] : "";
            $user = $this->model->loginUser($username, $pass, $errors);
            if(empty($errors)) {
                $_SESSION["usuario"] = $user;
                Redirect::to("/lobby/read");                 
            }else {   
                $this->presenter->render("view/loginView.mustache", ["errors"=>$errors]);
            }
        }

    }

?>
