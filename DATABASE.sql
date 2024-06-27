DROP DATABASE IF EXISTS qampa;

CREATE DATABASE IF NOT EXISTS qampa;


USE qampa;

CREATE TABLE usuario (
                         id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
                         fullname VARCHAR(255) NOT NULL,
                         yearOfBirth INT NOT NULL,
                         gender VARCHAR(255) NOT NULL,
                         country VARCHAR(255) NOT NULL,
                         city VARCHAR(255) NOT NULL,
                         email VARCHAR(255) NOT NULL,
                         pass VARCHAR(255) NOT NULL,
                         username VARCHAR(255) NOT NULL,
                         profilePicture VARCHAR(255) NOT NULL,
                         token VARCHAR(255) NOT NULL,
                         active BOOLEAN NOT NULL,
                         answeredQuestions INT DEFAULT 0,
                         correctAnswers INT DEFAULT 0,
                         userRole VARCHAR(255) DEFAULT 'player',
                         dateCreated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pregunta (
                          idQuestion INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
                          question VARCHAR(255) NOT NULL,
                          category VARCHAR(255) NOT NULL,
                          dateCreated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          correctAnswers INT DEFAULT 0,
                          totalAnswers INT DEFAULT 0,
                          difficulty VARCHAR(255) DEFAULT 'easy'
);

CREATE TABLE usuario_pregunta (
                                  idUsuario INT NOT NULL,
                                  idPregunta INT NOT NULL,
                                  PRIMARY KEY (idUsuario, idPregunta),
                                  FOREIGN KEY (idUsuario) REFERENCES usuario(id),
                                  FOREIGN KEY (idPregunta) REFERENCES pregunta(idQuestion)
);

CREATE TABLE respuesta (
                           idAnswer INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
                           idQuestion INT NOT NULL,
                           answer VARCHAR(255) NOT NULL,
                           correct BOOLEAN NOT NULL,
                           FOREIGN KEY (idQuestion) REFERENCES pregunta(idQuestion)
);

CREATE TABLE partida (
                         id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
                         score INT NOT NULL,
                         dateGame TIMESTAMP NOT NULL,
                         idUser INT NOT NULL,
                         FOREIGN KEY (idUser) REFERENCES usuario(id)
);

CREATE TABLE report (
                        idReport INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
                        idQuestion INT NOT NULL,
                        idUser INT NOT NULL,
                        reason TEXT NOT NULL,
                        dateReported TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (idQuestion) REFERENCES pregunta(idQuestion),
                        FOREIGN KEY (idUser) REFERENCES usuario(id)
);

CREATE TABLE pregunta_sugerida
(
    idSuggestion INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    idUser       INT                            NOT NULL,
    question     VARCHAR(255)                   NOT NULL,
    category     VARCHAR(255)                   NOT NULL,
    answer1      VARCHAR(255)                   NOT NULL,
    answer2      VARCHAR(255)                   NOT NULL,
    answer3      VARCHAR(255)                   NOT NULL,
    answer4      VARCHAR(255)                   NOT NULL,
    correct      INT                            NOT NULL,
    FOREIGN KEY (idUser) REFERENCES usuario (id)
);

CREATE TABLE challenge (
                           id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
                           challenger_id INT NOT NULL,
                           challenged_id INT NOT NULL,
                           challenger_score INT DEFAULT 0,
                           challenged_score INT DEFAULT 0,
                           status ENUM('pending', 'accepted', 'resolved') NOT NULL,
                           FOREIGN KEY (challenger_id) REFERENCES usuario(id),
                           FOREIGN KEY (challenged_id) REFERENCES usuario(id)
);

INSERT INTO usuario (fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, token, active, userRole)
VALUES ('Messi', 1990, 'Masculino', 'Argentina', 'Rosario', 'usuario@email.com', '123', 'Leo', 'public/img/9163b1ee956ebfc8d3e37edba53d7d0b.png', 'tokenUsuario', 1, 'player');


INSERT INTO usuario (fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, token, active, userRole)
VALUES ('Pancho', 1985, 'Masculino', 'Argentina', 'Buenos Aires', 'editor@email.com', '123', 'Pancho', 'public/img/pancho.png', 'tokenEditor', 1, 'editor');


INSERT INTO usuario (fullname, yearOfBirth, gender, country, city, email, pass, username, profilePicture, token, active, userRole) VALUES
('El admin', 1990, 'masculino', 'argentina', 'buenos aires', 'admin@gmail.com', '1234', 'admin', 'public/img/1.jpg', '1234567890qwerty1', '1', 'admin');

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