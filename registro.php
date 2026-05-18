<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

if (usuarioAutenticado()) {
    redirigir('perfil.php');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura de datos del formulario de registro.
    $cedula = limpiarCadena($_POST['cedula'] ?? '');
    $nombre = limpiarCadena($_POST['nombre'] ?? '');
    $correo = limpiarCadena($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validacion de campos vacios y formato de correo.
    if ($cedula === '' || $nombre === '' || $correo === '' || $password === '') {
        $error = 'Todos los campos son obligatorios.';
    } elseif (!correoValido($correo)) {
        $error = 'El correo no tiene un formato valido.';
    } else {
        // Verifica que el correo no este repetido.
        $stmt = $pdo->prepare('SELECT correo FROM usuarios WHERE correo = :correo LIMIT 1');
        $stmt->execute(['correo' => $correo]);

        if ($stmt->fetch()) {
            $error = 'Ese correo ya esta registrado.';
        } else {
            // La contrasena se guarda cifrada con password_hash.
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare(
                'INSERT INTO usuarios (cedula, nombre, correo, password) VALUES (:cedula, :nombre, :correo, :password)'
            );

            try {
                $insert->execute([
                    'cedula' => $cedula,
                    'nombre' => $nombre,
                    'correo' => $correo,
                    'password' => $hash,
                ]);
                setFlash('success', 'Registro completado. Ya puedes iniciar sesion.');
                redirigir('index.php');
            } catch (PDOException $exception) {
                if ((int) $exception->getCode() === 23000) {
                    $error = 'La cedula o el correo ya existen.';
                } else {
                    $error = 'No se pudo registrar el usuario.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main class="auth-layout">
        <section class="card">
            <h1>Registro de usuario</h1>
            <p class="subtitle">Crea tu cuenta para ingresar al sistema.</p>

            <?php if ($error): ?>
                <div class="alert error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form method="post" class="form">
                <label for="cedula">Cedula</label>
                <input type="text" id="cedula" name="cedula" maxlength="10" value="<?= valorAnterior('cedula') ?>" required>

                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" maxlength="100" value="<?= valorAnterior('nombre') ?>" required>

                <label for="correo">Correo</label>
                <input type="email" id="correo" name="correo" maxlength="150" value="<?= valorAnterior('correo') ?>" required>

                <label for="password">Contrasena</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Registrarme</button>
            </form>

            <p class="helper">Ya tienes cuenta? <a href="index.php">Inicia sesion</a></p>
        </section>
    </main>
</body>
</html>
