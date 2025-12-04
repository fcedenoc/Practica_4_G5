<?php
// Iniciar sesión antes de cualquier salida
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Mostrar errores para depuración (puedes quitar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
include __DIR__ . '/../php/conexionBD.php';
$mysqli = abrirConexion();

// Validar ID de la tarea
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id === 0) {
    header("Location: listaTareas.php");
    exit;
}

// Arreglo para errores
$errors = [];

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['tarea_nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $estado = intval($_POST['estado_id'] ?? 0);
    $urlImagen = null;

    // Validaciones
    if ($nombre === '') $errors[] = "El nombre es obligatorio.";
    if (strlen($nombre) > 255) $errors[] = "El nombre no puede superar 255 caracteres.";
    if ($descripcion === '') $errors[] = "La descripción es obligatoria.";
    if (strlen($descripcion) > 1000) $errors[] = "La descripción no puede superar 1000 caracteres.";
    if ($estado <= 0) $errors[] = "Debe seleccionar un estado válido.";

    // Manejo de imagen opcional
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

    // Actualizar tarea si no hay errores
    if (empty($errors)) {
        if ($urlImagen !== null) {
            $stmt = $mysqli->prepare("UPDATE tareaUsuario SET tarea_nombre=?, descripcion=?, estado_id=?, url_imagen=? WHERE id=?");
            $stmt->bind_param('ssisi', $nombre, $descripcion, $estado, $urlImagen, $id);
        } else {
            $stmt = $mysqli->prepare("UPDATE tareaUsuario SET tarea_nombre=?, descripcion=?, estado_id=? WHERE id=?");
            $stmt->bind_param('ssii', $nombre, $descripcion, $estado, $id);
        }
        $ok = $stmt->execute();
        $stmt->close();

        if ($ok) {
            cerrarConexion($mysqli);
            header("Location: listaTareas.php");
            exit;
        } else {
            $errors[] = 'Error al actualizar la tarea: ' . $mysqli->error;
        }
    }
}

// Consultar tarea para el usuario logueado
$userId = $_SESSION['id'] ?? 0;
$stmt = $mysqli->prepare(
    "SELECT t.id, t.tarea_nombre, t.descripcion, t.estado_id, t.url_imagen, e.nombre_estado
     FROM tareaUsuario t
     INNER JOIN estados e ON t.estado_id = e.id
     WHERE t.id = ? AND t.usuario_id = ?"
);
$stmt->bind_param("ii", $id, $userId);
$stmt->execute();
$taskData = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Obtener estados para el select
$estados = $mysqli->query("SELECT id, nombre_estado FROM estados ORDER BY id");

cerrarConexion($mysqli);

// Si no hay tarea, redirigir
if (!$taskData) {
    header("Location: listaTareas.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Tarea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/home.css">
</head>
<body>
<?php include __DIR__ . '/../php/componentes/navbar.php'; ?>

<div class="container mt-5">
    <div class="card p-4 shadow">
        <h3 class="text-center text-primary mb-4">Editar Tarea</h3>

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
                <input type="text" name="tarea_nombre" value="<?= htmlspecialchars($taskData['tarea_nombre']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3" required><?= htmlspecialchars($taskData['descripcion']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Estado</label>
                <select class="form-select" name="estado_id" required>
                    <?php while ($row = $estados->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>" <?= $row['id'] == $taskData['estado_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($row['nombre_estado']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Imagen (opcional, reemplaza la actual)</label>
                <input type="file" name="url_imagen" class="form-control">
                <?php if ($taskData['url_imagen']): ?>
                    <small class="form-text text-muted">
                        Imagen actual: <a href="<?= htmlspecialchars($taskData['url_imagen']) ?>" target="_blank">Ver</a>
                    </small>
                <?php endif; ?>
            </div>

            <div class="text-end">
                <button class="btn btn-success" type="submit">Guardar</button>
                <a href="listaTareas.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>