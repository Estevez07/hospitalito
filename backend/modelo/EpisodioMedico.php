<?php
/*************************************************************
 * EpisodioMedico.php
 * Objetivo: representa la descripcion del episodio medico de cada paciente
 *************************************************************/
error_reporting(E_ALL);
include_once("Cita.php");
include_once("Medico.php");
include_once("Paciente.php");
include_once("AccesoDatos.php");

class EpisodioMedico extends Cita{
private ? string $sintomas;
private ? string $fotoLesiones;

	public function buscarTodosPorFiltro():array{
	$oAccesoDatos=new AccesoDatos();
	$sQuery="";
	$arrRS=null;
	$arrRet = array();
	$oEpis=null;
	$arrParams=array();
		if ($this->paciente == null)
			throw new Exception("EpisodioMedico->buscarTodosPorFiltro: faltan datos");
		else{
			if ($oAccesoDatos->conectar()){
				$sQuery = "SELECT t1.idCita, t2.correoPaciente, t2.correoMedico, 
						t2.feCita,t2.horaCita, t1.sintomas, t1.fotoLesiones							
				        FROM episodiomedico t1
						JOIN cita t2 ON t2.idCita = t1.idCita
					    WHERE t2.correoPaciente = :correo
					    ORDER BY t2.feCita";
				$arrParams = array(":correo"=>$this->paciente->getCorreo());
				$arrRS = $oAccesoDatos->ejecutarConsulta($sQuery, $arrParams);
				$oAccesoDatos->desconectar();
				if ($arrRS){
					foreach($arrRS as $arrLinea){
						$oEpis = new EpisodioMedico();
						$oEpis->setIdCita($arrLinea[0]);
						$oEpis->setPaciente(new Paciente());
						$oEpis->getPaciente()->setCorreo($arrLinea[1]);
                        $oEpis->setMedico(new Medico());
						$oEpis->getMedico()->setCorreo($arrLinea[2]);
                        $oEpis->setFechaCita(DateTime::createFromFormat('Y-m-d', $arrLinea[3]));
						$oEpis->setHoraCita($arrLinea[4]);
						$oEpis->setSintomas($arrLinea[5]);
						$oEpis->setFotoLesiones($arrLinea[6]);			
						$arrRet[] = $oEpis;
					}
				}
			}
		}
		return $arrRet;
	}

	
	public function buscar():bool{
		throw new Exception("Unsupported Operation");
	}



	public function buscarHorasDisponibles():array{
		$oAccesoDatos=new AccesoDatos();
		$sQuery="";
		$arrRS=null;
		$arrRet = array();
		$oEpis=null;
		$arrParams=array();
			if ($this->medico == null || $this->paciente ==null)
				throw new Exception("EpisodioMedico->buscarHorasDisponibles: faltan datos");
			else{
				if ($oAccesoDatos->conectar()){
					$sQuery = "SELECT DISTINCT t1.horaCita, t1.idCita, t1.feCita
           FROM cita t1
           WHERE (t1.correoMedico = :correoMedico OR t1.correoPaciente = :correoPaciente) 
           AND t1.feCita = :fecha
           ORDER BY t1.horaCita";
					$arrParams = array(":correoMedico"=>$this->medico->getCorreo(),":correoPaciente"=>$this->paciente->getCorreo(),":fecha"=>$this->fechaCita->format("Y-m-d"));
					$arrRS = $oAccesoDatos->ejecutarConsulta($sQuery, $arrParams);
					$oAccesoDatos->desconectar();
					if ($arrRS){
						foreach($arrRS as $arrLinea){
							
							$oEpis = new EpisodioMedico();
							$oEpis->setHoraCita($arrLinea[0]);
							$oEpis->setIdCita($arrLinea[1]);
							$oEpis->setFechaCita(DateTime::createFromFormat('Y-m-d',$arrLinea[2]));
							
							$arrRet[] = $oEpis;
						}
					}
				}
			}
			return $arrRet;
		}

		



	public function insertar():int {
		$oAccesoDatos = new AccesoDatos();
		$sQueryCita = "";
		$sQueryEpisodio = "";
		$arrParamsCita = array();
		$arrParamsEpisodio = array();
		$nRet = -1;

			// Primera Query para tabla Cita
			$sQueryCita ="INSERT INTO cita(correoPaciente,correoMedico,feCita,horaCita)
			 VALUES(:correoP, :correoM, :fechaCita, :horaCita)";
	
			$arrParamsCita = array(
				":correoP" => $this->paciente->getCorreo(),
				":correoM" => $this->medico->getCorreo(),
				":fechaCita" => $this->fechaCita->format('Y-m-d'), // Formatear la fecha correctamente
				":horaCita" => $this->horaCita
			);
		
			// Ejecutar la query para Cita
			if ($oAccesoDatos->conectar()) {
				$nRetCita= $oAccesoDatos->ejecutarComando($sQueryCita, $arrParamsCita);
				$idCitaInsertada=$oAccesoDatos->obtenerUltimoIdInsertado();
				$oAccesoDatos->desconectar(); // Desconectar después de ejecutar la consulta
			}

			$sQueryEpisodio ="INSERT INTO episodiomedico(idCita,sintomas,fotoLesiones)
			 VALUES(:idCita,:sintomas, :fotoLesiones)";


			$arrParamsEpisodio = array(
				":idCita"=> $idCitaInsertada,
				":sintomas" => $this->sintomas,				
				":fotoLesiones" => $this->fotoLesiones
			);
	
			// Ejecutar la segunda query para Episodio Medico
			if ($nRetCita > 0) {
				if ($oAccesoDatos->conectar()) {
					$nRetEpisodio = $oAccesoDatos->ejecutarComando($sQueryEpisodio, $arrParamsEpisodio);
					$oAccesoDatos->desconectar(); // Desconectar después de ejecutar la consulta
				}
			}



	// Establecer el valor de retorno en base al éxito de ambas queries
	if ($nRetCita > 0 && $nRetEpisodio > 0) {
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
	


     public function setSintomas(string $valor){
        $this->sintomas = $valor;
     }
     public function getSintomas():?string{
        return $this->sintomas;
     }

     public function setFotoLesiones(string $valor){
        $this->fotoLesiones = $valor;
     }
     public function getFotoLesiones():?string{
        return $this->fotoLesiones;
     }

}
?>


