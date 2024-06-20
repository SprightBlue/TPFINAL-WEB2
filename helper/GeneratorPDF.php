<?php

    use Dompdf\Dompdf;
    use Dompdf\Options;

    class GeneratorPDF {

        public static function generate($data) {

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

            $options = new Options();
            $options->set("isRemoteEnabled", true);
            $options->set("isHtml5ParserEnabled", true);
            
            $dompdf = new Dompdf($options);

            $dompdf->loadHtml($html);
            $dompdf->setPaper("A4", "portrait");
            $dompdf->render();

            $dompdf->stream("REPORTE.pdf", ["Attachment" => 0]);

        }

    }

?>