<?php

    class EntornoModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function getCantidadPreguntas($id) {
            $stmt = $this->database->query("SELECT COUNT(*) as cantidad
                                            FROM pregunta p
                                            WHERE p.idCreador = :id");
            $stmt->execute(array(":id"=>$id));
            $preguntas = $stmt->fetch(PDO::FETCH_ASSOC);
            return $preguntas["cantidad"];
        }

    }
