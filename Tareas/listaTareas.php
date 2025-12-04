<?php

ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['usuario'])){
    header("Location: ../index.php");
    exit();
}

include __DIR__ . '/../php/conexionBD.php'; 

$mysqli = abrirConexion(); 


$userId = $_SESSION['id'] ?? 0; 


$sql = "
SELECT t.id, t.tarea_nombre, t.descripcion, e.nombre_estado, t.fecha_creacion
FROM tareaUsuario t
INNER JOIN estados e ON t.estado_id = e.id
WHERE t.usuario_id = ?
ORDER BY t.fecha_creacion DESC
";

$stmt = $mysqli->prepare($sql); 
$stmt->bind_param("i", $userId); 
$stmt->execute(); 
$resultado = $stmt->get_result(); 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Listado de Tareas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> 

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> 


</head>
<body>

<?php include __DIR__ . '/../php/componentes/navbar.php'?>

<div class="container mt-5"> 

    <div class="card p-4 shadow"> 

        <div class="d-flex justify-content-between mb-5"> 
            <h3>Lista de Tareas</h3>
            <a class="btn btn-success" href="agregaTarea.php">+ Agregar Tarea</a> 
        </div>

        <table id="tabla" class="table table-striped table-hover align-middle"> 
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tarea</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Fecha Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($t = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= $t['id'] ?></td>
                    <td><?= htmlspecialchars($t['tarea_nombre']) ?></td>
                    <td><?= htmlspecialchars($t['descripcion']) ?></td>
                    <td><?= htmlspecialchars($t['nombre_estado']) ?></td>
                    <td><?= $t['fecha_creacion'] ?></td>
                    <td>
                     <a class="btn btn-secondary btn-sm" href="/Sem3chepeo/Tareas/editar_tarea.php?id=<?= $t['id'] ?>">Editar</a>
                     <a onclick="return confirm('¿Deseas eliminar esta tarea?')" class="btn btn-danger btn-sm"
                        href="/Sem3chepeo/Tareas/eliminaTarea.php?id=<?= $t['id'] ?>">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>

</div>


<?php cerrarConexion($mysqli); ?> 
</body>
</html>






