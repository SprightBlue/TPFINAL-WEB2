<?php

    class PlayModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }
  
        public function getQuestion() {
            $usedQuestions = $_SESSION["preguntasUtilizadas"] ?? [];
            if(count($usedQuestions) >= $this->database->getCountQuestions()) {$usedQuestions = [];}
            $question = $this->database->getQuestionRandom($usedQuestions);
            if($question) {
                $usedQuestion[] = $question["idQuestion"];
                $_SESSION['preguntasUtilizadas'] = $usedQuestion;
                $answers = $this->database->getAnsers($question["idQuestion"]);
                return ["pregunta"=>$question["question"], "categoria"=>$question["category"], "respuestas"=>$answers];
            }
            return null;
        }
        
        public function saveGame($idUser, $score){
            $this->database->saveGame($idUser, $score);
        } 
  
    }

?>