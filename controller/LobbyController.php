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
            //$this->verifySessionThirdParties();
            $data = $this->getData($_SESSION["usuario"]);
            $this->presenter->render("view/lobbyView.mustache", $data);
        }

        public function close() {
            session_destroy();
            Redirect::to("/login/read");
        }

        private function getData($user) {
            $isPlayer = ($user["idRole"] == 1);
            $isEditor = ($user["idRole"] == 2);
            $isAdmin = ($user["idRole"] == 3);
            $isEnterprise = ($user["idRole"] == 4);
            $userScore = $this->model->getUserScore($user["id"]);
            $data = ["user"=>$user, "userScore"=>$userScore, "isPlayer"=>$isPlayer, "isEditor"=>$isEditor, "isAdmin"=>$isAdmin, "isEnterprise"=>$isEnterprise];
            /*
            if (isset($_SESSION["sesionTerceros"])) {
                $data["nameThirdParties"] = $this->model->getNameThirdParties($_SESSION["modoTerceros"]["idEnterprise"]);
            }
            */
            return $data;
        }

        public function suggestQuestionView() {
            $this->verifyPlayerSession();
            //$this->verifySessionThirdParties();
            $this->presenter->render("view/suggestQuestionView.mustache",["action" => "/lobby/suggestQuestion"]);
        }

        public function suggestQuestion() {
            $this->verifyPlayerSession();
            //$this->verifySessionThirdParties();
            if (isset($_POST["enviar"])) {
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
            Redirect::to("/lobby/read");
        }

        private function verifyUserSession() {
            if (!isset($_SESSION["usuario"])) {
                Redirect::to("/login/read");
            }  
        }

        private function verifyPlayerSession() {
            if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["idRole"] != 1) {
                Redirect::to("/login/read");
            }  
        }

        /*
        private function verifySessionThirdParties() {
            if (isset($_SESSION["sesionTerceros"])) {
                $currentTime = date("Y-m-d H:i:s");
                $session = $this->model->getSessionThirdParties($_SESSION["sesionTerceros"]["idEnterprise"], $_SESSION["usuario"]["id"], $currentTime);
                if ($session == false) {
                    $_SESSION["sesionTerceros"] = null;
                }
            } 
        }
        */

    }
