<?php

    class AdminController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function read() {
            if(isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"]=="admin") {
                $filter = $_GET["filter"] ?? "day";
                $data = $this->getData($filter);
                $this->presenter->render("view/adminView.mustache", $data);
            }else {
                Redirect::to("/login/read");
            }
        }

        public function create() {
            if(isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"]=="admin") {
                $filter = $_GET["filter"] ?? "day";
                $data = $this->getData($filter);
                $html = $this->generateHtml($data);
                GeneratorPDF::generate($html);
            }else {
                Redirect::to("/login/read");
            }
        }

        private function generateHtml($data) {

            $imagePathCountry = realpath($data["usersByCountryGraph"]);
            $imageDataCountry = base64_encode(file_get_contents($imagePathCountry));
            $imageSrcCountry = 'data:image/png;base64,' . $imageDataCountry;

            $imagePathGender = realpath($data["usersByGenderGraph"]);
            $imageDataGender = base64_encode(file_get_contents($imagePathGender));
            $imageSrcGender = 'data:image/png;base64,' . $imageDataGender;

            $imagePathAgeGroup = realpath($data["usersByAgeGroupGraph"]);
            $imageDataAgeGroup = base64_encode(file_get_contents($imagePathAgeGroup));
            $imageSrcAgeGroup = 'data:image/png;base64,' . $imageDataAgeGroup;

            $html = '
                    <!DOCTYPE html>
                    <html lang="en">
                    <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Report</title>
                        <style>
                            body { font-family: Arial, sans-serif; }
                            .container { width: 100%; margin: 0 auto; }
                            .section { margin-bottom: 20px; }
                            .header h1 { font-size: 24px; }
                            .section h2 { font-size: 20px; margin-bottom: 10px; }
                            .graph { margin-bottom: 10px; }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <div class="section">
                                <h1>Estadisticas de Q&A</h1>
                                <p>Cantidad de jugadores que tiene la aplicacion: ' . $data["playersCount"] . '</p>
                                <p>Cantidad de partidas jugadas: ' . $data["gamesCount"] . '</p>
                                <p>Cantidad de preguntas en el juego: ' . $data["questionsCount"] . '</p>
                                <p>Cantidad de preguntas creadas: ' . $data["questionsCreated"] . '</p>
                                <p>Cantidad de usuarios nuevos: ' . $data["newUsers"] . '</p>
                            </div>
                            <div class="section">
                                <h2>Porcentaje de preguntas respondidas correctamente por usuario</h2>';             
                                foreach($data["correctPercentageGraph"] as $graph) {
                                    $imagePath = realpath($graph["graph"]);
                                    $imageData = base64_encode(file_get_contents($imagePath));
                                    $imageSrc = 'data:image/png;base64,' . $imageData;
                                    $html .= '<div class="graph"><img src="' . $imageSrc . '" alt="Correct Percentage Graph"></div>';
                                }
            $html .= '
                            </div>
                            <div class="section">
                                <h2>Distribucion de usuarios por pais</h2>
                                <div class="graph"><img src="' . $imageSrcCountry . '" alt="Users by Country Graph"></div>
                            </div>
                            <div class="section">
                                <h2>Distribucion de usuarios por genero</h2>
                                <div class="graph"><img src="' . $imageSrcGender . '" alt="Users by Gender Graph"></div>
                            </div>
                            <div class="section">
                                <h2>Distribucion de usuarios por grupo de edad</h2>
                                <div class="graph"><img src="' . $imageSrcAgeGroup . '" alt="Users by Age Group Graph"></div>
                            </div>
                        </div>
                    </body>
                    </html>';
            return $html;
        }

        private function getData($filter) {
            $currentDate = date('Y-m-d H:i:s');
            switch($filter) {
                case "year":
                    $lastDate = date('Y-m-d H:i:s', strtotime('-1 year'));
                    break;
                case "month":
                    $lastDate = date('Y-m-d H:i:s', strtotime('-1 month'));
                    break;
                case "week":
                    $lastDate = date('Y-m-d H:i:s', strtotime('-1 week'));
                    break;
                case "day":
                default:
                    $lastDate = date('Y-m-d H:i:s', strtotime('-1 day'));     
                    break;    
            }  
            $data = $this->setData($currentDate, $lastDate, $filter);       
            return $data;
        }

        private function setData($currentDate, $lastDate, $filter) {
            $playersCount = $this->model->getPlayersCount($currentDate, $lastDate);
            $gamesCount = $this->model->getGamesCount($currentDate, $lastDate);
            $questionsCount = $this->model->getQuestionsCount($currentDate, $lastDate);
            $questionsCreated = $this->model->getQuestionsCreated($currentDate, $lastDate);
            $newUsers = $this->model->getNewUsers($currentDate, $lastDate);            
            $correctPercentage = $this->model->getCorrectPercentage($currentDate, $lastDate);
            $usersByCountry = $this->model->getUsersByCountry($currentDate, $lastDate);
            $usersByGender = $this->model->getUsersByGender($currentDate, $lastDate);
            $usersByAgeGroup = $this->model->getUsersByAgeGroup($currentDate, $lastDate);
            $data = $this->createData($playersCount, $gamesCount, $questionsCount, $questionsCreated, $newUsers, $correctPercentage, $usersByCountry, $usersByGender, $usersByAgeGroup, $filter);
            return $data;
        }

        private function createData($playersCount, $gamesCount, $questionsCount, $questionsCreated, $newUsers, $correctPercentage, $usersByCountry, $usersByGender, $usersByAgeGroup, $filter) {
            $user = $_SESSION["usuario"];
            foreach($correctPercentage as $row) {
                $incorrectPercentage = 100 - $row["correctPercentage"];
                $correctPercentageGraph[]["graph"] = GeneratorGraph::generateCorrectPercentage($row["username"], $row["correctPercentage"], $incorrectPercentage);
            }
            $usersByCountryGraph = GeneratorGraph::generateUsersByCountry($usersByCountry);
            $usersByGenderGraph = GeneratorGraph::generateUsersByGender($usersByGender);
            $usersByAgeGroupGraph = GeneratorGraph::generateUsersByAgeGroup($usersByAgeGroup);
            $data = ["user"=>$user, "playersCount"=>$playersCount, "gamesCount"=>$gamesCount, "questionsCount"=>$questionsCount, "questionsCreated"=>$questionsCreated, "newUsers"=>$newUsers,
                    "correctPercentageGraph"=>$correctPercentageGraph, "usersByCountryGraph"=>$usersByCountryGraph, "usersByGenderGraph"=>$usersByGenderGraph, "usersByAgeGroupGraph"=>$usersByAgeGroupGraph];
            switch($filter) {
                case "year":
                    $data["year"] = $filter;
                    break;
                case "month":
                    $data["month"] = $filter;
                    break;
                case "week":
                    $data["week"] = $filter;
                    break;
                case "day":
                default:
                    $data["day"] = $filter;
                    break;
            }  
            return $data;
        }

    }

?>
