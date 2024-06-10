<?php

    class RankingController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            $user = isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : false;
            if(isset($_SESSION["usuario"])) {
                $data = $this->getData($user);
                $this->presenter->render("view/rankingView.mustache", $data);
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
            $matchHistory = $this->model->getMatchHistory($user["id"]);
            $rankingScore = $this->model->getRankingScore(); 
            $data = ["user"=>$user, "userScore"=>$userScore, "matchHistory"=>$matchHistory, "rankingScore"=>[]];
            if($rankingScore != false) {foreach($rankingScore as $index => $score) {$data["rankingScore"] = ["rank"=>$index+1, "username"=>$score["username"], "maxScore"=>$score["maxScore"]];}}
            return $data;
        }

    }

?>
