<?php

    class LobbyModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function getScore($idUser) {
            return $this->database->getScore($idUser);
        }
    }

?>