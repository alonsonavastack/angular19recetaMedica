<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Content-Type: application/json; charset=utf-8'); // Set content type to JSON

$json = file_get_contents('php://input'); // RECIBE EL JSON DE ANGULAR

$params = json_decode($json); // DECODIFICA EL JSON Y LO GUARADA EN LA VARIABLE

// Check for JSON decoding errors
if ($params === null && json_last_error() !== JSON_ERROR_NONE) {
    $response = ['error' => 'JSON decoding error: ' . json_last_error_msg()];
    echo json_encode($response);
    exit;
}

require("conexion.php"); // IMPORTA EL ARCHIVO CON LA CONEXION A LA DB

$conexion = conexion(); // CREA LA CONEXION

// Sanitize input to prevent SQL injection
$email = mysqli_real_escape_string($conexion, $params->email);
$password = mysqli_real_escape_string($conexion, $params->password);

// Check if the email exists
$query_email = "SELECT * FROM users WHERE email = '$email'";
$registros_email = mysqli_query($conexion, $query_email);
$email_exists = mysqli_num_rows($registros_email);

if ($email_exists == 0) {
    // Email doesn't exist
    $response = ['error' => 'El correo electrónico no existe'];
    echo json_encode($response);
    exit;
} else {
    //Email exist
    // Check email and password
    $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $registros = mysqli_query($conexion, $query);
    $user_exists = mysqli_num_rows($registros);
    if ($user_exists > 0) {
        // User exists and password is correct
        $datos = [];
        while($resultado = mysqli_fetch_array($registros)){
            $datos[]=$resultado;
        }
        $response = $datos;
    } else {
        // Incorrect password
        $response = ['error' => 'Contraseña incorrecta'];
    }
}

//Return response
echo json_encode($response);

mysqli_close($conexion);
?>
