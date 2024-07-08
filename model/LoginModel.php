<?php

    class LoginModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function loginUser($username, $pass, &$errors) {
            $stmt = $this->database->query("SELECT * 
                                            FROM usuario 
                                            WHERE username=:username 
                                            AND pass=:pass");
            $stmt->execute(array(":username"=>$username, ":pass"=>$pass));
            if($stmt->rowCount() > 0) {$user = $stmt->fetch(PDO::FETCH_ASSOC);}
            else {$user = false;}
            if($user == false) {$errors["validations"] = "El usuario y/o contraseña son incorrectos.";} 
            else if ($user["active"] == 0) {$errors["active"] = "Debes verificar tu correo electrónico antes de poder iniciar sesión.";}
            return $user;
        }
        
        public function addSuggestQuestion($idUser, $question, $category, $answer1, $answer2, $answer3, $answer4, $correct) {
            $stmt = $this->database->prepare("INSERT INTO pregunta_sugerida (idUser, question, category, answer1, answer2, answer3, answer4, correct) VALUES (:idUser, :question, :category, :answer1, :answer2, :answer3, :answer4, :correct)");
            $stmt->execute(array(":idUser"=>$idUser, ":question"=>$question, ":category"=>$category, ":answer1"=>$answer1, ":answer2"=>$answer2, ":answer3"=>$answer3, ":answer4"=>$answer4, ":correct"=>$correct));
        }

        public function createEntorno($idTerceros, $idUsuario, $startTime, $endTime) {
            $stmt = $this->database->query("INSERT INTO entorno (idTerceros, idUsuario, inicio, fin)
                                            VALUES (:idTerceros, :idUsuario, :startTime, :endTime)");
            $stmt->execute(array(":idTerceros"=>$idTerceros, ":idUsuario"=>$idUsuario, ":startTime"=>$startTime, ":endTime"=>$endTime));
        }

        public function getEntorno($idTerceros, $idUsuario, $currentTime) {
            $stmt = $this->database->query("SELECT *
                                            FROM entorno e
                                            WHERE e.idTerceros = :idTerceros
                                            AND e.idUsuario = :idUsuario
                                            AND :currentTime BETWEEN e.inicio AND e.fin");
            $stmt->execute(array(":idTerceros"=>$idTerceros, ":idUsuario"=>$idUsuario, ":currentTime"=>$currentTime));
            return ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        }

    }
