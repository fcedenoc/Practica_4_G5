<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include __DIR__ . '/../php/conexionBD.php';

$mysqli = abrirConexion();

$sql = "
SELECT t.id, t.tarea_nombre, t.descripcion, e.nombre_estado, t.fecha_creacion 
FROM tareaUsuario t
INNER JOIN estados e ON t.estado_id = e.id
ORDER BY t.fecha_creacion DESC
";

$resultado = $mysqli->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Listado de Tareas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

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
                     <a class="btn btn-secondary btn-sm" href="editar_tarea.php?id=<?= $t['id'] ?>">Editar</a>
                     <a onclick="return confirm('¿Deseas eliminar esta tarea?')" class="btn btn-danger btn-sm"
                        href="eliminar_tarea.php?id=<?= $t['id'] ?>">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>

</div>

<script>
$(document).ready(function(){
    $('#tabla').dataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    })
})
</script>

<?php cerrarConexion($mysqli); ?>
</body>
</html>






