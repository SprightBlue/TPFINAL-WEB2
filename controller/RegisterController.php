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

            if(isset($_SESSION["usuario"])) {
                $logger->info("Usuario ya está en sesión, redirigiendo a /lobby/read");
                Redirect::to("/lobby/read");
            } else {
                $countries = $this->model->getCountries();
                $genders = $this->model->getGenders();
                $logger->info("Renderizando vista de registro con países y géneros");

                $logger->info("Obteniendo países...");
                $countries = $this->model->getCountries();
                $logger->info("Países obtenidos: " . count($countries));

                $logger->info("Obteniendo géneros...");
                $genders = $this->model->getGenders();
                $logger->info("Géneros obtenidos: " . count($genders));

                $this->presenter->render("view/registerView.mustache", ['countries' => $countries, 'genders' => $genders]);
            }
        }

        public function insert(){
            if(isset($_POST["registrarse"])) {
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
                if(empty($errors)) {             
                    $profileUrl = "http:/localhost/profile/get?username=$username";
                    $pathImg = "public/qr/qr-". $username . ".png";
                    GeneratorQR::generate($profileUrl, $pathImg); 
                    $verificationUrl = "http://localhost/register/active?token=$token";
                    Mailer::send($email, $fullname, $verificationUrl);
                    $this->presenter->render("view/loginView.mustache", ["mail"=>"Se envio un mensaje a su email para activar su cuenta."]);
                }else {
                    $this->presenter->render("view/registerView.mustache", ["errors"=>$errors]);
                }                
            }else {
                Redirect::to("/lobby/read");
            }
        }

        public function active() {
            $token = $_GET["token"];
            $this->model->activeUser($token);
            $this->presenter->render("view/loginView.mustache", ["verify"=>"Su cuenta se ah activado exitosamente."]);
        }

        public function update() {
            if(isset($_SESSION["usuario"])) {
                $id = $_GET["id"];
                $data = $this->getData($id);
                $this->presenter->render("view/registerView.mustache", $data);
            }else {
                Redirect::to("/login/read"); 
            }  
        }

        public function set() { 
            if(isset($_POST["actualizar"])) {
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
                if(empty($errors)) {   
                    if(!file_exists("public/qr/qr-" . $username . ".png")) {
                        unlink("public/qr/qr-" . $user["username"] . ".png");
                        $profileUrl = "http:/localhost/profile/get?username=$username";
                        $pathImg = "public/qr/qr-". $username . ".png";
                        GeneratorQR::generate($profileUrl, $pathImg);                     
                    }
                    $user = $this->model->getUser($id);
                    $_SESSION["usuario"] = $user;
                    Redirect::to("/profile/read");
                }else {
                    $this->presenter->render("view/registerView.mustache", ["errors"=>$errors]);
                }                
            }else {
                Redirect::to("/lobby/read");
            }
        }

        private function getData($id) {
            $user = $this->model->getUser($id);
            $genders = $this->model->getGenders();
            $countries = $this->model->getCountries();

            foreach ($genders as &$gender) {
                $gender['selected'] = $gender['id'] == $user['idGenero'];
            }
            foreach ($countries as &$country) {
                $country['selected'] = $country['id'] == $user['idPais'];
            }
            $data = [
                "user" => $user,
                "genders" => $genders,
                "countries" => $countries
            ];
            return $data;
        }

    }


