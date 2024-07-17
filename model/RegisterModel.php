<?php

    class RegisterModel {

        private $database;
        
        public function __construct($database) {
            $this->database = $database;
        }

        public function createUser($fullname, $yearOfBirth, $gender, $country, $city, $email, $pass, $repeatPass, $username, $img, $token, &$errors) {
            if ($this->emailExists($email)) {
                $errors["errorEmail"] = "El correo electrónico ya está en uso.";
            }
            if ($pass != $repeatPass) {
                $errors["errorPass"] = "Las contraseñas ingresadas no coinciden.";
            }
            if ($this->usernameExists($username)) {
                $errors["errorUsername"] = "El nombre de usuario ya está en uso.";
            }
            if (empty($img["name"])) {
                $errors["errorImg"] = "No se ha subido ningún archivo.";
            } else {
                $imgName = $this->addImg($img, $errors);
            }
            if (empty($errors)) {
                $stmt = $this->database->query("INSERT INTO usuario(fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, token)
                                                VALUES(:fullname, :yearOfBirth, :gender, :country, :city, :email, :pass, :username, :profilePicture, :token)");
                $stmt->execute(array(":fullname" => $fullname, ":yearOfBirth" => $yearOfBirth, ":gender" => $gender, ":country" => $country, ":city" => $city, ":email" => $email, ":pass" => $pass, ":username" => $username, ":profilePicture" => $imgName, ":token" => $token));
            }
        }

        public function activeUser($token) {
            $stmt = $this->database->query("UPDATE usuario 
                                            SET active = 1 
                                            WHERE token = :token");
            $stmt->execute(array(":token" => $token));
        }

        public function updateUser($id, $fullname, $yearOfBirth, $gender, $country, $city, $email, $pass, $repeatPass, $username, $img, &$errors) {
            $user = $this->getUser($id);
            if ($user["email"] != $email && $this->emailExists($email)) {
                $errors["errorEmail"] = "El correo electrónico ya está en uso.";
            }
            if ($pass != $repeatPass) {
                $errors["errorPass"] = "Las contraseñas ingresadas no coinciden.";
            }
            if ($user["username"] != $username && $this->usernameExists($username)) {
                $errors["errorUsername"] = "El nombre de usuario ya está en uso.";
            }
            if (empty($img["name"])) {
                $imgName = $user["profilePicture"];
            } else {
                $imgName = $this->addImg($img, $errors);
                $this->deleteImg($user["profilePicture"]);
            }
            if (empty($errors)) {
                $stmt = $this->database->query("UPDATE usuario 
                                                SET fullname=:fullname, yearOfBirth=:yearOfBirth, gender=:gender, country=:country, city=:city, email=:email, pass=:pass, username=:username, profilePicture=:profilePicture
                                                WHERE id = :id");
                $stmt->execute(array("id" => $id, ":fullname" => $fullname, ":yearOfBirth" => $yearOfBirth, ":gender" => $gender, ":country" => $country, ":city" => $city, ":email" => $email, ":pass" => $pass, ":username" => $username, ":profilePicture" => $imgName));
            }
        }

        public function getUser($id) {
            $stmt = $this->database->query("SELECT * 
                                            FROM usuario u
                                            WHERE u.id= :id AND u.active = 1");
            $stmt->execute(array(":id" => $id));
            return ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        }

        private function emailExists($email) {
            $stmt = $this->database->query("SELECT * FROM usuario 
                                            WHERE email=:email");
            $stmt->execute(array(":email" => $email));
            return $stmt->rowCount() > 0;
        }

        private function usernameExists($username) {
            $stmt = $this->database->query("SELECT * FROM usuario 
                                            WHERE username=:username");
            $stmt->execute(array(":username" => $username));
            return $stmt->rowCount() > 0;
        }

        private function addImg($img, &$errors){
            if ($img["error"] !== UPLOAD_ERR_OK) {
                $errors["errorImg"] = "Error al subir el archivo.";
                return false;
            }
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($img["tmp_name"]);
            $allowedMimes = ["image/jpeg", "image/png", "image/gif"];
            if (!in_array($mime, $allowedMimes)) {
                $errors["errorImg"] = "El archivo no es una imagen válida.";
                return false;
            }
            $ext = strtolower(pathinfo($img["name"], PATHINFO_EXTENSION));
            $allowedExts = ["jpg", "jpeg", "png", "gif"];
            if (!in_array($ext, $allowedExts)) {
                $errors["errorImg"] = "Extensión de archivo no permitida.";
                return false;
            }
            $imgName = bin2hex(random_bytes(16)) . "." . $ext;
            $destination = "public/img/" . $imgName;
            if (!move_uploaded_file($img["tmp_name"], $destination)) {
                $errors["errorImg"] = "Error al mover el archivo.";
                return false;
            }
            return $imgName;
        }

        private function deleteImg($img) {
            if (file_exists("public/img/" . $img)) {
                unlink("public/img/" . $img);
            }
        }

        
        public function getSessionThirdParties($idEnterprise, $idUser, $currentTime) {
            $stmt = $this->database->query("SELECT *
                                            FROM sesionTerceros
                                            WHERE idEnterprise = :idEnterprise
                                            AND idUser = :idUser
                                            AND :currentTime BETWEEN startDate AND endDate");
            $stmt->execute(array(":idEnterprise"=>$idEnterprise, ":idUser"=>$idUser, ":currentTime"=>$currentTime));
            return ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        }
        

    }
