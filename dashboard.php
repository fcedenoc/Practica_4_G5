<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: php/login/login.php');
    exit();
}

require_once 'php/conexion/conexion.php';

$usuario_id = $_SESSION['usuario_id'];

$query = "SELECT t.*, e.nombre_estado 
          FROM tareaUsuario t 
          INNER JOIN estados e ON t.estado_id = e.id 
          WHERE t.usuario_id = ? 
          ORDER BY t.fecha_creacion DESC";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

function limitarNombre($nombre) {
    if (strlen($nombre) > 50) {
        return substr($nombre, 0, 50) . '...';
    }
    return $nombre;
}

function obtenerColorEstado($estado) {
    switch($estado) {
        case 'Pendiente':
            return 'warning';
        case 'En Progreso':
            return 'info';
        case 'Completada':
            return 'success';
        default:
            return 'secondary';
    }
}

$total_tareas = $result->num_rows;
$pendientes = 0;
$completadas = 0;

$tareas = [];
while($tarea = $result->fetch_assoc()) {
    $tareas[] = $tarea;
    if($tarea['nombre_estado'] == 'Pendiente') $pendientes++;
    if($tarea['nombre_estado'] == 'Completada') $completadas++;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Administrador de Tareas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/styleDashboard.css">
    
</head>
<body>
    <?php include 'php/includes/navbar.php'; ?>

    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-4 mb-2">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </h1>
                    <p class="lead mb-0">
                        Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre_usuario'] ?? 'Usuario'); ?>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="php/tareas/agregar_tarea.php" class="btn btn-light btn-lg shadow">
                        <i class="fas fa-plus-circle"></i> Nueva Tarea
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row mb-5">
            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <i class="fas fa-tasks fa-2x mb-3 text-primary"></i>
                    <div class="stats-number"><?php echo $total_tareas; ?></div>
                    <p class="text-muted mb-0">Total de Tareas</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <i class="fas fa-clock fa-2x mb-3 text-warning"></i>
                    <div class="stats-number"><?php echo $pendientes; ?></div>
                    <p class="text-muted mb-0">Pendientes</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <i class="fas fa-check-circle fa-2x mb-3 text-success"></i>
                    <div class="stats-number"><?php echo $completadas; ?></div>
                    <p class="text-muted mb-0">Completadas</p>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col">
                <h2 class="mb-0">
                    <i class="fas fa-clipboard-list"></i> Mis Tareas
                </h2>
                <p class="text-muted">Gestiona y organiza todas tus tareas</p>
            </div>
            <div class="col text-end">
                <a href="php/tareas/listar_tareas.php" class="btn btn-outline-primary">
                    <i class="fas fa-list"></i> Ver Lista Completa
                </a>
            </div>
        </div>

        <?php if (count($tareas) > 0): ?>
            <div class="row g-4">
                <?php foreach($tareas as $tarea): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-tarea">
                            <div class="position-relative">
                                <?php if ($tarea['url_imagen'] && !empty($tarea['url_imagen'])): ?>
                                    <img src="<?php echo htmlspecialchars($tarea['url_imagen']); ?>" 
                                         class="card-img-top card-img-top-custom" 
                                         alt="<?php echo htmlspecialchars($tarea['tarea_nombre']); ?>"
                                         onerror="this.parentElement.innerHTML='<div class=\'card-img-top-custom img-placeholder\'><i class=\'fas fa-clipboard-list\'></i></div>'">
                                <?php else: ?>
                                    <div class="card-img-top-custom img-placeholder">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <span class="badge bg-<?php echo obtenerColorEstado($tarea['nombre_estado']); ?> badge-estado">
                                    <i class="fas fa-circle"></i> <?php echo htmlspecialchars($tarea['nombre_estado']); ?>
                                </span>
                            </div>
                            
                            <div class="card-body card-body-custom">
                                <h5 class="card-title-custom" title="<?php echo htmlspecialchars($tarea['tarea_nombre']); ?>">
                                    <i class="fas fa-file-alt text-primary"></i>
                                    <?php echo htmlspecialchars(limitarNombre($tarea['tarea_nombre'])); ?>
                                </h5>
                                
                                <p class="card-text-custom">
                                    <?php 
                                    $desc = $tarea['descripcion'];
                                    if (empty($desc)) {
                                        echo '<em class="text-muted">Sin descripción</em>';
                                    } else {
                                        echo htmlspecialchars($desc); 
                                    }
                                    ?>
                                </p>
                                
                                <div class="fecha-info">
                                    <div class="row">
                                        <div class="col-6">
                                            <small>
                                                <i class="fas fa-calendar-plus"></i> Creada:<br>
                                                <strong><?php echo date('d/m/Y', strtotime($tarea['fecha_creacion'])); ?></strong>
                                            </small>
                                        </div>
                                        <div class="col-6 text-end">
                                            <small>
                                                <i class="fas fa-calendar-check"></i> Actualizada:<br>
                                                <strong><?php echo date('d/m/Y', strtotime($tarea['fecha_actualizacion'])); ?></strong>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2 mt-3">
                                    <a href="php/tareas/editar_tarea.php?id=<?php echo $tarea['id']; ?>" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-edit"></i> Ver y Editar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-tareas">
                <i class="fas fa-clipboard-list"></i>
                <h3 class="text-muted mb-3">No tienes tareas aún</h3>
                <p class="text-muted mb-4">Comienza a organizar tu trabajo creando tu primera tarea</p>
                <a href="php/tareas/agregar_tarea.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus-circle"></i> Crear Primera Tarea
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const colorTema = document.getElementById('colorTema');
            if (colorTema) {
                colorTema.addEventListener('change', function() {
                    document.body.style.backgroundColor = this.value;
                });
            }
        });
    </script>
</body>
</html>

<?php
$stmt->close();
?>