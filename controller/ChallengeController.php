<?php

    class ChallengeController {

        private $model;
        private $presenter;
    
        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function readChallenges() {
            $this->verifyUserSession();
            $this->verifySessionThirdParties();
            $userId = $_SESSION["usuario"]["id"];
            $allChallenges = $this->model->getAllChallenges($userId);
            $pendingChallenges = $this->model->getPendingChallenges($userId);
            $this->presenter->render("view/challengesView.mustache", ["user"=>$_SESSION["usuario"], "allChallenges"=>$allChallenges, "pendingChallenges"=>$pendingChallenges]);
        }

        public function createChallenge() {
            $this->verifyUserSession();
            $this->verifySessionThirdParties();
            $challengerId = $_SESSION["usuario"]["id"];
            $challengedId = $_POST["challenged_id"];
            $challengeId = $this->model->createChallenge($challengerId, $challengedId);
            $_SESSION["challenge_id"] = $challengeId;
            Redirect::to("/play/read?challenge_id=$challengeId");
        }

        public function acceptChallenge() {
            $this->verifyUserSession();
            $this->verifySessionThirdParties();
            if (isset($_POST["aceptar"])) {
                $challengeId = $_POST["challengeId"];
                $this->model->updateChallengeStatus($challengeId, "accepted");
                $_SESSION["challenge_id"] = $challengeId;
                Redirect::to("/play/read?challenge_id=$challengeId");
            } else {
                Redirect::to("/challenge/readChallenges");
            }
        }

        private function verifyUserSession() {
            if (!isset($_SESSION["usuario"])) {
                Redirect::to("/login/read");
            }
        }

        
        private function verifySessionThirdParties() {
            if (isset($_SESSION["modoTerceros"])) {
                $currentTime = date("Y-m-d H:i:s");
                $session = $this->model->getSessionThirdParties($_SESSION["modoTerceros"]["idEnterprise"], $_SESSION["usuario"]["id"], $currentTime);
                if ($session == false) {
                    $_SESSION["modoTerceros"] = null;
                }
            } 
        }

    }
