<?php

    use Dompdf\Dompdf;
    use Dompdf\Options;

    class GeneratorPDF {

        public static function generate($data) {

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
                        <h1>Estadísticas de Q&A</h1>
                        <p>Cantidad de jugadores que tiene la aplicación: ' . (isset($data["playersCount"]) ? $data["playersCount"] : '0') . '</p>
                        <p>Cantidad de partidas jugadas: ' . (isset($data["gamesCount"]) ? $data["gamesCount"] : '0') . '</p>
                        <p>Cantidad de preguntas en el juego: ' . (isset($data["questionsCount"]) ? $data["questionsCount"] : '0') . '</p>
                        <p>Cantidad de preguntas creadas: ' . (isset($data["questionsCreated"]) ? $data["questionsCreated"] : '0') . '</p>
                        <p>Cantidad de usuarios nuevos: ' . (isset($data["newUsers"]) ? $data["newUsers"] : '0') . '</p>
                    </div>
                    <div class="section">
                        <h2>Porcentaje de preguntas respondidas correctamente por usuario</h2>';
                        if (!empty($data["correctPercentageGraph"])) {
                            foreach($data["correctPercentageGraph"] as $row) {
                                if (isset($row["graph"])) {
                                    $imagePath = realpath($row["graph"]);
                                    if ($imagePath) {
                                        $imageData = base64_encode(file_get_contents($imagePath));
                                        $imageSrc = 'data:image/png;base64,' . $imageData;
                                        $html .= '<div class="graph"><img src="' . $imageSrc . '" alt="Correct Percentage Graph"></div>';
                                    }
                                }
                            }
                        } else {
                            $html .= '<div><caption>No hay datos disponibles.</caption></div>';
                        }
            $html .= '
                    </div>
                    <div class="section">
                        <h2>Distribución de usuarios por país</h2>';
                        if (isset($data["usersByCountryGraph"])) {
                            $imagePathCountry = realpath($data["usersByCountryGraph"]);
                            if ($imagePathCountry) {
                                $imageDataCountry = base64_encode(file_get_contents($imagePathCountry));
                                $imageSrcCountry = 'data:image/png;base64,' . $imageDataCountry;
                                $html .= '<div class="graph"><img src="' . $imageSrcCountry . '" alt="Users by Country Graph"></div>';
                            }
                        } else {
                            $html .= '<div><caption>No hay datos disponibles.</caption></div>';
                        }
            $html .= '
                    </div>
                    <div class="section">
                        <h2>Distribución de usuarios por género</h2>';
                        if (isset($data["usersByGenderGraph"])) {
                            $imagePathGender = realpath($data["usersByGenderGraph"]);
                            if ($imagePathGender) {
                                $imageDataGender = base64_encode(file_get_contents($imagePathGender));
                                $imageSrcGender = 'data:image/png;base64,' . $imageDataGender;
                                $html .= '<div class="graph"><img src="' . $imageSrcGender . '" alt="Users by Gender Graph"></div>';
                            }
                        } else {
                            $html .= '<div><caption>No hay datos disponibles.</caption></div>';
                        }
            $html .= '
                    </div>
                    <div class="section">
                        <h2>Distribución de usuarios por grupo de edad</h2>';
                        if (isset($data["usersByAgeGroupGraph"])) {
                            $imagePathAgeGroup = realpath($data["usersByAgeGroupGraph"]);
                            if ($imagePathAgeGroup) {
                                $imageDataAgeGroup = base64_encode(file_get_contents($imagePathAgeGroup));
                                $imageSrcAgeGroup = 'data:image/png;base64,' . $imageDataAgeGroup;
                                $html .= '<div class="graph"><img src="' . $imageSrcAgeGroup . '" alt="Users by Age Group Graph"></div>';
                            }
                        } else {
                            $html .= '<div><caption>No hay datos disponibles.</caption></div>';
                        }
            $html .= '
                    </div>
                    <div class="section">
                        <h2>Bonus Acumuladas por Usuario:</h2>';
                        if (!empty($data["userBonusCount"])) {
                            $html .= '<ul>';
                            foreach ($data["userBonusCount"] as $userBonus) {
                                if (isset($userBonus["username"]) && isset($userBonus["bonusCount"])) {
                                    $html .= '<li>' . htmlspecialchars($userBonus["username"]) . ' &raquo; ' . htmlspecialchars($userBonus["bonusCount"]) . '</li>';
                                }
                            } 
                            $html .= '</ul>';
                        } else {
                            $html .= '<div><caption>No hay datos disponibles.</caption></div>';
                        }
            $html .= '</div>
                    <div class="section">
                        <p>Ganancias de los Bonus: $' . ( isset($data["earningsBonus"]) ? $data["earningsBonus"] : '0') . '</p>';
            $html .= '</div>
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
