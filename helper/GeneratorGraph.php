<?php

    class GeneratorGraph {

        public static function generateCorrectPercentage($username, $correct, $incorrect) {
            
            $fileName = "public/graph/" . $username . "correctPercentageGraph.png";

            if(file_exists($fileName)) {unlink($fileName);}
            
            $data = [$correct, $incorrect];
            $labels = ["Correctas", "Incorrectas"];            
            
            $graph = new PieGraph(400, 300, "auto");
            $graph->SetScale("textlin");
            $graph->title->Set("Porcentaje de Respuestas Correctas de " . $username);

            $p1 = new PiePlot($data);
            $p1->SetLegends($labels);
        
            $graph->Add($p1);        
            $graph->Stroke($fileName);
        
            return $fileName;

        }

        public static function generateUsersByCountry($usersByCountry) {

            $fileName = "public/graph/usersByCountryGraph.png";

            if(file_exists($fileName)) {unlink($fileName);}
            
            $data = [];
            $labels = [];
            foreach ($usersByCountry as $row) {
                $data[] = $row["usersCount"];
                $labels[] = $row["country"];
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
            $graph->Stroke($fileName);
        
            return $fileName;

        }

        public static function generateUsersByGender($usersByGender) {
            
            $fileName = "public/graph/usersByGenderGraph.png";
            
            if(file_exists($fileName)) {unlink($fileName);}
            
            $data = [];
            $labels = [];
            foreach ($usersByGender as $row) {
                $data[] = $row["usersCount"];
                $labels[] = $row["gender"];
            }
        
            $graph = new PieGraph(400, 300, "auto");
            $graph->SetScale("textlin");
            $graph->title->Set("Distribución de Usuarios por Género");
        
            $p1 = new PiePlot($data);
            $p1->SetLegends($labels);
        
            $graph->Add($p1);
            $graph->Stroke($fileName);
        
            return $fileName;
        
        }

        public static function generateUsersByAgeGroup($usersByAgeGroup) {

            $fileName = "public/graph/usersByAgeGroupGraph.png";

            if(file_exists($fileName)) {unlink($fileName);}
            
            $data = [];
            $labels = [];
            foreach ($usersByAgeGroup as $row) {
                $data[] = $row["usersCount"];
                $labels[] = $row["ageGroup"];
            }
        
            $graph = new PieGraph(400, 300, "auto");
            $graph->SetScale("textlin");
            $graph->title->Set("Distribución de Usuarios por Grupo de Edad");
        
            $p1 = new PiePlot($data);
            $p1->SetLegends($labels);

            $graph->Add($p1);
            $graph->Stroke($fileName);
        
            return $fileName;
        
        }

    }

?>