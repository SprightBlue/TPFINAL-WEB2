<?php

    class RegisterModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function createUser($fullname, $yearOfBirth, $gender, $country, $city, $email, $pass, $repeatPass, $username, $img, &$errors) {
            if($this->emailExists($email)) {$errors["errorEmail"] = "El correo electrónico ya está en uso.";}
            if($pass != $repeatPass) {$errors["errorPass"] = "Las contraseñas ingresadas no coinciden.";}
            if($this->usernameExists($username)) {$errors["errorUsername"] = "El nombre de usuario ya está en uso.";}
            if(empty($img['name'])) {$errors["errorImg"] = "No se ha subido ningún archivo.";} 
            else {$destination = $this->addImg($img, $errors);}
            if(empty($errors)) {
                $token = bin2hex(random_bytes(16));           
                $profileUrl = "http:/localhost/profile/get?username=$username";
                $path = "public/img/qr-". $username . ".png";
                QRcode::png($profileUrl, $path, QR_ECLEVEL_L, 8);      
                $stmt = $this->database->query("INSERT INTO usuario(fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, token, active)
                                                VALUES(:fullname, :yearOfBirth, :gender, :country, :city, :email, :pass, :username, :profilePicture, :token, 0)");
                $stmt->execute(array(":fullname"=>$fullname, ":yearOfBirth"=>$yearOfBirth, ":gender"=>$gender, ":country"=>$country, ":city"=>$city, ":email"=>$email, ":pass"=>$pass, ":username"=>$username, ":profilePicture"=>$destination, ":token"=>$token));
                $verificationUrl = "http://localhost/register/active?token=$token";
                Mailer::send($email, $fullname, $verificationUrl);
            }
        }

        public function activeUser($token) {
            $stmt = $this->database->query("UPDATE usuario 
                                            SET active=1 
                                            WHERE token=:token");
            $stmt->execute(array(":token"=>$token));
        }

        private function emailExists($email) {
            $stmt = $this->database->query("SELECT * FROM usuario 
                                            WHERE email=:email");
            $stmt->execute(array(":email"=>$email));
            return $stmt->rowCount() > 0;
        }

        private function usernameExists($username) {
            $stmt = $this->database->query("SELECT * FROM usuario 
                                            WHERE username=:username");
            $stmt->execute(array(":username"=>$username));
            return $stmt->rowCount() > 0;
        }

        private function addImg($img, &$errors) {
            if($img['error'] !== UPLOAD_ERR_OK) {
                $errors["errorImg"] = "Error al subir el archivo.";
                return false;
            }
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($img['tmp_name']);
            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
            if(!in_array($mime, $allowedMimes)) {
                $errors["errorImg"] = "El archivo no es una imagen válida.";
                return false;
            }
            $ext = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
            if(!in_array($ext, $allowedExts)) {
                $errors["errorImg"] = "Extensión de archivo no permitida.";
                return false;
            }
            $destination = 'public/img/' . bin2hex(random_bytes(16)) . '.' . $ext;
            if(!move_uploaded_file($img['tmp_name'], $destination)) {
                $errors["errorImg"] = "Error al mover el archivo.";
                return false;
            }
            return $destination;
        }
    
    }

?>
