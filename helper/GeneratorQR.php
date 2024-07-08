<?php

    class GeneratorQR {

        public static function generate($profileUrl, $pathImg) {

            QRcode::png($profileUrl, $pathImg, QR_ECLEVEL_L, 8); 

        }

    }
