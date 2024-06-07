<?php

    class Database {

        private $conn;

        public function __construct($host, $dbname, $username, $password) {
            try {
                $this->conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   
            } catch(Exception $e) {
                die("Connection failed: " . $e->getMessage());
            }     
        }


        public function createUser($fullname, $yearOfBirth, $gender, $country, $city, $email, $pass, $username, $profilePicture, $token, $score) {
            $stmt = $this->conn->prepare("INSERT INTO usuario(fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, token, active, score)
        VALUES(:fullname, :yearOfBirth, :gender, :country, :city, :email, :pass, :username, :profilePicture, :token, 0, :score)");
            $stmt->execute(array(":fullname"=>$fullname, ":yearOfBirth"=>$yearOfBirth, ":gender"=>$gender, ":country"=>$country, ":city"=>$city, ":email"=>$email, ":pass"=>$pass, ":username"=>$username, ":profilePicture"=>$profilePicture, ":token"=>$token, ":score"=>$score));
        }

        public function emailExists($email) {
            $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE email=:email");
            $stmt->execute(array(":email"=>$email));
            return $stmt->rowCount() > 0;
        }

        public function usernameExists($username) {
            $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE username=:username");
            $stmt->execute(array(":username"=>$username));
            return $stmt->rowCount() > 0;
        }

        public function activeUser($token) {
            $stmt = $this->conn->prepare("UPDATE usuario SET active=1 WHERE token=:token");
            $stmt->execute(array(":token"=>$token));
        }        
        
        public function getUser($username, $password) {
            $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE username=:username AND pass=:pass");
            $stmt->execute(array(":username"=>$username, ":pass"=>$password));
            if($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                return $user;
            }
            return false;
        }       
        
        public function getCountQuestions() {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM pregunta"); 
            $stmt->execute();
            return $stmt;    
        }

        public function getQuestionRandom($usedQuestions) {
            $placeholders = '';
            if(count($usedQuestions) > 0) {$placeholders = rtrim(str_repeat('?,', count($usedQuestions)), ',');}
            $sql = "SELECT * FROM pregunta " . (count($usedQuestions) > 0 ? "WHERE idQuestion NOT IN ($placeholders)" : "") . "ORDER BY RAND() LIMIT 1;";
            $stmt = $this->conn->prepare($sql); ;
            if(count($usedQuestions) > 0) {$stmt->execute($usedQuestions);}
            else{$stmt->execute();}
            if($stmt->rowCount() > 0) {
                $question = $stmt->fetch(PDO::FETCH_ASSOC);
                return $question;
            }
            return false;   
        }

        public function getAnswers($idQuestion) {
            $stmt = $this->conn->prepare("SELECT * FROM respuesta WHERE idQuestion=:idQuestion ORDER BY RAND()");
            $stmt->execute(array(":idQuestion"=>$idQuestion));
            if($stmt->rowCount() > 0) {
                $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $answers;
            }
            return false;
        }

        public function createGame($idUser, $score) {
            $stmt = $this->conn->prepare("INSERT INTO partida(score, dateGame, idUser) VALUES (:score, NOW(), :idUser)");
            $stmt->execute(array(":score"=>$score, ":idUser"=>$idUser));
        }

        public function updateScore($idUser, $score) {
            $stmt = $this->conn->prepare("UPDATE usuario SET score = score + :score WHERE id = :idUser");
            $stmt->execute(array(":idUser"=>$idUser, ":score"=>$score));
        }

        public function getScore($idUser) {
            $stmt = $this->conn->prepare("SELECT score FROM usuario WHERE id = :idUser");
            $stmt->execute(array(":idUser"=>$idUser));
            return $stmt->fetchColumn();
        }


        public function __destruct() {
            $this->conn = null;
        }

    }


?>