<?php

    class ProfileModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function getUser($username) {
            $stmt = $this->database->query("SELECT * FROM usuario 
                                            WHERE username=:username");
            $stmt->execute(array(":username"=>$username));
            if($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                return $user;
            }
            return false;
        }

    }

?>