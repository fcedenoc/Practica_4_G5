<?php
// Calcular base path del proyecto relativo al DOCUMENT_ROOT para generar URLs correctas
$appFs = realpath(__DIR__ . '/../../');
$docRoot = realpath($_SERVER['DOCUMENT_ROOT']);
$basePath = '';
if ($appFs && $docRoot && strpos($appFs, $docRoot) === 0) {
    $basePath = str_replace('\\', '/', substr($appFs, strlen($docRoot)));
}
if ($basePath === '') $basePath = ''; // si está en la raíz
?>

<link rel="stylesheet" href="<?= $basePath ?>/assets/css/home.css">

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">Administrador de Tareas</a>

        <div class="ms-auto d-flex align-items-center gap-3">

            <div class="color-picker-container">
                <label for="themeColor" class="text-white mb-0">Fondo:</label>
                <select name="colorTema" id="colorTema" class="form-select">
                    <option value="#ffffff">Blanco</option>
                    <option value="#001f3f">Azul Marino</option>
                    <option value="#343a40">Gris Oscuro</option>
                </select>
            </div>

          <a href="<?= $basePath ?>/home.php" class="nav-link">Inicio</a>
<a href="<?= $basePath ?>/Tareas/listaTareas.php" class="nav-link">Tareas</a>
<a href="<?= $basePath ?>/php/usuarios/listar_usuarios.php" class="nav-link">Usuarios</a>
<a href="<?= $basePath ?>/php/login/logout.php" class="nav-link">Cerrar sesión</a>
        </div>
    </div>
</nav>


