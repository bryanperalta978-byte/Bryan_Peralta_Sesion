<?php
declare(strict_types=1);

function limpiarCadena(string $valor): string
{
    return trim($valor);
}

function correoValido(string $correo): bool
{
    return filter_var($correo, FILTER_VALIDATE_EMAIL) !== false;
}

function redirigir(string $ruta): void
{
    header("Location: {$ruta}");
    exit;
}

function usuarioAutenticado(): bool
{
    return isset($_SESSION['usuario_cedula']);
}

function requireAuth(): void
{
    // Protege paginas privadas para que solo entren usuarios autenticados.
    if (!usuarioAutenticado()) {
        $_SESSION['flash_error'] = 'Debes iniciar sesion para acceder.';
        redirigir('index.php');
    }
}

function setFlash(string $tipo, string $mensaje): void
{
    $_SESSION["flash_{$tipo}"] = $mensaje;
}

function getFlash(string $tipo): ?string
{
    $clave = "flash_{$tipo}";
    if (!isset($_SESSION[$clave])) {
        return null;
    }

    $mensaje = $_SESSION[$clave];
    unset($_SESSION[$clave]);

    return $mensaje;
}

function valorAnterior(string $clave): string
{
    return htmlspecialchars($_POST[$clave] ?? '', ENT_QUOTES, 'UTF-8');
}
