<?php

    class PlayController{

        private $playModel;
        private $challengeModel;
        private $presenter;

        public function __construct($playModel, $challengeModel, $presenter){
            $this->playModel = $playModel;
            $this->challengeModel = $challengeModel;
            $this->presenter = $presenter;
        }

        public function read() {
            unset($_SESSION["partida"]);
            $this->verifyUserSession();
            if (isset($_SESSION["partida"]) && !empty($_SESSION["partida"])) {
                if (isset($_SESSION["page_loaded"])) {
                    $this->incorrectCase();
                    return;
                } else {
                    $data = $_SESSION["partida"];
                    $_SESSION["page_loaded"] = true;
                }
            } else {
                /*$sessionThirdParties = isset($_SESSION["modoTerceros"]) ? $_SESSION["modoTerceros"] : null;
                if ($sessionThirdParties != null) {
                    $data = $this->playModel->getDataSessionThirdParties($_SESSION["usuario"]["id"], 0, $sessionThirdParties["idEnterprise"]);
                } else {*/
                    $data = $this->playModel->getData($_SESSION["usuario"]["id"], 0);
                //}
                $_SESSION["partida"] = $data;
                $_SESSION["startTime"] = time();

            }
            $data["isChallenge"] = isset($_SESSION['challenge_id']);
            $this->presenter->render("view/playView.mustache", $data);
        }


        public function verify() {
            $this->verifyUserSession();
            $isCorrect = isset($_POST["isCorrect"]) ? $_POST["isCorrect"] : null;
            $elapsedTime = isset($_SESSION["startTime"]) ? time() - $_SESSION["startTime"] : null;
            $this->updateAnswerStats($isCorrect, $elapsedTime);
            if ($isCorrect && $elapsedTime > 0) {
                $_SESSION["partida"]["score"] += 1;
                $this->correctCase();
            } else {
                $this->incorrectCase();
            }
        }

        public function useBonus() {
            $this->verifyUserSession();
            $_POST["isCorrect"] = true;
            $this->playModel->updateUserBonus($_SESSION["usuario"]["id"]);
            $_SESSION["usuario"] = $this->playModel->getUser($_SESSION["usuario"]["id"]);
            $this->verify();
        }

        private function updateAnswerStats($isCorrect, $elapsedTime) {
            $this->playModel->incrementTotalAnswers($_SESSION["partida"]["question"]["idQuestion"]);
            $this->playModel->incrementUserAnsweredQuestions($_SESSION["usuario"]["id"]);
            if ($isCorrect && $elapsedTime > 0) {
                $this->playModel->incrementCorrectAnswers($_SESSION["partida"]["question"]["idQuestion"]);
                $this->playModel->incrementUserCorrectAnswers($_SESSION["usuario"]["id"]);
            }
            $this->playModel->updateQuestionDifficulty($_SESSION["partida"]["question"]["idQuestion"]);
        }

        private function correctCase() {
            $data = $this->playModel->getData($_SESSION["usuario"]["id"], $_SESSION["partida"]["score"]);
            $_SESSION["partida"] = $data;
            $_SESSION["page_loaded"] = false;
            $data["gameOver"] = false;
            $this->presenter->render("view/playView.mustache", $data);
        }

        private function incorrectCase() {
            $data = $_SESSION["partida"];
       
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
                /*$sessionThirdParties = isset($_SESSION["modoTerceros"]) ? $_SESSION["modoTerceros"] : null;
                if ($sessionThirdParties != null) {
                    $this->playModel->saveGameSessionThirdParties($_SESSION["usuario"]["id"], $data["score"], $sessionThirdParties["idEnterprise"]);
                } else {*/
                    $this->playModel->saveGame($_SESSION["usuario"]["id"], $data["score"]);
                //}
                $data["challenge"] = false;
            }

            if (!$data["challenge"]) {
                $data["modal"] = ($data["score"] === 0) ? "0 puntos mejor suerte la próxima" : $data["score"];    
            }

            $data["gameOver"] = true;
            $_SESSION["page_loaded"] = false;
            $_SESSION["startTime"] = null;
            $this->presenter->render("view/playView.mustache", $data);
        }

        public function reportQuestion() {
            $this->verifyUserSession();
            $this->incorrectCase();
            $idUser = $_SESSION["usuario"]["id"];
            $idQuestion = $_POST["idQuestion"];
            $reason = $_POST["reason"];
            $this->playModel->insertReport($idUser, $idQuestion, $reason);
        }

        public function lostGame(){
            $this->verifyUserSession();
            if ($_SESSION["partida"]["score"] > 0){
                $_SESSION["partida"]["score"] = $_SESSION["partida"]["score"] - 1;
            }
            $this->incorrectCase();
        }

        private function verifyUserSession() {
            if (!isset($_SESSION["usuario"])) {
                Redirect::to("/login/read");
            }
        }

    }
