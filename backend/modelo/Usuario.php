<?php
/*************************************************************
 * Usuario.php
 * Objetivo: clase que encapsula el manejo de la entidad Usuario

 *************************************************************/
error_reporting(E_ALL);
include_once("AccesoDatos.php");

abstract class Usuario {
/*En este caso los atributos son protegidos (en lugar de privados)
  para utilizarlos en la herencia*/
protected ?string $correo;
protected ?string $foto; 
protected ?string $contrasenia;
protected ?string $numCelular; 
protected ?string $nombre;
protected ?string $primerApe;
protected ?string $segundoApe;
protected ?string $sexo;
protected ?DateTime $fecNacimiento;
protected bool $activa=true;

	abstract public function buscar():bool;
	
	abstract public function buscarCvePwd():bool;

	abstract public function buscarTodos():array;

	abstract public function insertar():int;

	abstract public function modificar():int;

	abstract public function eliminar():int;
	
    public function setCorreo(string $valor){
       $this->correo = $valor;
    }
    public function getCorreo():?string{
       return $this->correo;
    }
	
   
    public function setFoto(string $valor){
      $this->foto = $valor;
   }
   public function getFoto():?string{
      return $this->foto;
   }
  
    public function setContrasenia(string $valor){
      $this->contrasenia = $valor;
   }
   public function getContrasenia():?string{
      return $this->contrasenia;
   }

   public function setNumCelular(string $valor){
      $this->numCelular = $valor;
   }
   public function getNumCelular():?string{
      return $this->numCelular;
   }


    public function setNombre(string $valor){
       $this->nombre = $valor;
    }
    public function getNombre():?string{
       return $this->nombre;
    }
   
    public function setPrimerApe(string $valor){
       $this->primerApe = $valor;
    }
    public function getPrimerApe():?string{
       return $this->primerApe;
    }
   
    public function setSegundoApe(?string $valor){
       $this->segundoApe = $valor;
    }
    public function getSegundoApe():?string{
       return $this->segundoApe;
	}

  public function setSexo(?string $valor){
   $this->sexo = $valor;
   }
   public function getSexo():?string{
      return $this->sexo;
   }

   public function setFecNacimiento(DateTime $valor){
      $this->fecNacimiento = $valor;
   }
   public function getFecNacimiento():?DateTime{
      return $this->fecNacimiento;
  }
 

    public function setActiva(bool $valor){
       $this->activa = $valor;
    }
    public function getActiva():bool{
       return $this->activa;
	}
	
	public function getNombreCompleto():string{
		return $this->nombre." ".$this->primerApe." ".$this->segundoApe;
	}
}


?>