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
                $filter = $_GET["filter"] ?? "day";
                $data = $this->getData($filter);
                $this->presenter->render("view/adminView.mustache", $data);
            }else {
                Redirect::to("/login/read");
            }
        }

        public function create() {
            if(isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"]=="admin") {
                $filter = $_GET["filter"] ?? "day";
                $data = $this->getData($filter);
                GeneratorPDF::generate($data);
            }else {
                Redirect::to("/login/read");
            }
        }

        private function getData($filter) {
            $currentDate = date('Y-m-d H:i:s');
            switch($filter) {
                case "year":
                    $startDate = date('Y-m-d H:i:s', strtotime('-1 year'));
                    break;
                case "month":
                    $startDate = date('Y-m-d H:i:s', strtotime('-1 month'));
                    break;
                case "week":
                    $startDate = date('Y-m-d H:i:s', strtotime('-1 week'));
                    break;
                case "day":
                default:
                    $startDate = date('Y-m-d H:i:s', strtotime('-1 day'));     
                    break;    
            }  
            $data = $this->setData($currentDate, $startDate, $filter);       
            return $data;
        }

        private function setData($currentDate, $startDate, $filter) {
            $playersCount = $this->model->getPlayersCount();
            $gamesCount = $this->model->getGamesCount();
            $questionsCount = $this->model->getQuestionsCount();
            $questionsCreated = $this->model->getQuestionsCreated($currentDate, $startDate);
            $newUsers = $this->model->getNewUsers($currentDate, $startDate);            
            $correctPercentage = $this->model->getCorrectPercentage($currentDate, $startDate);
            $usersByCountry = $this->model->getUsersByCountry($currentDate, $startDate);
            $usersByGender = $this->model->getUsersByGender($currentDate, $startDate);
            $usersByAgeGroup = $this->model->getUsersByAgeGroup($currentDate, $startDate);
            $data = $this->createData($playersCount, $gamesCount, $questionsCount, $questionsCreated, $newUsers, $correctPercentage, $usersByCountry, $usersByGender, $usersByAgeGroup, $filter);
            return $data;
        }

        private function createData($playersCount, $gamesCount, $questionsCount, $questionsCreated, $newUsers, $correctPercentage, $usersByCountry, $usersByGender, $usersByAgeGroup, $filter) {
            $user = $_SESSION["usuario"];
            foreach($correctPercentage as $row) {
                $incorrectPercentage = 100 - $row["correctPercentage"];
                $correctPercentageGraph[]["graph"] = GeneratorGraph::generateCorrectPercentage($row["username"], $row["correctPercentage"], $incorrectPercentage);
            }
            $usersByCountryGraph = GeneratorGraph::generateUsersByCountry($usersByCountry);
            $usersByGenderGraph = GeneratorGraph::generateUsersByGender($usersByGender);
            $usersByAgeGroupGraph = GeneratorGraph::generateUsersByAgeGroup($usersByAgeGroup);
            $data = ["user"=>$user, "playersCount"=>$playersCount, "gamesCount"=>$gamesCount, "questionsCount"=>$questionsCount, "questionsCreated"=>$questionsCreated, "newUsers"=>$newUsers,
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
