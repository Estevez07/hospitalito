<?php
/*************************************************************
 * Medico.php
 * Objetivo: clase que encapsula el manejo de la entidad Medico

 *************************************************************/
error_reporting(E_ALL);
include_once("AccesoDatos.php");
include_once("Usuario.php");

class Medico extends Usuario {
private ?string $cedProf; 
private ?string $especialidad; 
private ?string $horario; 
private ?string $diaTrabajo;

	public function buscarCvePwd():bool {
	$oAccesoDatos=new AccesoDatos();
	$sQuery="";
	$arrRS=null;
	$bRet = false;
	$arrParams=array();
		if (empty($this->correo) || empty($this->contrasenia))
			throw new Exception("Medico->buscarCvePwd: faltan datos");
		else{
			if ($oAccesoDatos->conectar()){
		 		$sQuery = "SELECT t1.nombre, t1.primApe, t1.segApe
						FROM usuario t1
						JOIN medico t2 ON t2.correo = t1.correo
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
		$resp=false;
		$arrParams=array();
		if (empty($this->correo))
				throw new Exception("Medico->buscar: faltan datos");
			else{
			if ($oAccesoDatos->conectar()){
				$sQuery = "SELECT t1.foto,t1.nombre, t1.primApe, t1.segApe, 
				t1.sexo,t1.fechaNacim, t1.numCelular,t1.activa,t2.horario,t2.diaTrabajo,
				t2.cedProf,t2.especialidad
				FROM usuario t1
				JOIN medico t2 ON t2.correo = t1.correo
				WHERE t1.correo = :cve
				AND t1.activa = TRUE";
				$arrParams = array(":cve"=>$this->correo);
				$arrRS = $oAccesoDatos->ejecutarConsulta($sQuery, $arrParams);
				$oAccesoDatos->desconectar();
				if ($arrRS){
					
				     	$this->foto = $arrRS[0][0];
						$this->nombre = $arrRS[0][1];
						$this->primerApe = $arrRS[0][2];
						$this->segundoApe = $arrRS[0][3];
						$this->sexo = $arrRS[0][4];
						$this->fecNacimiento = DateTime::createFromFormat('Y-m-d', $arrRS[0][5]);
						$this->numCelular = $arrRS[0][6];
						$this->activa= $arrRS[0][7];
						$this->horario =$arrRS[0][8];
						$this->diaTrabajo =$arrRS[0][9];
						$this->cedProf = $arrRS[0][10];
						$this->especialidad = $arrRS[0][11];
						
					$resp=true;
				}
			}
		}
			return $resp;


	}
	
	public function buscarTodos():array{
	$oAccesoDatos=new AccesoDatos();
	$sQuery="";
	$arrRS=null;
	$arrRet = null;
	$oMedico=null;
		if ($oAccesoDatos->conectar()){
			$sQuery = "SELECT t1.correo,t1.foto,t1.nombre, t1.primApe, t1.segApe, 
			t1.sexo,t1.fechaNacim, t1.numCelular,t1.activa,t2.horario,t2.diaTrabajo,
			t2.cedProf,t2.especialidad
		    FROM usuario t1
			JOIN medico t2 ON t2.correo = t1.correo
		    AND t1.activa = TRUE ORDER BY t1.correo";
			
			$arrRS = $oAccesoDatos->ejecutarConsulta($sQuery, array());
			$oAccesoDatos->desconectar();
			if ($arrRS){
				$arrRet = array();
				foreach($arrRS as $arrLinea){
					$oMedico = new Medico();
					$oMedico->setCorreo($arrLinea[0]);
					$oMedico->setFoto($arrLinea[1]);
					$oMedico->setNombre($arrLinea[2]);
					$oMedico->setPrimerApe($arrLinea[3]);
					$oMedico->setSegundoApe($arrLinea[4]);
					$oMedico->setSexo($arrLinea[5]);
					$oMedico->setFecNacimiento(DateTime::createFromFormat('Y-m-d', $arrLinea[6]));
					$oMedico->setNumCelular($arrLinea[7]);
					$oMedico->setActiva($arrLinea[8]);
					$oMedico->setHorario($arrLinea[9]);
					$oMedico->setDiaTrabajo($arrLinea[10]);
					$oMedico->setCedProf($arrLinea[11]);
					$oMedico->setEspecialidad($arrLinea[12]);
					$arrRet[] = $oMedico;
				}
			}
		}
		return $arrRet;
	}

	public function insertar(): int {
		$oAccesoDatos = new AccesoDatos();
		$sQueryUsuario = "";
		$sQueryMedico = "";
		$arrParamsUsuario = array();
		$arrParamsMedico = array();
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
			

			// Segunda Query para tabla medico
			$sQueryMedico = "INSERT INTO medico (correo,cedProf,especialidad,horario,diaTrabajo)
							   VALUES (:correo, :cedProf, :especialidad,:horario,:diaTrabajo)";
	
			$arrParamsMedico = array(
				":correo" => $this->correo,				
				":cedProf" => $this->cedProf,
				":especialidad" => $this->especialidad,
				":horario" => $this->horario,
				":diaTrabajo" => $this->diaTrabajo
			);
	
			// Ejecutar la segunda query para paciente
			if ($nRetUsuario > 0) {
				if ($oAccesoDatos->conectar()) {
					$nRetMedico = $oAccesoDatos->ejecutarComando($sQueryMedico, $arrParamsMedico);
					$oAccesoDatos->desconectar(); // Desconectar después de ejecutar la consulta
				}
			}
	
			// Establecer el valor de retorno en base al éxito de ambas queries
			if ($nRetUsuario > 0 && $nRetMedico > 0) {
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
	
    public function setCedProf(string $valor){
       $this->cedProf = $valor;
    }
    public function getCedProf():?string{
       return $this->cedProf;
    }
   
	public function setEspecialidad(string $valor){
		$this->especialidad = $valor;
	 }
	 public function getEspecialidad():?string{
		return $this->especialidad;
	 }
	 public function setHorario(string $valor){
		$this->horario = $valor;
	 }
	 public function getHorario():?string{
		return $this->horario;
	 }

	 public function setDiaTrabajo(string $valor){
		$this->diaTrabajo = $valor;
	 }
	 public function getDiaTrabajo():?string{
		return $this->diaTrabajo;
	 }
}
?>