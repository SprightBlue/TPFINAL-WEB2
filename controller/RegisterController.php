<?php

    class RegisterController {

        private $model;
        private $presenter;
        private $logger;

        public function __construct($model, $presenter, $logger){
            $this->model = $model;
            $this->presenter = $presenter;
            $this->logger = $logger;
        }

        public function read() {
            $logger = new Logger();
            $this->verifyUser();

            $logger->info("Obteniendo países...");
            $countries = $this->model->getCountries();
            $logger->info("Países obtenidos: " . count($countries));

            $logger->info("Obteniendo géneros...");
            $genders = $this->model->getGenders();
            $logger->info("Géneros obtenidos: " . count($genders));

            $this->presenter->render("view/registerView.mustache", ['countries' => $countries, 'genders' => $genders]);
        }

        public function insert(){
            $this->verifyUser();
            if (!isset($_POST["registrarse"])) {Redirect::to("/lobby/read");}
            $errors = [];
            $fullname = $_POST["fullname"];
            $yearOfBirth = $_POST["yearOfBirth"];
            $gender = $_POST["gender"];
            $country = $_POST["country"];
            $city = $_POST["city"];
            $email = $_POST["email"];
            $pass = $_POST["pass"];
            $repeatPass = $_POST["repeatPass"];
            $username = $_POST["username"];
            $img = $_FILES["img"];
            $token = bin2hex(random_bytes(16)); 
            $this->model->createUser($fullname, $yearOfBirth, $gender, $country, $city, $email, $pass, $repeatPass, $username, $img, $token, $errors);
            if(!empty($errors)) {$this->presenter->render("view/registerView.mustache", ["errors"=>$errors]);}                
            $profileUrl = "http:/localhost/profile/get?username=$username";
            $pathImg = "public/qr/qr-". $username . ".png";
            GeneratorQR::generate($profileUrl, $pathImg); 
            $verificationUrl = "http://localhost/register/active?token=$token";
            Mailer::send($email, $fullname, $verificationUrl);
            $this->presenter->render("view/loginView.mustache", ["mail"=>"Se envio un mensaje a su email para activar su cuenta."]);
        }

        public function active() {
            $token = $_GET["token"];
            $this->model->activeUser($token);
            $this->presenter->render("view/loginView.mustache", ["verify"=>"Su cuenta se ah activado exitosamente."]);
        }

        public function update() {
            $this->verifyUserSession();
            $this->verifyEntorno();
            $id = $_GET["id"];
            $data = $this->getData($id);
            $this->presenter->render("view/registerView.mustache", $data);
        }

        public function set() { 
            $this->verifyUserSession();
            $this->verifyEntorno();
            if(!isset($_POST["actualizar"])) {Redirect::to("/lobby/read");}
            $errors = [];
            $id = $_POST["id"];
            $fullname = $_POST["fullname"];
            $yearOfBirth = $_POST["yearOfBirth"];
            $gender = $_POST["gender"];
            $country = $_POST["country"];
            $city = $_POST["city"];
            $email = $_POST["email"];
            $pass = $_POST["pass"];
            $repeatPass = $_POST["repeatPass"];
            $username = $_POST["username"];
            $img = $_FILES["img"];
            $user = $this->model->getUser($id);
            $this->model->updateUser($id, $fullname, $yearOfBirth, $gender, $country, $city, $email, $pass, $repeatPass, $username, $img, $errors);
            if(!empty($errors)) {$this->presenter->render("view/registerView.mustache", ["errors"=>$errors]);}  
            if (!file_exists("public/qr/qr-" . $username . ".png")) {
                unlink("public/qr/qr-" . $user["username"] . ".png");
                $profileUrl = "http:/localhost/profile/get?username=$username";
                $pathImg = "public/qr/qr-". $username . ".png";
                GeneratorQR::generate($profileUrl, $pathImg);                     
            }
            $user = $this->model->getUser($id);
            $_SESSION["usuario"] = $user;
            Redirect::to("/profile/read");
        }

        private function getData($id) {
            $user = $this->model->getUser($id);
            $genders = $this->model->getGenders();
            $countries = $this->model->getCountries();
            foreach ($genders as &$gender) {$gender['selected'] = $gender['id'] == $user['idGenero'];}
            foreach ($countries as &$country) {$country['selected'] = $country['id'] == $user['idPais'];}
            $data = ["user" => $user, "genders" => $genders, "countries" => $countries];
            return $data;
        }

        private function verifyUser() {
            if (isset($_SESSION["usuario"])) {Redirect::to("/lobby/read");}
        }

        private function verifyUserSession() {
            if (!isset($_SESSION["usuario"])) {Redirect::to("/login/read");}  
        }

        private function verifyEntorno() {
            if (isset($_SESSION["entorno"])) {
                $idEmpresa = $_SESSION["entorno"]["idEmpresa"];
                $idUsuario = $_SESSION["usuario"]["id"];
                $currentTime = date("Y-m-d H:i:s");
                $result = $this->model->getEntorno($idEmpresa, $idUsuario, $currentTime);
                if (!$result) {$_SESSION["entorno"] = null;}
            } 
        }

    }
