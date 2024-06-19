<?php

    class AdminModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }     

        public function getPlayersCount($currentDate, $lastDate) {
            $stmt = $this->database->query("SELECT COUNT(*) AS playersCount
                                            FROM usuario
                                            WHERE userRole = 'player'");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result["playersCount"];
        }

        public function getGamesCount($currentDate, $lastDate) {
            $stmt = $this->database->query("SELECT COUNT(*) AS gamesCount
                                            FROM partida");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result["gamesCount"];            
        }

        public function getQuestionsCount($currentDate, $lastDate) {
            $stmt = $this->database->query("SELECT COUNT(*) AS questionsCount
                                            FROM pregunta");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result["questionsCount"];
        }

        public function getQuestionsCreated($currentDate, $lastDate) {
            $stmt = $this->database->query("SELECT COUNT(*) AS questionsCreated
                                            FROM pregunta
                                            WHERE dateCreated <= :currentDate 
                                            AND dateCreated >= :lastDate");
            $stmt->execute(array(":currentDate"=>$currentDate, ":lastDate"=>$lastDate));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result["questionsCreated"];
        }

        public function getNewUsers($currentDate, $lastDate) {
            $stmt = $this->database->query("SELECT COUNT(*) AS newUsers
                                            FROM usuario
                                            WHERE dateCreated <= :currentDate 
                                            AND dateCreated >= :lastDate
                                            AND userRole = 'player'");
            $stmt->execute(array(":currentDate"=>$currentDate, ":lastDate"=>$lastDate));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result["newUsers"];
        }

        public function getCorrectPercentage($currentDate, $lastDate) {
            $stmt = $this->database->query("SELECT username, (correctAnswers / answeredQuestions) * 100 AS correctPercentage
                                            FROM usuario
                                            WHERE answeredQuestions > 0
                                            AND userRole = 'player'
                                            GROUP BY username, correctAnswers, answeredQuestions");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }

        public function getUsersByCountry($currentDate, $lastDate) {
            $stmt = $this->database->query("SELECT country, COUNT(*) AS usersCount
                                            FROM usuario
                                            WHERE userRole = 'player'
                                            GROUP BY country");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }

        public function getUsersByGender($currentDate, $lastDate) {
            $stmt = $this->database->query("SELECT gender, COUNT(*) AS usersCount
                                            FROM usuario
                                            WHERE userRole = 'player'
                                            GROUP BY gender");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }

        public function getUsersByAgeGroup($currentDate, $lastDate) {
            $stmt = $this->database->query("SELECT CASE
                                                WHEN yearOfBirth > YEAR(CURDATE()) - 18 THEN 'menor'
                                                WHEN yearOfBirth <= YEAR(CURDATE()) - 65 THEN 'jubilado'
                                                ELSE 'medio'
                                            END AS ageGroup, COUNT(*) AS usersCount
                                            FROM usuario
                                            WHERE userRole = 'player'
                                            GROUP BY ageGroup");            
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }

    }

?>