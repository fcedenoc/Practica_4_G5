<?php
// Este archivo muestra la lista de todos los usuarios registrados.

ini_set('display_errors', 1); // Mostrar errores.
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../conexionBD.php'; // Incluir conexión.

$mysqli= abrirConexion(); // Abrir conexión.

$resultado = $mysqli->query("SELECT id, nombre, correo, usuario, fecha_nacimiento, genero FROM usuarios"); // Obtener usuarios.

cerrarConexion($mysqli); // Cerrar conexión.

?>

<!-- Página para listar usuarios -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Usuarios</title>
      <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> <!-- jQuery -->

    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet"> <!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

</head>
<body>

<?php include '../componentes/navbar.php'?> <!-- Barra de navegación -->

<div class="container mt-5"> <!-- Contenedor -->

    <div class="card p-4 shadow"> <!-- Tarjeta -->

        <div class="d-flex justify-content-between mb-5"> <!-- Encabezado -->
            <h3>Usuarios Registrados</h3>
            <a class="btn btn-success" href="">+ Agregar Usuario</a> <!-- Botón (no funciona aún) -->
        </div>

        <table id="tabla" class="table table-striped table-hover align-middle" > <!-- Tabla de usuarios -->
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Usuario</th>
                    <th>Fecha Nac.</th>
                    <th>Género</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $fila['id'] ?></td>
                    <td><?= htmlspecialchars($fila['nombre']) ?></td>
                    <td><?= htmlspecialchars($fila['correo']) ?></td>
                    <td><?= htmlspecialchars($fila['usuario']) ?></td>
                    <td><?= htmlspecialchars($fila['fecha_nacimiento']) ?></td>
                    <td><?= htmlspecialchars($fila['genero']) ?></td>
                    <td>
                     <a class="btn btn-secondary btn-sm" href="editar_usuario.php?id=<?= $fila['id'] ?>">Editar</a>
                     <a onclick="return confirm('Deseas eliminar este usuario?')" class="btn btn-danger btn-sm" href="eliminar_usuario.php?id=<?= $fila['id'] ?>">Eliminar</a>    
                    </td>
                </tr>

                <?php endwhile; ?>

            </tbody>
        </table>

    </div>

</div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script> <!-- Inicializar DataTable -->

        $(document).ready(function(){
            $('#tabla').dataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                }
            })
        })
    </script>

</body>
</html>