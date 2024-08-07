DROP DATABASE IF EXISTS qampa;

CREATE DATABASE IF NOT EXISTS qampa;

USE qampa;

CREATE TABLE rol (
	id INT AUTO_INCREMENT PRIMARY KEY,
    descriptionName VARCHAR(255)
);

INSERT INTO rol (descriptionName) VALUES 
('player'),
('editor'),
('admin'),
('enterprise');

CREATE TABLE usuario (
	id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255),
    yearOfBirth INT,
    gender VARCHAR(255),
    country VARCHAR(255),
    city VARCHAR(255),
    email VARCHAR(255),
    pass VARCHAR(255),
    username VARCHAR(255),
    profilePicture VARCHAR(255), 
    active BOOLEAN DEFAULT 0,
    token VARCHAR(255),
    answeredQuestions INT DEFAULT 0,
    correctAnswers INT DEFAULT 0,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    bonus INT DEFAULT 0,
    idRole INT DEFAULT 1,
    FOREIGN KEY (idRole) REFERENCES rol(id)
);

CREATE TABLE sesionTerceros (
	idEnterprise INT,
	idUser INT,
	startDate TIMESTAMP,
	endDate TIMESTAMP,
	PRIMARY KEY(idEnterprise, idUser),
	FOREIGN KEY(idEnterprise) REFERENCES usuario(id),
	FOREIGN KEY(idUser) REFERENCES usuario(id)
);

CREATE TABLE compraBonus (
	id INT AUTO_INCREMENT PRIMARY KEY,
	amount INT,
	totalPrice DECIMAL(10, 2),
	created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    idUser INT,
	FOREIGN KEY(idUser) REFERENCES usuario(id)
);

CREATE TABLE pregunta (
    idQuestion INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(255),
    category VARCHAR(255),
    dateCreated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    correctAnswers INT DEFAULT 50,
    totalAnswers INT DEFAULT 100,
    difficulty VARCHAR(255) DEFAULT 'easy',
    idCreator INT,
    FOREIGN KEY (idCreator) REFERENCES usuario(id)
);

CREATE TABLE respuesta (
    idAnswer INT AUTO_INCREMENT PRIMARY KEY,
    answer VARCHAR(255),
    correct BOOLEAN,
    idQuestion INT,         
    FOREIGN KEY (idQuestion) REFERENCES pregunta(idQuestion)
);

CREATE TABLE pregunta_sugerida (
	idSuggestion INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(255),
	category VARCHAR(255),
	answer1 VARCHAR(255),
	answer2 VARCHAR(255),
	answer3 VARCHAR(255),
	answer4 VARCHAR(255),
	correct INT,
	idUser INT,
	FOREIGN KEY (idUser) REFERENCES usuario (id)
);

CREATE TABLE partida (
	id INT AUTO_INCREMENT PRIMARY KEY,
	score INT,
	dateGame TIMESTAMP,
	idUser INT,
	idThirdParties INT,
	FOREIGN KEY (idUser) REFERENCES usuario(id),
	FOREIGN KEY (idThirdParties) REFERENCES usuario(id)
);

CREATE TABLE usuario_pregunta (
    idUsuario INT,
    idPregunta INT,
    PRIMARY KEY (idUsuario, idPregunta),
    FOREIGN KEY (idUsuario) REFERENCES usuario(id),
    FOREIGN KEY (idPregunta) REFERENCES pregunta(idQuestion)
);

CREATE TABLE report (
    idReport INT AUTO_INCREMENT PRIMARY KEY,
    reason TEXT,
    dateReported TIMESTAMP DEFAULT CURRENT_TIMESTAMP,    
    idQuestion INT,
    idUser INT,
    FOREIGN KEY (idQuestion) REFERENCES pregunta(idQuestion),
    FOREIGN KEY (idUser) REFERENCES usuario(id)
);

CREATE TABLE challenge (
    id INT AUTO_INCREMENT PRIMARY KEY,
    challenger_id INT,
    challenged_id INT,
    challenger_score INT DEFAULT 0,
    challenged_score INT DEFAULT 0,
    status ENUM('pending', 'accepted', 'resolved'),
    winner_id INT,
    loser_id INT,
    is_tie BOOLEAN DEFAULT 0,
    FOREIGN KEY (challenger_id) REFERENCES usuario(id),
    FOREIGN KEY (challenged_id) REFERENCES usuario(id),
    FOREIGN KEY (winner_id) REFERENCES usuario(id),
    FOREIGN KEY (loser_id) REFERENCES usuario(id)
);

