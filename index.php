<?php
// Este archivo es la página de inicio de sesión para que los usuarios entren al sistema.

ini_set('display_errors', 1); // Mostrar errores de PHP para depurar.
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Código comentado de una función de ejemplo para sumar números (no se usa aquí).
// function sumarNumeros($num1, $num2){

// return $num1 + $num2;

// }

// echo sumarNumeros(15, 25);

?>

<!-- Comienza el HTML de la página de login -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"> <!-- Para que se vean bien las letras -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Para móviles -->
    <title>Inicio Sesión</title> <!-- Título de la página -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous"> <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/styles.css"> <!-- Nuestro CSS -->
</head>

<body>

    <div class="login-card"> <!-- Caja del formulario de login -->

        <h3>Iniciar Sesión</h3> <!-- Título del formulario -->

        <form id="loginForm" action=""> <!-- Formulario para enviar usuario y contraseña -->

            <div class="mb-3"> <!-- Grupo para el campo de usuario -->
                <label for="usuario" class="form-label">Usuario</label> <!-- Etiqueta para el campo -->
                <input type="text" class="form-control" name="usuario" id="usuario"> <!-- Campo de texto para el nombre de usuario -->
            </div>

            <div class="mb-3"> <!-- Grupo para la contraseña -->
                <label for="contrasenna" class="form-label">Contraseña</label>
                <input type="password" class="form-control" name="contrasenna" id="contrasenna"> <!-- Campo de contraseña (oculta los caracteres) -->
            </div>

            <button type="submit" class="btn btn-login">Ingresar</button> <!-- Botón para enviar el formulario -->

            <div class="enlaces"> <!-- Enlaces adicionales -->
                <p> <a href="#">Olvidaste tu contraseña?</a></p> <!-- Enlace para recuperar contraseña (no funciona aún) -->
                <p> <a href="registro.php">Crear cuenta nueva</a> </p> <!-- Enlace a la página de registro -->
            </div>

        </form>

    </div>

    <!-- Scripts para funcionalidades -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script> <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Para alertas -->
    <script src="php/login/login.js"></script> <!-- JS para manejar el login -->

</body>

</html> <!-- Fin del HTML -->