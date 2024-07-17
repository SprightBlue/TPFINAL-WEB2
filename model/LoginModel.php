<?php

    class LoginModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function loginUser($username, $pass, &$errors) {
            $stmt = $this->database->query("SELECT * 
                                            FROM usuario 
                                            WHERE username = :username AND pass = :pass");
            $stmt->execute(array(":username"=>$username, ":pass"=>$pass));
            $user = ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
            if ($user == false) {
                $errors["validations"] = "El usuario y/o contraseña son incorrectos.";
            } else if ($user["active"] == 0) {
                $errors["active"] = "Debes verificar tu correo electrónico antes de poder iniciar sesión.";}
            return $user;
        }
        
        /*
        public function createSessionThirdParties($idEnterprise, $idUser, $startDate, $endDate) {
            $stmt = $this->database->query("INSERT INTO sesionTerceros (idEnterprise, idUser, startDate, endDate)
                                            VALUES (:idEnterprise, :idUser, :startDate, :endDate)");
            $stmt->execute(array(":idEnterprise"=>$idEnterprise, ":idUser"=>$idUser, ":startDate"=>$startDate, ":endDate"=>$endDate));
        }

        public function updateSessionThirdParties($idEnterprise, $idUser, $startDate, $endDate) {
            $stmt = $this->database->query("UPDATE sesionTerceros
                                            SET startDate = :startDate, endDate = :endDate
                                            WHERE idEnterprise = :idEnterprise AND idUser = :idUser");
            $stmt->execute(array(":idEnterprise"=>$idEnterprise, ":idUser"=>$idUser, ":startDate"=>$startDate, ":endDate"=>$endDate));
        }

        public function sessionThirdPartiesExists($idEnterprise, $idUser) {
            $stmt = $this->database->query("SELECT *
                                            FROM sesionTerceros
                                            WHERE idEnterprise = :idEnterprise AND idUser = :idUser");
            $stmt->execute(array(":idEnterprise"=>$idEnterprise, ":idUser"=>$idUser));
            return ($stmt->rowCount() > 0);
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
        */

    }
