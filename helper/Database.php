<?php

    class Database {

        private $conn;

        public function __construct($host, $dbname, $username, $password) {
            try {
                $this->conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   
            }catch(Exception $e) {
                die("Connection failed: " . $e->getMessage());
            }     
        }

        public function createUser($fullname, $yearOfBirth, $gender, $country, $city, $email, $pass, $username, $profilePicture, $token) {
            $stmt = $this->conn->prepare("INSERT INTO usuario(fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, token, active)
            VALUES(:fullname, :yearOfBirth, :gender, :country, :city, :email, :pass, :username, :profilePicture, :token, 0)");
            $stmt->execute(array(":fullname"=>$fullname, ":yearOfBirth"=>$yearOfBirth, ":gender"=>$gender, ":country"=>$country, ":city"=>$city, ":email"=>$email, ":pass"=>$pass, ":username"=>$username, ":profilePicture"=>$profilePicture, ":token"=>$token));
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
            if($stmt->rowCount()>0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                return $user;
            }else {
                return false;
            }
        }

        public function __destruct() {
            $this->conn = null;
        }

    }

