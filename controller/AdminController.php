<?php

    class AdminController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            $this->verifyAdminSession();
            //$this->verifySessionThirdParties();
            $filter = $_GET["filter"] ?? "day";
            $data = $this->getData($_SESSION["usuario"], $filter);
            $this->presenter->render("view/adminView.mustache", $data);
        }

        public function create() {
            $this->verifyAdminSession();
            //$this->verifySessionThirdParties();
            $filter = $_GET["filter"] ?? "day";
            $data = $this->getData($_SESSION["usuario"], $filter);
            GeneratorPDF::generate($data);
        }

        private function getData($user, $filter) {
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
            
            $playersCount = $this->model->getPlayersCount();
            $gamesCount = $this->model->getGamesCount();
            $questionsCount = $this->model->getQuestionsCount();
            $questionsCreated = $this->model->getQuestionsCreated($currentDate, $startDate);
            $newUsers = $this->model->getNewUsers($currentDate, $startDate);            
            $correctPercentage = $this->model->getCorrectPercentage($currentDate, $startDate);
            $usersByCountry = $this->model->getUsersByCountry($currentDate, $startDate);
            $usersByGender = $this->model->getUsersByGender($currentDate, $startDate);
            $usersByAgeGroup = $this->model->getUsersByAgeGroup($currentDate, $startDate);
            $userBonusCount = $this->model->getUserBonusCount($currentDate, $startDate);
            $earningsBonus = $this->model->getEarningsBonus($currentDate, $startDate);

            $correctPercentageGraph = null;
            $usersByCountryGraph = null;
            $usersByGenderGraph = null;
            $usersByAgeGroupGraph = null;
            if(!empty($correctPercentage)) {
                foreach($correctPercentage as $row) {
                    $incorrectPercentage = 100 - $row["correctPercentage"];
                    $correctPercentageGraph[]["graph"] = GeneratorGraph::generateCorrectPercentage($row["username"], $row["correctPercentage"], $incorrectPercentage);
                }                
            }
            if (!empty($usersByCountry)) {
                $usersByCountryGraph = GeneratorGraph::generateUsersByCountry($usersByCountry);
            } 
            if (!empty($usersByGender)) {
                $usersByGenderGraph = GeneratorGraph::generateUsersByGender($usersByGender);
            }
            if (!empty($usersByAgeGroup)) {
                $usersByAgeGroupGraph = GeneratorGraph::generateUsersByAgeGroup($usersByAgeGroup);
            }
            $data = [
                        "user"=>$user, "filter"=>$filter, 
                        "playersCount"=>$playersCount, "gamesCount"=>$gamesCount, "questionsCount"=>$questionsCount, 
                        "questionsCreated"=>$questionsCreated, "newUsers"=>$newUsers, "correctPercentageGraph"=>$correctPercentageGraph, 
                        "usersByCountryGraph"=>$usersByCountryGraph, "usersByGenderGraph"=>$usersByGenderGraph, "usersByAgeGroupGraph"=>$usersByAgeGroupGraph,
                        "userBonusCount"=>$userBonusCount, "earningsBonus"=>$earningsBonus
                    ];
            return $data;            
        }
        
        private function verifyAdminSession() {
            if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["idRole"] != 3) {
                Redirect::to("/login/read");
            }  
        }

        /*
        private function verifySessionThirdParties() {
            if (isset($_SESSION["modoTerceros"])) {
                $currentTime = date("Y-m-d H:i:s");
                $session = $this->model->getSessionThirdParties($_SESSION["modoTerceros"]["idEnterprise"], $_SESSION["usuario"]["id"], $currentTime);
                if ($session == false) {
                    $_SESSION["modoTerceros"] = null;
                }
            } 
        }
        */
    
    }
