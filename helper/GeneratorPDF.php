<?php

    use Dompdf\Dompdf;
    use Dompdf\Options;

    class GeneratorPDF {

        public static function generate($html) {

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