<?php
/*Archivo:  ctrlLogin.php
Objetivo: control para iniciar sesión
		  CAMBIOS debido al paso de construcción en front-end:
		  - cabecera que indica permisos de acceso y método POST.

*/
require_once ("../modelo/Medico.php");
require_once ("../modelo/Paciente.php");
require_once ("../utils/ErroresAplic.php");
session_start(); //Le avisa al servidor que va a utilizar sesiones
$nErr=-1;
$oUsu=new Medico();
$sJsonRet = "";
$oErr = null;
	/*Verifica que hayan llegado los datos*/
	if (isset($_REQUEST["txtCorreo"]) && !empty($_REQUEST["txtCorreo"]) &&
		isset($_REQUEST["txtPwd"]) && !empty($_REQUEST["txtPwd"])){
		try{
			//Pasa los datos al objeto
			$oUsu->setCorreo($_REQUEST["txtCorreo"]);
			$oUsu->setContrasenia($_REQUEST["txtPwd"]);
			//Busca en la base de datos
			if ($oUsu->buscarCvePwd()){
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
				$oUsu->setContrasenia($_REQUEST["txtPwd"]);
				//Busca en la base de datos
				if ($oUsu->buscarCvePwd()){
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
	
	if ($nErr==-1){
		$data = [];
		if(is_a($oUsu,'Medico')){
			$data[] = [
				"nombre" => $oUsu->getNombreCompleto(),
				"tipo" => "Medico",
				"token" =>session_id(),
				"correo"=>$oUsu->getCorreo()
			];

	}else{

		$data[] = [
			"nombre" => $oUsu->getNombreCompleto(),
			"tipo" => "Paciente",
			"token" =>session_id(),
			"correo"=>$oUsu->getCorreo()
		];


	}




	$sJsonRet = json_encode([
        "success" => true,
        "status" => "ok",
        "data" =>  $data[0] 
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
	
	//Retornar JSON a quien hizo la llamada
	header('Content-type: application/json');
	echo $sJsonRet;
?>