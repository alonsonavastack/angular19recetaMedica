<?php
header('Access-Control-Allow-Origin: *'); 
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

$json = file_get_contents('php://input'); // Recibe el json de angular

$params = json_decode($json); // Decodifica el json

require("conexion.php"); // importa el archivo de la conexion a la BD
$conexion = conexion();
//$rutaimagen = addslashes(file_get_contents($_FILES['imagen']['tmp_name']))
mysqli_query($conexion, "UPDATE historial 
    SET pesohistorial ='$params->pesohistorial',
        tallahistorial ='$params->tallahistorial',
        fchistorial ='$params->fchistorial',        
        citahistorial ='$params->citahistorial',
        idpaciente =$params->idpaciente,
        fechahistorial ='$params->fechahistorial',
        diagnostico ='$params->diagnostico'
        WHERE idhistorial=$params->idhistorial");

class Result {}

//Generar los datos de respuesta
$response = new Result();
$response->resultado = 'OK';
$response->mensaje = 'Expediente editado';

//Envio de informacion del JSON
header('Content-Type: application/json');
echo json_encode($response); // Muestra el json generado
?>