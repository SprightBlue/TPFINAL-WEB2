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
            $this->verifyEntorno();
            $userId = $_SESSION["usuario"]["id"];
            $allChallenges = $this->model->getAllChallenges($userId);
            $pendingChallenges = $this->model->getPendingChallenges($userId);
            $this->presenter->render("view/challengesView.mustache",["allChallenges"=>$allChallenges, "pendingChallenges"=>$pendingChallenges]);
        }

        public function createChallenge() {
            $this->verifyUserSession();
            $this->verifyEntorno();
            $challengerId = $_SESSION["usuario"]["id"];
            $challengedId = $_POST['challenged_id'];
            $challengeId = $this->model->createChallenge($challengerId, $challengedId);
            $_SESSION['challenge_id'] = $challengeId;
            Redirect::to("/play/read?challenge_id=$challengeId");
        }

        public function acceptChallenge() {
            $this->verifyUserSession();
            $this->verifyEntorno();
            $challengeId = $_POST['challengeId'];
            $this->model->updateChallengeStatus($challengeId, 'accepted');
            $_SESSION['challenge_id'] = $challengeId;
            Redirect::to("/play/read?challenge_id=$challengeId");
        }

        private function verifyUserSession() {
            if (!isset($_SESSION["usuario"])) {Redirect::to("/login/read");}  
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
