<?php

    class PlayController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            if(isset($_SESSION["usuario"])) {
                if(isset($_SESSION["partida"]) && !empty($_SESSION["partida"])) {
                    $data = $_SESSION["partida"];
                }else {
                    $data = $this->model->getData($_SESSION["usuario"]["id"], 0);
                    $_SESSION["partida"] = $data;
                    $_SESSION["startTime"] = time();
                }
                $this->presenter->render("view/playView.mustache", $data);
            }else {
                Redirect::to("/login/read");
            }
        }

        public function verify() {
            if(isset($_SESSION["usuario"])) {
                $isCorrect = isset($_POST["isCorrect"]) ? $_POST["isCorrect"] : null;
                $elapsedTime = isset($_SESSION["startTime"]) ? time() - $_SESSION["startTime"] : null;
                $this->updateAnswerStats($isCorrect, $elapsedTime);
                if($isCorrect && $elapsedTime > 0) {$this->correctCase();}
                else {$this->incorrectCase();}
            }else {
                Redirect::to("/login/read");
            }
        }


        private function updateAnswerStats($isCorrect, $elapsedTime) {
            $this->model->incrementTotalAnswers($_SESSION["partida"]["question"]["idQuestion"]);
            $this->model->incrementUserAnsweredQuestions($_SESSION["usuario"]["id"]);
            if ($isCorrect && $elapsedTime > 0) {
                $_SESSION["partida"]["score"] += 1;
                $this->model->incrementCorrectAnswers($_SESSION["partida"]["question"]["idQuestion"]);
                $this->model->incrementUserCorrectAnswers($_SESSION["usuario"]["id"]);
            }
            $this->model->updateQuestionDifficulty($_SESSION["partida"]["question"]["idQuestion"]);
        }

        private function correctCase() {
            $data = $this->model->getData($_SESSION["usuario"]["id"], $_SESSION["partida"]["score"]);
            $_SESSION["partida"] = $data;
            $data["gameOver"] = false;
            $this->presenter->render("view/playView.mustache", $data);
        }

        private function incorrectCase() {
            $data = $_SESSION["partida"];
            unset($_SESSION["partida"]);
            $this->model->saveGame($_SESSION["usuario"]["id"], $data["score"]);
            $data["modal"] = ($data["score"] === 0) ? "0 puntos mejor suerte la proxima" : $data["score"];
            $data["gameOver"] = true;
            $this->presenter->render("view/playView.mustache", $data);
        }
        public function reportQuestion() {
            $this->incorrectCase();
            if(isset($_SESSION["usuario"])) {

                $idUser = $_SESSION["usuario"]["id"];
                $idQuestion = $_POST["idQuestion"];
                $reason = $_POST["reason"];
                $this->model->insertReport($idUser, $idQuestion, $reason);
            } else {

            }
        }
    }

