# Sistema de perfil de usuario en PHP

Proyecto en PHP puro con:

- Registro de usuarios
- Login con sesiones
- Perfil privado
- Cambio de contrasena
- Logout
- MySQL con `password_hash()` y `password_verify()`

## Base de datos

Credenciales configuradas en `config.php`:

- Host: `127.0.0.1`
- Puerto: `3306`
- Base de datos: `sistema_perfil_usuario`
- Usuario: `root`
- Contrasena: `Qedazcwxs123@`

Script SQL incluido en `database.sql`.

## Archivos principales

- `index.php`: inicio de sesión
- `registro.php`: registro de usuario
- `perfil.php`: zona privada con actualización de nombre y correo
- `cambiar_password.php`: cambio de contrasena
- `logout.php`: cierre de sesión

## Cómo ejecutar en local

1. Asegúrate de tener Apache o un servidor PHP local con MySQL.
2. Crea la base de datos ejecutando `database.sql`.
3. Copia esta carpeta dentro de `htdocs` si usas XAMPP, o publícala en tu servidor local.
4. Abre en el navegador:

```text
http://localhost/Bryan_Peralta_php_sql_sesion/index.php
```

Si usas el servidor embebido de PHP, ejecuta desde esta carpeta:

```text
php -S localhost:8000
```

y abre:

```text
http://localhost:8000
```
