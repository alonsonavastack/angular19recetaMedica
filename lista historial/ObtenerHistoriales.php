<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

require("conexion.php"); // importa el archivo de la conexion a la BD
$conexion = conexion();

// Check if the connection was successful
if (!$conexion) {
    $error = mysqli_connect_error();
    error_log("Connection error: " . $error);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => "Database connection error: " . $error]);
    exit; // Stop execution
}
$query = "SELECT DISTINCT * FROM historial h INNER JOIN  pacientes p ON h.idpaciente = p.idpaciente ORDER BY h.idhistorial DESC";

$registros = mysqli_query($conexion, $query);

// Check if the query was successful
if (!$registros) {
    $error = mysqli_error($conexion);
    error_log("Query error: " . $error); // Log the error
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => "Database query error: " . $error]);
    exit; // Stop execution
}

$datos = [];
//recorre el resultado y lo guarda en un array
while ($resultado = $registros->fetch_assoc()) {
    $datos[] = $resultado;
}

$json = json_encode($datos); // Genera el json con los datos obtenidos

//Envio de informacion del JSON
header('Content-Type: application/json; charset=utf-8');
echo $json;

mysqli_close($conexion); // Close the connection
?>
