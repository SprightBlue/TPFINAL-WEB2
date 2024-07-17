<?php

    class EditorModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function getSuggestedQuestions() {
            $stmt = $this->database->query("SELECT * 
                                            FROM pregunta_sugerida");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getSuggestedQuestion($idSuggestion) {
            $stmt = $this->database->query("SELECT * 
                                            FROM pregunta_sugerida 
                                            WHERE idSuggestion = :idSuggestion");
            $stmt->execute(array(":idSuggestion" => $idSuggestion));
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        /**
        * @throws Exception
        */
        public function acceptSuggestion($suggestedQuestion) {
            $this->database->beginTransaction();
            try {
                $stmt = $this->database->query("INSERT INTO pregunta (question, category) 
                                                VALUES (:question, :category)");
                $stmt->execute(array(":question" => $suggestedQuestion["question"], ":category" => $suggestedQuestion["category"]));
                $idQuestion = $this->database->lastInsertId();

                for ($i = 1; $i <= 4; $i++) {
                    $stmt = $this->database->query("INSERT INTO respuesta (idQuestion, answer, correct) 
                                                    VALUES (:idQuestion, :answer, :correct)");
                    $correct = ($i == intval($suggestedQuestion["correct"])) ? 1 : 0;
                    $stmt->execute(array(":idQuestion" => $idQuestion, ":answer" => $suggestedQuestion["answer" . $i], ":correct" => $correct));
                }

                $this->denySuggestion($suggestedQuestion["idSuggestion"]);

                $this->database->commit();

            } catch (Exception $e) {
                $this->database->rollBack();
                throw $e;
            }
        }

        public function denySuggestion($idSuggestion) {
            $stmt = $this->database->query("DELETE FROM pregunta_sugerida 
                                            WHERE idSuggestion = :idSuggestion");
            $stmt->execute(array(":idSuggestion" => $idSuggestion));
        }

        public function getQuestions() {
            $stmt = $this->database->query("SELECT * 
                                            FROM pregunta");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function createQuestion($question, $category, $answer1, $answer2, $answer3, $answer4, $correct) {
            $stmt = $this->database->query("INSERT INTO pregunta (question, category) 
                                            VALUES (:question, :category)");
            $stmt->execute(array(":question" => $question, ":category" => $category));
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
    
        public function deleteQuestion($idQuestion) {
            $stmt = $this->database->query("DELETE FROM pregunta 
                                            WHERE idQuestion = :idQuestion");
            $stmt->execute(array(":idQuestion"=>$idQuestion));
        }
    
        public function deleteAnswers($idQuestion) {
            $stmt = $this->database->query("DELETE FROM respuesta 
                                            WHERE idQuestion = :idQuestion");
            $stmt->execute(array(":idQuestion"=>$idQuestion));
        }
    
        public function updateQuestion($idQuestion, $question, $category, $answer1, $answer2, $answer3, $answer4, $correct) {
            $stmt = $this->database->query("UPDATE pregunta 
                                            SET question = :question, category = :category 
                                            WHERE idQuestion = :idQuestion");
            $stmt->execute(array(":question" => $question, ":category" => $category, ":idQuestion" => $idQuestion));
            $this->updateAnswers($idQuestion, $answer1, $answer2, $answer3, $answer4, $correct);
        }

        private function updateAnswers($idQuestion, $answer1, $answer2, $answer3, $answer4, $correct) {
            $answers = [$answer1, $answer2, $answer3, $answer4];
            $currentAnswers = $this->getAnswers($idQuestion);

            for ($i = 0; $i < 4; $i++) {
                $isCorrect = ($i + 1 == $correct) ? 1 : 0;
                $idAnswer = $currentAnswers[$i]['idAnswer'];
                $stmt = $this->database->query("UPDATE respuesta 
                                                SET answer = :answer, correct = :correct 
                                                WHERE idQuestion = :idQuestion AND idAnswer = :idAnswer");
                $stmt->execute(array(":answer" => $answers[$i], ":correct" => $isCorrect, ":idQuestion" => $idQuestion, ":idAnswer" => $idAnswer));
            }
        }

        public function getQuestion($idQuestion) {
            $stmt = $this->database->query("SELECT * 
                                            FROM pregunta 
                                            WHERE idQuestion = :idQuestion");
            $stmt->execute(array(":idQuestion" => $idQuestion));
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function getAnswers($idQuestion) {
            $stmt = $this->database->query("SELECT * 
                                            FROM respuesta 
                                            WHERE idQuestion = :idQuestion 
                                            ORDER BY idAnswer");
            $stmt->execute(array(":idQuestion" => $idQuestion));
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getReportedQuestions() {
            $stmt = $this->database->query("SELECT report.*, pregunta.question, usuario.username 
                                            FROM report 
                                            JOIN pregunta ON report.idQuestion = pregunta.idQuestion 
                                            JOIN usuario ON report.idUser = usuario.id");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function ignoreReport($idReport) {
            $stmt = $this->database->query("DELETE FROM report 
                                            WHERE idReport = :idReport");
            $stmt->execute(array(":idReport" => $idReport));
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
