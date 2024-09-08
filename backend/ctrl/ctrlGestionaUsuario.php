<?php
/*Archivo:  ctrlGestionaUsuario.php
Objetivo: control para gestionar (insertar, modificar, eliminar, reactivar)
          un Usuario 

*/

require_once ("../modelo/Paciente.php");
require_once ("../modelo/Medico.php");
include_once("../utils/ErroresAplic.php");
$nErr=-1;
$oUsu=null;
$sJsonRet = "";
$oErr = null;
$sUrl = "";
$sOpe="";
$nTipo=0;


try{                
    /*Verifica que hayan llegado los datos mínimos (clave, operación,rol)*/
    if (isset($_REQUEST["txtCorreo"]) && !empty($_REQUEST["txtCorreo"]) &&
        isset($_REQUEST["txtOpe"]) && !empty($_REQUEST["txtOpe"])    &&
        isset($_REQUEST["txtRol"]) && !empty($_REQUEST["txtRol"]) ) {
        
            
            if($_REQUEST["txtRol"] === 'Medico'){
                $oUsu = new Medico();
                $nTipo=1;
                
            }else if($_REQUEST["txtRol"] === 'Paciente'){
                $oUsu = new Paciente();
                $nTipo=2;
            }

        // Verifica la operación recibida
        $sOpe = $_REQUEST["txtOpe"];
        if ($sOpe == 'a' || $sOpe == 'b' || 
            $sOpe == 'm' || $sOpe == 'r') {
            // Sin importar la operación, todos requieren el identificador
            $oUsu->setCorreo($_REQUEST["txtCorreo"]);
            
            // Paso de datos a menos que sea baja
            if ($sOpe != 'b') {
                if($nTipo == 1){
                if (
                    isset($_REQUEST["txtNombre"]) && !empty($_REQUEST["txtNombre"]) &&
                    isset($_REQUEST["txtPwd"]) && !empty($_REQUEST["txtPwd"]) &&
                    isset($_REQUEST["txtNumCelular"]) && !empty($_REQUEST["txtNumCelular"]) &&
                    isset($_REQUEST["txtPrimApe"]) && !empty($_REQUEST["txtPrimApe"]) &&
                    isset($_REQUEST["txtSegApe"]) && !empty($_REQUEST["txtSegApe"]) &&
                    isset($_REQUEST["txtSexo"]) && !empty($_REQUEST["txtSexo"]) &&
                    isset($_REQUEST["txtFechaNacim"]) && !empty($_REQUEST["txtFechaNacim"]) &&
                    isset($_REQUEST["txtActiva"]) && !empty($_REQUEST["txtActiva"]) &&
                    isset($_REQUEST["txtFoto"]) && !empty($_REQUEST["txtFoto"]) &&
                    isset($_REQUEST["txtCedProf"]) && !empty($_REQUEST["txtCedProf"]) &&
                    isset($_REQUEST["txtEspecialidad"]) && !empty($_REQUEST["txtEspecialidad"]) &&
                    isset($_REQUEST["txtHorario"]) && !empty($_REQUEST["txtHorario"]) &&
                    isset($_REQUEST["txtDiaTrabajo"]) && !empty($_REQUEST["txtDiaTrabajo"])
                ) {
                    $oUsu->setContrasenia($_REQUEST["txtPwd"]);
                    $oUsu->setNombre($_REQUEST["txtNombre"]);
                    $oUsu->setNumCelular($_REQUEST["txtNumCelular"]);
                    $oUsu->setPrimerApe($_REQUEST["txtPrimApe"]);
                    $oUsu->setSegundoApe($_REQUEST["txtSegApe"]);
                    $oUsu->setSexo($_REQUEST["txtSexo"]);
                    $oUsu->setFecNacimiento(DateTime::createFromFormat("Y-m-d", $_REQUEST["txtFechaNacim"]));
                    $oUsu->setActiva($_REQUEST["txtActiva"]);
                    $oUsu->setFoto($_REQUEST["txtFoto"]);
                    $oUsu->setCedProf($_REQUEST["txtCedProf"]);
                    $oUsu->setEspecialidad($_REQUEST["txtEspecialidad"]);
                    $oUsu->setHorario($_REQUEST["txtHorario"]);
                    $oUsu->setDiaTrabajo($_REQUEST["txtDiaTrabajo"]);
                } else {
                    $nErr = ErroresAplic::FALTAN_DATOS;
                }

            }else if ($nTipo == 2){
                if (
                    isset($_REQUEST["txtNombre"]) && !empty($_REQUEST["txtNombre"]) &&
                    isset($_REQUEST["txtPwd"]) && !empty($_REQUEST["txtPwd"]) &&
                    isset($_REQUEST["txtNumCelular"]) && !empty($_REQUEST["txtNumCelular"]) &&
                    isset($_REQUEST["txtPrimApe"]) && !empty($_REQUEST["txtPrimApe"]) &&
                    isset($_REQUEST["txtSegApe"]) && !empty($_REQUEST["txtSegApe"]) &&
                    isset($_REQUEST["txtSexo"]) && !empty($_REQUEST["txtSexo"]) &&
                    isset($_REQUEST["txtFechaNacim"]) && !empty($_REQUEST["txtFechaNacim"]) &&
                    isset($_REQUEST["txtActiva"]) && !empty($_REQUEST["txtActiva"]) &&
                    isset($_REQUEST["txtFoto"]) && !empty($_REQUEST["txtFoto"]) &&
                    isset($_REQUEST["txtGrupoSang"]) && !empty($_REQUEST["txtGrupoSang"]) &&
                    isset($_REQUEST["txtUbicGPS"]) && !empty($_REQUEST["txtUbicGPS"])
                ) {
                    $oUsu->setContrasenia($_REQUEST["txtPwd"]);
                    $oUsu->setNombre($_REQUEST["txtNombre"]);
                    $oUsu->setNumCelular($_REQUEST["txtNumCelular"]);
                    $oUsu->setPrimerApe($_REQUEST["txtPrimApe"]);
                    $oUsu->setSegundoApe($_REQUEST["txtSegApe"]);
                    $oUsu->setSexo($_REQUEST["txtSexo"]);
                    $oUsu->setFecNacimiento(DateTime::createFromFormat("Y-m-d", $_REQUEST["txtFechaNacim"]));
                    $oUsu->setActiva($_REQUEST["txtActiva"]);
                    $oUsu->setFoto($_REQUEST["txtFoto"]);
                    $oUsu->setGrupoSanguineo($_REQUEST["txtGrupoSang"]);
                    $oUsu->setUbicGps($_REQUEST["txtUbicGPS"]);
                } else {
                    $nErr = ErroresAplic::FALTAN_DATOS;
                }


            }








                
            }
            if ($nErr == -1) {
                // Llama al método dependiendo de la operación
                switch ($sOpe) {
                    case 'a': $nAfectados = $oUsu->insertar();
                              break;
                    case 'b': $nAfectados = $oUsu->eliminar();
                              break;
                    case 'm': $nAfectados = $oUsu->modificar();
                              break;
                    case 'r': $nAfectados = $oUsu->reactivar();
                              break;
                }
                // Si no afectó al menos un registro, se trata de un error
                if ($nAfectados < 1) {
                    $nErr = ErroresAplic::OPE_NO_REALIZADA;
                }
            }
        } else {
            $nErr = ErroresAplic::OPE_DESCONOCIDA;
        }
    } else {
        $nErr = ErroresAplic::FALTAN_DATOS;
    }
} catch (Exception $e) {
    // Enviar el error específico a la bitácora de php (dentro de php\logs\php_error_log)
    error_log($e->getFile()." ".$e->getLine()." ".$e->getMessage(), 0);
    $nErr = ErroresAplic::ERROR_EN_BD;
}

if ($nErr == -1) {
    $sJsonRet = 
    '{
        "success": true,
        "status": "ok",
        "data": {}
    }';
} else {
    $oErr = new ErroresAplic();
    $oErr->setError($nErr);
    $sJsonRet = 
    '{
        "success": false,
        "status": "'.$oErr->getTextoError().'",
        "data": {}
    }';
}

/* Para permitir la llamada desde otro lugar, en este caso Node.js */
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Retornar JSON a quien hizo la llamada
header('Content-type: application/json');

echo $sJsonRet;
?>
