<?php

    class PlayController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            if(isset($_SESSION["usuario"])) {
                if(isset($_SESSION["partida"]) && !empty($_SESSION["partida"])) {
                    $data = $_SESSION["partida"];
                }else {
                    $_SESSION["puntaje"] = 0;
                    $_SESSION["preguntasUtilizadas"] = [];
                    unset($_SESSION["modal"]);
                    $data = $this->model->getData($_SESSION["preguntasUtilizadas"], $_SESSION["puntaje"]);
                    $_SESSION["partida"] = $data;
                    $_SESSION["startTime"] = time();

                }
                $this->presenter->render("view/playView.mustache", $data);                 
            }else {
                Redirect::to("/login/read");
            }
        }

        public function verify() {
            if(isset($_SESSION["usuario"])) {
                $isCorrect = isset($_POST["isCorrect"]) ? $_POST["isCorrect"] : null;
                $elapsedTime = isset($_SESSION["startTime"]) ? time() - $_SESSION["startTime"] : null;
                if ($isCorrect && $elapsedTime > 0) {
                    $_SESSION["puntaje"] += 1;
                    $data = $this->model->getData($_SESSION["preguntasUtilizadas"], $_SESSION["puntaje"]);
                    $_SESSION["partida"] = $data;
                    $data["gameOver"] = false;
                } else {
                    $finalScore = ($_SESSION["puntaje"] === 0) ? $_SESSION["puntaje"] . " " : $_SESSION["puntaje"];
                    $data = $_SESSION["partida"];
                    unset($_SESSION["partida"]);
                    $this->model->saveGame($_SESSION["usuario"]["id"], $finalScore);
                    $data["modal"] = $finalScore . "";
                    $data["gameOver"] = true;
                }
                $this->presenter->render("view/playView.mustache", $data);
            } else {
                Redirect::to("/login/read");
            }
        }
    }

?>