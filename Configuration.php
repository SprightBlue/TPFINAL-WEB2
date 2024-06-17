<?php

    include_once("helper/Database.php");
    include_once("helper/Mailer.php");
    include_once("helper/MustachePresenter.php");
    include_once("helper/Redirect.php");
    include_once("helper/Router.php");
    include_once("helper/GeneratorPDF.php");
    include_once("helper/GeneratorGraph.php");

    include_once("vendor/autoload.php");
    include_once("vendor/mustache/src/Mustache/Autoloader.php");
    include_once("vendor/phpqrcode/qrlib.php");
    include_once("vendor/dompdf/autoload.inc.php");
    include_once('vendor/jpgraph/src/jpgraph.php');
    include_once('vendor/jpgraph/src/jpgraph_line.php');

    include_once("model/RegisterModel.php");
    include_once("controller/RegisterController.php");

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

    include_once("model/AdminModel.php");
    include_once("controller/AdminController.php");

    class Configuration {

        public static function getDatabase() {
            $config = self::getConfig();
            return new Database($config["host"], $config["dbname"], $config["username"], $config["password"]);
        }

        private static function getConfig() {
            return parse_ini_file("config/config.ini"); 
        }

        public static function getRegisterController() {
            return new RegisterController(self::getRegisterModel(), self::getPresenter());
        }

        private static function getRegisterModel() {
            return new RegisterModel(self::getDatabase());
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

        public static function getAdminController() {
            return new AdminController(self::getAdminModel(), self::getPresenter());
        }

        private static function getAdminModel() {
            return new AdminModel(self::getDatabase());
        }

        public static function getRouter() {
            return new Router("getLobbyController", "read");
        }

        public static function getPresenter() {
            return new MustachePresenter("view/template");
        }

    }

?>