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

        public function getGamesCount() {
            $stmt = $this->database->query("SELECT COUNT(*) AS gamesCount
                                            FROM partida");
            $stmt->execute();
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

        public function getQuestionsCreated($currentDate, $startDate) {
            $stmt = $this->database->query("SELECT COUNT(*) AS questionsCreated
                                            FROM pregunta
                                            WHERE dateCreated BETWEEN :startDate AND :currentDate");
            $stmt->execute(array(":startDate"=>$startDate, ":currentDate"=>$currentDate));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result["questionsCreated"];
        }

        public function getNewUsers($currentDate, $startDate) {
            $stmt = $this->database->query("SELECT COUNT(*) AS newUsers
                                            FROM usuario
                                            WHERE userRole = 'player'
                                            AND dateCreated BETWEEN :startDate AND :currentDate");
            $stmt->execute(array(":startDate"=>$startDate, ":currentDate"=>$currentDate));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result["newUsers"];
        }

        public function getCorrectPercentage($currentDate, $startDate) {
            $stmt = $this->database->query("SELECT username, (correctAnswers / answeredQuestions) * 100 AS correctPercentage
                                            FROM usuario
                                            WHERE answeredQuestions > 0
                                            AND userRole = 'player'
                                            AND dateCreated BETWEEN :startDate AND :currentDate
                                            GROUP BY username, correctAnswers, answeredQuestions");
            $stmt->execute(array(":startDate"=>$startDate, ":currentDate"=>$currentDate));
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }

        public function getUsersByCountry($currentDate, $startDate) {
            $stmt = $this->database->query("SELECT country, COUNT(*) AS usersCount
                                            FROM usuario
                                            WHERE userRole = 'player'
                                            AND dateCreated BETWEEN :startDate AND :currentDate
                                            GROUP BY country");
            $stmt->execute(array(":startDate"=>$startDate, ":currentDate"=>$currentDate));
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }

        public function getUsersByGender($currentDate, $startDate) {
            $stmt = $this->database->query("SELECT gender, COUNT(*) AS usersCount
                                            FROM usuario
                                            WHERE userRole = 'player'
                                            AND dateCreated BETWEEN :startDate AND :currentDate
                                            GROUP BY gender");
            $stmt->execute(array(":startDate"=>$startDate, ":currentDate"=>$currentDate));
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }

        public function getUsersByAgeGroup($currentDate, $startDate) {
            $stmt = $this->database->query("SELECT CASE
                                                WHEN yearOfBirth > YEAR(CURDATE()) - 18 THEN 'menor'
                                                WHEN yearOfBirth <= YEAR(CURDATE()) - 65 THEN 'jubilado'
                                                ELSE 'medio'
                                            END AS ageGroup, COUNT(*) AS usersCount
                                            FROM usuario
                                            WHERE userRole = 'player'
                                            AND dateCreated BETWEEN :startDate AND :currentDate
                                            GROUP BY ageGroup");            
            $stmt->execute(array(":startDate"=>$startDate, ":currentDate"=>$currentDate));
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }

    }

?>