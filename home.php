<?php

ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
}

if(!isset($_SESSION['usuario'])){ 
    header("Location: index.php");
    exit(); 
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/home.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Inicio</title>
</head>

<body>

<?php


include __DIR__ . '/php/conexionBD.php'; 
$mysqli = abrirConexion(); 

$userId = $_SESSION['id'] ?? 0; 
$sql = "
SELECT t.id, t.tarea_nombre, t.descripcion, e.nombre_estado, t.fecha_creacion
FROM tareaUsuario t
INNER JOIN estados e ON t.estado_id = e.id
WHERE t.usuario_id = ?
ORDER BY t.fecha_creacion DESC
LIMIT 5
";

$stmt = $mysqli->prepare($sql); 
$stmt->bind_param("i", $userId); 
$stmt->execute(); 
$resultado = $stmt->get_result(); 

function limitarNombre($nombre) {
    if (strlen($nombre) > 50) {
        return substr($nombre, 0, 50) . '...';
    }
    return $nombre;
}


if (!$resultado) { 
    die("Error en la consulta: " . $mysqli->error);
}

$sql_total = "SELECT COUNT(*) as total FROM tareaUsuario WHERE usuario_id = ?"; 
$stmt_total = $mysqli->prepare($sql_total);
$stmt_total->bind_param("i", $userId);
$stmt_total->execute();
$total_tareas = $stmt_total->get_result()->fetch_assoc()['total'];

$sql_estados = "SELECT e.nombre_estado, COUNT(t.id) as cantidad FROM estados e LEFT JOIN tareaUsuario t ON e.id = t.estado_id AND t.usuario_id = ? GROUP BY e.id"; // Contar tareas por estado del usuario.
$stmt_estados = $mysqli->prepare($sql_estados);
$stmt_estados->bind_param("i", $userId);
$stmt_estados->execute();
$result_estados = $stmt_estados->get_result();
$estadisticas = []; 
while ($row = $result_estados->fetch_assoc()) { 
    $estadisticas[] = $row;
}
?>

    <?php include __DIR__ . '/php/componentes/navbar.php'; ?>

    <div class="container my-5">

        
        <section>
            <h2>Resumen de Tareas</h2>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Total Tareas</h5>
                            <p class="card-text fs-3"><?=$total_tareas?></p>
                        </div>
                    </div>
                </div>
                <?php foreach ($estadisticas as $stat): ?>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title"><?=$stat['nombre_estado']?></h5>
                            <p class="card-text fs-3"><?=$stat['cantidad']?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>


       
        <section class="mt-5">
            <h2>Lista de Tareas</h2>

            <div class="d-flex flex-wrap gap-3">

<?php while ($fila = $resultado->fetch_assoc()):
    $bgColor = '#f8f9fa'; 
    switch (strtolower($fila['nombre_estado'])) {
        case 'pendiente':
            $bgColor = '#e76875ff'; 
            break;
        case 'en progreso':
            $bgColor = '#f0df89ff'; 
            break;
        case 'completada':
            $bgColor = '#a6ddaeff';
            break;
    }
?>
    <div class="card mb-3" style="max-width: 18rem; background-color: <?=$bgColor?>;">
        <div class="card-header">
            <?= htmlspecialchars($fila['nombre_estado']) ?>
        </div>

        <div class="card-body">
            <h5 class="card-title">
                <?= htmlspecialchars(strlen($fila['tarea_nombre']) > 50 ? substr($fila['tarea_nombre'], 0, 50) . '...' : $fila['tarea_nombre']) ?>
            </h5>

            <p class="card-text">
                <?= htmlspecialchars($fila['descripcion']) ?>
            </p>

            <p class="card-text">
                <small class="text-muted">
                    Creada: <?= htmlspecialchars($fila['fecha_creacion']) ?>
                </small>
            </p>
        </div>
    </div>
<?php endwhile; ?> 

</div>

            <div class="text-center mt-3">
                <a href="Tareas/listaTareas.php" class="btn btn-primary">Ver Todas las Tareas</a>
            </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/home.js"></script>

</body>

</html> 
