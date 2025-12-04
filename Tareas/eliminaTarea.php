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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$userId = $_SESSION['id'] ?? 0;

if ($id === 0) {
    header("Location: listaTareas.php");
    exit;
}

$stmt_check = $mysqli->prepare("SELECT id FROM tareaUsuario WHERE id = ? AND usuario_id = ?");
$stmt_check->bind_param("ii", $id, $userId);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows === 0) {
    header("Location: listaTareas.php");
    exit;
}
$stmt_check->close();


$stmt = $mysqli->prepare("DELETE FROM tareaUsuario WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $id, $userId);
$success = $stmt->execute();
$stmt->close();

cerrarConexion($mysqli);

if ($success) {
    echo "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <title>Eliminando...</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Tarea eliminada',
                text: 'La tarea se ha eliminado correctamente.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = 'listaTareas.php';
            });
        </script>
    </body>
    </html>
    ";
} else {
    echo "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <title>Error</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo eliminar la tarea.',
            }).then(() => {
                window.location.href = 'listaTareas.php';
            });
        </script>
    </body>
    </html>
    ";
}
?>