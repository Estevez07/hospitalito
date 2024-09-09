<?php
/*Archivo:  ctrlLogout.php
Objetivo: control para terminar sesión
		  CAMBIOS debido al paso de construcción en front-end:
		  - cabecera que indica permisos de acceso,
		  - devuelve JSON (antes redireccionaba a index.php).
Autor:    BAOZ
*/
require_once ("../modelo/Medico.php");
require_once ("../modelo/Paciente.php");
session_start(); //Le avisa al servidor que va a utilizar sesiones
    session_regenerate_id(true); //para que reinicie el ID de sesión
	session_destroy(); //destruye la información de la sesión
	
	$sJsonRet = 
		'{
			"success":true,
			"status": "ok",
			"data":{}
		}';
	
	/*Para permitir la llamada desde otro lugar, en este caso Node.js*/
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET');
	
	//Retornar JSON a quien hizo la llamada
	header('Content-type: application/json');
	echo $sJsonRet;
