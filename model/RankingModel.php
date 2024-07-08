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
                                                FROM usuario u JOIN partida p ON u.id=p.idUser
                                                WHERE p.dateGame BETWEEN :oneMonthAgo AND :currentDate
                                                AND u.userRole = 'player'
                                                GROUP BY u.id, u.username
                                                ORDER BY maxScore DESC
                                                LIMIT 10");
            $stmt->execute(array(":currentDate"=>$currentDate, ":oneMonthAgo"=>$oneMonthAgo));
            return ($stmt->rowCount() > 0) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : false;   
        }   

        public function getEntorno($idTerceros, $idUsuario, $currentTime) {
            $stmt = $this->database->query("SELECT *
                                            FROM entorno e
                                            WHERE e.idTerceros = :idTerceros
                                            AND e.idUsuario = :idUsuario
                                            AND :currentTime BETWEEN e.inicio AND e.fin");
            $stmt->execute(array(":idTerceros"=>$idTerceros, ":idUsuario"=>$idUsuario, ":currentTime"=>$currentTime));
            return ($stmt->rowCount() > 0);
        }

        public function getNombreEntorno($idTerceros) {
            $stmt = $this->database->query("SELECT username
                                            FROM usuario
                                            WHERE id = :idTerceros");
            $stmt->execute(array(":idTerceros"=>$idTerceros));
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user["username"];
        }

        public function getUserScoreModoEntorno($idUser, $idEntorno) {
            $currentDate = date('Y-m-d H:i:s');
            $oneMonthAgo = date('Y-m-d H:i:s', strtotime('-1 month'));
            $stmt = $this->database->query("SELECT MAX(p.score) AS maxScore
                                            FROM partida p
                                            WHERE p.idUser = :idUser 
                                            AND p.idEntorno = :idEntorno
                                            AND p.dateGame BETWEEN :oneMonthAgo AND :currentDate");
            $stmt->execute(array(":idUser"=>$idUser, ":idEntorno"=>$idEntorno, ":currentDate"=>$currentDate, "oneMonthAgo"=>$oneMonthAgo));
            return ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        }

        public function getMatchHistoryModoEntorno($idUser, $idEntorno) {
            $currentDate = date('Y-m-d H:i:s');
            $oneMonthAgo = date('Y-m-d H:i:s', strtotime('-1 month'));
            $stmt = $this->database->query("SELECT p.score AS score, p.dateGame AS dateGame
                                                FROM partida p
                                                WHERE p.idUser = :idUser 
                                                AND p.idEntorno = :idEntorno
                                                AND p.dateGame BETWEEN :oneMonthAgo AND :currentDate
                                                ORDER BY p.dateGame DESC
                                                LIMIT 10");
            $stmt->execute(array(":idUser"=>$idUser, ":idEntorno"=>$idEntorno, ":currentDate"=>$currentDate, "oneMonthAgo"=>$oneMonthAgo));
            return ($stmt->rowCount() > 0) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : false;     
        }        
        
        public function getRankingScoreModoEntorno($idEntorno) {
            $currentDate = date('Y-m-d H:i:s');
            $oneMonthAgo = date('Y-m-d H:i:s', strtotime('-1 month'));
            $stmt = $this->database->query("SELECT u.id AS id, u.username AS username, MAX(p.score) AS maxScore 
                                                FROM usuario u JOIN partida p ON u.id=p.idUser
                                                WHERE p.idEntorno = :idEntorno
                                                AND p.dateGame BETWEEN :oneMonthAgo AND :currentDate
                                                AND u.userRole = 'player'
                                                GROUP BY u.id, u.username
                                                ORDER BY maxScore DESC
                                                LIMIT 10");
            $stmt->execute(array(":idEntorno"=>$idEntorno ,":currentDate"=>$currentDate, ":oneMonthAgo"=>$oneMonthAgo));
            return ($stmt->rowCount() > 0) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : false;   
        }  

    }
