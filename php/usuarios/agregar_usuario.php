<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../conexionBD.php';

$mysqli = abrirConexion();

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Tomar los datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $contrasenna = trim($_POST['contrasenna'] ?? '');
    $fechaNacimiento = trim($_POST['fecha_nacimiento'] ?? '');
    $genero = trim($_POST['genero'] ?? '');

    // Validaciones
    if ($nombre === '') $errors[] = "Nombre es obligatorio.";
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errors[] = "Correo no válido.";
    if ($usuario === '') $errors[] = "Usuario es obligatorio.";
    if ($contrasenna === '') $errors[] = "Contraseña es obligatoria.";
    if ($contrasenna !== '' && strlen($contrasenna) < 6) $errors[] = "La contraseña debe contener al menos 6 caracteres.";

    if (empty($errors)) {
        $clave = password_hash($contrasenna, PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare("INSERT INTO usuarios(nombre, correo, usuario, clave, fecha_nacimiento, genero) VALUES(?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            $errors[] = "Error en prepare al insertar el usuario: " . $mysqli->error;
        } else {
            $stmt->bind_param('ssssss', $nombre, $correo, $usuario, $clave, $fechaNacimiento, $genero);

            if (!$stmt->execute()) {
                $errors[] = "Error al ejecutar la inserción del usuario: " . $stmt->error;
            } else {
                $success = true;
            }

            $stmt->close();
        }
    }

    if ($success) {
        cerrarConexion($mysqli);
        header("Location: listar_usuarios.php");
        exit;
    }
}

cerrarConexion($mysqli);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear nuevo usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    
<?php include '../componentes/navbar.php'; ?>

<div class="container mt-5">

    <div class="card p-4 shadow">

        <h3 class="text-center text-success mb-4">Agregar Nuevo Usuario</h3>

         <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
            <ul>
              <?php foreach($errors as $e): ?>
                  <li><?= htmlspecialchars($e) ?></li>
              <?php endforeach; ?>
            </ul>
      </div>
    <?php endif; ?>
         
        <form novalidate method="post">

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control">
            </div>

            <div class="mb-3">
                <label for="correo" class="form-label">Correo</label>
                <input type="email" name="correo" id="correo" class="form-control">
            </div>

            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" name="usuario" id="usuario" class="form-control">
            </div>

            <div class="mb-3">
                <label for="contrasenna" class="form-label">Contraseña</label>
                <input type="password" name="contrasenna" id="contrasenna" class="form-control">
            </div>

            <div class="mb-3">
                <label for="fecha_nacimiento" class="form-label">Fecha Nacimiento</label>
                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control">
            </div>
            
            <div class="mb-3">
                <label for="genero" class="form-label">Genero</label>
                <select class="form-select" name="genero" id="genero">
                    <option value="masculino">Masculino</option>
                    <option value="femenino">Femenino</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
            <div class="text-end">
                <button class="btn btn-success" type="submit">Guardar</button>
                <a href="listar_usuarios.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>