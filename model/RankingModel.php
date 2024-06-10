<?php

    class RankingModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function getUserScore($idUser) {
            $currentDate = date('Y-m-d H:i:s');
            $oneMonthAgo = date('Y-m-d H:i:s', strtotime('-1 month'));
            $stmt = $this->database->query("SELECT MAX(p.score) AS maxScore
                                                FROM partida p
                                                WHERE p.idUser = :idUser AND p.dateGame BETWEEN :oneMonthAgo AND :currentDate");
            $stmt->execute(array(":idUser"=>$idUser, ":currentDate"=>$currentDate, "oneMonthAgo"=>$oneMonthAgo));
            if($stmt->rowCount() > 0) {
                $scoreUser = $stmt->fetch(PDO::FETCH_ASSOC);
                return $scoreUser;
            }
            return false;
        }   

        public function getMatchHistory($idUser) {
            $currentDate = date('Y-m-d H:i:s');
            $oneMonthAgo = date('Y-m-d H:i:s', strtotime('-1 month'));
            $stmt = $this->database->query("SELECT p.score AS score, p.dateGame AS dateGame
                                                FROM partida p
                                                WHERE p.idUser = :idUser AND p.dateGame BETWEEN :oneMonthAgo AND :currentDate
                                                ORDER BY p.dateGame DESC
                                                LIMIT 10");
            $stmt->execute(array(":idUser"=>$idUser, ":currentDate"=>$currentDate, "oneMonthAgo"=>$oneMonthAgo));
            if($stmt->rowCount() > 0) {
                $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $history;
            }
            return false;         
        }        
        
        public function getRankingScore() {
            $currentDate = date('Y-m-d H:i:s');
            $oneMonthAgo = date('Y-m-d H:i:s', strtotime('-1 month'));
            $stmt = $this->database->query("SELECT u.id AS id, u.username AS username, MAX(p.score) AS maxScore 
                                                FROM usuario u JOIN partida p ON u.id=p.idUser
                                                WHERE p.dateGame BETWEEN :oneMonthAgo AND :currentDate
                                                GROUP BY u.id, u.username
                                                ORDER BY maxScore DESC
                                                LIMIT 10");
            $stmt->execute(array(":currentDate"=>$currentDate, ":oneMonthAgo"=>$oneMonthAgo));
            if($stmt->rowCount() > 0) {
                $ranking = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $ranking;
            }
            return false;     
        }

    }

?>
