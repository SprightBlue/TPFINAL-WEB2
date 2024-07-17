<?php

    class EnterpriseController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            $this->verifyEnterpriseSession();
            $cantidadPreguntas = $this->model->getQuestionsCount($_SESSION["usuario"]["id"]);
            if ($cantidadPreguntas >= 10) {
                if (!file_exists("public/qr-thirdParties/qr-thirdParties-" . $_SESSION["usuario"]["id"] . ".png")) {
                    $profileUrl = "http:/localhost/login/active?idThirdParties=" . $_SESSION["usuario"]["id"];
                    $pathImg = "public/qr-thirdParties/qr-thirdParties-". $_SESSION["usuario"]["id"] . ".png";
                    GeneratorQR::generate($profileUrl, $pathImg);                     
                }
                $downloadLink = "/" . $pathImg;
                $this->presenter->render("view/enterpriseView.mustache", ["user"=>$_SESSION["usuario"], "qr"=>$pathImg, "downloadLink" => $downloadLink]);                
            } else {
                $this->presenter->render("view/enterpriseView.mustache", ["user"=>$_SESSION["usuario"], "mensaje"=>"La cantidad de preguntas creadas debe ser de almenos diez."]);
            }   
        }

        public function create() {
            $this->verifyEnterpriseSession();
            $this->presenter->render("view/enterpriseCreateQuestion.mustache", ["user"=>$_SESSION["usuario"]]);
        }

        public function set() {
            $this->verifyEnterpriseSession();
            if(isset($_POST["crear"])) {
                $this->model->createQuestion($_POST["question"], $_POST["category"], $_POST["idCreator"], $_POST["answer1"], $_POST["answer2"], $_POST["answer3"], $_POST["answer4"], $_POST["correct"]);
                $this->presenter->render("view/enterpriseCreateQuestion.mustache", ["user"=>$_SESSION["usuario"], "mensaje"=>"Pregunta Creada Exitosamente."]);                
            }else {
                Redirect::to("/enterprise/create");
            }
        }

        private function verifyEnterpriseSession() {
            if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["idRole"] != 4) {
                Redirect::to("/login/read");
            }  
        }

    }
