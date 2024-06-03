<?php

    class PlayController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            if (isset($_SESSION['usuario'])) {
                if (isset($_SESSION["partida"]) && !empty($_SESSION["partida"])) {
                    $data = $_SESSION["partida"];
                }else {
                    unset($_SESSION["modal"]);
                    unset($_SESSION["preguntasUtilizadas"]);
                    $_SESSION["puntaje"] = 0;
                    $data = $this->getData();
                    $_SESSION["partida"] = $data;
                }
                $this->presenter->render("play", $data);                 
            }else {
                Redirect::to('/login/read');
            }
        }

        public function verify() {
            $correct = $_POST["esCorrecta"];
            if ($correct === "1") {
                $_SESSION["puntaje"] += 1;
                $data = $this->getData();
                $_SESSION["partida"] = $data;
            }else {
                $finalScore = ($_SESSION["puntaje"] === 0) ? $_SESSION["puntaje"] . " " : $_SESSION["puntaje"];
                $data = $_SESSION["partida"];
                unset($_SESSION["partida"]);
                $this->model->saveGame($_SESSION["usuario"]["id"], $finalScore);
                $data["modal"] = "$finalScore";
            }
            $this->presenter->render("play", $data);
        }    

        private function getData() {
            $qampa = $this->model->getQuestion();
            if($qampa) {
                $styles = ["Arte"=>"danger", "Ciencia"=>"success", "Deporte"=>"secondary", "Entretenimiento"=>"info", "Geografía"=>"primary", "Historia"=>"warning"];
                $categoryStyle = $styles[$qampa["categoria"]] ?? "light";
                return ["usuario"=>$_SESSION["usuario"], "pregunta"=>$qampa["pregunta"], "respuestas"=>$qampa["respuestas"], "categoria"=>$qampa["categoria"], "estilo" => $categoryStyle, "puntaje"=>$_SESSION["puntaje"] ?? 0];
            }
            return null;
        }

    }

?>