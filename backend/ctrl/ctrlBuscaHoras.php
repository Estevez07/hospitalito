<?php
/*Archivo:  ctrlBuscaTodosEpisodiosMedicos.php
Objetivo: control para buscar todos los episodios médicos de un paciente,
*/

require_once("../modelo/EpisodioMedico.php");
require_once("../modelo/Medico.php");
require_once("../modelo/Paciente.php");
include_once("../utils/ErroresAplic.php");

$nErr = -1;
$oEpisodio = new EpisodioMedico();
$arrEncontrados = array();
$sJsonRet = "";
$oErr = null;

$cabeceras = apache_request_headers();
if (isset($cabeceras["Tokenaut"]) && !empty($cabeceras["Tokenaut"])){
    //Verificar que sea administrador, requiere las variables de sesión
    session_id($cabeceras["Tokenaut"]);
    session_start();
    if (isset($_SESSION["oFirmado"]) && is_a($_SESSION["oFirmado"],'Paciente')){
try {                
    if (isset($_REQUEST["txtCorreoMedico"]) && !empty($_REQUEST["txtCorreoMedico"])&&
    isset($_REQUEST["txtCorreoPaciente"]) && !empty($_REQUEST["txtCorreoPaciente"])&&
    isset($_REQUEST["txtFechaCita"]) && !empty($_REQUEST["txtFechaCita"])
    
    
    ) {
        $medico = new Medico();
        $medico->setCorreo($_REQUEST["txtCorreoMedico"]);
        $oEpisodio->setMedico($medico);
        $paciente = new Paciente();
        $paciente->setCorreo($_REQUEST["txtCorreoPaciente"]);
        $oEpisodio->setPaciente($paciente);
        $oEpisodio->setFechaCita(DateTime::createFromFormat("Y-m-d", $_REQUEST["txtFechaCita"]));
        $arrEncontrados = $oEpisodio->buscarHorasDisponibles();
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
				"arrHorarios": [
		';
		//Recorrer arreglo para llenar objetos


    foreach ($arrEncontrados as $oEpis) {
     
        $sJsonRet = $sJsonRet.'{
            "idCita":'.$oEpis->getIdCita().',
         "fechaCita":"'.$oEpis->getFechaCita()->format('Y-m-d').'",  
            "horaCita":"'.$oEpis->getHoraCita().'"
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
header('Access-Control-Allow-Methods: GET');
header("Access-Control-Allow-Headers: Content-Type, Tokenaut");
// Retornar JSON a quien hizo la llamada
header('Content-type: application/json');
echo $sJsonRet;
?>