INSERT INTO pregunta (question, category) VALUES
('¿Cuál es el río más largo del mundo?', 'Geografía'),
('¿En qué continente se encuentra Mongolia?', 'Geografía'),
('¿Cuál es la capital de Australia?', 'Geografía'),
('¿Qué país tiene la mayor cantidad de islas en el mundo?', 'Geografía'),
('¿Cuál es la montaña más alta de África?', 'Geografía'),
('¿Quién pintó "La Última Cena"?', 'Arte'),
('¿En qué ciudad se encuentra el Museo del Prado?', 'Arte'),
('¿Cuál de las siguientes obras fue escrita por William Shakespeare?', 'Arte'),
('¿Quién es el autor de la escultura "El Pensador"?', 'Arte'),
('¿Qué estilo artístico es Salvador Dalí famoso por representar?', 'Arte'),
('¿Cuál es el elemento químico con el símbolo "O"?', 'Ciencia'),
('¿Qué científico propuso la teoría de la relatividad?', 'Ciencia'),
('¿Qué planeta es conocido como el "Planeta Rojo"?', 'Ciencia'),
('¿Cuál es la unidad de medida de la corriente eléctrica?', 'Ciencia'),
('¿Quién descubrió la penicilina?', 'Ciencia'),
('¿En qué deporte se utiliza una pelota llamada "shuttlecock"?', 'Deporte'),
('¿Cuántos jugadores hay en un equipo de baloncesto en la cancha?', 'Deporte'),
('¿En qué país se originó el judo?', 'Deporte'),
('¿Cuál es el evento deportivo más grande del mundo?', 'Deporte'),
('¿Quién es conocido como "El Rey del Fútbol"?', 'Deporte'),
('¿Cuál es la película con mayor recaudación de todos los tiempos (hasta 2023)?', 'Entretenimiento'),
('¿Qué serie de televisión cuenta la historia de la familia Stark?', 'Entretenimiento'),
('¿Quién es el creador de Mickey Mouse?', 'Entretenimiento'),
('¿En qué año se lanzó el primer videojuego de "Super Mario Bros."?', 'Entretenimiento'),
('¿Quién interpreta a Iron Man en las películas del Universo Cinematográfico de Marvel?', 'Entretenimiento'),
('¿En qué año cayó el Muro de Berlín?', 'Historia'),
('¿Quién fue el primer presidente de los Estados Unidos?', 'Historia'),
('¿Cuál fue el barco que se hundió en su viaje inaugural en 1912?', 'Historia'),
('¿Quién fue el líder de la Revolución Rusa de 1917?', 'Historia'),
('¿Qué imperio construyó la Gran Muralla China?', 'Historia');

