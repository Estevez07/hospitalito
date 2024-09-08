<?php
/*************************************************************
 * Paciente.php
 * Objetivo: clase que encapsula el manejo de la entidad Paciente

 *************************************************************/
error_reporting(E_ALL);
include_once("AccesoDatos.php");
include_once("Usuario.php");

class Paciente extends Usuario {

private ?string $ubicGPS; 
private ?string $grupoSanguineo;

	public function buscarCvePwd():bool {
	$oAccesoDatos=new AccesoDatos();
	$sQuery="";
	$arrRS=null;
	$bRet = false;
	$arrParams=array();
		if (empty($this->correo) || empty($this->contrasenia))
			throw new Exception("Paciente->buscarCvePwd: faltan datos");
		else{
			if ($oAccesoDatos->conectar()){
		 		$sQuery = "SELECT t1.nombre, t1.primApe, t1.segApe					
					FROM usuario t1
						JOIN paciente t2 ON t2.correo = t1.correo
					WHERE t1.correo = :cve
					AND t1.pwd = :pwd
					AND t1.activa = TRUE";
				$arrParams = array(":cve"=>$this->correo, "pwd"=>$this->contrasenia);
				$arrRS = $oAccesoDatos->ejecutarConsulta($sQuery, $arrParams);
				$oAccesoDatos->desconectar();
				if ($arrRS){
					$this->nombre = $arrRS[0][0];
					$this->primerApe = $arrRS[0][1];
					$this->segundoApe = $arrRS[0][2];
					
					
					$bRet = true;
				}
			} 
		}
		return $bRet;
	}
	













	
	public function buscar():bool{
	
		$oAccesoDatos=new AccesoDatos();
		$sQuery="";
		$arrRS=null;
		$bRet = false;
		$arrParams=array();
			if (empty($this->correo))
				throw new Exception("Paciente->buscar: faltan datos");
			else{
				if ($oAccesoDatos->conectar()){
					 $sQuery = "SELECT t1.nombre, t1.primApe, t1.segApe, 
							t1.sexo,t1.fechaNacim, t1.numCelular,t2.grupoSang,
							t1.foto,t2.ubicGPS
						FROM usuario t1
							JOIN paciente t2 ON t2.correo = t1.correo
						WHERE t1.correo = :cve
						AND t1.activa = TRUE";
					$arrParams = array(":cve"=>$this->correo);
					$arrRS = $oAccesoDatos->ejecutarConsulta($sQuery, $arrParams);
					$oAccesoDatos->desconectar();
					if ($arrRS){
						$this->nombre = $arrRS[0][0];
						$this->primerApe = $arrRS[0][1];
						$this->segundoApe = $arrRS[0][2];
						$this->sexo = $arrRS[0][3];
						$this->fecNacimiento = DateTime::createFromFormat('Y-m-d', $arrRS[0][4]);
						$this->numCelular = $arrRS[0][5];
						$this->grupoSanguineo =$arrRS[0][6];
						$this->foto = $arrRS[0][7];
						$this->ubicGPS = $arrRS[0][8];
						
						$bRet = true;
					}
				} 
			}
			return $bRet;


	}
	
	public function buscarTodos():array{
		throw new Exception("Unsupported Operation");
	}

	public function insertar(): int {
		$oAccesoDatos = new AccesoDatos();
		$sQueryUsuario = "";
		$sQueryPaciente = "";
		$arrParamsUsuario = array();
		$arrParamsPaciente = array();
		$nRet = -1;
	
		
			// Primera Query para tabla usuario
			$sQueryUsuario = "INSERT INTO usuario (correo,foto, pwd, numCelular, nombre, primApe, segApe, sexo, fechaNacim, activa)
							  VALUES (:correo,:foto, :pwd, :numCelular, :nombre, :primApe, :segApe, :sexo, :fechaNacim, :activa)";
	
			$arrParamsUsuario = array(
				":correo" => $this->correo,
                ":foto" => $this->foto,
				":pwd" => $this->contrasenia,
				":numCelular" => $this->numCelular,
				":nombre" => $this->nombre,
				":primApe" => $this->primerApe,
				":segApe" => $this->segundoApe,
				":sexo" => $this->sexo,
				":fechaNacim" => $this->fecNacimiento->format('Y-m-d'), // Formatear la fecha correctamente
				":activa" => $this->activa
			);
		
			// Ejecutar la primera query para usuario
			if ($oAccesoDatos->conectar()) {
				$nRetUsuario = $oAccesoDatos->ejecutarComando($sQueryUsuario, $arrParamsUsuario);
				$oAccesoDatos->desconectar(); // Desconectar después de ejecutar la consulta
			}
	
			// Segunda Query para tabla paciente
			$sQueryPaciente = "INSERT INTO paciente (correo, ubicGPS,grupoSang)
							   VALUES (:correo, :ubicGPS, :grupoSang)";
	
			$arrParamsPaciente = array(
				":correo" => $this->correo,				
				":ubicGPS" => $this->ubicGPS,
				":grupoSang" => $this->grupoSanguineo
			);
	
			// Ejecutar la segunda query para paciente
			if ($nRetUsuario > 0) {
				if ($oAccesoDatos->conectar()) {
					$nRetPaciente = $oAccesoDatos->ejecutarComando($sQueryPaciente, $arrParamsPaciente);
					$oAccesoDatos->desconectar(); // Desconectar después de ejecutar la consulta
				}
			}
	
			// Establecer el valor de retorno en base al éxito de ambas queries
			if ($nRetUsuario > 0 && $nRetPaciente > 0) {
				$nRet = 1;
			}
	
	
		return $nRet;
	}
	
	public function modificar():int{
		throw new Exception("Unsupported Operation");
	}

	public function eliminar():int {
		throw new Exception("Unsupported Operation");
	}
	
 
	public function setUbicGPS(string $valor){
		$this->ubicGPS = $valor;
	 }
	 public function getUbicGPS():?string{
		return $this->ubicGPS;
	 }
	 public function setGrupoSanguineo(?string $valor){
		$this->grupoSanguineo = $valor;
	 }
	 public function getGrupoSanguineo():?string{
		return $this->grupoSanguineo;
	}

}
?>