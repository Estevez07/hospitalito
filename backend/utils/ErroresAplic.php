<?php
/*
Archivo:  ErroresAplic.php
Objetivo: clase que encapsula los errores que manejan la mayoría de 
		las aplicaciones a utilizar en el semestre.
Autor:    BAOZ
*/

class ErroresAplic{
	private int $nError=-1;
	//Errores considerados
	CONST NO_FIRMADO = 1;
	CONST USR_DESCONOCIDO = 2;
	CONST ERROR_EN_BD = 3;
	CONST FALTAN_DATOS = 4;
	CONST NO_EXISTE_BUSCADO = 5;
	CONST SIN_PERMISOS = 6;
	CONST ERROR_DATOS = 7;
	CONST ERROR_NAV = 8;
	CONST ARCH_NO_COPIADO = 9;
	CONST ARCH_MAYOR = 10;
	CONST ARCH_TIPO_MAL = 11;
	CONST ARCH_PROBL = 12;
	CONST OPE_NO_REALIZADA = 13;
	CONST OPE_DESCONOCIDA = 14;

	public function getError(){
		return $this->nError;
	}
	public function setError(int $val){
		$this->nError = $val;
	}
	
	public function getTextoError(){
	$sMsjError = "";
		switch ($this->nError){
			case self::NO_FIRMADO: $sMsjError = "No ha ingresado al sistema";
									break;
			case self::USR_DESCONOCIDO: $sMsjError ="Usuario desconocido";
									break;
			case self::ERROR_EN_BD: $sMsjError ="Error al acceder al repositorio";
									break;
			case self::FALTAN_DATOS: $sMsjError = "Faltan datos";
									break;
			case self::NO_EXISTE_BUSCADO: $sMsjError = "No existe el registro buscado";
									break;
			case self::SIN_PERMISOS: $sMsjError = "No tiene permisos para ver la pantalla solicitada";
									break;
			case self::ERROR_DATOS: $sMsjError = "Los datos son de tipo erróneo";
									break;
			case self::ERROR_NAV: $sMsjError = "Error de navegación";
									break;
			case self::ARCH_MAYOR: $sMsjError = "El archivo es mayor a 200 KB";
									break;
			case self::ARCH_TIPO_MAL: $sMsjError = "El archivo es de tipo incorrecto";
									break;
			case self::ARCH_PROBL: $sMsjError = "El archivo presenta problemas";
									break;
			case self::OPE_NO_REALIZADA: $sMsjError = "Operación no realizada";
									break;
			case self::OPE_DESCONOCIDA: $sMsjError = "Operación desconocida";
									break;												
			default: $sMsjError = "Error desconocido";
		}
		return $sMsjError;
	}
}
?>