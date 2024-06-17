<?php

    class AdminController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            if(isset($_SESSION["usuario"]) && $_SESSION["usuario"]["role"]=="admin") {
                $filter = $_GET["filtro"] ?? "day";
                $data = $this->getData($filter);
                $this->presenter->render("/view/adminView.mustache", $data);
            }else {
                Redirect::to("/login/read");
            }
        }

        public function create() {
            if(isset($_SESSION["usuario"]) && $_SESSION["usuario"]["role"]=="admin") {
                $filter = $_GET["filter"] ?? "day";
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
                    $data = $this->setDataFilter($currentDate, $lastDate);
                    break;
                case "month":
                    $lastDate = date('Y-m-d H:i:s', strtotime('-1 month'));
                    $data = $this->setDataFilter($currentDate, $lastDate);
                    break;
                case "week":
                    $lastDate = date('Y-m-d H:i:s', strtotime('-1 week'));
                    $data = $this->setDataFilter($currentDate, $lastDate);
                    break;
                case "day":
                default:
                    $lastDate = date('Y-m-d H:i:s', strtotime('-1 day')); 
                    $data = $this->setDataDefault($currentDate, $lastDate);       
                    break;    
            }  
            return $data;
        }

        private function setDataDefault($currentDate, $lastDate) {
            $playersCount = $this->model->getPlayersCount();
            $gamesCount = $this->model->getGamesCount();
            $questionsCount = $this->model->getQuestionsCount();
            $questionsCreated = $this->model->getQuestionsCreated($currentDate, $lastDate);
            $newUsers = $this->model->getNewUsers($currentDate, $lastDate);
            $correctPercentage = $this->model->getCorrectPercentage();
            $usersByCountry = $this->model->getUsersByCountry();
            $usersByGender = $this->model->getUsersByGender();
            $usersByAgeGroup = $this->model->getUsersByAgeGroup();
            $data = $this->createData($playersCount, $gamesCount, $questionsCount, $questionsCreated, $newUsers, $correctPercentage, $usersByCountry, $usersByGender, $usersByAgeGroup);
            return $data;
        }

        private function setDataFilter($currentDate, $lastDate) {
            $playersCount = $this->model->getPlayersCountFilter($currentDate, $lastDate);
            $gamesCount = $this->model->getGamesCountFilter($currentDate, $lastDate);
            $questionsCount = $this->model->getQuestionsCountFilter($currentDate, $lastDate);
            $questionsCreated = $this->model->getQuestionsCreated($currentDate, $lastDate);
            $newUsers = $this->model->getNewUsers($currentDate, $lastDate);
            $correctPercentage = $this->model->getCorrectPercentageFilter($currentDate, $lastDate);
            $usersByCountry = $this->model->getUsersByCountryFilter($currentDate, $lastDate);
            $usersByGender = $this->model->getUsersByGenderFilter($currentDate, $lastDate);
            $usersByAgeGroup = $this->model->getUsersByAgeGroup($currentDate, $lastDate);
            $data = $this->createData($playersCount, $gamesCount, $questionsCount, $questionsCreated, $newUsers, $correctPercentage, $usersByCountry, $usersByGender, $usersByAgeGroup);
            return $data;
        }

        private function createData($playersCount, $gamesCount, $questionsCount, $questionsCreated, $newUsers, $correctPercentage, $usersByCountry, $usersByGender, $usersByAgeGroup) {
            foreach($correctPercentage as $row) {
                $incorrectPercentage = 100 - $row["correctPerncentage"];
                $correctPercentageGraph[] = GeneratorGraph::generateCorrectPercentage($row["username"], row["correctPercentage"], $incorrectPercentage);
            }
            $usersByCountryGraph = GeneratorGraph::generateUsersByCountry($usersByCountry);
            $usersByGenderGraph = GeneratorGraph::generateUsersByGender($usersByGender);
            $usersByAgeGroupGraph = GeneratorGraph::generateUsersByAgeGroup($usersByAgeGroup);
            $data = ["playersCount"=>$playersCount, "gamesCount"=>$gamesCount, "questionsCount"=>$questionsCount, "questionsCreated"=>$questionsCreated, "newUsers"=>$newUsers,
            "correctPercentageGraph"=>$correctPercentageGraph, "usersByCountryGraph"=>$usersByCountryGraph, "usersByGenderGraph"=>$usersByGenderGraph, "usersByAgeGroupGraph"=>$usersByAgeGroupGraph];
            return $data;
        }

    }

?>