<?php

include '../conexionBD.php';

$mysqli = abrirConexion();

if(isset($_GET['id'])){  //Si esta la variable llena, que le haga get al parametro ID

    $id = intval($_GET['id']);

    $stmt = $mysqli->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id); //valor de tipo integer y el que vamos a agregar es el ID.
    $stmt->execute();
    $stmt->close();

}

cerrarConexion($mysqli);
header('Location: listar_usuarios.php');
exit();

?>