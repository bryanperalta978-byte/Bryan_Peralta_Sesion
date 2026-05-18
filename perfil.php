<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// Verifica que exista una sesion activa antes de mostrar el perfil.
requireAuth();

$cedula = $_SESSION['usuario_cedula'];
$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Permite actualizar nombre y correo desde la zona privada.
    $nombre = limpiarCadena($_POST['nombre'] ?? '');
    $correo = limpiarCadena($_POST['correo'] ?? '');

    // Validacion del lado del servidor para los datos actualizados.
    if ($nombre === '' || $correo === '') {
        $error = 'Nombre y correo son obligatorios.';
    } elseif (!correoValido($correo)) {
        $error = 'Debes ingresar un correo valido.';
    } else {
        // Evita guardar un correo que ya pertenezca a otro usuario.
        $validar = $pdo->prepare('SELECT cedula FROM usuarios WHERE correo = :correo AND cedula <> :cedula LIMIT 1');
        $validar->execute([
            'correo' => $correo,
            'cedula' => $cedula,
        ]);

        if ($validar->fetch()) {
            $error = 'El correo ya pertenece a otro usuario.';
        } else {
            $update = $pdo->prepare('UPDATE usuarios SET nombre = :nombre, correo = :correo WHERE cedula = :cedula');
            $update->execute([
                'nombre' => $nombre,
                'correo' => $correo,
                'cedula' => $cedula,
            ]);

            $_SESSION['usuario_nombre'] = $nombre;
            $_SESSION['usuario_correo'] = $correo;
            $success = 'Datos actualizados correctamente.';
        }
    }
}

// Consulta los datos del usuario autenticado para mostrarlos en pantalla.
$stmt = $pdo->prepare('SELECT cedula, nombre, correo, fecha_registro FROM usuarios WHERE cedula = :cedula LIMIT 1');
$stmt->execute(['cedula' => $cedula]);
$usuario = $stmt->fetch();

if (!$usuario) {
    session_unset();
    session_destroy();
    redirigir('index.php');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main class="dashboard">
        <section class="card profile-card">
            <div class="topbar">
                <div>
                    <h1>Zona privada</h1>
                    <p class="subtitle">Bienvenido, <?= htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <a class="link-button danger" href="logout.php">Cerrar sesion</a>
            </div>

            <?php if ($error): ?>
                <div class="alert error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <div class="info-grid">
                <div class="info-box">
                    <span>Cedula</span>
                    <strong><?= htmlspecialchars($usuario['cedula'], ENT_QUOTES, 'UTF-8') ?></strong>
                </div>
                <div class="info-box">
                    <span>Fecha de registro</span>
                    <strong><?= htmlspecialchars($usuario['fecha_registro'], ENT_QUOTES, 'UTF-8') ?></strong>
                </div>
            </div>

            <form method="post" class="form">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" maxlength="100" value="<?= htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8') ?>" required>

                <label for="correo">Correo</label>
                <input type="email" id="correo" name="correo" maxlength="150" value="<?= htmlspecialchars($usuario['correo'], ENT_QUOTES, 'UTF-8') ?>" required>

                <button type="submit">Guardar cambios</button>
            </form>

            <div class="actions">
                <a class="link-button" href="cambiar_password.php">Cambiar contrasena</a>
            </div>
        </section>
    </main>
</body>
</html>
