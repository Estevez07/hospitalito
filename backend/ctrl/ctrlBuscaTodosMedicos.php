<?php
/*Archivo:  ctrlBuscaTodosMedicos.php
Objetivo: control para buscar todos los medicos
*/
require_once ("../modelo/Paciente.php");
require_once("../modelo/Medico.php");
include_once("../utils/ErroresAplic.php");
$nErr=-1;
$oMed= new Medico();
$arrEncontrados=array();
$sJsonRet = "";
$oErr = null;
$sDatos = "";
$cabeceras = apache_request_headers();




if (isset($cabeceras["Tokenaut"]) && !empty($cabeceras["Tokenaut"])){
    //Verificar que sea administrador, requiere las variables de sesión
    session_id($cabeceras["Tokenaut"]);
    session_start();
    if (isset($_SESSION["oFirmado"]) && is_a($_SESSION["oFirmado"],'Paciente')){
	/*Verifica por correo*/
	
			try{				
	
            $arrEncontrados=$oMed->buscarTodos();
		

		}catch(Exception $e){
				//Enviar el error específico a la bitácora de php (dentro de php\logs\php_error_log
				error_log($e->getFile()." ".$e->getLine()." ".$e->getMessage(),0);
				$nErr = ErroresAplic::ERROR_EN_BD;
			}
        }
        else
        $nErr = ErroresAplic::NO_FIRMADO;
        }
        else
            $nErr = ErroresAplic::NO_FIRMADO;
		if ($nErr == -1) {
    $data = [];
    foreach ($arrEncontrados as $oMedico) {
        $data[] = [
            "correo" => $oMedico->getCorreo(),
            "nombre" => $oMedico->getNombreCompleto(),
            "sexo" => $oMedico->getSexo(),
            "foto" => $oMedico->getFoto(),
            "fecha_nacimiento" => $oMedico->getFecNacimiento()->format('Y-m-d'),
            "numeroCelular" => $oMedico->getNumCelular(),
            "activa" => $oMedico->getActiva() ? 'true' : 'false',
            "horario" => $oMedico->getHorario(),
            "diaTrabajo" => $oMedico->getDiaTrabajo(),
            "cedProf" => $oMedico->getCedProf(),
            "especialidad" => $oMedico->getEspecialidad()
        ];
    }
    
    $sJsonRet = json_encode([
        "success" => true,
        "status" => "ok",
        "data" => ["arrMedicos" => $data]
    ]);
} else {
    $oErr = new ErroresAplic();
    $oErr->setError($nErr);
    $sJsonRet = json_encode([
        "success" => false,
        "status" => $oErr->getTextoError(),
        "data" => "{}"
    ]);
}

	/*Para permitir la llamada desde otro lugar, en este caso Node.js*/
	header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

    header("Access-Control-Allow-Headers: Content-Type, Tokenaut");
	//Retornar JSON a quien hizo la llamada
	header('Content-type: application/json');
	echo $sJsonRet;
?>