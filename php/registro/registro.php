<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../conexionBD.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $nombre = $_POST["nombre"] ??  '';
    $correo = $_POST["correo"] ??  '';
    $usuario = $_POST["usuario"] ??  '';
    $clave = $_POST["clave"] ??  '';
    $confirmar = $_POST["confirmar"] ??  '';
    $fecha = $_POST["fecha"] ??  '';
    $genero = $_POST["genero"] ??  '';

    $claveHash = password_hash($clave, PASSWORD_DEFAULT);

    $conexion = abrirConexion();

    $sql = "INSERT INTO usuarios (nombre, correo, usuario, clave, fecha_nacimiento, genero)
    VALUES(?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssss", $nombre, $correo, $usuario, $claveHash, $fecha, $genero);

    if($stmt->execute()){
        echo "ok";
    }else{
        echo "error: ".$conexion->error;
    }

    $stmt->close();
    cerrarConexion($conexion);

}

?>