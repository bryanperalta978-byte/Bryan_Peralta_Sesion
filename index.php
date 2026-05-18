<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

if (usuarioAutenticado()) {
    redirigir('perfil.php');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Datos recibidos desde el formulario de inicio de sesion.
    $correo = limpiarCadena($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validacion basica antes de consultar la base.
    if ($correo === '' || $password === '') {
        $error = 'Todos los campos son obligatorios.';
    } elseif (!correoValido($correo)) {
        $error = 'Ingresa un correo valido.';
    } else {
        // Busca el usuario por correo para validar las credenciales.
        $stmt = $pdo->prepare('SELECT cedula, nombre, correo, password FROM usuarios WHERE correo = :correo LIMIT 1');
        $stmt->execute(['correo' => $correo]);
        $usuario = $stmt->fetch();

        // Verifica la contrasena usando password_verify.
        if (!$usuario || !password_verify($password, $usuario['password'])) {
            $error = 'Credenciales incorrectas.';
        } else {
            // Si el login es correcto, crea la sesion del usuario.
            session_regenerate_id(true);
            $_SESSION['usuario_cedula'] = $usuario['cedula'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_correo'] = $usuario['correo'];
            redirigir('perfil.php');
        }
    }
}

$flashError = getFlash('error');
$flashSuccess = getFlash('success');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesion</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main class="auth-layout">
        <section class="card">
            <h1>Iniciar sesion</h1>
            <p class="subtitle">Accede a tu zona privada.</p>

            <?php if ($flashError): ?>
                <div class="alert error"><?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <?php if ($flashSuccess): ?>
                <div class="alert success"><?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form method="post" class="form">
                <label for="correo">Correo</label>
                <input type="email" id="correo" name="correo" value="<?= valorAnterior('correo') ?>" required>

                <label for="password">Contrasena</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Entrar</button>
            </form>

            <p class="helper">No tienes cuenta? <a href="registro.php">Registrate aqui</a></p>
        </section>
    </main>
</body>
</html>
