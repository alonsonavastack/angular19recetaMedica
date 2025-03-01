<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$json = file_get_contents('php://input');

// Check for JSON decoding errors
$params = json_decode($json);
if ($params === null && json_last_error() !== JSON_ERROR_NONE) {
    $response = new Result();
    $response->resultado = 'ERROR';
    $response->mensaje = 'JSON decoding error: ' . json_last_error_msg();
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

require("conexion.php");

$conexion = conexion();

// Use prepared statements to prevent SQL injection
$stmt = $conexion->prepare("INSERT INTO historial (
                                                  pesohistorial, 
                                                  tallahistorial,
                                                  fchistorial, 
                                                  citahistorial,
                                                  idpaciente,
                                                  fechahistorial,
                                                  diagnostico) VALUES (?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    $response = new Result();
    $response->resultado = 'ERROR';
    $response->mensaje = 'Error preparing the statement: ' . $conexion->error;
    header('Content-Type: application/json');
    echo json_encode($response);
    $conexion->close();
    exit;
}
$stmt->bind_param("ssssiss", $params->pesohistorial, $params->tallahistorial, $params->fchistorial, $params->citahistorial, $params->idpaciente, $params->fechahistorial, $params->diagnostico);

// Check for bind_param errors
if (!$stmt) {
    $response = new Result();
    $response->resultado = 'ERROR';
    $response->mensaje = 'Error binding parameters: ' . $stmt->error;
    header('Content-Type: application/json');
    echo json_encode($response);
    $stmt->close();
    $conexion->close();
    exit;
}
if ($stmt->execute()) {
    class Result {}
    $response = new Result();
    $response->resultado = 'OK';
    $response->mensaje = 'SE REGISTRO EXITOSAMENTE EL HISTORIAL';
    $response->inserted_id = $conexion->insert_id;
} else {
    class Result {}
    $response = new Result();
    $response->resultado = 'ERROR';
    $response->mensaje = 'Error al registrar el historial: ' . $stmt->error; // Get the specific error
}

header('Content-Type: application/json');
echo json_encode($response);

$stmt->close();
$conexion->close();
?>
