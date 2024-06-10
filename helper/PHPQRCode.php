<?php

    class PHPQRCode {

        public static function generate($profileUrl, $username) {
            QRcode::png($profileUrl, "public/img/qr-". $username . ".png", QR_ECLEVEL_L, 8);
        }

    }

?>