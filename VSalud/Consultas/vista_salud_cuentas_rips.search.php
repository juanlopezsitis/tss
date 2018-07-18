<?php

session_start();
$idUser=$_SESSION['idUser'];
$TipoUser=$_SESSION['tipouser'];
//$myPage="titulos_comisiones.php";
include_once("../../modelo/php_conexion.php");

include_once("../css_construct.php");


if(isset($_REQUEST["idEPS"]) or !empty($_REQUEST["idFactura"]) or !empty($_REQUEST["CuentaRIPS"]) or !empty($_REQUEST["CuentaGlobal"]) or !empty($_REQUEST["CmdEstadoGlosa"]) or !empty($_REQUEST["Page"])){
    $css =  new CssIni("id",0);
    $obGlosas = new conexion($idUser);
    
    // Consultas enviadas a traves de la URL
    $statement="";
    if(isset($_REQUEST['st'])){

        $statement= base64_decode($_REQUEST['st']);
        //print($statement);
    }
    
    //Paginacion
    if(isset($_REQUEST['Page'])){
        $NumPage=$obGlosas->normalizar($_REQUEST['Page']);
    }else{
        $NumPage=1;
    }
    
    //////////////////
    //Busco por EPS
    if(isset($_REQUEST["idEPS"])){
        $idEPS=$obGlosas->normalizar($_REQUEST['idEPS']);
        //print("id $idEPS");
        if($idEPS==''){
            
            $Filtros=" WHERE EstadoGlosa<>11";
        }else{
            $Filtros="WHERE cod_enti_administradora='$idEPS' AND EstadoGlosa<>11";
        }
        $statement=" vista_salud_cuentas_rips $Filtros ORDER BY Total DESC";
    }
    //Busco por Cuenta Numero de Factura
    if(isset($_REQUEST["idFactura"])){
        $NumFactura=$obGlosas->normalizar($_REQUEST['idFactura']);
        //$css->CrearNotificacionRoja("Cuentas que contienen la factura: ".$NumFactura, 16);        
        $statement=" `vista_salud_cuentas_rips` WHERE `CuentaRIPS`=(SELECT CuentaRIPS FROM salud_archivo_facturacion_mov_generados WHERE num_factura='$NumFactura')";
    }
    //Busco por Cuenta RIPS
    if(isset($_REQUEST["CuentaRIPS"])){
        $CuentaRIPS=$obGlosas->normalizar($_REQUEST['CuentaRIPS']);
        $statement=" `vista_salud_cuentas_rips` WHERE `CuentaRIPS`='$CuentaRIPS'";
    }
    //Busco por Cuenta Global
    if(isset($_REQUEST["CuentaGlobal"])){
        $CuentaGlobal=$obGlosas->normalizar($_REQUEST['CuentaGlobal']);
        $statement=" `vista_salud_cuentas_rips` WHERE `CuentaGlobal`='$CuentaGlobal'";
    }
    //Busco por Estado de Glosa
    if(isset($_REQUEST["CmdEstadoGlosa"])){
        $idEstadoGlosa=$obGlosas->normalizar($_REQUEST['CmdEstadoGlosa']);
        $statement=" `vista_salud_cuentas_rips` WHERE `idEstadoGlosa`='$idEstadoGlosa'";
    }
    //Paginacion
    $limit = 10;
    $startpoint = ($NumPage * $limit) - $limit;
    $VectorST = explode("LIMIT", $statement);
    $statement = $VectorST[0]; 
    $query = "SELECT COUNT(*) as `num` FROM {$statement}";
    $row = $obGlosas->FetchArray($obGlosas->Query($query));
    $ResultadosTotales = $row['num'];
        
    $statement.=" LIMIT $startpoint,$limit";
    
    //print("st:$statement");
    $consulta=$obGlosas->Query("SELECT * FROM $statement");
    if($obGlosas->NumRows($consulta)){
        $Resultados=$obGlosas->NumRows($consulta);
        $css->CrearTabla();
        //Paginacion
        if($Resultados){
            $st= base64_encode($statement);
            if($ResultadosTotales>$limit){
                
                $css->FilaTabla(16);
                print("<td colspan='3' style=text-align:center>");
                if($NumPage>1){
                    $NumPage1=$NumPage-1;
                    $Page="Consultas/vista_salud_cuentas_rips.search.php?st=$st&Page=$NumPage1&Carry=";
                    $FuncionJS="EnvieObjetoConsulta(`$Page`,`idEPS`,`DivCuentas`,`5`);return false ;";
                    
                    $css->CrearBotonEvento("BtnMas", "Page $NumPage1", 1, "onclick", $FuncionJS, "rojo", "");
                    
                }
                print("</td>");
                $TotalPaginas= ceil($ResultadosTotales/$limit);
                print("<td colspan=5 style=text-align:center>");
                print("<strong>Pagina: </strong>");
                                
                $Page="Consultas/vista_salud_cuentas_rips.search.php?st=$st&Page=";
                $FuncionJS="EnvieObjetoConsulta(`$Page`,`CmbPage`,`DivCuentas`,`5`);return false ;";
                $css->CrearSelect("CmbPage", $FuncionJS,70);
                    for($p=1;$p<=$TotalPaginas;$p++){
                        if($p==$NumPage){
                            $sel=1;
                        }else{
                            $sel=0;
                        }
                        $css->CrearOptionSelect($p, "$p", $sel);
                    }
                    
                $css->CerrarSelect();
                
                print("</td>");
                print("<td colspan='4' style=text-align:center>");
                if($ResultadosTotales>($startpoint+$limit)){
                    $NumPage1=$NumPage+1;
                    $Page="Consultas/vista_salud_cuentas_rips.search.php?st=$st&Page=$NumPage1&Carry=";
                    $FuncionJS="EnvieObjetoConsulta(`$Page`,`idEPS`,`DivCuentas`,`5`);return false ;";
                    $css->CrearBotonEvento("BtnMas", "Page $NumPage1", 1, "onclick", $FuncionJS, "verde", "");
                }
                print("</td>");
               $css->CierraFilaTabla(); 
            }
        }   
        $css->FilaTabla(12);
            $css->ColTabla("<strong>Cuenta RIPS</strong>", 1);
            $css->ColTabla("<strong>Cuenta Global</strong>", 1);
            $css->ColTabla("<strong>Cod EPS</strong>", 1);
            $css->ColTabla("<strong>Nombre</strong>", 1);
            $css->ColTabla("<strong>Fecha Inicial</strong>", 1);
            $css->ColTabla("<strong>Fecha Final</strong>", 1);
            $css->ColTabla("<strong>Fecha de Radicado</strong>", 1);
            $css->ColTabla("<strong>Numero de Radicado</strong>", 1);
            $css->ColTabla("<strong>Cantidad de Facturas</strong>", 1);
            $css->ColTabla("<strong>Total Cuenta</strong>", 1);
            $css->ColTabla("<strong>Estado Glosa</strong>", 1);
            $css->ColTabla("<strong>Abrir</strong>", 1);
            
        $css->CierraFilaTabla();
        
        while($DatosCuenta=$obGlosas->FetchArray($consulta)){
            
            $css->FilaTabla(12);
                $css->ColTabla($DatosCuenta["CuentaRIPS"], 1);
                $css->ColTabla($DatosCuenta["CuentaGlobal"], 1);
                $css->ColTabla($DatosCuenta["cod_enti_administradora"], 1);
                $css->ColTabla($DatosCuenta["nom_enti_administradora"], 1);
                $css->ColTabla($DatosCuenta["FechaDesde"], 1);
                $css->ColTabla($DatosCuenta["FechaHasta"], 1);
                $css->ColTabla($DatosCuenta["fecha_radicado"], 1);
                $css->ColTabla($DatosCuenta["fecha_radicado"], 1);
                $css->ColTabla(number_format($DatosCuenta["NumFacturas"]), 1);
                $css->ColTabla(number_format($DatosCuenta["Total"]), 1);
                $css->ColTabla($DatosCuenta["EstadoGlosa"], 1);
                
                print("<td style='text-align:center'>");
                     $css->CrearBotonEvento("BtnMostrar", "ver cuenta", 1, "onClick", "MostrarFacturas($DatosCuenta[CuentaRIPS])", "naranja", "");
                print("</td>");
            $css->CierraFilaTabla();
        }
        $css->CerrarTabla();
    }else{
        $css->CrearNotificacionRoja("No se encontraron datos", 16);
    }
    
}else{
    print("No se enviaron parametros");
}
?>