INSERT INTO respuesta (idQuestion, answer, correct) VALUES
(1, 'Nilo', 0), (1, 'Amazonas', 1), (1, 'Yangtsé', 0), (1, 'Misisipi', 0),
(2, 'África', 0), (2, 'Europa', 0), (2, 'Asia', 1), (2, 'América del Sur', 0),
(3, 'Sídney', 0), (3, 'Melbourne', 0), (3, 'Canberra', 1), (3, 'Perth', 0),
(4, 'Noruega', 0), (4, 'Indonesia', 0), (4, 'Suecia', 1), (4, 'Filipinas', 0),
(5, 'Monte Kenia', 0), (5, 'Kilimanjaro', 1), (5, 'Drakensberg', 0), (5, 'Atlas', 0),
(6, 'Vincent van Gogh', 0), (6, 'Leonardo da Vinci', 1), (6, 'Pablo Picasso', 0), (6, 'Rembrandt', 0),
(7, 'Barcelona', 0), (7, 'Valencia', 0), (7, 'Sevilla', 0), (7, 'Madrid', 1),
(8, 'La Divina Comedia', 0), (8, 'Don Quijote', 0), (8, 'Hamlet', 1), (8, 'El Retrato de Dorian Gray', 0),
(9, 'Miguel Ángel', 0), (9, 'Donatello', 0), (9, 'Auguste Rodin', 1), (9, 'Bernini', 0),
(10, 'Impresionismo', 0), (10, 'Surrealismo', 1), (10, 'Cubismo', 0), (10, 'Barroco', 0),
(11, 'Oro', 0), (11, 'Oxígeno', 1), (11, 'Osmio', 0), (11, 'Oxalato', 0),
(12, 'Isaac Newton', 0), (12, 'Albert Einstein', 1), (12, 'Nikola Tesla', 0), (12, 'Galileo Galilei', 0),
(13, 'Júpiter', 0), (13, 'Venus', 0), (13, 'Marte', 1), (13, 'Saturno', 0),
(14, 'Voltio', 0), (14, 'Amperio', 1), (14, 'Ohmio', 0), (14, 'Vatio', 0),
(15, 'Marie Curie', 0), (15, 'Gregor Mendel', 0), (15, 'Alexander Fleming', 1), (15, 'Louis Pasteur', 0),
(16, 'Bádminton', 1), (16, 'Tenis', 0), (16, 'Cricket', 0), (16, 'Polo', 0),
(17, '11', 0), (17, '9', 0), (17, '7', 0), (17, '5', 1),
(18, 'China', 0), (18, 'Corea del Sur', 0), (18, 'Tailandia', 0), (18, 'Japón', 1),
(19, 'Copa Mundial de la FIFA', 0), (19, 'Tour de Francia', 0), (19, 'Juegos Olímpicos', 1), (19, 'Super Bowl', 0),
(20, 'Diego Maradona', 0), (20, 'Pelé', 1), (20, 'Lionel Messi', 0), (20, 'Cristiano Ronaldo', 0),
(21, 'Titanic', 0), (21, 'Avatar', 1), (21, 'Avengers: Endgame', 0), (21, 'Star Wars: The Force Awakens', 0),
(22, 'Breaking Bad', 0), (22, 'Game of Thrones', 1), (22, 'The Sopranos', 0), (22, 'The Walking Dead', 0),
(23, 'Chuck Jones', 0), (23, 'Walt Disney', 1), (23, 'Hanna-Barbera', 0), (23, 'Tex Avery', 0),
(24, '1980', 0), (24, '1983', 0), (24, '1985', 1), (24, '1988', 0),
(25, 'Chris Hemsworth', 0), (25, 'Robert Downey Jr.', 1), (25, 'Chris Evans', 0), (25, 'Mark Ruffalo', 0),
(26, '1985', 0), (26, '1989', 1), (26, '1991', 0), (26, '1995', 0),
(27, 'Thomas Jefferson', 0), (27, 'Abraham Lincoln', 0), (27, 'George Washington', 1), (27, 'John Adams', 0),
(28, 'Lusitania', 0), (28, 'Titanic', 1), (28, 'Britannic', 0), (28, 'Queen Mary', 0), 
(29, 'Josef Stalin', 0), (29, 'Vladimir Lenin', 1), (29, 'León Trotsky', 0), (29, 'Nikita Khrushchev', 0),
(30, 'Dinastía Tang', 0), (30, 'Dinastía Qin', 1), (30, 'Dinastía Ming', 0), (30, 'Dinastía Han', 0);

/*
 INSERTAR PRIMERO EL ADMIN Y EDITOR, LUEGO INSERTAR 2 USUARIOS
*/
INSERT INTO usuario (fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, active, idRole)
VALUES ('admin', 2002, 'Masculino', 'Argentina', 'Ramos Mejia', 'admin@gmail.com', '1234', 'admin', '0143Snorlax.png', 1, 3);

INSERT INTO usuario (fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, active, idRole)
VALUES ('editor', 1998, 'Masculino', 'Argentina', 'Liniers', 'editor@gmail.com', '1234', 'editor', '0079Slowpoke.png', 1, 2);


INSERT INTO usuario (fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, active, idRole)
VALUES ('Messi', 1990, 'Masculino','Argentina','Rosario', 'usuario@email.com', '123', 'messi', 'messirve.jpg',1,1);


INSERT INTO usuario (fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, active, idRole)
VALUES ('Panchito1', 2022,'Masculino','Argentina' ,'Buenos Aires', 'panchito111@email.com', '123', 'pancho', 'pancho.png',1,1);

INSERT INTO usuario (fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, active, idRole)
VALUES ('Blizzard Entertainment, Inc.', 1991, 'Prefiero no cargarlo', 'United States', 'Irvine', 'blizzard@email.com', '1234', 'Blizzard', '0097Hypno.png', 1, 4);



