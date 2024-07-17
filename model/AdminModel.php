<?php

    class AdminModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function getPlayersCount() {
            $stmt = $this->database->query("SELECT COUNT(*) AS playersCount
                                            FROM usuario u
                                            WHERE idRole = 1");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result["playersCount"];
        }

        public function getGamesCount() {
            $stmt = $this->database->query("SELECT COUNT(*) AS gamesCount
                                            FROM partida p JOIN usuario u ON u.id = p.idUser
                                            WHERE u.idRole = 1");
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
                                            WHERE idRole = 1
                                            AND created BETWEEN :startDate AND :currentDate");
            $stmt->execute(array(":startDate"=>$startDate, ":currentDate"=>$currentDate));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result["newUsers"];
        }

        public function getCorrectPercentage($currentDate, $startDate) {
            $stmt = $this->database->query("SELECT username, (correctAnswers / answeredQuestions) * 100 AS correctPercentage
                                            FROM usuario
                                            WHERE answeredQuestions > 0 AND idRole = 1
                                            AND created BETWEEN :startDate AND :currentDate
                                            GROUP BY id, username, correctAnswers, answeredQuestions");
            $stmt->execute(array(":startDate"=>$startDate, ":currentDate"=>$currentDate));
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getUsersByCountry($currentDate, $startDate) {
            $stmt = $this->database->query("SELECT country, COUNT(*) AS usersCount
                                            FROM usuario
                                            WHERE idRole = 1
                                            AND created BETWEEN :startDate AND :currentDate
                                            GROUP BY country");
            $stmt->execute(array(":startDate" => $startDate, ":currentDate" => $currentDate));
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getUsersByGender($currentDate, $startDate) {
            $stmt = $this->database->query("SELECT gender, COUNT(*) AS usersCount
                                            FROM usuario
                                            WHERE idRole = 1
                                            AND created BETWEEN :startDate AND :currentDate
                                            GROUP BY gender");
            $stmt->execute(array(":startDate"=>$startDate, ":currentDate"=>$currentDate));
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getUsersByAgeGroup($currentDate, $startDate) {
            $stmt = $this->database->query("SELECT CASE
                                                WHEN yearOfBirth > YEAR(CURDATE()) - 18 THEN 'menor'
                                                WHEN yearOfBirth <= YEAR(CURDATE()) - 65 THEN 'jubilado'
                                                ELSE 'medio'
                                            END AS ageGroup, COUNT(*) AS usersCount
                                            FROM usuario
                                            WHERE idRole = 1
                                            AND created BETWEEN :startDate AND :currentDate
                                            GROUP BY ageGroup");            
            $stmt->execute(array(":startDate"=>$startDate, ":currentDate"=>$currentDate));
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getUserBonusCount($currentDate, $startDate) {
            $stmt = $this->database->query("SELECT u.username, SUM(cb.amount) AS bonusCount
                                            FROM compraBonus cb JOIN usuario u ON u.id = cb.idUser
                                            WHERE u.idRole = 1
                                            AND cb.created BETWEEN :startDate AND :currentDate
                                            GROUP BY u.id, u.username");
            $stmt->execute(array(":startDate"=>$startDate, ":currentDate"=>$currentDate));
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    
        public function getEarningsBonus($currentDate, $startDate) {
            $stmt = $this->database->query("SELECT SUM(cb.totalPrice) AS earningsBonus 
                                            FROM compraBonus cb JOIN usuario u ON u.id = cb.idUser
                                            WHERE u.idRole = 1
                                            AND cb.created BETWEEN :startDate AND :currentDate");
            $stmt->execute(array(":startDate"=>$startDate, ":currentDate"=>$currentDate));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result["earningsBonus"];
        }

        
        public function getSessionThirdParties($idEnterprise, $idUser, $currentTime) {
            $stmt = $this->database->query("SELECT *
                                            FROM sesionTerceros
                                            WHERE idEnterprise = :idEnterprise
                                            AND idUser = :idUser
                                            AND :currentTime BETWEEN startDate AND endDate");
            $stmt->execute(array(":idEnterprise"=>$idEnterprise, ":idUser"=>$idUser, ":currentTime"=>$currentTime));
            return ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        }
        

    }
