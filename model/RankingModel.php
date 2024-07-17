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
                                            WHERE p.idUser = :idUser 
                                            AND p.dateGame BETWEEN :oneMonthAgo AND :currentDate");
            $stmt->execute(array(":idUser"=>$idUser, ":currentDate"=>$currentDate, "oneMonthAgo"=>$oneMonthAgo));
            return ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        }   

        public function getMatchHistory($idUser) {
            $currentDate = date('Y-m-d H:i:s');
            $oneMonthAgo = date('Y-m-d H:i:s', strtotime('-1 month'));
            $stmt = $this->database->query("SELECT p.score AS score, p.dateGame AS dateGame
                                                FROM partida p
                                                WHERE p.idUser = :idUser 
                                                AND p.dateGame BETWEEN :oneMonthAgo AND :currentDate
                                                ORDER BY p.dateGame DESC
                                                LIMIT 10");
            $stmt->execute(array(":idUser"=>$idUser, ":currentDate"=>$currentDate, "oneMonthAgo"=>$oneMonthAgo));
            return ($stmt->rowCount() > 0) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : false;     
        }        
        
        public function getRankingScore() {
            $currentDate = date('Y-m-d H:i:s');
            $oneMonthAgo = date('Y-m-d H:i:s', strtotime('-1 month'));
            $stmt = $this->database->query("SELECT u.id AS id, u.username AS username, MAX(p.score) AS maxScore 
                                                FROM usuario u JOIN partida p ON u.id = p.idUser
                                                WHERE u.idRole = 1
                                                AND p.dateGame BETWEEN :oneMonthAgo AND :currentDate 
                                                GROUP BY u.id, u.username
                                                ORDER BY maxScore DESC
                                                LIMIT 10");
            $stmt->execute(array(":currentDate"=>$currentDate, ":oneMonthAgo"=>$oneMonthAgo));
            return ($stmt->rowCount() > 0) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : false;   
        }   

        /* 
        public function getSessionThirdParties($idEnterprise, $idUser, $currentTime) {
            $stmt = $this->database->query("SELECT *
                                            FROM sesionTerceros
                                            WHERE idEnterprise = :idEnterprise
                                            AND idUser = :idUser
                                            AND :currentTime BETWEEN startDate AND endDate");
            $stmt->execute(array(":idEnterprise"=>$idEnterprise, ":idUser"=>$idUser, ":currentTime"=>$currentTime));
            return ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        }

        public function getNameThirdParties($idEnterprise) {
            $stmt = $this->database->query("SELECT username
                                            FROM usuario
                                            WHERE id = :idEnterprise");
            $stmt->execute(array(":idEnterprise"=>$idEnterprise));
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user["username"];
        }

        public function getUserScoreSessionThirdParties($idUser, $idEnterprise) {
            $currentDate = date('Y-m-d H:i:s');
            $oneMonthAgo = date('Y-m-d H:i:s', strtotime('-1 month'));
            $stmt = $this->database->query("SELECT MAX(p.score) AS maxScore
                                            FROM partida p
                                            WHERE p.idUser = :idUser AND p.idEnterprise = :idEnterprise
                                            AND p.dateGame BETWEEN :oneMonthAgo AND :currentDate");
            $stmt->execute(array(":idUser"=>$idUser, ":idEnterprise"=>$idEnterprise, ":currentDate"=>$currentDate, "oneMonthAgo"=>$oneMonthAgo));
            return ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        }

        public function getMatchHistorySessionThirdParties($idUser, $idEnterprise) {
            $currentDate = date('Y-m-d H:i:s');
            $oneMonthAgo = date('Y-m-d H:i:s', strtotime('-1 month'));
            $stmt = $this->database->query("SELECT p.score AS score, p.dateGame AS dateGame
                                                FROM partida p
                                                WHERE p.idUser = :idUser AND p.idEnterprise = :idEnterprise
                                                AND p.dateGame BETWEEN :oneMonthAgo AND :currentDate
                                                ORDER BY p.dateGame DESC
                                                LIMIT 10");
            $stmt->execute(array(":idUser"=>$idUser, ":idEnterprise"=>$idEnterprise, ":currentDate"=>$currentDate, "oneMonthAgo"=>$oneMonthAgo));
            return ($stmt->rowCount() > 0) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : false;     
        }        
        
        public function getRankingScoreSessionThirdParties($idEnterprise) {
            $currentDate = date('Y-m-d H:i:s');
            $oneMonthAgo = date('Y-m-d H:i:s', strtotime('-1 month'));
            $stmt = $this->database->query("SELECT u.id AS id, u.username AS username, MAX(p.score) AS maxScore 
                                                FROM usuario u JOIN partida p ON u.id=p.idUser
                                                WHERE p.idEnterprise = :idEnterprise AND u.idRole = 1
                                                AND p.dateGame BETWEEN :oneMonthAgo AND :currentDate
                                                GROUP BY u.id, u.username
                                                ORDER BY maxScore DESC
                                                LIMIT 10");
            $stmt->execute(array(":idEnterprise"=>$idEnterprise ,":currentDate"=>$currentDate, ":oneMonthAgo"=>$oneMonthAgo));
            return ($stmt->rowCount() > 0) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : false;   
        }  
        */            

    }
