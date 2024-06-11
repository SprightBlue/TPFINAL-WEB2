<?php

    class Database {

        private $conn;

        public function __construct($host, $dbname, $username, $password) {
            try {
                $this->conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   
            } catch(Exception $e) {
                die("Connection failed: " . $e->getMessage());
            }     
        }

        public function query($sql) {
            return $this->conn->prepare($sql);
        }

        public function __destruct() {
            $this->conn = null;
        }      
        
        //luego lo pondre en el model del juego     
        public function getCountQuestions() {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM pregunta"); 
            $stmt->execute();
            return $stmt;    
        }

        public function getAnswers($idQuestion) {
            $stmt = $this->conn->prepare("SELECT * FROM respuesta WHERE idQuestion=:idQuestion ORDER BY RAND()");
            $stmt->execute(array(":idQuestion"=>$idQuestion));
            if($stmt->rowCount() > 0) {
                $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $answers;
            }
            return false;
        }

        public function createGame($idUser, $score) {
            $stmt = $this->conn->prepare("INSERT INTO partida(score, dateGame, idUser) VALUES (:score, NOW(), :idUser)");
            $stmt->execute(array(":score"=>$score, ":idUser"=>$idUser));
        }

        public function updateScore($idUser, $score) {
            $stmt = $this->conn->prepare("UPDATE usuario SET score = score + :score WHERE id = :idUser");
            $stmt->execute(array(":idUser"=>$idUser, ":score"=>$score));
        }

        public function getScore($idUser) {
            $stmt = $this->conn->prepare("SELECT score FROM usuario WHERE id = :idUser");
            $stmt->execute(array(":idUser"=>$idUser));
            return $stmt->fetchColumn();
        }

        public function getAnsweredQuestions($idUser) {
            $stmt = $this->conn->prepare("SELECT answeredQuestions FROM usuario WHERE id = :idUser");
            $stmt->execute(array(":idUser"=>$idUser));
            return $stmt->fetchColumn();
        }
       //Preguntas
        public function incrementCorrectAnswers($idQuestion) {
            $stmt = $this->conn->prepare("UPDATE pregunta SET correctAnswers = correctAnswers + 1 WHERE idQuestion = :idQuestion");
            $stmt->execute(array(":idQuestion"=>$idQuestion));
        }
        //Preguntas
        public function incrementTotalAnswers($idQuestion) {
            $stmt = $this->conn->prepare("UPDATE pregunta SET totalAnswers = totalAnswers + 1 WHERE idQuestion = :idQuestion");
            $stmt->execute(array(":idQuestion"=>$idQuestion));
        }
        //Usuario
        public function incrementUserAnsweredQuestions($idUser) {
            $stmt = $this->conn->prepare("UPDATE usuario SET answeredQuestions = answeredQuestions + 1 WHERE id = :idUser");
            $stmt->execute(array(":idUser"=>$idUser));
        }

        //Usuario
         public function incrementUserCorrectAnswers($idUser) {
            $stmt = $this->conn->prepare("UPDATE usuario SET correctAnswers = correctAnswers + 1 WHERE id = :idUser");
            $stmt->execute(array(":idUser"=>$idUser));
        }

        public function getQuestionDifficulty($idQuestion) {
            $stmt = $this->conn->prepare("SELECT correctAnswers, totalAnswers FROM pregunta WHERE idQuestion = :idQuestion");
            $stmt->execute(array(":idQuestion"=>$idQuestion));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result['totalAnswers'] == 0) {
                return null; // or some default value
            }
            return $result['correctAnswers'] / $result['totalAnswers'];
        }


        public function getUserRatio($idUser) {
            $stmt = $this->conn->prepare("SELECT correctAnswers, answeredQuestions FROM usuario WHERE id = :idUser");
            $stmt->execute(array(":idUser"=>$idUser));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result['answeredQuestions'] == 0) {
                return null; // or some default value
            }
            return $result['correctAnswers'] / $result['answeredQuestions'];
        }

        public function getQuestionRandom($idUser, $difficulty) {
            // Obtén las preguntas que el usuario ya ha respondido
            $answeredQuestions = $this->getUserQuestions($idUser);

            // Verifica si el array $answeredQuestions está vacío
            if (empty($answeredQuestions)) {
                // Prepara la consulta SQL sin la cláusula NOT IN
                $stmt = $this->conn->prepare("SELECT * FROM pregunta WHERE difficulty = :difficulty ORDER BY RAND() LIMIT 1");
                // Ejecuta la consulta SQL
                $stmt->execute([':difficulty' => $difficulty]);
            } else {
                // Prepara los placeholders para la consulta SQL
                $placeholders = implode(',', array_fill(0, count($answeredQuestions), '?'));

                // Prepara la consulta SQL
                $stmt = $this->conn->prepare("SELECT * FROM pregunta WHERE idQuestion NOT IN ($placeholders) AND difficulty = ? ORDER BY RAND() LIMIT 1");

                // Ejecuta la consulta SQL
                $stmt->execute(array_merge($answeredQuestions, [$difficulty]));
            }

            // Devuelve la pregunta obtenida o false si no se obtuvo ninguna pregunta
            return $stmt->rowCount() > 0 ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        }



        public function getUserQuestions($idUser) {
            $stmt = $this->conn->prepare("SELECT idPregunta FROM usuario_pregunta WHERE idUsuario = :idUser");
            $stmt->execute(array(":idUser"=>$idUser));
            return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        }
        public function resetUserQuestions($idUser) {
            $stmt = $this->conn->prepare("DELETE FROM usuario_pregunta WHERE idUsuario = :idUser");
            $stmt->execute(array(":idUser"=>$idUser));
        }
        public function addUserQuestion($idUser, $idQuestion) {
            $stmt = $this->conn->prepare("INSERT INTO usuario_pregunta (idUsuario, idPregunta) VALUES (:idUser, :idQuestion)");
            $stmt->execute(array(":idUser"=>$idUser, ":idQuestion"=>$idQuestion));
        }
        public function updateQuestionDifficulty($idQuestion) {
            $ratio = $this->getQuestionDifficulty($idQuestion);
            $difficulty = $ratio >= 0.7 ? 'easy' : 'hard';
            $stmt = $this->conn->prepare("UPDATE pregunta SET difficulty = :difficulty WHERE idQuestion = :idQuestion");
            $stmt->execute(array(":idQuestion"=>$idQuestion, ":difficulty"=>$difficulty));
        }
    }


?>