<?php

    use Dompdf\Dompdf;
    use Dompdf\Options;

    class GeneratorPDF {

        public static function generate($html) {

            $options = new Options();
            $options->set("isRemoteEnabled", true);
            $dompdf = new Dompdf($options);

            $dompdf->loadHtml($html);
            $dompdf->setPaper("A4", "portrait");
            $dompdf->render();

            $dompdf->stream("documento_generado.pdf", ["Attachment" => 1]);

        }

    }

?>