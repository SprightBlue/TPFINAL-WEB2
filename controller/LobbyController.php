<?php

    class LobbyController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            if(isset($_SESSION["usuario"])) {
                $user = $_SESSION["usuario"];
                $data = $this->getData($user);
                $this->presenter->render("view/lobbyView.mustache", $data);
            }else {
                Redirect::to("/login/read");
            }
        }

        public function close() {
            session_destroy();
            Redirect::to("/login/read");
        }

        private function getData($user) {
            $userScore = $this->model->getUserScore($user["id"]);
            $data = ["user"=>$user, "userScore"=>$userScore];
            return $data;
        }

    }

?>
