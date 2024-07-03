<?php

    class AdminModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function getPlayersCount($endDate) {
            $stmt = $this->database->query("SELECT COUNT(*) AS playersCount
                                    FROM usuario u
                                    WHERE u.dateCreated <= :endDate AND userRole = 'player'");
            $stmt->execute(array(":endDate"=>$endDate));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result["playersCount"];
        }

        public function getGamesCount($endDate) {
            $stmt = $this->database->query("SELECT COUNT(*) AS gamesCount
                                    FROM partida p JOIN usuario u ON u.id = p.idUser
                                    WHERE u.userRole = 'player' AND p.dateGame <= :endDate");
            $stmt->execute(array(":endDate"=>$endDate));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result["gamesCount"];
        }

        public function getQuestionsCount($endDate) {
            $stmt = $this->database->query("SELECT COUNT(*) AS questionsCount
                                    FROM pregunta p
                                    WHERE p.dateCreated <= :endDate");
            $stmt->execute(array(":endDate"=>$endDate));
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

        public function getTrampitasAcumuladasPorUsuario($currentDate, $startDate) {
            $stmt = $this->database->query("SELECT u.username AS nombreUsuario, SUM(vt.cantidad) AS trampitasAcumuladas
                                            FROM ventaTrampitas vt JOIN usuario u ON u.id = vt.idUsuario
                                            WHERE vt.fecha BETWEEN :startDate AND :currentDate
                                            AND u.userRole = 'player'
                                            GROUP BY u.id, u.username");
            $stmt->execute(array(":startDate"=>$startDate, ":currentDate"=>$currentDate));
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
    
        public function getGananciasTrampitas($currentDate, $startDate) {
            $stmt = $this->database->query("SELECT SUM(vt.precioTotal) AS gananciasTrampitas 
                                            FROM ventaTrampitas vt JOIN usuario u ON u.id = vt.idUsuario
                                            WHERE vt.fecha BETWEEN :startDate AND :currentDate
                                            AND u.userRole = 'player'");
            $stmt->execute(array(":startDate"=>$startDate, ":currentDate"=>$currentDate));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result["gananciasTrampitas"];
        }

    }

?>