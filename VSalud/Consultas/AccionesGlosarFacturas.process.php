<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../../modelo/php_conexion.php");
include_once("../css_construct.php");
include_once("../clases/GlosasTSS.class.php");


if( !empty($_REQUEST["idAccion"]) ){
    $css =  new CssIni("id",0);
    $obGlosas = new Glosas($idUser);
    
    switch ($_REQUEST["idAccion"]) {
        case 1: //Registra la devolucion de una factura
            if(empty($_REQUEST["idFactura"]) or empty($_REQUEST["FechaDevolucion"]) or empty($_REQUEST["FechaAuditoria"]) or empty($_REQUEST["Observaciones"]) or empty($_REQUEST["CodigoGlosa"]) ){
                
                exit("No se recibieron los valores esperados");
            }
            
            $idFactura=$obGlosas->normalizar($_REQUEST["idFactura"]);
            $FechaDevolucion=$obGlosas->normalizar($_REQUEST["FechaDevolucion"]);
            $FechaAuditoria=$obGlosas->normalizar($_REQUEST["FechaAuditoria"]);
            $Observaciones=$obGlosas->normalizar($_REQUEST["Observaciones"]);
            $CodigoGlosa=$obGlosas->normalizar($_REQUEST["CodigoGlosa"]);
            $ValorFactura=$obGlosas->normalizar($_REQUEST["ValorFactura"]);
            $destino='';
            if(!empty($_FILES['Soporte']['name'])){
            
                $Atras="../";
                $carpeta="SoportesSalud/SoportesDevoluciones/";
                opendir($Atras.$Atras.$carpeta);
                $Name=str_replace(' ','_',$FechaDevolucion."_".$idFactura."_".$_FILES['Soporte']['name']);
                $destino=$carpeta.$Name;
                move_uploaded_file($_FILES['Soporte']['tmp_name'],$Atras.$Atras.$destino);
            }
            $obGlosas->DevolverFactura($idFactura,$ValorFactura, $FechaDevolucion,$FechaAuditoria, $Observaciones, $CodigoGlosa, $idUser, $destino, "");
            print("<strong>Devolucion realizada</strong>");
        break;
        
        case 2: //Registra las glosas iniciales en la tabla temporal
            if(empty($_REQUEST["idActividad"]) or empty($_REQUEST["idFactura"]) or empty($_REQUEST["FechaIPS"]) or empty($_REQUEST["FechaAuditoria"]) or empty($_REQUEST["Observaciones"]) or empty($_REQUEST["ValorEPS"]) ){
                   
                   $Mensaje["msg"]="No se recibieron los valores esperados";
                   $Mensaje["Error"]=1;
                   exit($Mensaje);
            }
            $idFactura=$obGlosas->normalizar($_REQUEST["idFactura"]);
            $idActividad=$obGlosas->normalizar($_REQUEST["idActividad"]);
            $TipoArchivo=$obGlosas->normalizar($_REQUEST["TipoArchivo"]);
            $FechaIPS=$obGlosas->normalizar($_REQUEST["FechaIPS"]);
            $FechaAuditoria=$obGlosas->normalizar($_REQUEST["FechaAuditoria"]);
            
            $Observaciones=$obGlosas->normalizar($_REQUEST["Observaciones"]);
            $CodigoGlosa=$obGlosas->normalizar($_REQUEST["CodigoGlosa"]);
            $ValorEPS=$obGlosas->normalizar($_REQUEST["ValorEPS"]);
            $ValorAceptado=$obGlosas->normalizar($_REQUEST["ValorAceptado"]);
            $ValorConciliar=$obGlosas->normalizar($_REQUEST["ValorConciliar"]);
            $TotalActividad=$obGlosas->normalizar($_REQUEST["TotalActividad"]);
            
            $destino='';
            if(!empty($_FILES['Soporte']['name'])){
            
                $Atras="../";
                $carpeta="SoportesSalud/SoportesGlosas/";
                opendir($Atras.$Atras.$carpeta);
                $Name=str_replace(' ','_',$FechaIPS."_".$idActividad."_".$idFactura."_".$_FILES['Soporte']['name']);
                $destino=$carpeta.$Name;
                move_uploaded_file($_FILES['Soporte']['tmp_name'],$Atras.$Atras.$destino);
            }
            
            $Mensaje=$obGlosas->RegistrarGlosaInicialTemporal($TipoArchivo,$idFactura, $idActividad,$TotalActividad, $FechaIPS, $FechaAuditoria, $CodigoGlosa, $ValorEPS, $ValorAceptado, $ValorConciliar,$Observaciones,$destino, "");
            print(json_encode($Mensaje));
        break;
        case 3://eliminar un registro de la tabla temporal de glosas
            $idGlosa=$obGlosas->normalizar($_REQUEST["idGlosa"]);
            $obGlosas->EliminarGlosaTemporal($idGlosa);
            print("La glosa temporal ha sido eliminada");
        break;
        case 4://Se reciben los parametros para editar una glosa
            if(empty($_REQUEST["idGlosaTemp"]) or empty($_REQUEST["CodigoGlosa"]) or empty($_REQUEST["FechaIPS"]) or empty($_REQUEST["FechaAuditoria"]) or empty($_REQUEST["Observaciones"]) or empty($_REQUEST["ValorEPS"]) ){
                   exit("No se recibieron los valores esperados");
            }
            $idGlosaTemp=$obGlosas->normalizar($_REQUEST["idGlosaTemp"]);
            $DatosGlosaTemp=$obGlosas->DevuelveValores("salud_glosas_iniciales_temp", "ID", $idGlosaTemp);
            $idFactura=$DatosGlosaTemp["num_factura"];
            
            $idActividad=$DatosGlosaTemp["idArchivo"];
            $TipoArchivo=$DatosGlosaTemp["TipoArchivo"];
            $FechaIPS=$obGlosas->normalizar($_REQUEST["FechaIPS"]);
            $FechaAuditoria=$obGlosas->normalizar($_REQUEST["FechaAuditoria"]);
            
            $Observaciones=$obGlosas->normalizar($_REQUEST["Observaciones"]);
            $CodigoGlosa=$obGlosas->normalizar($_REQUEST["CodigoGlosa"]);
            $ValorEPS=$obGlosas->normalizar($_REQUEST["ValorEPS"]);
            $ValorAceptado=$obGlosas->normalizar($_REQUEST["ValorAceptado"]);
            $ValorConciliar=$obGlosas->normalizar($_REQUEST["ValorConciliar"]);
            $TotalActividad=$obGlosas->normalizar($_REQUEST["TotalActividad"]);
            
            $destino='';
            if(!empty($_FILES['Soporte']['name'])){
            
                $Atras="../";
                $carpeta="SoportesSalud/SoportesGlosas/";
                opendir($Atras.$Atras.$carpeta);
                $Name=str_replace(' ','_',$FechaIPS."_".$idActividad."_".$idFactura."_".$_FILES['Soporte']['name']);
                $destino=$carpeta.$Name;
                move_uploaded_file($_FILES['Soporte']['tmp_name'],$Atras.$Atras.$destino);
            }
            $idGlosa=$obGlosas->EditaGlosaRespuestaTemporal($idGlosaTemp,$TipoArchivo,$DatosGlosaTemp["idArchivo"],$idFactura,$DatosGlosaTemp["CodigoActividad"],$DatosGlosaTemp["NombreActividad"],$TotalActividad,$DatosGlosaTemp["EstadoGlosa"],$FechaIPS,$FechaAuditoria,$Observaciones,$CodigoGlosa,$ValorEPS,$ValorAceptado,0,$ValorConciliar,$destino,$idUser,"");
            //$idGlosa=$obGlosas->RegistraGlosaRespuestaTemporal($idGlosaTemp,$TipoArchivo,$idFactura, $idActividad,$TotalActividad, $FechaIPS, $FechaAuditoria, $CodigoGlosa, $ValorEPS, $ValorAceptado, $ValorConciliar,$Observaciones,$destino, $idUser,"");
            //$obGlosas->EliminarGlosaTemporal($idGlosaTemp);
            print("Glosa inicial editada en la tabla temporal");
        break;
        
        case 5:// Guarda las glosas de la tabla temporal a la real
            $obGlosas->GuardaGlosasTemporalesAIniciales($idUser, "");
            print("Glosas Registradas");
        break;
    
        case 6:// Guarda las repuestas a glosas a la tabla temporal 
            $idGlosaTemp=$obGlosas->normalizar($_REQUEST["idGlosa"]);
            
            $DatosGlosa=$obGlosas->DevuelveValores("salud_archivo_control_glosas_respuestas", "ID", $idGlosaTemp);
            $CuentaRIPS=$DatosGlosa["CuentaRIPS"];
            $TipoArchivo=$DatosGlosa["TipoArchivo"];
            $CodActividad=$DatosGlosa["CodigoActividad"];            
            $idFactura=$DatosGlosa["num_factura"];
            $idGlosa=$DatosGlosa["idGlosa"];
            $CodGlosa=$DatosGlosa["id_cod_glosa"];
            $sql="SELECT ID FROM salud_archivo_control_glosas_respuestas_temp WHERE idGlosa='$idGlosa'  AND EstadoGlosa=2";
            $consulta=$obGlosas->Query($sql);
            $DatosExistentes=$obGlosas->FetchArray($consulta);
            if($DatosExistentes["ID"]<>''){
                exit("<h4 style='color:red'>Ya exite una respuesta agregada a esta Glosa $idGlosa en la tabla temporal</h4>");
            }
            $sql="SELECT ID FROM salud_archivo_control_glosas_respuestas WHERE idGlosa='$idGlosa' AND id_cod_glosa='$CodGlosa' AND EstadoGlosa=2";
            $consulta=$obGlosas->Query($sql);
            $DatosExistentes=$obGlosas->FetchArray($consulta);
            if($DatosExistentes["ID"]<>''){
                exit("<h4 style='color:red'>Ya exite una respuesta agregada a esta Glosa $idGlosa</h4>");
            }
            //$DatosFactura=$obGlosas->ValorActual("salud_archivo_facturacion_mov_generados", "valor_neto_pagar,valor_total_pago,CuentaGlobal,CuentaRIPS ", "num_factura='$idFactura'");
            
            $TotalActividad=$DatosGlosa["valor_actividad"];            
            $TotalGlosado=$DatosGlosa["valor_glosado_eps"];
            
            $Descripcion= utf8_encode($DatosGlosa["DescripcionActividad"]);
            
            $FechaIPS=$obGlosas->normalizar($_REQUEST["FechaIPS"]);
            $FechaAuditoria=$obGlosas->normalizar($_REQUEST["FechaAuditoria"]);
            
            $Observaciones=$obGlosas->normalizar($_REQUEST["Observaciones"]);
            $CodigoGlosa=$obGlosas->normalizar($_REQUEST["CodigoGlosa"]);
            $ValorEPS=$obGlosas->normalizar($_REQUEST["ValorEPS"]);
            $ValorAceptado=$obGlosas->normalizar($_REQUEST["ValorAceptado"]);
            $ValorConciliar=$obGlosas->normalizar($_REQUEST["ValorConciliar"]);
                        
            $destino='';
            if(!empty($_FILES['Soporte']['name'])){
            
                $Atras="../";
                $carpeta="SoportesSalud/SoportesGlosas/";
                opendir($Atras.$Atras.$carpeta);
                $Name=str_replace(' ','_',"Respuesta_".$FechaIPS."_".$CuentaRIPS."_$idFactura"."_".$_FILES['Soporte']['name']);
                $destino=$carpeta.$Name;
                move_uploaded_file($_FILES['Soporte']['tmp_name'],$Atras.$Atras.$destino);
            }
            
            $obGlosas->RegistraGlosaRespuestaTemporal('',$TipoArchivo, $idGlosa, $idFactura, $CodActividad, $Descripcion, $TotalActividad, 2, $FechaIPS, $FechaAuditoria, $Observaciones, $CodigoGlosa, $ValorEPS, $ValorAceptado, 0, $ValorConciliar, $destino, $idUser, "");
            
            print("<h4 style='color:blue'>Respuesta a Glosas Registrada en la tabla temporal</h4>");
        break;
        
        case 7://Elimina una respuesta a glosa de la tabla temporal
            $idGlosaTemp=$obGlosas->normalizar($_REQUEST["idGlosa"]);
            $obGlosas->BorraReg("salud_archivo_control_glosas_respuestas_temp", "ID", $idGlosaTemp);
            print("Respuesta a Glosa temporal eliminada");
        break;    
        case 8:// Edita las repuestas a glosas a la tabla temporal 
            $idGlosa=$obGlosas->normalizar($_REQUEST["idGlosa"]);
            
            $DatosGlosa=$obGlosas->DevuelveValores("salud_archivo_control_glosas_respuestas_temp", "ID", $idGlosa);
            $CuentaRIPS=$DatosGlosa["CuentaRIPS"];
            $TipoArchivo=$DatosGlosa["TipoArchivo"];
            $CodActividad=$DatosGlosa["CodigoActividad"];            
            $idFactura=$DatosGlosa["num_factura"];
            
            $TotalActividad=$DatosGlosa["valor_actividad"];            
            $TotalGlosado=$DatosGlosa["valor_glosado_eps"];
                        
            $Descripcion= utf8_encode($DatosGlosa["DescripcionActividad"]);
            
            $FechaIPS=$obGlosas->normalizar($_REQUEST["FechaIPS"]);
            $FechaAuditoria=$obGlosas->normalizar($_REQUEST["FechaAuditoria"]);
            
            $Observaciones=$obGlosas->normalizar($_REQUEST["Observaciones"]);
            $CodigoGlosa=$obGlosas->normalizar($_REQUEST["CodigoGlosa"]);
            $ValorEPS=$obGlosas->normalizar($_REQUEST["ValorEPS"]);
            $ValorAceptado=$obGlosas->normalizar($_REQUEST["ValorAceptado"]);
            $ValorConciliar=$obGlosas->normalizar($_REQUEST["ValorConciliar"]);
            $ValorLevantado=$obGlosas->normalizar($_REQUEST["ValorLevantado"]);     
            $destino='';
            if(!empty($_FILES['Soporte']['name'])){
            
                $Atras="../";
                $carpeta="SoportesSalud/SoportesGlosas/";
                opendir($Atras.$Atras.$carpeta);
                $Name=str_replace(' ','_',"Respuesta_".$FechaIPS."_".$CuentaRIPS."_$idFactura"."_".$_FILES['Soporte']['name']);
                $destino=$carpeta.$Name;
                move_uploaded_file($_FILES['Soporte']['tmp_name'],$Atras.$Atras.$destino);
            }
            
            $obGlosas->RegistraGlosaRespuestaTemporal($idGlosa,$TipoArchivo, $DatosGlosa["idGlosa"], $idFactura, $CodActividad, $Descripcion, $TotalActividad, $DatosGlosa["EstadoGlosa"], $FechaIPS, $FechaAuditoria, $Observaciones, $CodigoGlosa, $ValorEPS, $ValorAceptado, $ValorLevantado, $ValorConciliar, $destino, $idUser, "");
            
            print("<h4 style='color:orange'>Respuesta a Glosas Editada en la tabla temporal</h4>");
        break;
        case 9:// Guarda las respuesta de las glosas de la tabla temporal a la real
            $obGlosas->GuardaConciliacionesTemporalAReal($idUser, "");
            $obGlosas->GuardaRespuestaContraGlosasTemporalAReal($idUser, "");
            $obGlosas->GuardaContraGlosasTemporalAReal($idUser, "");
            $obGlosas->GuardaRespuestasGlosasTemporalAReal($idUser, "");
            $obGlosas->VaciarTabla("salud_archivo_control_glosas_respuestas_temp");
            print("Respuestas a Glosas Registradas");
        break;
    
        case 10:// Guarda contra glosa a la tabla temporal de respuestas
            $idGlosaTemp=$obGlosas->normalizar($_REQUEST["idGlosa"]);
            
            $DatosGlosa=$obGlosas->DevuelveValores("salud_archivo_control_glosas_respuestas", "ID", $idGlosaTemp);
            $CuentaRIPS=$DatosGlosa["CuentaRIPS"];
            $TipoArchivo=$DatosGlosa["TipoArchivo"];
            $CodActividad=$DatosGlosa["CodigoActividad"];            
            $idFactura=$DatosGlosa["num_factura"];
            $idGlosa=$DatosGlosa["idGlosa"];
            $CodGlosa=$DatosGlosa["id_cod_glosa"];
            $sql="SELECT ID FROM salud_archivo_control_glosas_respuestas_temp WHERE idGlosa='$idGlosa'  AND EstadoGlosa=3";
            $consulta=$obGlosas->Query($sql);
            $DatosExistentes=$obGlosas->FetchArray($consulta);
            if($DatosExistentes["ID"]<>''){
                exit("<h4 style='color:red'>Ya exite una respuesta agregada a esta Glosa $idGlosa en la tabla temporal</h4>");
            }
            $sql="SELECT ID FROM salud_archivo_control_glosas_respuestas WHERE idGlosa='$idGlosa' AND id_cod_glosa='$CodGlosa' AND EstadoGlosa=3";
            $consulta=$obGlosas->Query($sql);
            $DatosExistentes=$obGlosas->FetchArray($consulta);
            if($DatosExistentes["ID"]<>''){
                exit("<h4 style='color:red'>Ya exite una respuesta agregada a esta Glosa $idGlosa</h4>");
            }
            //$DatosFactura=$obGlosas->ValorActual("salud_archivo_facturacion_mov_generados", "valor_neto_pagar,valor_total_pago,CuentaGlobal,CuentaRIPS ", "num_factura='$idFactura'");
            
            $TotalActividad=$DatosGlosa["valor_actividad"];            
            $TotalGlosado=$DatosGlosa["valor_glosado_eps"];
            
            $Descripcion= utf8_encode($DatosGlosa["DescripcionActividad"]);
            
            $FechaIPS=$obGlosas->normalizar($_REQUEST["FechaIPS"]);
            $FechaAuditoria=$obGlosas->normalizar($_REQUEST["FechaAuditoria"]);
            
            $Observaciones=$obGlosas->normalizar($_REQUEST["Observaciones"]);
            $CodigoGlosa=$obGlosas->normalizar($_REQUEST["CodigoGlosa"]);
            $ValorEPS=$obGlosas->normalizar($_REQUEST["ValorEPS"]);
            $ValorAceptado=$obGlosas->normalizar($_REQUEST["ValorAceptado"]);
            $ValorConciliar=$obGlosas->normalizar($_REQUEST["ValorConciliar"]);
            $ValorLevantado=$obGlosas->normalizar($_REQUEST["ValorLevantado"]);
                        
            $destino='';
            if(!empty($_FILES['Soporte']['name'])){
            
                $Atras="../";
                $carpeta="SoportesSalud/SoportesGlosas/";
                opendir($Atras.$Atras.$carpeta);
                $Name=str_replace(' ','_',"Respuesta_".$FechaIPS."_".$CuentaRIPS."_$idFactura"."_".$_FILES['Soporte']['name']);
                $destino=$carpeta.$Name;
                move_uploaded_file($_FILES['Soporte']['tmp_name'],$Atras.$Atras.$destino);
            }
            
            $obGlosas->RegistraGlosaRespuestaTemporal('',$TipoArchivo, $idGlosa, $idFactura, $CodActividad, $Descripcion, $TotalActividad, 3, $FechaIPS, $FechaAuditoria, $Observaciones, $CodigoGlosa, $ValorEPS, $ValorAceptado, $ValorLevantado, $ValorConciliar, $destino, $idUser, "");
            
            print("<h4 style='color:blue'>Respuesta a Glosas Registrada en la tabla temporal</h4>");
        break;
        
        case 11:// Guarda las repuestas a glosas a la tabla temporal 
            $idGlosaTemp=$obGlosas->normalizar($_REQUEST["idGlosa"]);
            
            $DatosGlosa=$obGlosas->DevuelveValores("salud_archivo_control_glosas_respuestas", "ID", $idGlosaTemp);
            $CuentaRIPS=$DatosGlosa["CuentaRIPS"];
            $TipoArchivo=$DatosGlosa["TipoArchivo"];
            $CodActividad=$DatosGlosa["CodigoActividad"];            
            $idFactura=$DatosGlosa["num_factura"];
            $idGlosa=$DatosGlosa["idGlosa"];
            $CodGlosa=$DatosGlosa["id_cod_glosa"];
            $sql="SELECT ID FROM salud_archivo_control_glosas_respuestas_temp WHERE idGlosa='$idGlosa'  AND EstadoGlosa=4";
            $consulta=$obGlosas->Query($sql);
            $DatosExistentes=$obGlosas->FetchArray($consulta);
            if($DatosExistentes["ID"]<>''){
                exit("<h4 style='color:red'>Ya exite una respuesta agregada a esta Contra Glosa $idGlosa en la tabla temporal</h4>");
            }
            $sql="SELECT ID FROM salud_archivo_control_glosas_respuestas WHERE idGlosa='$idGlosa' AND id_cod_glosa='$CodGlosa' AND EstadoGlosa=4";
            $consulta=$obGlosas->Query($sql);
            $DatosExistentes=$obGlosas->FetchArray($consulta);
            if($DatosExistentes["ID"]<>''){
                exit("<h4 style='color:red'>Ya exite una respuesta agregada a esta Contra Glosa $idGlosa</h4>");
            }
            
            $TotalActividad=$DatosGlosa["valor_actividad"];            
            $TotalGlosado=$DatosGlosa["valor_glosado_eps"];
            
            $Descripcion= utf8_encode($DatosGlosa["DescripcionActividad"]);
            
            $FechaIPS=$obGlosas->normalizar($_REQUEST["FechaIPS"]);
            $FechaAuditoria=$obGlosas->normalizar($_REQUEST["FechaAuditoria"]);
            
            $Observaciones=$obGlosas->normalizar($_REQUEST["Observaciones"]);
            $CodigoGlosa=$obGlosas->normalizar($_REQUEST["CodigoGlosa"]);
            $ValorEPS=$obGlosas->normalizar($_REQUEST["ValorEPS"]);
            $ValorAceptado=$obGlosas->normalizar($_REQUEST["ValorAceptado"]);
            $ValorConciliar=$obGlosas->normalizar($_REQUEST["ValorConciliar"]);
            $ValorLevantado=$obGlosas->normalizar($_REQUEST["ValorLevantado"]);
                        
            $destino='';
            if(!empty($_FILES['Soporte']['name'])){
            
                $Atras="../";
                $carpeta="SoportesSalud/SoportesGlosas/";
                opendir($Atras.$Atras.$carpeta);
                $Name=str_replace(' ','_',"RespuestaContraGlosa_".$FechaIPS."_".$CuentaRIPS."_$idFactura"."_".$_FILES['Soporte']['name']);
                $destino=$carpeta.$Name;
                move_uploaded_file($_FILES['Soporte']['tmp_name'],$Atras.$Atras.$destino);
            }
            
            $obGlosas->RegistraGlosaRespuestaTemporal('',$TipoArchivo, $idGlosa, $idFactura, $CodActividad, $Descripcion, $TotalActividad, 4, $FechaIPS, $FechaAuditoria, $Observaciones, $CodigoGlosa, $ValorEPS, $ValorAceptado, $ValorLevantado, $ValorConciliar, $destino, $idUser, "");
            
            print("<h4 style='color:blue'>Respuesta a Glosas Registrada en la tabla temporal</h4>");
        break;
        
        case 12:// Guarda las conciliaciones en la tabla temporal
            $idGlosaTemp=$obGlosas->normalizar($_REQUEST["idGlosa"]);
            
            $DatosGlosa=$obGlosas->DevuelveValores("salud_archivo_control_glosas_respuestas", "ID", $idGlosaTemp);
            $CuentaRIPS=$DatosGlosa["CuentaRIPS"];
            $TipoArchivo=$DatosGlosa["TipoArchivo"];
            $CodActividad=$DatosGlosa["CodigoActividad"];            
            $idFactura=$DatosGlosa["num_factura"];
            $idGlosa=$DatosGlosa["idGlosa"];
            $CodGlosa=$DatosGlosa["id_cod_glosa"];
            $sql="SELECT ID FROM salud_archivo_control_glosas_respuestas_temp WHERE idGlosa='$idGlosa'  AND EstadoGlosa=5";
            $consulta=$obGlosas->Query($sql);
            $DatosExistentes=$obGlosas->FetchArray($consulta);
            if($DatosExistentes["ID"]<>''){
                exit("<h4 style='color:red'>Ya exite una conciliacion agregada a esta Contra Glosa $idGlosa en la tabla temporal</h4>");
            }
            $sql="SELECT ID FROM salud_archivo_control_glosas_respuestas WHERE idGlosa='$idGlosa' AND id_cod_glosa='$CodGlosa' AND EstadoGlosa=5";
            $consulta=$obGlosas->Query($sql);
            $DatosExistentes=$obGlosas->FetchArray($consulta);
            if($DatosExistentes["ID"]<>''){
                exit("<h4 style='color:red'>Ya exite una conciliacion agregada a esta Contra Glosa $idGlosa</h4>");
            }
            
            $TotalActividad=$DatosGlosa["valor_actividad"];            
            $TotalGlosado=$DatosGlosa["valor_glosado_eps"];
            
            $Descripcion= utf8_encode($DatosGlosa["DescripcionActividad"]);
            
            $FechaIPS=$obGlosas->normalizar($_REQUEST["FechaIPS"]);
            $FechaAuditoria=$obGlosas->normalizar($_REQUEST["FechaAuditoria"]);
            
            $Observaciones=$obGlosas->normalizar($_REQUEST["Observaciones"]);
            $CodigoGlosa=$obGlosas->normalizar($_REQUEST["CodigoGlosa"]);
            $ValorEPS=$obGlosas->normalizar($_REQUEST["ValorEPS"]);
            $ValorAceptado=$obGlosas->normalizar($_REQUEST["ValorAceptado"]);
            $ValorConciliar=$obGlosas->normalizar($_REQUEST["ValorConciliar"]);
            $ValorLevantado=$obGlosas->normalizar($_REQUEST["ValorLevantado"]);
                        
            $destino='';
            if(!empty($_FILES['Soporte']['name'])){
            
                $Atras="../";
                $carpeta="SoportesSalud/SoportesGlosas/";
                opendir($Atras.$Atras.$carpeta);
                $Name=str_replace(' ','_',"Conciliacion_".$FechaIPS."_".$CuentaRIPS."_$idFactura"."_".$_FILES['Soporte']['name']);
                $destino=$carpeta.$Name;
                move_uploaded_file($_FILES['Soporte']['tmp_name'],$Atras.$Atras.$destino);
            }
            
            $obGlosas->RegistraGlosaRespuestaTemporal('',$TipoArchivo, $idGlosa, $idFactura, $CodActividad, $Descripcion, $TotalActividad, 5, $FechaIPS, $FechaAuditoria, $Observaciones, $CodigoGlosa, $ValorEPS, $ValorAceptado, $ValorLevantado, $ValorConciliar, $destino, $idUser, "");
            
            print("<h4 style='color:blue'>Conciliacion de la Glosa Registrada en la tabla temporal</h4>");
        break;
        case 13://Anular una glosa
            $idGlosa=$obGlosas->normalizar($_REQUEST["idGlosa"]);
            $idFactura=$obGlosas->normalizar($_REQUEST["idFactura"]);
            $CodActividad=$obGlosas->normalizar($_REQUEST["CodActividad"]);
            $TipoArchivo=$obGlosas->normalizar($_REQUEST["TipoArchivo"]);
            $Observaciones=$obGlosas->normalizar($_REQUEST["Observaciones"]);
            $SoloRespuesta=$obGlosas->normalizar($_REQUEST["SoloRespuesta"]);//Si es 1 solo se anulará la respuesta, si es 0 se anulará tambien la glosa inicial
            $obGlosas->AnularGlosa($idGlosa, $Observaciones, $idUser,$SoloRespuesta, "");
            
            if($SoloRespuesta==1){
                $DatosGlosa=$obGlosas->ValorActual("salud_archivo_control_glosas_respuestas", "idGlosa", " ID='$idGlosa'");
                $idGlosaInicial=$DatosGlosa["idGlosa"];//para consultar cual fue el anterior estado a la anulacion de la respuesta
                $DatosGlosa=$obGlosas->ValorActual("salud_archivo_control_glosas_respuestas", "MAX(ID) as UltimoID", " EstadoGlosa<>12 AND idGlosa='$idGlosaInicial'");
                $idGlosaAnterior=$DatosGlosa["UltimoID"]; // para editar la columna Tratado en 0 y poder ver el estado anterior tras una anulacion
                $DatosGlosa=$obGlosas->ValorActual("salud_archivo_control_glosas_respuestas", "EstadoGlosa,valor_levantado_eps,valor_aceptado_ips,valor_glosado_eps", " ID='$idGlosaAnterior'");//Consultamos los valores del ultimo registro para actualizarlos en la glosa inicial
                $ValorAceptado=$DatosGlosa["valor_aceptado_ips"];
                $ValorLevantado=$DatosGlosa["valor_levantado_eps"];
                $ValorGlosado=$DatosGlosa["valor_glosado_eps"];
                $EstadoGlosa=$DatosGlosa["EstadoGlosa"];
                $ValorXConciliar=$ValorGlosado-$ValorAceptado-$ValorLevantado;
                $sql="UPDATE salud_archivo_control_glosas_respuestas SET Tratado='0' "
                        . "WHERE ID='$idGlosaAnterior'";
                $obGlosas->Query($sql); //Actualizo la ultima repuesta a tratado 0 para poder visualizarla nuevamente
                
                $sql="UPDATE salud_glosas_iniciales SET EstadoGlosa='$EstadoGlosa',ValorLevantado='$ValorLevantado',ValorAceptado='$ValorAceptado',ValorXConciliar='$ValorXConciliar' WHERE ID='$idGlosaInicial'";
                $obGlosas->Query($sql);//Actulizo los valores de la glosa inicial y el estado en el que queda
                
                
            }
            $obGlosas->ActualiceEstados($idFactura, $TipoArchivo, $CodActividad, "");
            print("Glosa Anulada");
        break;    
        case 14:
            if(empty($_REQUEST["idGlosaInicial"]) or empty($_REQUEST["idGlosaRespuesta"]) or empty($_REQUEST["FechaIPS"]) or empty($_REQUEST["FechaAuditoria"]) or empty($_REQUEST["Observaciones"]) or empty($_REQUEST["ValorEPS"]) ){
                   exit("No se recibieron los valores esperados");
            }
            $idGlosaInicial=$obGlosas->normalizar($_REQUEST["idGlosaInicial"]);
            $idGlosaRespuesta=$obGlosas->normalizar($_REQUEST["idGlosaRespuesta"]);
            $DatosGlosa=$obGlosas->DevuelveValores("salud_glosas_iniciales", "ID", $idGlosaInicial);
            $DatosGlosaRespuesta=$obGlosas->DevuelveValores("salud_archivo_control_glosas_respuestas", "ID", $idGlosaRespuesta);
            $idFactura=$DatosGlosa["num_factura"];
            $CodActividad=$DatosGlosa["CodigoActividad"];
            
            $FechaIPS=$obGlosas->normalizar($_REQUEST["FechaIPS"]);
            $FechaAuditoria=$obGlosas->normalizar($_REQUEST["FechaAuditoria"]);
            
            $Observaciones=$obGlosas->normalizar($_REQUEST["Observaciones"]);
            $CodigoGlosa=$obGlosas->normalizar($_REQUEST["CodigoGlosa"]);
            $ValorEPS=$obGlosas->normalizar($_REQUEST["ValorEPS"]);
            $ValorAceptado=$obGlosas->normalizar($_REQUEST["ValorAceptado"]);
            $ValorConciliar=$obGlosas->normalizar($_REQUEST["ValorConciliar"]);
            $TotalActividad=$DatosGlosa["ValorActividad"];
            
            $destino='';
            if(!empty($_FILES['Soporte']['name'])){
            
                $Atras="../";
                $carpeta="SoportesSalud/SoportesGlosas/";
                opendir($Atras.$Atras.$carpeta);
                $Name=str_replace(' ','SoporteGlosaInicial_',$FechaIPS."_".$idGlosaInicial."_".$idFactura."_".$_FILES['Soporte']['name']);
                $destino=$carpeta.$Name;
                move_uploaded_file($_FILES['Soporte']['tmp_name'],$Atras.$Atras.$destino);
            }
            $obGlosas->EditaGlosaInicial($idGlosaInicial, $idFactura, $CodActividad, $TotalActividad, 1, $FechaIPS, $FechaAuditoria, $CodigoGlosa, $ValorEPS, $ValorAceptado, 0, $ValorConciliar, $destino, $idUser, "");
            $obGlosas->EditaTablaControlRespuestasGlosas($idGlosaRespuesta, $DatosGlosaRespuesta["TipoArchivo"], $idGlosaInicial, $idFactura, $CodActividad, $DatosGlosaRespuesta["DescripcionActividad"], $TotalActividad, 1, $FechaIPS, $FechaAuditoria, $Observaciones, $CodigoGlosa, $ValorEPS, 0, 0, $ValorEPS, $destino, $idUser, "");
            print("Glosa inicial editada en la tabla temporal");
        break; 
        
        case 15:// Edita las repuestas a glosas  
            $idGlosa=$obGlosas->normalizar($_REQUEST["idGlosa"]);
            
            $DatosGlosa=$obGlosas->DevuelveValores("salud_archivo_control_glosas_respuestas", "ID", $idGlosa);
            $CuentaRIPS=$DatosGlosa["CuentaRIPS"];
            $TipoArchivo=$DatosGlosa["TipoArchivo"];
            $CodActividad=$DatosGlosa["CodigoActividad"];            
            $idFactura=$DatosGlosa["num_factura"];
            
            $TotalActividad=$DatosGlosa["valor_actividad"];            
            $TotalGlosado=$DatosGlosa["valor_glosado_eps"];
                        
            $Descripcion= utf8_encode($DatosGlosa["DescripcionActividad"]);
            
            $FechaIPS=$obGlosas->normalizar($_REQUEST["FechaIPS"]);
            $FechaAuditoria=$obGlosas->normalizar($_REQUEST["FechaAuditoria"]);
            
            $Observaciones=$obGlosas->normalizar($_REQUEST["Observaciones"]);
            $CodigoGlosa=$obGlosas->normalizar($_REQUEST["CodigoGlosa"]);
            $ValorEPS=$obGlosas->normalizar($_REQUEST["ValorEPS"]);
            $ValorAceptado=$obGlosas->normalizar($_REQUEST["ValorAceptado"]);
            $ValorConciliar=$obGlosas->normalizar($_REQUEST["ValorConciliar"]);
            $ValorLevantado=$obGlosas->normalizar($_REQUEST["ValorLevantado"]);     
            $destino='';
            if(!empty($_FILES['Soporte']['name'])){
            
                $Atras="../";
                $carpeta="SoportesSalud/SoportesGlosas/";
                opendir($Atras.$Atras.$carpeta);
                $Name=str_replace(' ','_',"Respuesta_".$FechaIPS."_".$CuentaRIPS."_$idFactura"."_".$_FILES['Soporte']['name']);
                $destino=$carpeta.$Name;
                move_uploaded_file($_FILES['Soporte']['tmp_name'],$Atras.$Atras.$destino);
            }
            $obGlosas->EditaTablaControlRespuestasGlosas($idGlosa, $TipoArchivo, $DatosGlosa["idGlosa"], $idFactura, $CodActividad, $Descripcion, $TotalActividad, $DatosGlosa["EstadoGlosa"], $FechaIPS, $FechaAuditoria, $Observaciones, $CodigoGlosa, $ValorEPS, $ValorAceptado, $ValorLevantado, $ValorConciliar, $destino, $idUser, "");
            $idGlosaInicial=$DatosGlosa["idGlosa"];
            $sql="UPDATE salud_glosas_iniciales SET ValorLevantado='$ValorLevantado',ValorAceptado='$ValorAceptado',ValorXConciliar='$ValorConciliar' WHERE ID='$idGlosaInicial'";
            $obGlosas->Query($sql);
            print("<h4 style='color:orange'>Edicion Realizada</h4>");
        break;
        
        case 16:// Conciliar X actividad
                        
            //$DatosGlosa=$obGlosas->DevuelveValores("salud_archivo_control_glosas_respuestas", "ID", $idGlosa);
            //$CuentaRIPS=$DatosGlosa["CuentaRIPS"];
            $TipoArchivo=$obGlosas->normalizar($_REQUEST["TipoArchivo"]);
            $CodActividad=$obGlosas->normalizar($_REQUEST["CodActividad"]);           
            $idFactura=$obGlosas->normalizar($_REQUEST["idFactura"]);
            
            $TotalActividad=$obGlosas->normalizar($_REQUEST["TotalActividad"]);  
            $TotalGlosado=$obGlosas->normalizar($_REQUEST["ValorEPS"]);  
                        
            $Descripcion= $obGlosas->normalizar($_REQUEST["DescripcionActividad"]);
            
            $FechaIPS=$obGlosas->normalizar($_REQUEST["FechaIPS"]);
            $FechaAuditoria=$obGlosas->normalizar($_REQUEST["FechaAuditoria"]);
            
            $Observaciones=$obGlosas->normalizar($_REQUEST["Observaciones"]);
            $CodigoGlosa=$obGlosas->normalizar($_REQUEST["CodigoGlosa"]);
            $ValorEPS=$obGlosas->normalizar($_REQUEST["ValorEPS"]);
            $ValorAceptado=$obGlosas->normalizar($_REQUEST["ValorAceptado"]);
            $ValorConciliar=$obGlosas->normalizar($_REQUEST["ValorConciliar"]);
            $ValorLevantado=$obGlosas->normalizar($_REQUEST["ValorLevantado"]);     
            $destino='';
            if(!empty($_FILES['Soporte']['name'])){
            
                $Atras="../";
                $carpeta="SoportesSalud/SoportesGlosas/";
                opendir($Atras.$Atras.$carpeta);
                $Name=str_replace(' ','_',"ConciliacionXActividad_".$FechaIPS."_".$CodActividad."_$idFactura"."_".$_FILES['Soporte']['name']);
                $destino=$carpeta.$Name;
                move_uploaded_file($_FILES['Soporte']['tmp_name'],$Atras.$Atras.$destino);
            }
            $sql="UPDATE salud_archivo_control_glosas_respuestas SET EstadoGlosa=13 WHERE num_factura='$idFactura' AND CodigoActividad='$CodActividad'";
            $obGlosas->Query($sql);
            $sql="UPDATE salud_glosas_iniciales SET EstadoGlosa=13 WHERE num_factura='$idFactura' AND CodigoActividad='$CodActividad'";
            $obGlosas->Query($sql);
            $idGlosa=$obGlosas->RegistrarGlosaInicialConciliada(6, $idFactura, $CodActividad, $TotalActividad, $FechaIPS, $FechaAuditoria, '', $ValorEPS, $ValorAceptado, $ValorConciliar, $ValorLevantado, "");
            $obGlosas->RegistraGlosaRespuesta($TipoArchivo, $idGlosa, $idFactura, $CodActividad, $Descripcion, $TotalActividad, 6, $FechaIPS, $FechaAuditoria, $Observaciones, '', $ValorEPS, $ValorAceptado, $ValorLevantado, $ValorConciliar, $destino, $idUser, "");
            $obGlosas->ActualiceEstados($idFactura, $TipoArchivo, $CodActividad, "");
            print("<h4 style='color:orange'>Conciliacion X Actividad Realizada</h4>");
        break;
    }
          
}else{
    print("No se enviaron parametros");
}
?>