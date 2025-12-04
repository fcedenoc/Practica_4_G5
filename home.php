<?php
// Este archivo es la página principal (home) del usuario después de iniciar sesión.
// Muestra un resumen de tareas y estadísticas para que el usuario vea su progreso.

ini_set('display_errors', 1); // Activar la muestra de errores en PHP para facilitar la depuración.
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Iniciar la sesión para verificar si el usuario está logueado.

if(!isset($_SESSION['usuario'])){ // Si no hay un usuario en la sesión (no está logueado), redirigir a la página de login.
    header("Location: index.php");
    exit(); // Detener la ejecución del script.
}

?>

<!-- Comienza el código HTML de la página web -->
<!DOCTYPE html>
<html lang="en"> <!-- El idioma de la página es inglés, pero el contenido está en español -->

<head> <!-- Configuración de la cabeza de la página -->
    <meta charset="UTF-8"> <!-- Codificación de caracteres para que se vean bien las letras con acentos -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Para que la página se vea bien en móviles -->
    <link rel="stylesheet" href="assets/css/home.css"> <!-- Enlace a nuestro archivo CSS personalizado -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap para estilos bonitos -->
    <title>Inicio</title> <!-- Título de la página que aparece en la pestaña del navegador -->
</head>

<body> <!-- Aquí va todo el contenido visible de la página -->

<?php
// Código PHP para obtener datos de la base de datos antes de mostrar la página.

include __DIR__ . '/php/conexionBD.php'; // Incluir el archivo que tiene la función para conectar a la base de datos.
$mysqli = abrirConexion(); // Abrir la conexión a MySQL usando la función del archivo incluido.

// Consulta SQL para obtener las últimas 5 tareas del usuario, ordenadas por fecha de creación.
$sql = "
SELECT t.id, t.tarea_nombre, t.descripcion, e.nombre_estado, t.fecha_creacion
FROM tareaUsuario t
INNER JOIN estados e ON t.estado_id = e.id
ORDER BY t.fecha_creacion DESC
LIMIT 5
";

$resultado = $mysqli->query($sql); // Ejecutar la consulta en la base de datos.

// MOSTRAR ERROR SI LA CONSULTA FALLA
if (!$resultado) { // Si la consulta falló, mostrar el error y detener el script.
    die("Error en la consulta: " . $mysqli->error);
}

// Consultas para estadísticas de tareas
$sql_total = "SELECT COUNT(*) as total FROM tareaUsuario"; // Contar todas las tareas.
$result_total = $mysqli->query($sql_total);
$total_tareas = $result_total->fetch_assoc()['total']; // Guardar el número total.

$sql_estados = "SELECT e.nombre_estado, COUNT(t.id) as cantidad FROM estados e LEFT JOIN tareaUsuario t ON e.id = t.estado_id GROUP BY e.id"; // Contar tareas por estado.
$result_estados = $mysqli->query($sql_estados);
$estadisticas = []; // Array para guardar las estadísticas.
while ($row = $result_estados->fetch_assoc()) { // Recorrer los resultados y guardarlos en el array.
    $estadisticas[] = $row;
}
?>

    <!-- Barra de navegación en la parte superior de la página -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Administrador de Tareas</a> <!-- Nombre de la aplicación -->
            <div class="ms-auto d-flex align-items-center gap-3">
                <div class="color-picker-container">
                    <label for="themeColor" class="text-white mb-0">Fondo:</label> <!-- Selector para cambiar el color de fondo -->
                    <select name="colorTema" id="colorTema" class="form-select form-select-sm">
                        <option value="#ffffff">Blanco</option>
                        <option value="#001f3f">Azul Marino</option>
                        <option value="#343a40">Gris Oscuro</option>
                    </select>
                </div>

                <a href="home.php" class="nav-link">Inicio</a> <!-- Enlace a la página principal -->
                <a href="Tareas/listaTareas.php" class="nav-link">Tareas</a> <!-- Enlace a la lista de tareas -->
                <a href="php/usuarios/listar_usuarios.php" class="nav-link">Usuarios</a> <!-- Enlace a la lista de usuarios -->
                <a href="php/login/logout.php" class="nav-link">Cerrar sesión</a> <!-- Enlace para cerrar la sesión -->

            </div>
        </div>
    </nav>

    <div class="container my-5"> <!-- Contenedor principal con margen para centrar el contenido -->

        <!-- ============================= -->
        <!--       RESUMEN DE TAREAS       -->
        <!-- ============================= -->
        <section> <!-- Sección que muestra estadísticas rápidas de las tareas -->
            <h2>Resumen de Tareas</h2> <!-- Título de la sección -->
            <div class="row g-3"> <!-- Fila con tarjetas para las estadísticas -->
                <div class="col-md-3"> <!-- Tarjeta para el total de tareas -->
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Total Tareas</h5>
                            <p class="card-text fs-3"><?=$total_tareas?></p> <!-- Número total de tareas -->
                        </div>
                    </div>
                </div>
                <?php foreach ($estadisticas as $stat): ?> <!-- Bucle para mostrar cada estado con su cantidad -->
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title"><?=$stat['nombre_estado']?></h5> <!-- Nombre del estado, ej. Pendiente -->
                            <p class="card-text fs-3"><?=$stat['cantidad']?></p> <!-- Cantidad de tareas en ese estado -->
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>


        <!-- ============================= -->
        <!--      TAREAS RECIENTES         -->
        <!-- ============================= -->
        <section class="mt-5"> <!-- Sección para mostrar las tareas más recientes -->
            <h2>Tareas Recientes</h2> <!-- Título de la sección -->

            <div class="d-flex flex-wrap gap-3"> <!-- Contenedor flexible para las tarjetas de tareas -->

<?php while ($fila = $resultado->fetch_assoc()): ?> <!-- Bucle para mostrar cada tarea -->
    <div class="card text-bg-light mb-3" style="max-width: 18rem;"> <!-- Tarjeta para cada tarea -->
        <div class="card-header">
            <?= htmlspecialchars($fila['nombre_estado']) ?> <!-- Estado de la tarea, ej. Pendiente -->
        </div>

        <div class="card-body">
            <h5 class="card-title">
                <?= htmlspecialchars($fila['tarea_nombre']) ?> <!-- Nombre de la tarea -->
            </h5>

            <p class="card-text">
                <?= htmlspecialchars($fila['descripcion']) ?> <!-- Descripción de la tarea -->
            </p>

            <p class="card-text">
                <small class="text-muted">
                    Creada: <?= htmlspecialchars($fila['fecha_creacion']) ?> <!-- Fecha de creación -->
                </small>
            </p>
        </div>
    </div>
<?php endwhile; ?> <!-- Fin del bucle -->

</div>

            <div class="text-center mt-3"> <!-- Botón centrado para ver todas las tareas -->
                <a href="Tareas/listaTareas.php" class="btn btn-primary">Ver Todas las Tareas</a>
            </div>
    </div>

    <!-- Scripts de JavaScript para funcionalidades de la página -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script> <!-- Bootstrap JS para componentes interactivos -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert para alertas bonitas -->
    <script src="assets/js/home.js"></script> <!-- Nuestro archivo JS personalizado para esta página -->

</body>

</html> <!-- Fin del documento HTML -->
