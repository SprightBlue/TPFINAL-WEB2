<?php

    class PlayModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function getData($idUser, $score) {
            $answeredQuestions = $this->database->getAnsweredQuestions($idUser);
            if ($answeredQuestions <= 10) {
                $difficulty = 'easy';
            } else {
                $ratio = $this->database->getUserRatio($idUser);
                $difficulty = $this->getDifficulty($ratio);
            }
            $question = $this->database->getQuestionRandom($idUser, $difficulty);
            if(!$question) {
                // Si no se obtuvo ninguna pregunta, reinicia las preguntas del usuario
                $this->database->resetUserQuestions($idUser);
                // Intenta obtener una pregunta aleatoria nuevamente
                $question = $this->database->getQuestionRandom($idUser, $difficulty);
            }
            if($question) {
                $this->database->addUserQuestion($idUser, $question["idQuestion"]);
                $answers = $this->database->getAnswers($question["idQuestion"]);
                $styles = ["Arte"=>"primary", "Ciencia"=>"success", "Deporte"=>"info", "Entretenimiento"=>"warning", "Geografía"=>"danger", "Historia"=>"secondary"];
                $style = $styles[$question["category"]]??"light";
                $data = ["question"=>$question, "style"=>$style, "answers"=>$answers, "score"=>$score];
                return $data;
            }
            return false;
        }
        public function updateQuestionDifficulty($idQuestion) {
            $this->database->updateQuestionDifficulty($idQuestion);
        }
        public function incrementTotalAnswers($idQuestion) {
            $this->database->incrementTotalAnswers($idQuestion);
        }

        public function incrementUserAnsweredQuestions($idUser) {
            $this->database->incrementUserAnsweredQuestions($idUser);
        }
        public function incrementCorrectAnswers($idQuestion) {
            $this->database->incrementCorrectAnswers($idQuestion);
        }

        public function incrementUserCorrectAnswers($idUser) {
            $this->database->incrementUserCorrectAnswers($idUser);
        }

        private function getDifficulty($ratio) {
            if ($ratio < 0.3) {
                return 'hard';
            } else {
                return 'easy';
            }
        }



        public function saveGame($idUser, $score){
            $this->database->createGame($idUser, $score);
            $this->database->updateScore($idUser, $score);
        }
    }

?>