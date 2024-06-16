<?php

    class AdminModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function getPlayersCount() {
            $stmt = $this->database->query("SELECT COUNT(*) AS playersCount
                                            FROM usuario
                                            WHERE userRole = 'player'");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result["playersCount"];
        }      

        public function getPlayersCountFilter($currentDate, $lastDate) {
            $stmt = $this->database->query("SELECT COUNT(*) AS playersCount
                                            FROM usuario
                                            WHERE userRole = 'player'
                                            AND dateCreated NOT BETWEEN :currentDate AND :lastDate");
            $stmt->execute(array(":currentDate"=>$currentDate, ":lastDate"=>$lastDate));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result["playersCount"];
        }

        public function getGamesCount() {
            $stmt = $this->database->query("SELECT COUNT(*) AS gamesCount
                                            FROM partida");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result["gamesCount"];            
        }

        public function getGamesCountFilter($currentDate, $lastDate) {
            $stmt = $this->database->query("SELECT COUNT(*) AS gamesCount
                                            FROM partida
                                            WHERE dateGame NOT BETWEEN :currentDate AND :lastDate");
            $stmt->execute(array(":currentDate"=>$currentDate, ":lastDate"=>$lastDate));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result["gamesCount"];            
        }

        public function getQuestionsCount() {
            $stmt = $this->database->query("SELECT COUNT(*) AS questionsCount
                                            FROM pregunta");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result["questionsCount"];
        }

        public function getQuestionsCountFilter($currentDate, $lastDate) {
            $stmt = $this->database->query("SELECT COUNT(*) AS questionsCount
                                            FROM pregunta
                                            WHERE dateCreated NOT BETWEEN :currentDate AND :lastDate");
            $stmt->execute(array(":currentDate"=>$currentDate, ":lastDate"=>$lastDate));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result["questionsCount"];
        }

        public function getQuestionsCreated($currentDate, $lastDate) {
            $stmt = $this->database->query("SELECT COUNT(*) AS questionsCreated
                                            FROM pregunta
                                            WHERE dateCreated BETWEEN :currentDate AND :lastDate");
            $stmt->execute(array(":currentDate"=>$currentDate, ":lastDate"=>$lastDate));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result["questionsCreated"];
        }

        public function getNewUsers($currentDate, $lastDate) {
            $stmt = $this->database->query("SELECT COUNT(*) AS newUsers
                                            FROM usuario
                                            WHERE dateCreated BETWEEN :currentDate AND :lastDate");
            $stmt->execute(array(":currentDate"=>$currentDate, ":lastDate"=>$lastDate));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result["newUsers"];
        }

        public function getCorrectPercentage() {
            $stmt = $this->database->query("SELECT username, (correctAnswers / answeredQuestions) * 100 AS correctPercentage
                                            FROM usuario
                                            WHERE answeredQuestions > 0");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        }

        public function getCorrectPercentageFilter($currentDate, $lastDate) {
            $stmt = $this->database->query("SELECT username, (correctAnswers / answeredQuestions) * 100 AS correctPercentage
                                            FROM usuario
                                            WHERE answeredQuestions > 0
                                            AND dateCreated NOT BETWEEN :currentDate AND :lastDate");
            $stmt->execute(array(":currentDate"=>$currentDate, ":lastDate"=>$lastDate));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        }

        public function getUsersByCountry() {
            $stmt = $this->database->query("SELECT country, COUNT(*) AS usersCount
                                            FROM usuario");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        }

        public function getUsersByCountryFilter($currentDate, $lastDate) {
            $stmt = $this->database->query("SELECT country, COUNT(*) AS usersCount
                                            FROM usuario
                                            WHERE dateCreated NOT BETWEEN :currentDate AND :lastDate");
            $stmt->execute(array(":currentDate"=>$currentDate, ":lastDate"=>$lastDate));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        }

        public function getUsersByGender() {
            $stmt = $this->database->query("SELECT gender, COUNT(*) AS usersCount
                                            FROM usuario");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        }

        public function getUsersByGenderFilter($currentDate, $lastDate) {
            $stmt = $this->database->query("SELECT gender, COUNT(*) AS usersCount
                                            FROM usuario
                                            WHERE dateCreated NOT BETWEEN :currentDate AND :lastDate");
            $stmt->execute(array(":currentDate"=>$currentDate, ":lastDate"=>$lastDate));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        }

        public function getUsersByAgeGroup() {
            $stmt = $this->database->query("SELECT CASE
                                                WHEN yearOfBirth > YEAR(CURDATE()) - 18 THEN 'menor'
                                                WHEN yearOfBirth <= YEAR(CURDATE()) - 65 THEN 'jubilado'
                                                ELSE 'medio'
                                            END AS ageGroup, COUNT(*) AS usersCount
                                            FROM usuario
                                            GROUP BY ageGroup");            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        }

        public function getUsersByAgeGroupFilter($currentDate, $lastDate) {
            $stmt = $this->database->query("SELECT CASE
                                                WHEN yearOfBirth > YEAR(CURDATE()) - 18 THEN 'menor'
                                                WHEN yearOfBirth <= YEAR(CURDATE()) - 65 THEN 'jubilado'
                                                ELSE 'medio'
                                            END AS ageGroup, COUNT(*) AS usersCount
                                            FROM usuario
                                            WHERE dateCreated NOT BETWEEN :currentDate AND :lastDate
                                            GROUP BY ageGroup");            
            $stmt->execute(array(":currentDate"=>$currentDate, ":lastDate"=>$lastDate));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        }

    }

?>