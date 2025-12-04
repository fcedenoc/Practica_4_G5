<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$response = [
    'status' => 'error',
    'mensaje' => 'Error inesperado',
    'debug' => 'inicio',
];

try{

    include '../conexionBD.php';

    $raw = file_get_contents("php://input");
    $datos = json_decode($raw, true);

    if(!$datos){
        $response['mensaje'] = 'Los datos no pudieron ser procesados correctamente.';
        $response['debug'] = 'json inválido: ' . substr($raw, 0, 200);
        echo json_encode($response);
        exit();
    }

    $usuario = trim($datos['usuario'] ?? '');
    $clave = trim($datos['contrasenna'] ?? '');

    if(!$usuario || !$clave){
        $response['mensaje'] = 'Usuario o contraseña vacíos.';
        $response['debug'] = 'Campos vacíos';
        echo json_encode($response);
        exit();
    }

    $mysqli = abrirConexion();

    $sql = "SELECT id, nombre, usuario, clave FROM usuarios WHERE usuario = ?";
    $stmt = $mysqli->prepare($sql);

    if (!$stmt){
        $response['mensaje'] = 'Error al preparar la consulta.';
        $response['debug'] = 'prepare falló: ' . $mysqli->error;
        echo json_encode($response);
        exit();
    }
    
    $stmt->bind_param("s", $usuario);
    $stmt->execute();

    $resultado = $stmt->get_result();

    $response['debug'] = 'consulta ejecutada';

    if($resultado && $resultado->num_rows > 0){

        $fila = $resultado->fetch_assoc();

        
        if(password_verify($clave, $fila['clave'])){

            $_SESSION['id'] = $fila['id'];
            $_SESSION['nombre'] = $fila['nombre'];
            $_SESSION['usuario'] = $fila['usuario'];

            $response = [
                    'status' => 'ok',
                    'nombre' => $fila['nombre'],
                    'debug' => 'login exitoso',
            ];

        }else{
            $response['mensaje'] = 'Contraseña incorrecta';
            $response['debug'] = 'fallo de contraseña verify (false)';
        }

    }else{
        $response['mensaje'] = 'Usuario no encontrado';
        $response['debug'] = 'usuario no existe';
    }

    cerrarConexion($mysqli);

}catch (Exception $e){
        $response['mensaje'] = 'Sucedió un error al realizar el login';
        $response['debug'] = 'catch exception: ' . $e->getMessage();
}

echo json_encode($response);
exit();

?>