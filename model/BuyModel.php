<?php

    class BuyModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function buyTrampitas($idUsuario, $cantidad, $precioTotal) {
            $stmt = $this->database->query("INSERT INTO ventaTrampitas (idUsuario, cantidad, precioTotal) 
                                            VALUES (:idUsuario, :cantidad, :precioTotal)");
            $stmt->execute(array(':idUsuario' => $idUsuario, ':cantidad' => $cantidad, ':precioTotal' => $precioTotal));
        }

        public function updateCantidadTrampitasUsuario($idUsuario, $cantidad) {
            $stmt = $this->database->query("UPDATE usuario u 
                                            SET u.trampitas = u.trampitas + :cantidad 
                                            WHERE id = :idUsuario");
            $stmt->execute(array(':cantidad' => $cantidad, ':idUsuario' => $idUsuario));
        }

        public function getUser($idUsuario) {
            $stmt = $this->database->query("SELECT * 
                                            FROM usuario u 
                                            WHERE u.id=:idUsuario");
            $stmt->execute(array(":idUsuario"=>$idUsuario));
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
