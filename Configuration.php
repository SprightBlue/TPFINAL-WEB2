<?php

    include_once("helper/Database.php");
    include_once("helper/Mailer.php");
    include_once("helper/MustachePresenter.php");
    include_once("helper/PHPQRCode.php");
    include_once("helper/Redirect.php");
    include_once("helper/Router.php");

    include_once("vendor/autoload.php");
    include_once("vendor/mustache/src/Mustache/Autoloader.php");
    include_once("vendor/phpqrcode/qrlib.php");

    include_once("model/RegistroModel.php");
    include_once("controller/RegistroController.php");

    include_once("model/LoginModel.php");
    include_once("controller/LoginController.php");

    include_once("model/LobbyModel.php");
    include_once("controller/LobbyController.php");

    include_once("model/PlayModel.php");
    include_once("controller/PlayController.php");

    include_once("model/RankingModel.php");
    include_once("controller/RankingController.php");

    include_once("model/ProfileModel.php");
    include_once("controller/ProfileController.php");   

    class Configuration {

        public static function getDatabase() {
            $config = self::getConfig();
            return new Database($config["host"], $config["dbname"], $config["username"], $config["password"]);
        }

        private static function getConfig() {
            return parse_ini_file("config/config.ini"); 
        }

        public static function getRegistroController() {
            return new RegistroController(self::getRegistroModel(), self::getPresenter());
        }

        private static function getRegistroModel() {
            return new RegistroModel(self::getDatabase());
        }

        public static function getLoginController() {
            return new LoginController(self::getLoginModel(), self::getPresenter());
        }

        private static function getLoginModel() {
            return new LoginModel(self::getDatabase());
        }

        public static function getLobbyController() {
            return new LobbyController(self::getLobbyModel(), self::getPresenter());
        }

        private static function getLobbyModel() {
            return new LobbyModel(self::getDatabase());
        }

        public static function getProfileController() {
            return new ProfileController(self::getProfileModel(), self::getPresenter());
        }

        private static function getProfileModel() {
            return new ProfileModel(self::getDatabase());
        }

        public static function getPlayController() {
            return new PlayController(self::getPlayModel(), self::getPresenter());
        }

        private static function getPlayModel() {
            return new PlayModel(self::getDatabase());
        }

        public static function getRankingController() {
            return new RankingController(self::getRankingModel(), self::getPresenter());
        }

        private static function getRankingModel() {
            return new RankingModel(self::getDatabase());
        }

        public static function getRouter() {
            return new Router("getLobbyController", "read");
        }

        public static function getPresenter() {
            return new MustachePresenter("view/template");
        }

    }

?>