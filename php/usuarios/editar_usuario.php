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

$mysqli= abrirConexion();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    header("Location: listar_usuarios.php");
    exit;
}

$errors = [];

// Actualizar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    //Tomar los datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $contrasenna = trim($_POST['contrasenna'] ?? '');
    $fechaNacimiento = trim($_POST['fecha_nacimiento'] ?? '');
    $genero = trim($_POST['genero'] ?? '');

    //Validaciones
    if($nombre == '') $errors[] = "Nombre es obligatorio.";
    if(strlen($nombre) > 255) $errors[] = "Nombre no puede superar 255 caracteres.";

    if(!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Correo no válido.";
    }
    if(strlen($correo) > 255) $errors[] = "Correo no puede superar 255 caracteres.";

    if($usuario == '') $errors[] = "Usuario es obligatorio.";
    if(strlen($usuario) > 50) $errors[] = "Usuario no puede superar 50 caracteres.";

    if($contrasenna != '' && strlen($contrasenna) < 6) {
        $errors[] = "La contraseña debe contener al menos 6 caracteres.";
    }
    if($contrasenna != '' && strlen($contrasenna) > 255) $errors[] = "Contraseña no puede superar 255 caracteres.";

    if (empty($errors)) {

        if ($contrasenna != '') {
            $clave = password_hash($contrasenna, PASSWORD_DEFAULT);

            $stmt = $mysqli->prepare("UPDATE usuarios 
            SET nombre=?, correo=?, usuario=?, clave=?, fecha_nacimiento=?, genero=? 
            WHERE id = ?");
            $stmt->bind_param('ssssssi', $nombre, $correo, $usuario, $clave, $fechaNacimiento, $genero, $id);

        } else {

            $stmt = $mysqli->prepare("UPDATE usuarios 
            SET nombre=?, correo=?, usuario=?, fecha_nacimiento=?, genero=? 
            WHERE id = ?");
            $stmt->bind_param('sssssi', $nombre, $correo, $usuario, $fechaNacimiento, $genero, $id);
        }

        $ok = $stmt->execute();

        if($ok){
            cerrarConexion($mysqli);
            header("Location: listar_usuarios.php");
            exit;
        } else {
            $errors[] = 'Error al actualizar al usuario. Error: ' . $mysqli->error;
        }
    }
}

// Consultar usuario
$stmt = $mysqli->prepare("SELECT id, nombre, correo, usuario, fecha_nacimiento, genero FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$userData = $stmt->get_result()->fetch_assoc();

$stmt->close();

cerrarConexion($mysqli);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    
<?php include '../componentes/navbar.php'; ?>

<div class="container mt-5">

    <div class="card p-4 shadow">

        <h3 class="text-center text-success mb-4">Editar Usuario</h3>

         <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
            <ul>
              <?php foreach($errors as $e): ?>
                  <li><?= htmlspecialchars($e)?></li>
              <?php endforeach ;?>
            </ul>
      </div>
    <?php endif; ?>
         
        <form novalidate method="post">

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" name="nombre" id="nombre" value=<?= htmlspecialchars($userData['nombre']) ?> class="form-control">
            </div>

            <div class="mb-3">
                <label for="correo" class="form-label">Correo</label>
                <input type="email" name="correo" value=<?= htmlspecialchars($userData['correo']) ?> id="correo" class="form-control">
            </div>

            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" name="usuario" value=<?= htmlspecialchars($userData['usuario']) ?> id="usuario" class="form-control">
            </div>

            <div class="mb-3">
                <label for="contrasenna" class="form-label">Nueva Contraseña (Opcional)</label>
                <input type="password" name="contrasenna" id="contrasenna" class="form-control">
            </div>

            <div class="mb-3">
                <label for="fechaNacimiento" class="form-label">Fecha Nacimiento</label>
                <input type="date" name="fecha_nacimiento" id="fechaNacimiento" value="<?= htmlspecialchars($userData['fecha_nacimiento']) ?>" class="form-control">
            </div>
            
           <div class="mb-3">
    <label for="genero" class="form-label">Género</label>
    <select class="form-select" name="genero" id="genero">
        <?php $sel = $userData['genero']; ?>
        <option value="masculino" <?= $sel === 'masculino' ? 'selected' : '' ?>>Masculino</option>
        <option value="femenino" <?= $sel === 'femenino' ? 'selected' : '' ?>>Femenino</option>
        <option value="otro" <?= $sel === 'otro' ? 'selected' : '' ?>>Otro</option>
    </select>
</div>
            <div class="text-end">
                <button class="btn btn-success" type="submit">Guardar</button>
                <a href="listar_usuarios.php" class="btn btn-secondary" type= "submit" >Cancelar</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>