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
            return ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        }

        public function getEntorno($idTerceros, $idUsuario, $currentTime) {
            $stmt = $this->database->query("SELECT *
                                            FROM entorno e
                                            WHERE e.idTerceros = :idTerceros
                                            AND e.idUsuario = :idUsuario
                                            AND :currentTime BETWEEN e.inicio AND e.fin");
            $stmt->execute(array(":idTerceros"=>$idTerceros, ":idUsuario"=>$idUsuario, ":currentTime"=>$currentTime));
            return ($stmt->rowCount() > 0);
        }

    }
