<?php

    include_once("helper/Database.php");
    include_once("helper/Mailer.php");
    include_once("helper/MustachePresenter.php");
    include_once("helper/Redirect.php");
    include_once("helper/Router.php");

    include_once("vendor/mustache/src/Mustache/Autoloader.php");
    include_once("vendor/PHPMailer/src/PHPMailer.php");
    include_once("vendor/PHPMailer/src/Exception.php");
    include_once("vendor/PHPMailer/src/SMTP.php");

    include_once("model/RegistroModel.php");
    include_once("controller/RegistroController.php");

    include_once("model/LoginModel.php");
    include_once("controller/LoginController.php");

    include_once("model/LobbyModel.php");
    include_once("controller/LobbyController.php");

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

        public static function getRouter() {
            return new Router("getLobbyController", "read");
        }

        public static function getPresenter() {
            return new MustachePresenter("view/template");
        }

    }

?>