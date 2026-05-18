<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Credenciales de conexion a MySQL.
$dbHost = '127.0.0.1';
$dbPort = '3306';
$dbName = 'sistema_perfil_usuario';
$dbUser = 'root';
$dbPass = 'Qedazcwxs123@';

try {
    // Conexion inicial al servidor para crear la base si todavia no existe.
    $pdoServer = new PDO(
        "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    $pdoServer->exec(
        "CREATE DATABASE IF NOT EXISTS {$dbName} CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci"
    );

    // Conexion principal a la base de datos de la aplicacion.
    $pdo = new PDO(
        "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    // Tabla requerida para almacenar usuarios del sistema.
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS usuarios (
            cedula VARCHAR(10) NOT NULL,
            nombre VARCHAR(100) NOT NULL,
            correo VARCHAR(150) NOT NULL,
            password VARCHAR(255) NOT NULL,
            fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (cedula),
            UNIQUE KEY uq_correo (correo)
        )'
    );
} catch (PDOException $exception) {
    exit('Error de conexion con la base de datos: ' . htmlspecialchars($exception->getMessage()));
}
