<?php

    class RankingController {

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
            $data = $this->getData($user, $entorno);
            $this->presenter->render("view/rankingView.mustache", $data);   
        }

        private function getData($user, $entorno) {
            if ($entorno != null) {
                $userScore = $this->model->getUserScoreModoEntorno($user["id"], $entorno["id"]);
                $matchHistory = $this->model->getMatchHistoryModoEntorno($user["id"], $entorno["id"]);
                $rankingScore = $this->model->getRankingScoreModoEntorno($entorno["id"]); 
            } else {
                $userScore = $this->model->getUserScore($user["id"]);        
                $matchHistory = $this->model->getMatchHistory($user["id"]);
                $rankingScore = $this->model->getRankingScore(); 
            }
            $data = ["user"=>$user, "userScore"=>$userScore, "matchHistory"=>$matchHistory, "rankingScore"=>[]];
            if ($rankingScore != false) {foreach($rankingScore as $index => $score) {$data["rankingScore"][] = ["rank"=>$index+1, "username"=>$score["username"], "maxScore"=>$score["maxScore"]];}}
            if ($entorno != null) {$data["nombreEntorno"] = $this->getNombreEntorno($entorno["idTerceros"]);}
            return $data;
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
