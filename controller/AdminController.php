<?php

    class AdminController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            if(isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"]=="admin") {
                $filter = $_GET["filter"];
                $data = $this->getData($filter);
                $this->presenter->render("view/adminView.mustache", $data);
            }else {
                Redirect::to("/login/read");
            }
        }

        public function create() {
            if(isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"]=="admin") {
                $filter = $_GET["filter"];
                $data = $this->getData($filter);
                $html = $this->presenter->generateHtml("view/adminView.mustache", $data);
                GeneratorPDF::generate($html);
            }else {
                Redirect::to("/login/read");
            }
        }

        private function getData($filter) {
            $currentDate = date('Y-m-d H:i:s');
            switch($filter) {
                case "year":
                    $lastDate = date('Y-m-d H:i:s', strtotime('-1 year'));
                    break;
                case "month":
                    $lastDate = date('Y-m-d H:i:s', strtotime('-1 month'));
                    break;
                case "week":
                    $lastDate = date('Y-m-d H:i:s', strtotime('-1 week'));
                    break;
                case "day":
                default:
                    $lastDate = date('Y-m-d H:i:s', strtotime('-1 day'));     
                    break;    
            }  
            $data = $this->setData($currentDate, $lastDate, $filter);       
            return $data;
        }

        private function setData($currentDate, $lastDate, $filter) {
            $playersCount = $this->model->getPlayersCount($currentDate, $lastDate);
            $gamesCount = $this->model->getGamesCount($currentDate, $lastDate);
            $questionsCount = $this->model->getQuestionsCount($currentDate, $lastDate);
            $questionsCreated = $this->model->getQuestionsCreated($currentDate, $lastDate);
            $newUsers = $this->model->getNewUsers($currentDate, $lastDate);            
            $correctPercentage = $this->model->getCorrectPercentage($currentDate, $lastDate);
            $usersByCountry = $this->model->getUsersByCountry($currentDate, $lastDate);
            $usersByGender = $this->model->getUsersByGender($currentDate, $lastDate);
            $usersByAgeGroup = $this->model->getUsersByAgeGroup($currentDate, $lastDate);
            $data = $this->createData($playersCount, $gamesCount, $questionsCount, $questionsCreated, $newUsers, $correctPercentage, $usersByCountry, $usersByGender, $usersByAgeGroup, $filter);
            return $data;
        }

        private function createData($playersCount, $gamesCount, $questionsCount, $questionsCreated, $newUsers, $correctPercentage, $usersByCountry, $usersByGender, $usersByAgeGroup, $filter) {
            
            foreach($correctPercentage as $row) {
                $incorrectPercentage = 100 - $row["correctPercentage"];
                $correctPercentageGraph[]["graph"] = GeneratorGraph::generateCorrectPercentage($row["username"], $row["correctPercentage"], $incorrectPercentage);
            }
            $usersByCountryGraph = GeneratorGraph::generateUsersByCountry($usersByCountry);
            $usersByGenderGraph = GeneratorGraph::generateUsersByGender($usersByGender);
            $usersByAgeGroupGraph = GeneratorGraph::generateUsersByAgeGroup($usersByAgeGroup);
            $data = ["playersCount"=>$playersCount, "gamesCount"=>$gamesCount, "questionsCount"=>$questionsCount, "questionsCreated"=>$questionsCreated, "newUsers"=>$newUsers,
                    "correctPercentageGraph"=>$correctPercentageGraph, "usersByCountryGraph"=>$usersByCountryGraph, "usersByGenderGraph"=>$usersByGenderGraph, "usersByAgeGroupGraph"=>$usersByAgeGroupGraph];
            switch($filter) {
                case "year":
                    $data["year"] = $filter;
                    break;
                case "month":
                    $data["month"] = $filter;
                    break;
                case "week":
                    $data["week"] = $filter;
                    break;
                case "day":
                default:
                    $data["day"] = $filter;
                    break;
            }  
            return $data;
        }

    }

?>