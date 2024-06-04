<?php

    class PlayModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }
  
        public function getData(&$usedQuestions, $score) {
            if(count($usedQuestions) >= $this->database->getCountQuestions()) {$usedQuestions = [];}
            $question = $this->database->getQuestionRandom($usedQuestions);
            if($question) {
                $usedQuestion[] = $question["idQuestion"];
                $answers = $this->database->getAnswers($question["idQuestion"]);
                $styles = ["Arte"=>"primary", "Ciencia"=>"success", "Deporte"=>"info", "Entretenimiento"=>"warning", "Geografía"=>"danger", "Historia"=>"secondary"];
                $style = $styles[$question["category"]]??"light";
                $data = ["question"=>$question, "style"=>$style, "answers"=>$answers, "score"=>$score];
                return $data;
            }
            return false;
        }
        
        public function saveGame($idUser, $score){
            $this->database->createGame($idUser, $score);
        } 
  
    }

?>