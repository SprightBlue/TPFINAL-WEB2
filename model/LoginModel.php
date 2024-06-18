<?php

    class LoginModel {

        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function loginUser($username, $pass, &$errors) {
            $stmt = $this->database->query("SELECT * FROM usuario 
                                            WHERE username=:username AND pass=:pass");
            $stmt->execute(array(":username"=>$username, ":pass"=>$pass));
            
            if($stmt->rowCount() > 0) {$user = $stmt->fetch(PDO::FETCH_ASSOC);}
            else {$user = false;}
            
            if($user == false) {$errors["validations"] = "El usuario y/o contraseña son incorrectos.";} 
            else if ($user["active"] == 0) {$errors["active"] = "Debes verificar tu correo electrónico antes de poder iniciar sesión.";}

            return $user;
        }

    }

?>
