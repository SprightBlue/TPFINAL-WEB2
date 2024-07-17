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
            $this->verifySessionThirdParties();
            $data = $this->getData($_SESSION["usuario"]);
            $this->presenter->render("view/rankingView.mustache", $data);   
        }

        private function getData($user) {
            
            if (isset($_SESSION["modoTerceros"])) {
                $userScore = $this->model->getUserScoreSessionThirdParties($user["id"], $_SESSION["modoTerceros"]["idEnterprise"]);
                $matchHistory = $this->model->getMatchHistorySessionThirdParties($user["id"], $_SESSION["modoTerceros"]["idEnterprise"]);
                $rankingScore = $this->model->getRankingScoreSessionThirdParties($_SESSION["modoTerceros"]["idEnterprise"]); 
            } else {
                $userScore = $this->model->getUserScore($user["id"]);        
                $matchHistory = $this->model->getMatchHistory($user["id"]);
                $rankingScore = $this->model->getRankingScore(); 
            }
            $data = ["user"=>$user, "userScore"=>$userScore, "matchHistory"=>$matchHistory, "rankingScore"=>[]];
            if ($rankingScore != false) {
                foreach ($rankingScore as $index => $score) {
                    $data["rankingScore"][] = ["rank"=>$index+1, "id"=>$score["id"], "username"=>$score["username"], "maxScore"=>$score["maxScore"]];
                }
            }
            
            if (isset($_SESSION["modoTerceros"])) {
                $data["nameThirdParties"] = $this->model->getNameThirdParties($_SESSION["modoTerceros"]["idEnterprise"]);
            }
            
            return $data;
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
