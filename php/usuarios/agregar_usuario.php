<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['usuario'])){
    header("Location: ../../index.php");
    exit();
}

include '../conexionBD.php';

$mysqli = abrirConexion();

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $contrasenna = trim($_POST['contrasenna'] ?? '');
    $fechaNacimiento = trim($_POST['fecha_nacimiento'] ?? '');
    $genero = trim($_POST['genero'] ?? '');

    // Validaciones
    if ($nombre === '') $errors[] = "Nombre es obligatorio.";
    if (strlen($nombre) > 255) $errors[] = "Nombre no puede superar 255 caracteres.";
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errors[] = "Correo no válido.";
    if (strlen($correo) > 255) $errors[] = "Correo no puede superar 255 caracteres.";
    if ($usuario === '') $errors[] = "Usuario es obligatorio.";
    if (strlen($usuario) > 50) $errors[] = "Usuario no puede superar 50 caracteres.";
    if ($contrasenna === '') $errors[] = "Contraseña es obligatoria.";
    if ($contrasenna !== '' && strlen($contrasenna) < 6) $errors[] = "La contraseña debe contener al menos 6 caracteres.";
    if (strlen($contrasenna) > 255) $errors[] = "Contraseña no puede superar 255 caracteres.";

    if (empty($errors)) {
        
        $stmt_check = $mysqli->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $stmt_check->bind_param("s", $correo);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        if ($result_check->num_rows > 0) {
            $errors[] = "El correo ya está registrado.";
        }
        $stmt_check->close();

        
        $stmt_check2 = $mysqli->prepare("SELECT id FROM usuarios WHERE usuario = ?");
        $stmt_check2->bind_param("s", $usuario);
        $stmt_check2->execute();
        $result_check2 = $stmt_check2->get_result();
        if ($result_check2->num_rows > 0) {
            $errors[] = "El nombre de usuario ya está en uso.";
        }
        $stmt_check2->close();

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

            }
            $stmt_check2->close();
        }
        $stmt_check->close();
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