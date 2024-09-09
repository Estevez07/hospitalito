<?php
/*Archivo:  ctrlBuscaTodosEpisodioMedico.php
Objetivo: control para buscar todos los episodios médicos de un paciente,
*/

require_once("../modelo/EpisodioMedico.php");
require_once("../modelo/Paciente.php");
include_once("../utils/ErroresAplic.php");

$nErr = -1;
$oEpisodio = new EpisodioMedico();
$arrEncontrados = array();
$sJsonRet = "";
$oErr = null;

$cabeceras = apache_request_headers();
if (isset($cabeceras["Tokenaut"]) && !empty($cabeceras["Tokenaut"])){
    //Verificar que sea paciente, requiere las variables de sesión
    session_id($cabeceras["Tokenaut"]);
    session_start();
    if (isset($_SESSION["oFirmado"]) && is_a($_SESSION["oFirmado"],'Paciente')){
try {                
    if (isset($_REQUEST["txtCorreoPaciente"]) && !empty($_REQUEST["txtCorreoPaciente"])) {
        $paciente = new Paciente();
        $paciente->setCorreo($_REQUEST["txtCorreoPaciente"]);
        $oEpisodio->setPaciente($paciente);

        $arrEncontrados = $oEpisodio->buscarTodosPorFiltro();
        if (empty($arrEncontrados)) {
            $nErr = ErroresAplic::NO_EXISTE_BUSCADO;
        }
    } else {
        $nErr = ErroresAplic::FALTAN_DATOS;
    }
} catch(Exception $e) {
    // Enviar el error específico a la bitácora de php (dentro de php\logs\php_error_log)
    error_log($e->getFile()." ".$e->getLine()." ".$e->getMessage(), 0);
    $nErr = ErroresAplic::ERROR_EN_BD;
}
}
else
$nErr = ErroresAplic::NO_FIRMADO;
}
else
    $nErr = ErroresAplic::NO_FIRMADO;
if ($nErr == -1) {

    $sJsonRet = 
		'{
			"success":true,
			"status": "ok",
			"data":{
				"arrEpisodios": [
		';
		//Recorrer arreglo para llenar objetos


    foreach ($arrEncontrados as $oEpis) {
     
        $sJsonRet = $sJsonRet.'{
            "idCita":'.$oEpis->getIdCita().',
            "correoPaciente":"'.$oEpis->getPaciente()->getCorreo().'",
            "correoMedico":"'.$oEpis->getMedico()->getCorreo().'",
            "fechaCita":"'.$oEpis->getFechaCita()->format('Y-m-d').'",
            "horaCita":"'.$oEpis->getHoraCita().'",
            "sintomas":"'.$oEpis->getSintomas().'",
            "fotoLesiones":"'.$oEpis->getFotoLesiones().'"
            },';

    }
    	//Sobra una coma, eliminarla
		$sJsonRet = substr($sJsonRet,0, strlen($sJsonRet)-1);
		
		//Colocar cierre de arreglo y de objeto
		$sJsonRet = $sJsonRet.'
				]
			}
		}';

        


	}else{
		$oErr = new ErroresAplic();
		$oErr->setError($nErr);
		$sJsonRet = 
		'{
			"success":false,
			"status": "'.$oErr->getTextoError().'",
			"data":{}
		}';
	}
/* Para permitir la llamada desde otro lugar, en este caso Node.js */
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

header("Access-Control-Allow-Headers: Content-Type, Tokenaut");
// Retornar JSON a quien hizo la llamada
header('Content-type: application/json');
echo $sJsonRet;
?>
