<?php

    class ProfileModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function getUser($idUser) {
            $stmt = $this->database->query("SELECT *
                                            FROM usuario u
                                            WHERE u.id = :idUser AND u.active = 1");
            $stmt->execute(array(":idUser" => $idUser));
            return ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
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
