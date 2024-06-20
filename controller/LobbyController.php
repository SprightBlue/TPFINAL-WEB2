<?php

    class LobbyController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            if(isset($_SESSION["usuario"])) {
                $user = $_SESSION["usuario"];
                $user["isPlayer"] = $user["userRole"] == "player";
                $user["isEditor"] = $user["userRole"] == "editor";
                $data = $this->getData($user);
                $this->presenter->render("view/lobbyView.mustache", $data);
            }else {
                Redirect::to("/login/read");
            }
        }

        public function close() {
            session_destroy();
            Redirect::to("/login/read");
        }

        private function getData($user) {
            $userScore = $this->model->getUserScore($user["id"]);
            $data = ["user"=>$user, "userScore"=>$userScore];
            if($user["userRole"] == "admin") {
                $data["admin"] = "only admin mode";
            }
            return $data;
        }

        public function suggestQuestionView() {
            if(isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"] == "player") {
                $this->presenter->render("view/suggestQuestionView.mustache",["action" => "/lobby/suggestQuestion"]);
            } else {
                Redirect::to("/login/read");
            }
        }
        public function suggestQuestion() {
            if(isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"] == "player") {
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
    }


