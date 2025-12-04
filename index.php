<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// function sumarNumeros($num1, $num2){

// return $num1 + $num2;

// }

// echo sumarNumeros(15, 25);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>

    <div class="login-card">

        <h3>Iniciar Sesión</h3>

        <form id="loginForm" action="">

            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" class="form-control" name="usuario" id="usuario">
            </div>

            <div class="mb-3">
                <label for="contrasenna" class="form-label">Contraseña</label>
                <input type="password" class="form-control" name="contrasenna" id="contrasenna">
            </div>

            <button type="submit" class="btn btn-login">Ingresar</button>

            <div class="enlaces">
                <p> <a href="#">Olvidaste tu contraseña?</a></p>
                <p> <a href="registro.php">Crear cuenta nueva</a> </p>
            </div>

        </form>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="php/login/login.js"></script>

</body>

</html>