CREATE DATABASE IF NOT EXISTS sistema_perfil_usuario
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE sistema_perfil_usuario;

CREATE TABLE IF NOT EXISTS usuarios (
    cedula VARCHAR(10) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL,
    password VARCHAR(255) NOT NULL,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (cedula),
    UNIQUE KEY uq_correo (correo)
);

