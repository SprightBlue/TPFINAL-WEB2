<?php

    class PlayController{

        private $playModel;
        private $challengeModel;
        private $presenter;

        private $logger;

        public function __construct($playModel, $challengeModel, $presenter,$logger){
            $this->playModel = $playModel;
            $this->challengeModel = $challengeModel;
            $this->presenter = $presenter;
            $this->logger = $logger;
        }


        public function read() {
            $this->logger->info("PlayController: read");
            $this->verifyUserSession();
            if (isset($_SESSION["partida"]) && !empty($_SESSION["partida"])) {
                $this->logger->info("Partida en curso");
                $this->incorrectCase();

            } else {
                $sessionThirdParties = isset($_SESSION["modoTerceros"]) ? $_SESSION["modoTerceros"] : null;
                if ($sessionThirdParties != null) {
                    $data = $this->playModel->getDataSessionThirdParties($_SESSION["usuario"]["id"], 0, $sessionThirdParties["idEnterprise"]);
                } else {
                    $data = $this->playModel->getData($_SESSION["usuario"]["id"], 0);
                }
                $_SESSION["verificationToken"] = $data["verificationToken"];
                $_SESSION["partida"] = $data;
                $_SESSION["startTime"] = time();

            }
            $data["isChallenge"] = isset($_SESSION['challenge_id']);
            $this->presenter->render("view/playView.mustache", $data);
        }


        public function verify() {
            $this->verifyUserSession();
            $this->logger->info("Validando pregunta");

            if (!isset($_POST["verificationToken"]) || $_POST["verificationToken"] !== $_SESSION["verificationToken"]) {
                $this->logger->info("Token incorrecto");
                $this->incorrectCase();
                return;
            }

            unset($_SESSION["verificationToken"]);

            $isCorrect = isset($_POST["isCorrect"]) ? $_POST["isCorrect"] : null;
            $elapsedTime = isset($_SESSION["startTime"]) ? time() - $_SESSION["startTime"] : null;
            $this->updateAnswerStats($isCorrect, $elapsedTime);
            if ($isCorrect && $elapsedTime > 0) {
                $this->correctCase();
            } else {
                $this->incorrectCase();
            }
        }

        public function useBonus() {
            $this->logger->info("Usando bonificación");
            $this->verifyUserSession();

            if (!isset($_POST["verificationToken"]) || $_POST["verificationToken"] !== $_SESSION["verificationToken"]) {
                $this->incorrectCase();
                return;
            }

            unset($_SESSION["verificationToken"]);

            $this->playModel->updateUserBonus($_SESSION["usuario"]["id"]);
            $_SESSION["usuario"] = $this->playModel->getUser($_SESSION["usuario"]["id"]);
            $this->correctCase();
        }

        private function updateAnswerStats($isCorrect, $elapsedTime) {
            $this->logger->info("Actualizando estadísticas de respuesta");
            $this->playModel->incrementTotalAnswers($_SESSION["partida"]["question"]["idQuestion"]);
            $this->playModel->incrementUserAnsweredQuestions($_SESSION["usuario"]["id"]);
            if ($isCorrect && $elapsedTime > 0) {
                $this->playModel->incrementCorrectAnswers($_SESSION["partida"]["question"]["idQuestion"]);
                $this->playModel->incrementUserCorrectAnswers($_SESSION["usuario"]["id"]);
            }
            $this->playModel->updateQuestionDifficulty($_SESSION["partida"]["question"]["idQuestion"]);
        }

        private function correctCase() {
            $this->logger->info("Respuesta correcta");
            $_SESSION["partida"]["score"] += 1;

            $sessionThirdParties = isset($_SESSION["modoTerceros"]) ? $_SESSION["modoTerceros"] : null;
            if ($sessionThirdParties != null) {
                $data = $this->playModel->getDataSessionThirdParties($_SESSION["usuario"]["id"], $_SESSION["partida"]["score"], $sessionThirdParties["idEnterprise"]);
            } else {
                $data = $this->playModel->getData($_SESSION["usuario"]["id"], $_SESSION["partida"]["score"]);
            }
            $_SESSION["partida"] = $data;

            $data["gameOver"] = false;

            $_SESSION["verificationToken"] = $data["verificationToken"];

            $this->presenter->render("view/playView.mustache", $data);
        }

        private function incorrectCase() {
            $this->logger->info("Respuesta incorrecta");
            $data = $_SESSION["partida"] ?? null;

            if(!isset($_SESSION["partida"])) {
                Redirect::to("/lobby/read");
            }
            if (isset($_SESSION['challenge_id'])) {
                $this->logger->info("Fin de partida por desafío");
                if ($this->challengeModel->isChallenger($_SESSION['challenge_id'], $_SESSION["usuario"]["id"])) {
                    $this->challengeModel->updateChallengerScore($_SESSION['challenge_id'], $data["score"]);
                    $this->challengeModel->updateChallengeStatus($_SESSION['challenge_id'], 'pending');
                } else {
                    $this->challengeModel->updateChallengedScore($_SESSION['challenge_id'], $data["score"]);
                    $this->challengeModel->updateChallengeStatus($_SESSION['challenge_id'], 'resolved');
                    $this->challengeModel->compareScores($_SESSION['challenge_id']);
                }

                unset($_SESSION['challenge_id']);
                unset($_SESSION["partida"]);
                $data["challenge"] = true;

            } else {
                $sessionThirdParties = isset($_SESSION["modoTerceros"]) ? $_SESSION["modoTerceros"] : null;
                if ($sessionThirdParties != null) {
                    $this->playModel->saveGameSessionThirdParties($_SESSION["usuario"]["id"], $data["score"], $sessionThirdParties["idEnterprise"]);
                } else {
                    $this->playModel->saveGame($_SESSION["usuario"]["id"], $data["score"]);
                    $data["modal"] = ($data["score"] === 0) ? "0 puntos mejor suerte la próxima" : $data["score"];
                    $data["challenge"] = false;
                }

            }
            $this->logger->info("Fin de partida normal");
            unset($_SESSION["partida"]);
            $_SESSION["startTime"] = null;

            $this->presenter->render("view/playView.mustache", $data);
        }

        public function reportQuestion() {
            $this->logger->info("Reportando pregunta");
            $this->verifyUserSession();
            $this->incorrectCase();
            $idUser = $_SESSION["usuario"]["id"];
            $idQuestion = $_POST["idQuestion"];
            $reason = $_POST["reason"];
            $this->playModel->insertReport($idUser, $idQuestion, $reason);
        }

        public function lostGame(){
            $this->logger->info("Partida perdida");
            $this->verifyUserSession();
            $this->incorrectCase();
        }

        private function verifyUserSession() {

            if (!isset($_SESSION["usuario"])) {
                Redirect::to("/login/read");
            }
        }

    }