/*
 insercion de datos por dia para generar graficos

/*
 Cantidad de jugadores que tiene la aplicacion

 Porcentaje de preguntas respondidas correctamente por usuario

     Distribucion de Usuarios por pais, genero y grupo de edad
     */

INSERT INTO usuario (fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, active, idRole, created, answeredQuestions, correctAnswers)
VALUES ('Usuario Nuevo', 1900, 'Masculino', 'Brasil', 'Ciudad', 'nuevo4@email.com', 'pass', 'nuevousuario4', 'profile.jpg', 1, 1, CURDATE(), 100, 30);

INSERT INTO usuario (fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, active, idRole, created, answeredQuestions, correctAnswers)
VALUES ('Usuario Nuevo', 1995, 'Femenino', 'Chile', 'Ciudad', 'nuevo5@email.com', 'pass', 'nuevousuario5', 'profile.jpg', 1, 1, CURDATE(), 100, 100);

INSERT INTO usuario (fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, active, idRole, created, answeredQuestions, correctAnswers)
VALUES ('Usuario Nuevo', 2022, 'Masculino', 'Uruguay', 'Ciudad', 'nuevo6@email.com', 'pass', 'nuevousuario6', 'profile.jpg', 1, 1, CURDATE(), 20, 3);
/*
 Cantidad de partidas jugadas
 */
INSERT INTO partida (idUser, dateGame, score)
VALUES (4, CURDATE(), 25);
INSERT INTO partida (idUser, dateGame, score)
VALUES (5, CURDATE(), 25);
INSERT INTO partida (idUser, dateGame, score)
VALUES (4, CURDATE(), 21);
INSERT INTO partida (idUser, dateGame, score)
VALUES (4, CURDATE(), 2);
INSERT INTO partida (idUser, dateGame, score)
VALUES (5, CURDATE(), 25);
INSERT INTO partida (idUser, dateGame, score)
VALUES (4, CURDATE(), 22);

/*
 Cantidad de preguntas
 */
INSERT INTO pregunta (question, category, dateCreated)
VALUES ('¿Cuál es la capital de Francia?', 'Geografía', CURDATE());
INSERT INTO pregunta (question, category, dateCreated)
VALUES ('¿Cuál es la capital de Peru', 'Geografía', CURDATE());
INSERT INTO pregunta (question, category, dateCreated)
VALUES ('¿Cuál es la capital de La pampa?', 'Geografía', CURDATE());

-- Insertar ventas de trampitas para el día actual
INSERT INTO compraBonus (idUser,amount, totalPrice, created) VALUES
                                                                         (4, 10, 100, NOW()),
                                                                         (5, 5, 50, NOW()),
                                                                         (5, 20, 200, NOW());
/*
 insercion de datos por semana para generar graficos


 Cantidad de jugadores que tiene la aplicacion

 Porcentaje de preguntas respondidas correctamente por usuario

     Distribucion de Usuarios por pais, genero y grupo de edad
     */

INSERT INTO usuario (fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, active, idRole, created, answeredQuestions, correctAnswers)
VALUES ('Usuario Nuevo', 1900, 'Femenino', 'Chile', 'Ciudad', 'nuevo7@email.com', 'pass', 'nuevousuario7', 'profile.jpg', 1, 1, DATE_SUB(CURDATE(), INTERVAL 7 DAY), 100, 30);

INSERT INTO usuario (fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, active, idRole, created, answeredQuestions, correctAnswers)
VALUES ('Usuario Nuevo', 1995, 'Masculino', 'Uruguay', 'Ciudad', 'nuevo8@email.com', 'pass', 'nuevousuario8', 'profile.jpg', 1, 1, DATE_SUB(CURDATE(), INTERVAL 7 DAY), 100, 100);

INSERT INTO usuario (fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, active, idRole, created, answeredQuestions, correctAnswers)
VALUES ('Usuario Nuevo', 2022, 'Femenino', 'Argentina', 'Ciudad', 'nuevo9@email.com', 'pass', 'nuevousuario9', 'profile.jpg', 1, 1, DATE_SUB(CURDATE(), INTERVAL 7 DAY), 20, 3);


/*
  Cantidad de partidas jugadas
  */
