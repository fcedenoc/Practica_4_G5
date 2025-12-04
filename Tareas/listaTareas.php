<?php
// Este archivo muestra la lista completa de tareas en una tabla.

ini_set('display_errors', 1); // Mostrar errores.
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include __DIR__ . '/../php/conexionBD.php'; // Incluir conexión a BD.

$mysqli = abrirConexion(); // Abrir conexión.

$sql = " // Consulta para obtener todas las tareas con su estado.
SELECT t.id, t.tarea_nombre, t.descripcion, e.nombre_estado, t.fecha_creacion
FROM tareaUsuario t
INNER JOIN estados e ON t.estado_id = e.id
ORDER BY t.fecha_creacion DESC
";

$resultado = $mysqli->query($sql); // Ejecutar consulta.

?>
<!-- Página para listar todas las tareas -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Listado de Tareas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap -->

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> <!-- jQuery -->

    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet"> <!-- DataTables CSS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

</head>
<body>

<?php include __DIR__ . '/../php/componentes/navbar.php'?> <!-- Incluir la barra de navegación -->

<div class="container mt-5"> <!-- Contenedor principal -->

    <div class="card p-4 shadow"> <!-- Tarjeta con la tabla -->

        <div class="d-flex justify-content-between mb-5"> <!-- Encabezado con título y botón -->
            <h3>Lista de Tareas</h3>
            <a class="btn btn-success" href="agregaTarea.php">+ Agregar Tarea</a> <!-- Botón para agregar nueva tarea -->
        </div>

        <table id="tabla" class="table table-striped table-hover align-middle"> <!-- Tabla para mostrar las tareas -->
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

<script> <!-- Script para inicializar DataTable con idioma español -->
$(document).ready(function(){
    $('#tabla').dataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    })
})
</script>

<?php cerrarConexion($mysqli); ?> <!-- Cerrar la conexión a la base de datos -->
</body>
</html>






