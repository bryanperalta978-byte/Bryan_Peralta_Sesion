<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// Cierra la sesion activa y regresa al login.
session_unset();
session_destroy();

session_start();
setFlash('success', 'Sesion finalizada correctamente.');
redirigir('index.php');
