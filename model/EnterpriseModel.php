<?php

    class EnterpriseModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function getQuestionsCount($id) {
            $stmt = $this->database->query("SELECT COUNT(*) as questionsCount
                                            FROM pregunta p
                                            WHERE p.idCreator = :id");
            $stmt->execute(array(":id"=>$id));
            $preguntas = $stmt->fetch(PDO::FETCH_ASSOC);
            return $preguntas["questionsCount"];
        }

        public function createQuestion($question, $category, $idCreator, $answer1, $answer2, $answer3, $answer4, $correct) {
            $stmt = $this->database->query("INSERT INTO pregunta (question, category, idCreator) 
                                            VALUES (:question, :category, :idCreator)");
            $stmt->execute(array(":question" => $question, ":category" => $category, ":idCreator" => $idCreator));
            $idQuestion = $this->database->lastInsertId();
            $this->addAnswers($idQuestion, $answer1, $answer2, $answer3, $answer4, $correct);
        }

        private function addAnswers($idQuestion, $answer1, $answer2, $answer3, $answer4, $correct) {
            for ($i = 1; $i <= 4; $i++) {
                $answer = ${"answer" . $i};
                $isCorrect = ($i == $correct) ? 1 : 0;
                $stmt = $this->database->query("INSERT INTO respuesta (idQuestion, answer, correct) 
                                                VALUES (:idQuestion, :answer, :correct)");
                $stmt->execute(array(":idQuestion" => $idQuestion, ":answer" => $answer, ":correct" => $isCorrect));
            }
        }

        public function getSessionThirdParties($idEnterprise, $idUser, $currentTime) {
            $stmt = $this->database->query("SELECT *
                                            FROM sesionTerceros
                                            WHERE idEnterprise = :idEnterprise
                                            AND idUser = :idUser
                                            AND :currentTime BETWEEN startDate AND endDate");
            $stmt->execute(array(":idEnterprise"=>$idEnterprise, ":idUser"=>$idUser, ":currentTime"=>$currentTime));
            return ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        }

    }
