<?php

    class RegistroController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            $error = isset($_SESSION["error"]) ? $_SESSION["error"] : "";
            $this->presenter->render("view/registroView.mustache", ["error"=>$error]);
        }

        public function insert() {
            if($_POST["pass"] != $_POST["repeatPass"]) {
                $_SESSION["error"] = "Las contraseñas ingresadas no coinciden.";
                Redirect::to("/registro/read");
            }else {
                $_SESSION["error"] = "";
            }
            $this->model->createUser($_POST["fullname"], $_POST["yearOfBirth"], $_POST["gender"], $_POST["country"],
            $_POST["city"], $_POST["email"], $_POST["pass"], $_POST["username"], $_FILES["img"]["name"], $_FILES["img"]["tmp_name"]);
            Redirect::to("/login/read");
        }

    }

?>