<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// Solo un usuario con sesion activa puede cambiar su contrasena.
requireAuth();

$error = null;
$success = null;
$cedula = $_SESSION['usuario_cedula'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Datos requeridos para el cambio de contrasena.
    $actual = $_POST['password_actual'] ?? '';
    $nueva = $_POST['password_nueva'] ?? '';
    $confirmacion = $_POST['password_confirmacion'] ?? '';

    // Validaciones del formulario en el servidor.
    if ($actual === '' || $nueva === '' || $confirmacion === '') {
        $error = 'Todos los campos son obligatorios.';
    } elseif ($nueva !== $confirmacion) {
        $error = 'La nueva contrasena y su confirmacion no coinciden.';
    } else {
        $stmt = $pdo->prepare('SELECT password FROM usuarios WHERE cedula = :cedula LIMIT 1');
        $stmt->execute(['cedula' => $cedula]);
        $usuario = $stmt->fetch();

        // Verifica la contrasena actual usando password_verify.
        if (!$usuario || !password_verify($actual, $usuario['password'])) {
            $error = 'La contrasena actual es incorrecta.';
        } else {
            // Guarda la nueva contrasena cifrada con password_hash.
            $hash = password_hash($nueva, PASSWORD_DEFAULT);
            $update = $pdo->prepare('UPDATE usuarios SET password = :password WHERE cedula = :cedula');
            $update->execute([
                'password' => $hash,
                'cedula' => $cedula,
            ]);
            $success = 'Contrasena actualizada correctamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar contrasena</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main class="dashboard">
        <section class="card profile-card">
            <div class="topbar">
                <div>
                    <h1>Cambiar contrasena</h1>
                    <p class="subtitle">Actualiza tu clave de acceso.</p>
                </div>
                <a class="link-button" href="perfil.php">Volver al perfil</a>
            </div>

            <?php if ($error): ?>
                <div class="alert error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form method="post" class="form">
                <label for="password_actual">Contrasena actual</label>
                <input type="password" id="password_actual" name="password_actual" required>

                <label for="password_nueva">Nueva contrasena</label>
                <input type="password" id="password_nueva" name="password_nueva" required>

                <label for="password_confirmacion">Confirmar nueva contrasena</label>
                <input type="password" id="password_confirmacion" name="password_confirmacion" required>

                <button type="submit">Actualizar contrasena</button>
            </form>
        </section>
    </main>
</body>
</html>
