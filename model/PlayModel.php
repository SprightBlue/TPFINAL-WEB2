<?php

    class PlayModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function getData($idUser, $score) {
            $answeredQuestions = $this->getAnsweredQuestions($idUser);
            if($answeredQuestions <= 10) {
                $difficulty = "easy";
            }else {
                $ratio = $this->getUserRatio($idUser);
                $difficulty = $this->getDifficulty($ratio);
            }
            $question = $this->getQuestionRandom($idUser, $difficulty);
            if($question == false) {
                $this->resetUserQuestions($idUser);
                $question = $this->getQuestionRandom($idUser, $difficulty);
            }
            $this->addUserQuestion($idUser, $question["idQuestion"]);
            $answers = $this->getAnswers($question["idQuestion"]);
            $user = $this->getUser($idUser);
            $styles = ["Arte"=>"primary", "Ciencia"=>"success", "Deporte"=>"info", "Entretenimiento"=>"warning", "Geografía"=>"danger", "Historia"=>"secondary"];
            $data = ["question"=>$question, "style"=>$styles[$question["category"]], "answers"=>$answers, "score"=>$score, "trampitas"=>$user["trampitas"]>0, "user"=>$user];
            return $data;
        }

        public function updateQuestionDifficulty($idQuestion) {
            $ratio = $this->getQuestionDifficulty($idQuestion);
            $difficulty = $this->getDifficulty($ratio);
            $stmt = $this->database->query("UPDATE pregunta 
                                            SET difficulty=:difficulty 
                                            WHERE idQuestion=:idQuestion");
            $stmt->execute(array(":idQuestion"=>$idQuestion, ":difficulty"=>$difficulty));
        }

        public function incrementTotalAnswers($idQuestion) {
            $stmt = $this->database->query("UPDATE pregunta 
                                            SET totalAnswers=totalAnswers+1 
                                            WHERE idQuestion=:idQuestion");
            $stmt->execute(array(":idQuestion"=>$idQuestion));
        }

        public function incrementUserAnsweredQuestions($idUser) {
            $stmt = $this->database->query("UPDATE usuario 
                                            SET answeredQuestions=answeredQuestions+1 
                                            WHERE id=:idUser");
            $stmt->execute(array(":idUser"=>$idUser));
        }

        public function incrementCorrectAnswers($idQuestion) {
            $stmt = $this->database->query("UPDATE pregunta 
                                            SET correctAnswers=correctAnswers+1 
                                            WHERE idQuestion=:idQuestion");
            $stmt->execute(array(":idQuestion"=>$idQuestion));
        }

        public function incrementUserCorrectAnswers($idUser) {
            $stmt = $this->database->query("UPDATE usuario 
                                            SET correctAnswers=correctAnswers+1 
                                            WHERE id=:idUser");
            $stmt->execute(array(":idUser"=>$idUser));
        }

        public function saveGame($idUser, $score){
            $stmt = $this->database->query("INSERT INTO partida(score, dateGame, idUser) 
                                            VALUES (:score, NOW(), :idUser)");
            $stmt->execute(array(":score"=>$score, ":idUser"=>$idUser));
        }

        private function getAnsweredQuestions($idUser) {
            $stmt = $this->database->query("SELECT answeredQuestions 
                                            FROM usuario 
                                            WHERE id=:idUser");
            $stmt->execute(array(":idUser"=>$idUser));
            return $stmt->fetchColumn();
        }

        private function getUserRatio($idUser) {
            $stmt = $this->database->query("SELECT correctAnswers, answeredQuestions 
                                            FROM usuario WHERE id=:idUser");
            $stmt->execute(array(":idUser"=>$idUser));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return ($result["answeredQuestions"] == 0) ? null : $result["correctAnswers"] / $result["answeredQuestions"];
        }

        private function getDifficulty($ratio) {
            return ($ratio < 0.3) ? "hard" : "easy";
        }

        private function getUserQuestions($idUser) {
            $stmt = $this->database->query("SELECT idPregunta 
                                            FROM usuario_pregunta 
                                            WHERE idUsuario=:idUser");
            $stmt->execute(array(":idUser"=>$idUser));
            return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        }

        private function getQuestionRandom($idUser, $difficulty) {
            $answeredQuestions = $this->getUserQuestions($idUser);
            if(empty($answeredQuestions)) {
                $stmt = $this->database->query("SELECT * 
                                                FROM pregunta 
                                                WHERE difficulty=:difficulty 
                                                ORDER BY RAND() 
                                                LIMIT 1");
                $stmt->execute(array(":difficulty"=>$difficulty));
            }else {
                $placeholders = implode(",", array_fill(0, count($answeredQuestions), "?"));
                $stmt = $this->database->query("SELECT * 
                                                FROM pregunta 
                                                WHERE idQuestion NOT IN ($placeholders) AND difficulty=? 
                                                ORDER BY RAND() 
                                                LIMIT 1");
                $stmt->execute(array_merge($answeredQuestions, [$difficulty]));
            }
            return ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        }

        private function resetUserQuestions($idUser) {
            $stmt = $this->database->query("DELETE FROM usuario_pregunta 
                                            WHERE idUsuario=:idUser");
            $stmt->execute(array(":idUser"=>$idUser));
        }

        private function addUserQuestion($idUser, $idQuestion) {
            $stmt = $this->database->query("INSERT INTO usuario_pregunta (idUsuario, idPregunta) 
                                            VALUES (:idUser, :idQuestion)");
            $stmt->execute(array(":idUser"=>$idUser, ":idQuestion"=>$idQuestion));
        }

        private function getAnswers($idQuestion) {
            $stmt = $this->database->query("SELECT * 
                                            FROM respuesta 
                                            WHERE idQuestion=:idQuestion 
                                            ORDER BY RAND()");
            $stmt->execute(array(":idQuestion"=>$idQuestion));
            return ($stmt->rowCount() > 0) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : false;
        }

        private function getQuestionDifficulty($idQuestion) {
            $stmt = $this->database->query("SELECT correctAnswers, totalAnswers 
                                            FROM pregunta 
                                            WHERE idQuestion=:idQuestion");
            $stmt->execute(array(":idQuestion"=>$idQuestion));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return ($result["totalAnswers"] == 0) ? null : $result["correctAnswers"] / $result["totalAnswers"];
        }

        public function insertReport($idUser, $idQuestion, $reason) {
            $stmt = $this->database->query("INSERT INTO report (idQuestion, idUser, reason) 
                                            VALUES (:idQuestion, :idUser, :reason)");
            $stmt->execute(array(":idQuestion"=>$idQuestion, ":idUser"=>$idUser, ":reason"=>$reason));
        }

        public function getQuestion($idQuestion) {
            $stmt = $this->database->query("SELECT * 
                                            FROM question 
                                            WHERE idQuestion = :idQuestion");
            $stmt->execute(array(":idQuestion" => $idQuestion));
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function getUser($idUsuario) {
            $stmt = $this->database->query("SELECT *
                                            FROM usuario u
                                            WHERE u.id = :idUsuario");
            $stmt->execute(array(":idUsuario"=>$idUsuario));
            return ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        }

        public function updateCantidadTrampitasUsuario($idUsuario) {
            $stmt = $this->database->query("UPDATE usuario u 
                                            SET u.trampitas = u.trampitas - 1 
                                            WHERE id = :idUsuario");
            $stmt->execute(array(':idUsuario' => $idUsuario));
        }

    }

?>
