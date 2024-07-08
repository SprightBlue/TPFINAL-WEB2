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
            $idEntorno = $_SESSION["usuario"]["id"];
            $cantidadPreguntas = $this->model->getCantidadPreguntas($idEntorno);
            if ($cantidadPreguntas >= 10) {
                if (!file_exists("public/qr-entorno/qr-entorno-" . $idEntorno . ".png")) {
                    $profileUrl = "http:/localhost/profile/active?idEntorno=$idEntorno";
                    $pathImg = "public/qr-entorno/qr-entorno-". $idEntorno . ".png";
                    GeneratorQR::generate($profileUrl, $pathImg);                     
                }
                $downloadLink = "/" . $pathImg;
                $this->presenter->render("view/entornoView.mustache", ["qr"=>$pathImg, "downloadLink" => $downloadLink]);                
            } else {
                $this->presenter->render("view/entornoView.mustache", ["mensaje"=>"La cantidad de preguntas creadas debe ser de almenos diez para poder descargar el qr."]);
            }   
        }

        private function verifyEmpresaSession() {
            if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["userRole"] != "empresa") {Redirect::to("/login/read");}  
        }

    }
