CREATE DATABASE questiones;

USE questiones;

CREATE TABLE usuario (
	id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
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
    active BOOLEAN NOT NULL
)
