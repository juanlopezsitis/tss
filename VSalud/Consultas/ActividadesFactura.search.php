<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
$TipoUser=$_SESSION['tipouser'];

//$myPage="titulos_comisiones.php";
include_once("../../modelo/php_conexion.php");

include_once("../css_construct.php");


if( !empty($_REQUEST["idFactura"]) ){
    $css =  new CssIni("id",0);
    $obGlosas = new conexion($idUser);
    
    $idFactura=$obGlosas->normalizar($_REQUEST["idFactura"]);
    //$css->CrearNotificacionAzul("Factura No. $idFactura", 14);
    
    $css->CrearTabla();
            $css->FilaTabla(16);
                $css->ColTabla("<strong>Actividades</strong>", 6);
            $css->CierraFilaTabla();
            $css->FilaTabla(12);
                $css->ColTabla("Archivo", 1);
                $css->ColTabla("Codigo", 1);
                $css->ColTabla("Descripcion", 1);
                $css->ColTabla("Valor Unitario", 1);
                $css->ColTabla("Cantidad", 1);
                $css->ColTabla("Valor Contratado", 1);
                $css->ColTabla("Valor Total", 1);
                $css->ColTabla("Valor Glosado", 1);
                $css->ColTabla("Valor Levantado", 1);
                $css->ColTabla("Glosar", 1);
                $css->ColTabla("Responder", 1);
                $css->ColTabla("Valor Conciliado", 1);
                $css->ColTabla("Estado", 1);
            $css->CierraFilaTabla();
            
            $sql1="SELECT 'AC' as Archivo,id_consultas  as idArchivo,cod_consulta as Codigo,"
                    . "(SELECT descripcion_cups FROM salud_cups WHERE salud_cups.codigo_sistema=salud_archivo_consultas.cod_consulta) as Descripcion,"
                    . "`valor_consulta` as ValorUnitario, "
                    . "'1' as Cantidad, `valor_consulta` as Total,EstadoGlosa, "
                    . "(SELECT Estado_glosa FROM salud_estado_glosas WHERE salud_estado_glosas.ID=salud_archivo_consultas.EstadoGlosa) as Estado "
                    . "FROM `salud_archivo_consultas` WHERE `num_factura`='$idFactura'";
            
            $sql2="SELECT 'AP' as Archivo, id_procedimiento as idArchivo,cod_procedimiento as Codigo,"
                    . "(SELECT descripcion_cups FROM salud_cups WHERE salud_cups.codigo_sistema=salud_archivo_procedimientos.cod_procedimiento) as Descripcion,"
                    . "`valor_procedimiento` as ValorUnitario, "
                    . "'1' as Cantidad, `valor_procedimiento` as Total,EstadoGlosa, "
                    . "(SELECT Estado_glosa FROM salud_estado_glosas WHERE salud_estado_glosas.ID=salud_archivo_procedimientos.EstadoGlosa) as Estado "
                    . "FROM `salud_archivo_procedimientos` WHERE `num_factura`='$idFactura'";
            
            $sql3="SELECT 'AT' as Archivo,id_otro_servicios as idArchivo,cod_servicio as Codigo,"
                    . "nom_servicio as Descripcion,"
                    . "`valor_unit_material` as ValorUnitario, "
                    . " SUM(cantidad)  as Cantidad, SUM(`valor_total_material`) as Total,EstadoGlosa, "
                    . "(SELECT Estado_glosa FROM salud_estado_glosas WHERE salud_estado_glosas.ID=salud_archivo_otros_servicios.EstadoGlosa) as Estado "
                    . "FROM `salud_archivo_otros_servicios` WHERE `num_factura`='$idFactura' GROUP BY cod_servicio";
            
            $sql4="SELECT 'AM' as Archivo,id_medicamentos as idArchivo, `cod_medicamento` as Codigo,`nom_medicamento` as Descripcion,`valor_unit_medic` as ValorUnitario, "
                    . "SUM(`num_und_medic`) as Cantidad, SUM(`valor_total_medic`) as Total,EstadoGlosa, "
                    . "(SELECT Estado_glosa FROM salud_estado_glosas WHERE salud_estado_glosas.ID=salud_archivo_medicamentos.EstadoGlosa) as Estado "
                    . "FROM `salud_archivo_medicamentos` WHERE `num_factura`='$idFactura' GROUP BY cod_medicamento";
            
            $sql=$sql1." UNION ".$sql2." UNION ".$sql3." UNION ".$sql4;
            $Consulta=$obGlosas->Query($sql); 
            
            while($DatosFactura=$obGlosas->FetchArray($Consulta)){
                $css->FilaTabla(12);
                    $TipoArchivo=$DatosFactura["Archivo"];
                    $idArchivo=$DatosFactura["idArchivo"];
                    $css->ColTabla($DatosFactura["Archivo"], 1);
                    $css->ColTabla($DatosFactura["Codigo"], 1);
                    $css->ColTabla(utf8_encode($DatosFactura["Descripcion"]), 1);
                    $css->ColTabla(number_format($DatosFactura["ValorUnitario"]), 1);
                    $css->ColTabla($DatosFactura["Cantidad"], 1);
                    $css->ColTabla("", 1);//pendiente despues de contratos
                    $css->ColTabla(number_format($DatosFactura["Total"]), 1);
                    $css->ColTabla($DatosFactura["Archivo"], 1);
                    $css->ColTabla($DatosFactura["Archivo"], 1);
                    $css->ColTabla($DatosFactura["Archivo"], 1);
                    print("<td>");
                        $Enable=1;
                        if($DatosFactura["EstadoGlosa"]==9 or $DatosFactura["EstadoGlosa"]==5 or $DatosFactura["EstadoGlosa"]==11){
                            $Enable=0;
                        }
                        $css->CrearBotonEvento("BtnGlosarActividad", "Glosar", $Enable, "onClick", "GlosarActividad('$TipoArchivo','$idArchivo','$idFactura')", "naranja", "");
                    
                        print("</td>");
                    print("<td>");
                        $css->CrearBotonEvento("BtnResponderActividad", "Detalles", $Enable, "onClick", "VerDetallesActividad()", "verde", "");
                    print("</td>");
                    $css->ColTabla($DatosFactura["Estado"], 1);
                $css->CierraFilaTabla();
            }
            
        $css->CerrarTabla();
        
        
          
}else{
    print("No se enviaron parametros");
}
?>