<?php
/*************************************************************
 * Cita.php
 * Objetivo: clase que encapsula el manejo de la entidad Cita
 *************************************************************/
error_reporting(E_ALL);
include_once("Medico.php");
include_once("Paciente.php");
include_once("AccesoDatos.php");


abstract class  Cita{
    protected ? int $idCita;
    protected ? Paciente $paciente;
    protected ? Medico $medico;
    protected ? DateTime $fechaCita;
    protected ? string $horaCita;


    abstract public function buscar():bool;

	abstract public function buscarTodosPorFiltro():array;

   abstract public function buscarHorasDisponibles():array;

	abstract public function insertar():int;

	abstract public function modificar():int;

	abstract public function eliminar():int;

    public function setIdCita(int $valor){
        $this->idCita = $valor;
     }
     public function getIdCita():?int{
        return $this->idCita;
     }


    public function setPaciente(Paciente $valor){
        $this->paciente = $valor;
     }
     public function getPaciente():?Paciente{
        return $this->paciente;
     }
     
     public function setMedico(Medico $valor){
        $this->medico = $valor;
     }
     public function getMedico():?Medico{
        return $this->medico;
     }

     public function setFechaCita(DateTime $valor){
        $this->fechaCita = $valor;
     }
     public function getFechaCita():?DateTime{
        return $this->fechaCita;
     }


     public function setHoraCita(string $valor){
        $this->horaCita = $valor;
     }
     public function getHoraCita():?string{
        return $this->horaCita;
     }






}
?>