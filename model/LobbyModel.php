<?php

    class LobbyModel {

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

        public function addSuggestQuestion($idUser, $question, $category, $answer1, $answer2, $answer3, $answer4, $correct) {
            $stmt = $this->database->query("INSERT INTO pregunta_sugerida (idUser, question, category, answer1, answer2, answer3, answer4, correct) 
                                            VALUES (:idUser, :question, :category, :answer1, :answer2, :answer3, :answer4, :correct)");
            $stmt->execute(array(":idUser"=>$idUser, ":question"=>$question, ":category"=>$category, ":answer1"=>$answer1, ":answer2"=>$answer2, ":answer3"=>$answer3, ":answer4"=>$answer4, ":correct"=>$correct));
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
        */

    }
