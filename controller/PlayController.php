<?php

    class PlayController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }


        public function read() {
            if(!$this->isUserLoggedIn()) {
                $this->redirectToLoginPage();
                return;
            }

            if($this->isGameInProgress()) {
                $gameData = $_SESSION["partida"];
            } else {
                $this->startNewGame();
                $gameData = $this->model->getData($_SESSION["preguntasUtilizadas"], $_SESSION["puntaje"]);
                $_SESSION["partida"] = $gameData;
                $_SESSION["startTime"] = time();
            }

            $this->presenter->render("view/playView.mustache", $gameData);
        }

        public function verify() {
            if(!$this->isUserLoggedIn()) {
                $this->redirectToLoginPage();
                return;
            }

            $isCorrect = $this->isAnswerCorrect();
            $elapsedTime = $this->getElapsedTime();

            if ($isCorrect && $elapsedTime > 0) {
                $this->incrementScore();
                $gameData = $this->model->getData($_SESSION["preguntasUtilizadas"], $_SESSION["puntaje"]);
                $_SESSION["partida"] = $gameData;
                $gameData["gameOver"] = false;
            } else {
                $finalScore = $this->getFinalScore();
                $gameData = $_SESSION["partida"];
                unset($_SESSION["partida"]);
                $this->model->saveGame($_SESSION["usuario"]["id"], $finalScore);
                $gameData["modal"] = $finalScore . "";
                $gameData["gameOver"] = true;
            }

            $this->presenter->render("view/playView.mustache", $gameData);
        }


private function isUserLoggedIn() {
    return isset($_SESSION["usuario"]);
}

private function redirectToLoginPage() {
    Redirect::to("/login/read");
}

private function isGameInProgress() {
    return isset($_SESSION["partida"]) && !empty($_SESSION["partida"]);
}

private function startNewGame() {
    $_SESSION["puntaje"] = 0;
    $_SESSION["preguntasUtilizadas"] = [];
    unset($_SESSION["modal"]);
}

private function isAnswerCorrect() {
    return isset($_POST["isCorrect"]) ? $_POST["isCorrect"] : null;
}

private function getElapsedTime() {
    return isset($_SESSION["startTime"]) ? time() - $_SESSION["startTime"] : null;
}

private function incrementScore() {
    $_SESSION["puntaje"] += 1;
}

private function getFinalScore() {
    return ($_SESSION["puntaje"] === 0) ? $_SESSION["puntaje"] . " " : $_SESSION["puntaje"];
}
}

?>