INSERT INTO partida (idUser, dateGame, score)
VALUES (7, DATE_SUB(CURDATE(), INTERVAL 7 DAY), 25);
INSERT INTO partida (idUser, dateGame, score)
VALUES (7, DATE_SUB(CURDATE(), INTERVAL 7 DAY), 25);
INSERT INTO partida (idUser, dateGame, score)
VALUES (8, DATE_SUB(CURDATE(), INTERVAL 7 DAY), 21);
INSERT INTO partida (idUser, dateGame, score)
VALUES (8, DATE_SUB(CURDATE(), INTERVAL 7 DAY), 2);
INSERT INTO partida (idUser, dateGame, score)
VALUES (8, DATE_SUB(CURDATE(), INTERVAL 7 DAY), 25);
INSERT INTO partida (idUser, dateGame, score)
VALUES (8, DATE_SUB(CURDATE(), INTERVAL 7 DAY), 22);

/*
 Cantidad de preguntas
 */
INSERT INTO pregunta (question, category, dateCreated)
VALUES ('¿Cuál es la capital de Francia?', 'Geografía', DATE_SUB(CURDATE(), INTERVAL 7 DAY));
INSERT INTO pregunta (question, category, dateCreated)
VALUES ('¿Cuál es la capital de Peru', 'Geografía', DATE_SUB(CURDATE(), INTERVAL 7 DAY));
INSERT INTO pregunta (question, category, dateCreated)
VALUES ('¿Cuál es la capital de La pampa?', 'Geografía', DATE_SUB(CURDATE(), INTERVAL 7 DAY));


-- Insertar ventas de trampitas para el día actual
INSERT INTO compraBonus (idUser,amount, totalPrice, created) VALUES
                                                                         (7, 10, 100, DATE_SUB(NOW(), INTERVAL 7 DAY)),
                                                                         (7, 5, 50, DATE_SUB(NOW(), INTERVAL 7 DAY)),
                                                                         (8, 20, 200, DATE_SUB(NOW(), INTERVAL 7 DAY));

/*
 insercion de datos por Mes para generar graficos


Cantidad de jugadores que tiene la aplicacion

 Porcentaje de preguntas respondidas correctamente por usuario

     Distribucion de Usuarios por pais, genero y grupo de edad
     */
INSERT INTO usuario (fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, active, idRole, created, answeredQuestions, correctAnswers)
VALUES ('Usuario Nuevo', 1900, 'Masculino', 'Uruguay', 'Ciudad', 'nuevo10@email.com', 'pass', 'nuevousuario10', 'profile.jpg', 1, 1, DATE_SUB(CURDATE(), INTERVAL 1 MONTH), 100, 30);

INSERT INTO usuario (fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, active, idRole, created, answeredQuestions, correctAnswers)
VALUES ('Usuario Nuevo', 1995, 'Femenino', 'Argentina', 'Ciudad', 'nuevo11@email.com', 'pass', 'nuevousuario11', 'profile.jpg', 1, 1, DATE_SUB(CURDATE(), INTERVAL 1 MONTH), 100, 100);

INSERT INTO usuario (fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, active, idRole, created, answeredQuestions, correctAnswers)
VALUES ('Usuario Nuevo', 2022, 'Masculino', 'Brasil', 'Ciudad', 'nuevo12@email.com', 'pass', 'nuevousuario12', 'profile.jpg', 1, 1, DATE_SUB(CURDATE(), INTERVAL 1 MONTH), 20, 3);


/*
  Cantidad de partidas jugadas
  */
INSERT INTO partida (idUser, dateGame, score)
VALUES (10, DATE_SUB(CURDATE(), INTERVAL 1 MONTH), 25);
INSERT INTO partida (idUser, dateGame, score)
VALUES (11, DATE_SUB(CURDATE(), INTERVAL 1 MONTH), 25);
INSERT INTO partida (idUser, dateGame, score)
VALUES (11, DATE_SUB(CURDATE(), INTERVAL 1 MONTH), 21);
INSERT INTO partida (idUser, dateGame, score)
VALUES (10, DATE_SUB(CURDATE(), INTERVAL 1 MONTH), 2);
INSERT INTO partida (idUser, dateGame, score)
VALUES (11, DATE_SUB(CURDATE(), INTERVAL 1 MONTH), 25);
INSERT INTO partida (idUser, dateGame, score)
VALUES (11, DATE_SUB(CURDATE(), INTERVAL 1 MONTH), 22);

/*
 Cantidad de preguntas
 */
