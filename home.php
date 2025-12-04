<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

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

$sql = "
SELECT t.id, t.tarea_nombre, t.descripcion, e.nombre_estado, t.fecha_creacion 
FROM tareaUsuario t
INNER JOIN estados e ON t.estado_id = e.id
ORDER BY t.fecha_creacion DESC
";

$resultado = $mysqli->query($sql);
if ($resultado->num_rows === 0) {
    echo "<h3>No hay tareas registradas.</h3>";
}

// MOSTRAR ERROR SI LA CONSULTA FALLA
if (!$resultado) {
    die("Error en la consulta: " . $mysqli->error);
}
?>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Administrador de Tareas</a>
            <div class="ms-auto d-flex align-items-center gap-3">
                <div class="color-picker-container">
                    <label for="themeColor" class="text-white mb-0">Fondo:</label>
                    <select name="colorTema" id="colorTema" class="form-select form-select-sm">
                        <option value="#ffffff">Blanco</option>
                        <option value="#001f3f">Azul Marino</option>
                        <option value="#343a40">Gris Oscuro</option>
                    </select>
                </div>

                <a href="#" class="nav-link">Inicio</a>
                <a href="../../Tareas/listaTareas.php" class="nav-link">Tareas</a>
                <a href="php/usuarios/listar_usuarios.php" class="nav-link">Usuarios</a>
                <a href="php/login/logout.php" class="nav-link">Cerrar sesión</a>

            </div>
        </div>
    </nav>

    <div class="container my-5">

        <!-- ============================= -->
        <!--          CALCULADORA          -->
        <!-- ============================= -->
        <section>
            <h2>Calculadora</h2>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <input type="number" name="num1" id="num1" class="form-control" placeholder="Ingrese número 1">
                </div>

                <div class="col-md-3 mb-3">
                    <input type="number" name="num2" id="num2" class="form-control" placeholder="Ingrese número 2">
                </div>

                <div class="col-md-3 mb-3">
                    <select name="idOperacion" class="form-select" id="idOperacion">
                        <option value="suma">Suma</option>
                        <option value="resta">Resta</option>
                        <option value="multiplicacion">Multiplicación</option>
                        <option value="division">División</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <button id="btnCalcular" class="btn btn-success w-100">Calcular</button>
                </div>

                <h5 class="mt-3" id="resultadoOperacion"></h5>
            </div>
        </section>

        <!-- ============================= -->
        <!--   EVALUADOR DE DESEMPEÑO     -->
        <!-- ============================= -->
        <section class="mt-4">
            <h2>Evaluador de desempeño</h2>
            <div class="row g-3">
                <div class="col-md-8 mb-3">
                    <input type="number" name="nota" id="nota" class="form-control" placeholder="Ingrese la nota">
                </div>

                <div class="col-md-4">
                    <button id="btnEvaluacion" class="btn btn-warning w-100">Evaluar</button>
                </div>

                <ul id="listaTareas" class="list-group mt-3"></ul>
            </div>
        </section>

        <!-- ============================= -->
        <!--        CARDS DE BOOTSTRAP     -->
        <!-- ============================= -->
        <section class="mt-5">
            <h2>Lista de tareas</h2>

            <div class="d-flex flex-wrap gap-3">

<?php while ($fila = $resultado->fetch_assoc()): ?>
    <div class="card text-bg-light mb-3" style="max-width: 18rem;">
        <div class="card-header">
            <?= htmlspecialchars($fila['nombre_estado']) ?>
        </div>

        <div class="card-body">
            <h5 class="card-title">
                <?= htmlspecialchars($fila['tarea_nombre']) ?>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/home.js"></script>

</body>

</html>
