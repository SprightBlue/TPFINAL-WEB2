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

        public function query($sql) {
            return $this->conn->prepare($sql);
        }

        public function __destruct() {
            $this->conn = null;
        }      

    }

?>