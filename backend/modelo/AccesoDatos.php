<?php
/*************************************************************/
/* AccesoDatos.php
 * Objetivo: clase que encapsula el acceso a la base de datos (caso PDO)
 *			 Requiere habilitar pdo_pgsql y pdo_mysql en php.ini
 * Autor: BAOZ
 *************************************************************/
 error_reporting(E_ALL);
 class AccesoDatos{
 private $oConexion=null; 
		/*Realiza la conexión a la base de datos*/
     	function conectar(){
		$bRet = false;
			try{
				$this->oConexion = new PDO("pgsql:dbname=hospitalito; host=localhost; user=postgres; password=Luis17461"); 
				//$this->oConexion = new PDO("mysql:host=localhost;dbname=hospitalito","root","",array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'")); 
				
				//Configura la conexión para que lance excepción en caso de errores
				$this->oConexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$bRet = true;
			}catch(Exception $e){
				throw $e;
			}
			return $bRet;
		}
		
		/*Realiza la desconexión de la base de datos*/
     	function desconectar(){
		$bRet = true;
			if ($this->oConexion != null){
				$this->oConexion=null;
			}
			return $bRet;
		}
		
		/*Ejecuta en la base de datos la consulta que recibió por parámetro con los valores
		que recibió en el arreglo.
		Regresa
			Nulo si no hubo datos
			Un arreglo bidimensional de n filas y tantas columnas como campos se hayan
			solicitado en la consulta*/
      	function ejecutarConsulta($psConsulta, $parrParams){
		$arrRS = null;
		$rst = null;
		$oLinea = null;
		$sValCol = "";
		$i=0;
		$j=0;
			if ($psConsulta == ""){
		       throw new Exception("AccesoDatos->ejecutarConsulta: falta indicar la consulta");
			}
			if ($this->oConexion == null){
				throw new Exception("AccesoDatos->ejecutarConsulta: falta conectar la base");
			}
			try{
				$rst = $this->oConexion->prepare($psConsulta);
				$rst->execute($parrParams); 
			}catch(Exception $e){
				throw $e;
			}
			if ($rst){
				$arrRS = $rst->fetchAll();
			}
			return $arrRS;
		}
		
		/*Ejecuta en la base de datos el comando que recibió por parámetro con los valores
		indicados en el arreglo.
		Regresa
			el número de registros afectados por el comando*/
      	function ejecutarComando(string $psComando, array $parrParams){
		$nAfectados = -1;
		$pdo=null;
	       if ($psComando == ""){
		       throw new Exception("AccesoDatos->ejecutarComando: falta indicar el comando");
			}
			if ($this->oConexion == null){
				throw new Exception("AccesoDatos->ejecutarComando: falta conectar la base");
			}
			try{
	       	   $pdo=$this->oConexion->prepare($psComando);
			   $pdo->execute($parrParams);
			   $nAfectados =$pdo->rowCount();
			}catch(Exception $e){
				throw $e;
			}
			return $nAfectados;
		}
	


		/* Obtiene el último ID insertado en la base de datos */
		function obtenerUltimoIdInsertado(){
			$ultimoId = null;
			if ($this->oConexion == null){
				throw new Exception("AccesoDatos->obtenerUltimoIdInsertado: falta conectar la base");
			}
			try{
				$ultimoId = $this->oConexion->lastInsertId();
			}catch(Exception $e){
				throw $e;
			}
			return $ultimoId;
		}
	}

	
 ?>