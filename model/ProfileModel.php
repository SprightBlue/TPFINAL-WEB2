<?php

    class ProfileModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function getUser($username) {
            $stmt = $this->database->query("SELECT usuario.*, genero.nombre AS generoNombre, pais.nombre AS paisNombre 
                                      FROM usuario 
                                      INNER JOIN genero ON usuario.idGenero = genero.id 
                                      INNER JOIN pais ON usuario.idPais = pais.id 
                                      WHERE usuario.username = :username");
            $stmt->execute(array(":username" => $username));
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                return $user;
            }
            return false;
        }

    }


