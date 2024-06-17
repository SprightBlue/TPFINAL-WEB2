<?php

    class GeneratorGraph {

        public static function generateCorrectPercentage($username, $correct, $incorrect) {

            $data = [$correct, $incorrect];
            $labels = ["Correctas ($correct%)", "Incorrectas ($incorrect%)"];
        
            $graph = new PieGraph(400, 300, "auto");
            $graph->SetScale("textlin");
            $graph->title->Set("Porcentaje de Respuestas Correctas de " . $username);

            $p1 = new PiePlot($data);
            $p1->SetLegends($labels);
        
            $graph->Add($p1);
        
            $fileName = "/public/graph/" . $username . "correctPercentageGraph.png";
            $graph->Stroke($fileName);
        
            return $fileName;

        }

        public static function generateUsersByCountry($countryData) {
            
            $data = [];
            $labels = [];
            foreach ($countryData as $country) {
                $data[] = $country["usersCount"];
                $labels[] = $country["country"];
            }
        
            $graph = new Graph(800, 600);
            $graph->SetScale("textlin");
        
            $graph->title->Set("Distribución de Usuarios por País");
            $graph->SetMargin(50, 30, 50, 100);
        
            
            $barplot = new BarPlot($data);
            $barplot->SetFillColor("blue");
        
            $graph->xaxis->SetTickLabels($labels);
            $graph->xaxis->SetLabelAngle(45);
        
            $graph->Add($barplot);
        
            $fileName = "/public/graph/usersByCountryGraph.png";
            $graph->Stroke($fileName);
        
            return $fileName;

        }

        public static function generateUsersByGender($genderData) {
            
            $data = [];
            $labels = [];
            foreach ($genderData as $gender) {
                $data[] = $gender["usersCount"];
                $labels[] = "" . $gender["gender"] . " (" . $gender["usersCount"] . ")";
            }
        
            $graph = new PieGraph(400, 300, "auto");
            $graph->SetScale("textlin");
            $graph->title->Set("Distribución de Usuarios por Género");
        
            $p1 = new PiePlot($data);
            $p1->SetLegends($labels);
        
            $graph->Add($p1);
        
            $fileName = "/public/graph/usersByGenderGraph.png";
            $graph->Stroke($fileName);
        
            return $fileName;
        
        }

        public static function generateUsersByAgeGroup($ageGroupData) {
            
            $data = [];
            $labels = [];
            foreach ($ageGroupData as $ageGroup) {
                $data[] = $ageGroup["usersCount"];
                $labels[] = "" . $ageGroup["ageGroup"] . " (" . $ageGroup["usersCount"] . ")";
            }
        
            $graph = new PieGraph(400, 300, "auto");
            $graph->SetScale("textlin");
            $graph->title->Set("Distribución de Usuarios por Grupo de Edad");
        
            $p1 = new PiePlot($data);
            $graph->Add($p1);
        
            $fileName = "/public/graph/usersByAgeGroupGraph.png";
            $graph->Stroke($fileName);
        
            return $fileName;
        
        }

    }

?>