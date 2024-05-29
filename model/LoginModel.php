<?php

    class LoginModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function loginUser($username, $pass) {
            return $this->database->getUser($username, $pass);
        }

    }

?>