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
                }
                $this->presenter->render("view/playView.mustache", $data);                 
            }else {
                Redirect::to("/login/read");
            }
        }

        public function verify() {
            if(isset($_SESSION["usuario"])) {
                $isCorrect = $_POST["isCorrect"];
                $elapsedTime = time() - $_SESSION["startTime"]; // Calcula el tiempo transcurrido
                if ($isCorrect == 1 && $elapsedTime > 0) {
                    $_SESSION["puntaje"] += 1;
                    $data = $this->model->getData($_SESSION["preguntasUtilizadas"], $_SESSION["puntaje"]);
                    $_SESSION["partida"] = $data;
                } else {
                    $finalScore = ($_SESSION["puntaje"] === 0) ? $_SESSION["puntaje"] . " " : $_SESSION["puntaje"];
                    $data = $_SESSION["partida"];
                    unset($_SESSION["partida"]); // Elimina la partida actual de la sesión
                    $this->model->saveGame($_SESSION["usuario"]["id"], $finalScore); // Guarda el puntaje del juego y actualiza el puntaje total del usuario
                    $data["modal"] = $finalScore . "";

                }
                $this->presenter->render("view/playView.mustache", $data);
            } else {
                Redirect::to("/login/read");
            }
        }
    }

?>