<?php
// Este archivo permite agregar una nueva tarea al sistema.

if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Iniciar sesión para verificar usuario.
}

if(!isset($_SESSION['usuario'])){
    header("Location: ../index.php");
    exit();
}

ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include __DIR__ . '/../php/conexionBD.php'; // Incluir conexión a BD.

$mysqli = abrirConexion(); // Abrir conexión.

$success = false; // Variable para saber si se guardó correctamente.
$errors = []; // Array para errores.
$userId = $_SESSION['id'] ?? 0; // ID del usuario logueado.

$estados = $mysqli->query("SELECT id, nombre_estado FROM estados ORDER BY id"); // Obtener estados disponibles.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['tarea_nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $estado = intval($_POST['estado_id'] ?? 0);
    $urlImagen = null;

    if ($nombre === '') $errors[] = "El nombre es obligatorio.";
    if ($descripcion === '') $errors[] = "La descripciÃ³n es obligatoria.";
    if (strlen($nombre) > 255) $errors[] = "El nombre no puede superar 255 caracteres.";
    if (strlen($descripcion) > 1000) $errors[] = "La descripción no puede superar 1000 caracteres.";
    if ($estado <= 0) $errors[] = "Debe seleccionar un estado vÃ¡lido.";

    if (!empty($_FILES['url_imagen']['name'])) {

        if ($_FILES['url_imagen']['error'] === UPLOAD_ERR_OK) {

            $permitidos = ['image/jpeg','image/png','image/webp'];
            $tipo = $_FILES['url_imagen']['type'];

            if (!in_array($tipo, $permitidos)) {
                $errors[] = "Formato de imagen no permitido.";
            } elseif ($_FILES['url_imagen']['size'] > 2 * 1024 * 1024) {
                $errors[] = "La imagen supera 2MB.";
            } else {

                $ext = pathinfo($_FILES['url_imagen']['name'], PATHINFO_EXTENSION);
                $nuevoNombre = uniqid('img_') . '.' . $ext;

                $uploadDir = __DIR__ . '/../uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                $destino = $uploadDir . $nuevoNombre;

                if (move_uploaded_file($_FILES['url_imagen']['tmp_name'], $destino)) {
                    $urlImagen = 'uploads/' . $nuevoNombre;
                } else {
                    $errors[] = "Error al subir la imagen.";
                }
            }

        } else {
            $errors[] = "Error al procesar la imagen.";
        }
    }

    if (empty($errors)) {

        $stmt = $mysqli->prepare(
            "INSERT INTO tareaUsuario (tarea_nombre, descripcion, estado_id, usuario_id, url_imagen)
             VALUES (?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            $errors[] = "Error al preparar la consulta.";
        } else {

            $stmt->bind_param(
                "ssiis",
                $nombre,
                $descripcion,
                $estado,
                $userId,
                $urlImagen
            );

            if (!$stmt->execute()) {
                $errors[] = "No se pudo guardar la tarea.";
            } else {
                $success = true;
            }

            $stmt->close();
        }
    }

    cerrarConexion($mysqli);

    if ($success) {
        header("Location: listaTareas.php"); // Redirigir a la lista de tareas después de guardar.
        exit;
    }
}
?>

<!-- Página para agregar una nueva tarea -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Tarea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/home.css"> <!-- CSS personalizado -->
</head>
<body>

<?php include __DIR__ . '/../php/componentes/navbar.php'?> <!-- Incluir barra de navegación -->


<div class="container mt-5">

    <div class="card p-4 shadow">

        <h3 class="text-center text-primary mb-4">Agregar Nueva Tarea</h3>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" novalidate>

            <div class="mb-3">
                <label class="form-label">Nombre de la tarea</label>
                <input type="text" name="tarea_nombre" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Estado</label>
                <select class="form-select" name="estado_id">
                    <?php while ($row = $estados->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>">
                            <?= $row['nombre_estado'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Imagen (opcional)</label>
                <input type="file" name="url_imagen" class="form-control">
            </div>

            <div class="text-end">
                <button class="btn btn-success" type="submit">Guardar</button>
                <a href="listaTareas.php" class="btn btn-secondary">Cancelar</a>
            </div>

        </form>
    </div>
</div>
</body>
</html>

