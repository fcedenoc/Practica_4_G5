<?php
include __DIR__ . '/../php/conexionBD.php';
try {
    $mysqli = abrirConexion();
} catch (Exception $e) {
    echo "Conexión fallida: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
$res = $mysqli->query("SELECT id, nombre_estado FROM estados ORDER BY id");
if (!$res) {
    echo "Query error: " . $mysqli->error . PHP_EOL;
    exit(1);
}
echo "Found rows: " . $res->num_rows . PHP_EOL;
while ($r = $res->fetch_assoc()) {
    echo $r['id'] . " - " . $r['nombre_estado'] . PHP_EOL;
}
$mysqli->close();
?>
