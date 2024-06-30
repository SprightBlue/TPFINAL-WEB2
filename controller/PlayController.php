<?php

    class PlayController {

        private $playModel;
        private $challengeModel;
        private $presenter;

        public function __construct($playModel, $challengeModel, $presenter) {
            $this->playModel = $playModel;
            $this->challengeModel = $challengeModel;
            $this->presenter = $presenter;
        }

        public function read() {
            if(isset($_SESSION["usuario"])) {
                if(isset($_SESSION["partida"]) && !empty($_SESSION["partida"])) {
                    $data = $_SESSION["partida"];
                }else {
                    $data = $this->playModel->getData($_SESSION["usuario"]["id"], 0);
                    $_SESSION["partida"] = $data;
                    $_SESSION["startTime"] = time();
                }
                $data["isChallenge"] = isset($_SESSION['challenge_id']);
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
            $this->playModel->incrementTotalAnswers($_SESSION["partida"]["question"]["idQuestion"]);
            $this->playModel->incrementUserAnsweredQuestions($_SESSION["usuario"]["id"]);
            if ($isCorrect && $elapsedTime > 0) {
                $_SESSION["partida"]["score"] += 1;
                $this->playModel->incrementCorrectAnswers($_SESSION["partida"]["question"]["idQuestion"]);
                $this->playModel->incrementUserCorrectAnswers($_SESSION["usuario"]["id"]);
            }
            $this->playModel->updateQuestionDifficulty($_SESSION["partida"]["question"]["idQuestion"]);
        }

        private function correctCase() {
            $data = $this->playModel->getData($_SESSION["usuario"]["id"], $_SESSION["partida"]["score"]);
            $_SESSION["partida"] = $data;
            $data["gameOver"] = false;
            $this->presenter->render("view/playView.mustache", $data);
        }

        private function incorrectCase() {
            $data = $_SESSION["partida"];
            unset($_SESSION["partida"]);

            //challenge
            if (isset($_SESSION['challenge_id'])) {
                if ($this->challengeModel->isChallenger($_SESSION['challenge_id'], $_SESSION["usuario"]["id"])) {

                    $this->challengeModel->updateChallengerScore($_SESSION['challenge_id'], $data["score"]);
                    $this->challengeModel->updateChallengeStatus($_SESSION['challenge_id'], 'pending');
                } else {

                    $this->challengeModel->updateChallengedScore($_SESSION['challenge_id'], $data["score"]);
                    $this->challengeModel->updateChallengeStatus($_SESSION['challenge_id'], 'resolved');
                    $this->challengeModel->compareScores($_SESSION['challenge_id']);

                }
                unset($_SESSION['challenge_id']);
                $data["challenge"] = true;
            } else {
                $this->playModel->saveGame($_SESSION["usuario"]["id"], $data["score"]);
                $data["challenge"] = false;
            }


            if (!$data["challenge"]) {
                $data["modal"] = ($data["score"] === 0) ? "0 puntos mejor suerte la proxima" : $data["score"];
                $data["gameOver"] = true;
            } else {
                $data["gameOver"] = true;
            }

            $this->presenter->render("view/playView.mustache", $data);
        }

        public function reportQuestion() {
            $this->incorrectCase();
            if(isset($_SESSION["usuario"])) {

                $idUser = $_SESSION["usuario"]["id"];
                $idQuestion = $_POST["idQuestion"];
                $reason = $_POST["reason"];
                $this->playModel->insertReport($idUser, $idQuestion, $reason);
            } else {

            }
        }
    }

