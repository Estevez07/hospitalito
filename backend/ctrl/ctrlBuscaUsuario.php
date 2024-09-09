<?php
/*Archivo:  ctrlBuscaUsuario.php
Objetivo: control para traer los datos del perfil del usuario
		  CAMBIOS debido al paso de construcción en front-end:
		  - cabecera que indica permisos de acceso y método POST.

*/
require_once ("../modelo/Medico.php");
require_once ("../modelo/Paciente.php");
require_once ("../utils/ErroresAplic.php");

$nErr=-1;
$oUsu=new Medico();
$sJsonRet = "";
$oErr = null;
	
    $cabeceras = apache_request_headers();
if (isset($cabeceras["Tokenaut"]) && !empty($cabeceras["Tokenaut"])){
    //Verificar que sea paciente, requiere las variables de sesión
    session_id($cabeceras["Tokenaut"]);
    session_start();
    if (isset($_SESSION["oFirmado"]) && (is_a($_SESSION["oFirmado"],'Paciente')||is_a($_SESSION["oFirmado"],'Medico'))){
    /*Verifica que hayan llegado los datos*/
	if (isset($_REQUEST["txtCorreo"]) && !empty($_REQUEST["txtCorreo"])){
		try{
			//Pasa los datos al objeto
			$oUsu->setCorreo($_REQUEST["txtCorreo"]);
			
			//Busca en la base de datos
			if ($oUsu->buscar()){
				//Si lo encuentra, genera la variablede sesión y guarda sus datos
				$_SESSION["oFirmado"] = $oUsu;
				/*La variable de sesión va a servir para las páginas
				  de PHP con navegación tradicional, mientras que el 
				  token va a servir para los controladores que 
				  devuelven JSON si necesitan verificar que pasó por el 
				  login */
			}else {
				//Si no es Medico, es posible que sea un Paciente.
				$oUsu = new Paciente();
				$oUsu->setCorreo($_REQUEST["txtCorreo"]);
			
				//Busca en la base de datos
				if ($oUsu->buscar()){
					//Si lo encuentra, genera la variablede sesión y guarda sus datos
					$_SESSION["oFirmado"] = $oUsu;
				}else
					$nErr = ErroresAplic::USR_DESCONOCIDO;
			}
		}catch(Exception $e){
			//Enviar el error específico a la bitácora de php (dentro de php\logs\php_error_log
			error_log($e->getFile()." ".$e->getLine()." ".$e->getMessage(),0);
			$nErr = ErroresAplic::ERROR_EN_BD;
		}
	}
	else
		$nErr = ErroresAplic::FALTAN_DATOS;
}else
$nErr = ErroresAplic::NO_FIRMADO;
}else
$nErr = ErroresAplic::NO_FIRMADO;



	if ($nErr==-1){
        $data = [
            "nombre" => $oUsu->getNombreCompleto(),
            "tipo" => is_a($oUsu, 'Medico') ? "Medico" : "Paciente",
            "correo" => $oUsu->getCorreo(),
            "foto" => $oUsu->getFoto(),
            "numeroCelular" => $oUsu->getNumCelular(),
            "sexo" => $oUsu->getSexo(),
            "fecha_nacimiento" => $oUsu->getFecNacimiento()->format('Y-m-d'),
            "horario" => is_a($oUsu, 'Medico') ? $oUsu->getHorario() : null,
            "diaTrabajo" => is_a($oUsu, 'Medico') ? $oUsu->getDiaTrabajo() : null,
            "cedProf" => is_a($oUsu, 'Medico') ? $oUsu->getCedProf() : null,
            "especialidad" => is_a($oUsu, 'Medico') ? $oUsu->getEspecialidad() : null,
            "grupo_sangineo" => is_a($oUsu, 'Paciente') ? $oUsu->getGrupoSanguineo() : null,
            "ubicGPS" => is_a($oUsu, 'Paciente') ? $oUsu->getUbicGPS() : null
        ];
        
        $sJsonRet = json_encode([
            "success" => true,
            "status" => "ok",
            "data" => $data
        ]);

	}else{
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
	header('Access-Control-Allow-Methods: POST');
	header("Access-Control-Allow-Headers: Content-Type, Tokenaut");
	//Retornar JSON a quien hizo la llamada
	header('Content-type: application/json');
	echo $sJsonRet;
?>