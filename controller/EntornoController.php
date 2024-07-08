<?php

    class EntornoController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            $this->verifyEmpresaSession();
            $user = $_SESSION["usuario"];
            $idEntorno = $_SESSION["usuario"]["id"];
            $cantidadPreguntas = $this->model->getCantidadPreguntas($idEntorno);
            if ($cantidadPreguntas >= 10) {
                if (!file_exists("public/qr-entorno/qr-entorno-" . $idEntorno . ".png")) {
                    $profileUrl = "http:/localhost/profile/active?idEntorno=$idEntorno";
                    $pathImg = "public/qr-entorno/qr-entorno-". $idEntorno . ".png";
                    GeneratorQR::generate($profileUrl, $pathImg);                     
                }
                $downloadLink = "/" . $pathImg;
                $this->presenter->render("view/entornoView.mustache", ["user"=>$user, "qr"=>$pathImg, "downloadLink" => $downloadLink]);                
            } else {
                $this->presenter->render("view/entornoView.mustache", ["user"=>$user, "mensaje"=>"La cantidad de preguntas creadas debe ser de almenos diez para poder descargar el qr."]);
            }   
        }

        public function create() {
            $this->verifyEmpresaSession();
            $user = $_SESSION["usuario"];
            $this->presenter->render("view/entornoPregunta.mustache", ["user"=>$user]);
        }

        public function set() {
            $this->verifyEmpresaSession();
            $user = $_SESSION["usuario"];
            if(isset($_POST["crear"])) {
                $idCreador = $_SESSION["usuario"]["id"];
                $question = $_POST["question"];
                $category = $_POST["category"];
                $answer1 = $_POST["answer1"];
                $answer2 = $_POST["answer2"];
                $answer3 = $_POST["answer3"];
                $answer4 = $_POST["answer4"];
                $correct = $_POST["correct"];
                $this->model->createPregunta($question, $category, $idCreador, $answer1, $answer2, $answer3, $answer4, $correct);
                $this->presenter->render("view/entornoPregunta.mustache", ["user"=>$user, "mensaje"=>"Pregunta Creada Exitosamente"]);                
            }else {
                Redirect::to("/entorno/create");
            }
        }

        private function verifyEmpresaSession() {
            if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["userRole"] != "empresa") {Redirect::to("/login/read");}  
        }

    }
