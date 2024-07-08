<?php

    class LobbyController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            $this->verifyUserSession();
            $this->verifyEntorno();
            $user = $_SESSION["usuario"];
            $entorno = isset($_SESSION["entorno"]) ? $_SESSION["entorno"] : null;
            $user["isPlayer"] = $user["userRole"] == "player";
            $user["isEditor"] = $user["userRole"] == "editor";
            $user["isAdmin"] = $user["userRole"] == "admin";
            $data = $this->getData($user, $entorno);
            $this->presenter->render("view/lobbyView.mustache", $data);
        }

        public function close() {
            session_destroy();
            Redirect::to("/login/read");
        }

        private function getData($user, $entorno) {
            $userScore = $this->model->getUserScore($user["id"]);
            $data = ["user"=>$user, "userScore"=>$userScore];
            if ($entorno != null) {$data["nombreEntorno"] = $this->model->getNombreEntorno($entorno["idTerceros"]);}
            return $data;
        }

        public function suggestQuestionView() {
            $this->verifyPlayerSession();
            $this->verifyEntorno();
            $this->presenter->render("view/suggestQuestionView.mustache",["action" => "/lobby/suggestQuestion"]);
        }

        public function suggestQuestion() {
            $this->verifyPlayerSession();
            $this->verifyEntorno();
            $idUser = $_SESSION["usuario"]["id"];
            $question = $_POST["question"];
            $category = $_POST["category"];
            $answer1 = $_POST["answer1"];
            $answer2 = $_POST["answer2"];
            $answer3 = $_POST["answer3"];
            $answer4 = $_POST["answer4"];
            $correct = $_POST["correct"];
            $this->model->addSuggestQuestion($idUser, $question, $category, $answer1, $answer2, $answer3, $answer4, $correct);
        }

        private function verifyUserSession() {
            if (!isset($_SESSION["usuario"])) {Redirect::to("/login/read");}  
        }

        private function verifyPlayerSession() {
            if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["userRole"] != "player") {Redirect::to("/login/read");}  
        }

        private function verifyEntorno() {
            if (isset($_SESSION["entorno"])) {
                $idEmpresa = $_SESSION["entorno"]["idEmpresa"];
                $idUsuario = $_SESSION["usuario"]["id"];
                $currentTime = date("Y-m-d H:i:s");
                $result = $this->model->getEntorno($idEmpresa, $idUsuario, $currentTime);
                if (!$result) {$_SESSION["entorno"] = null;}
            } 
        }

    }
