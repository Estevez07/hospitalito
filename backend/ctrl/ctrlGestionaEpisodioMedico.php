<?php
/* Archivo: ctrlGestionaEpisodioMedico.php
 * Objetivo: control para gestionar (insertar, modificar, eliminar, reactivar)
 *           un episodio médico
 */
require_once ("../modelo/Paciente.php");
require_once("../modelo/Medico.php");
require_once("../modelo/EpisodioMedico.php");
include_once("../utils/ErroresAplic.php");

$nErr = -1;
$oEpisodio = null;
$sJsonRet = "";
$oErr = null;
$sOpe = "";


$cabeceras = apache_request_headers();
if (isset($cabeceras["tokenaut"]) && !empty($cabeceras["tokenaut"])){
    //Verificar que sea administrador, requiere las variables de sesión
    session_id($cabeceras["tokenaut"]);
    session_start();
    if (isset($_SESSION["oFirmado"]) && is_a($_SESSION["oFirmado"],'Paciente')){
try {
    /* Verifica que hayan llegado los datos mínimos (operación) */
    if (isset($_REQUEST["txtOpe"]) && !empty($_REQUEST["txtOpe"])) {
        
        $oEpisodio = new EpisodioMedico();
        
        // Verifica la operación recibida
        $sOpe = $_REQUEST["txtOpe"];
        if ($sOpe == 'a' || $sOpe == 'b' || $sOpe == 'm' || $sOpe == 'r') {
          
            
            // Paso de datos a menos que sea baja
            if ($sOpe != 'b') {
                if (
                    isset($_REQUEST["txtCorreoPaciente"]) && !empty($_REQUEST["txtCorreoPaciente"]) &&
                    isset($_REQUEST["txtCorreoMedico"]) && !empty($_REQUEST["txtCorreoMedico"]) &&
                    isset($_REQUEST["txtFechaCita"]) && !empty($_REQUEST["txtFechaCita"]) &&
                    isset($_REQUEST["txtHoraCita"]) && !empty($_REQUEST["txtHoraCita"]) &&
                    isset($_REQUEST["txtSintomas"]) && !empty($_REQUEST["txtSintomas"]) &&
                    isset($_REQUEST["txtFotoLesiones"]) && !empty($_REQUEST["txtFotoLesiones"])
                ) {
                    $paciente = new Paciente();
                    $paciente->setCorreo($_REQUEST["txtCorreoPaciente"]);
                    $oEpisodio->setPaciente($paciente);

                    $medico = new Medico();
                    $medico->setCorreo($_REQUEST["txtCorreoMedico"]);
                    $oEpisodio->setMedico($medico);

                    $oEpisodio->setFechaCita(DateTime::createFromFormat("Y-m-d", $_REQUEST["txtFechaCita"]));
                    $oEpisodio->setHoraCita($_REQUEST["txtHoraCita"]);
                    $oEpisodio->setSintomas($_REQUEST["txtSintomas"]);
                    $oEpisodio->setFotoLesiones($_REQUEST["txtFotoLesiones"]);
                } else {
                    $nErr = ErroresAplic::FALTAN_DATOS;
                }
            }
            if ($nErr == -1) {
                // Llama al método dependiendo de la operación
                switch ($sOpe) {
                    case 'a': $nAfectados = $oEpisodio->insertar();
                              break;
                    case 'b': $nAfectados = $oEpisodio->eliminar();
                              break;
                    case 'm': $nAfectados = $oEpisodio->modificar();
                              break;
                    case 'r': $nAfectados = $oEpisodio->reactivar();
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
    // Enviar el error específico a la bitácora de PHP (dentro de php\logs\php_error_log)
    error_log($e->getFile()." ".$e->getLine()." ".$e->getMessage(), 0);
    $nErr = ErroresAplic::ERROR_EN_BD;
}

}
else
$nErr = ErroresAplic::NO_FIRMADO;
}
else
    $nErr = ErroresAplic::NO_FIRMADO;
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
header("Access-Control-Allow-Headers: Content-Type, tokenAut");
// Retornar JSON a quien hizo la llamada
header('Content-type: application/json');

echo $sJsonRet;
?>