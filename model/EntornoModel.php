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

        public function createPregunta($question, $category, $idCreador, $answer1, $answer2, $answer3, $answer4, $correct) {
            $stmt = $this->database->query("INSERT INTO pregunta (question, category, idCreador) VALUES (:question, :category, :idCreador)");
            $stmt->execute(array(":question" => $question, ":category" => $category, ":idCreador" => $idCreador));
            $idQuestion = $this->database->lastInsertId();
            $this->addAnswers($idQuestion, $answer1, $answer2, $answer3, $answer4, $correct);
        }

        private function addAnswers($idQuestion, $answer1, $answer2, $answer3, $answer4, $correct) {
            for ($i = 1; $i <= 4; $i++) {
                $answer = ${"answer" . $i};
                $isCorrect = ($i == $correct) ? 1 : 0;
                $stmt = $this->database->query("INSERT INTO respuesta (idQuestion, answer, correct) VALUES (:idQuestion, :answer, :correct)");
                $stmt->execute(array(":idQuestion" => $idQuestion, ":answer" => $answer, ":correct" => $isCorrect));
            }
        }

    }