INSERT INTO pregunta (question, category, dateCreated)
VALUES ('¿Cuál es la capital de Francia?', 'Geografía', DATE_SUB(CURDATE(), INTERVAL 1 MONTH));
INSERT INTO pregunta (question, category, dateCreated)
VALUES ('¿Cuál es la capital de Peru', 'Geografía', DATE_SUB(CURDATE(), INTERVAL 1 MONTH));
INSERT INTO pregunta (question, category, dateCreated)
VALUES ('¿Cuál es la capital de La pampa?', 'Geografía',DATE_SUB(CURDATE(), INTERVAL 1 MONTH));


-- Insertar ventas de trampitas para el día actual
INSERT INTO compraBonus (idUser,amount, totalPrice, created) VALUES
                                                                         (10, 10, 100, DATE_SUB(NOW(), INTERVAL 1 MONTH)),
                                                                         (11, 5, 50, DATE_SUB(NOW(), INTERVAL 1 MONTH)),
                                                                         (11, 20, 200, DATE_SUB(NOW(), INTERVAL 1 MONTH));

/*
 insercion de datos por año para generar graficos
 Cantidad de jugadores que tiene la aplicacion

 Porcentaje de preguntas respondidas correctamente por usuario

     Distribucion de Usuarios por pais, genero y grupo de edad
     */
INSERT INTO usuario (fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, active, idRole, created, answeredQuestions, correctAnswers)
VALUES ('Usuario Nuevo', 1900, 'Femenino', 'Argentina', 'Ciudad', 'nuevo13@email.com', 'pass', 'nuevousuario13', 'profile.jpg', 1, 1, DATE_SUB(CURDATE(), INTERVAL 1 YEAR), 100, 30);

INSERT INTO usuario (fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, active, idRole, created, answeredQuestions, correctAnswers)
VALUES ('Usuario Nuevo', 1995, 'Masculino', 'Brasil', 'Ciudad', 'nuevo14@email.com', 'pass', 'nuevousuario14', 'profile.jpg', 1, 1, DATE_SUB(CURDATE(), INTERVAL 1 YEAR), 100, 100);

INSERT INTO usuario (fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, active, idRole, created, answeredQuestions, correctAnswers)
VALUES ('Usuario Nuevo', 2022, 'Femenino', 'Chile', 'Ciudad', 'nuevo15@email.com', 'pass', 'nuevousuario15', 'profile.jpg', 1, 1, DATE_SUB(CURDATE(), INTERVAL 1 YEAR), 20, 3);


/*
  Cantidad de partidas jugadas
  */
INSERT INTO partida (idUser, dateGame, score)
VALUES (14, DATE_SUB(CURDATE(), INTERVAL 1 YEAR), 25);
INSERT INTO partida (idUser, dateGame, score)
VALUES (14, DATE_SUB(CURDATE(), INTERVAL 1 YEAR), 25);
INSERT INTO partida (idUser, dateGame, score)
VALUES (14, DATE_SUB(CURDATE(), INTERVAL 1 YEAR), 21);
INSERT INTO partida (idUser, dateGame, score)
VALUES (13, DATE_SUB(CURDATE(), INTERVAL 1 YEAR), 2);
INSERT INTO partida (idUser, dateGame, score)
VALUES (13, DATE_SUB(CURDATE(), INTERVAL 1 YEAR), 25);
INSERT INTO partida (idUser, dateGame, score)
VALUES (13, DATE_SUB(CURDATE(), INTERVAL 1 YEAR), 22);

/*
 Cantidad de preguntas
 */
INSERT INTO pregunta (question, category, dateCreated)
VALUES ('¿Cuál es la capital de Francia?', 'Geografía', DATE_SUB(CURDATE(), INTERVAL 1 YEAR));
INSERT INTO pregunta (question, category, dateCreated)
VALUES ('¿Cuál es la capital de Peru', 'Geografía', DATE_SUB(CURDATE(), INTERVAL 1 YEAR));
INSERT INTO pregunta (question, category, dateCreated)
VALUES ('¿Cuál es la capital de La pampa?', 'Geografía',DATE_SUB(CURDATE(), INTERVAL 1 YEAR));


-- Insertar ventas de trampitas para el día actual
INSERT INTO compraBonus (idUser,amount, totalPrice, created) VALUES
                                                                        (13, 10, 100, DATE_SUB(NOW(), INTERVAL 1 YEAR)),
                                                                         (14, 5, 50, DATE_SUB(NOW(), INTERVAL 1 YEAR)),
                                                                         (14, 20, 200, DATE_SUB(NOW(), INTERVAL 1 YEAR));