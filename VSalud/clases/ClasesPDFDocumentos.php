<?php
/* 
 * Clase donde se realizaran la generacion de informes.
 * Julian Alvaran
 * Techno Soluciones SAS
 */
//include_once '../../modelo/php_tablas.php';
class Documento extends Tabla{
    
    //Comprobante de ingreso
    
     public function PDF_CobroPrejuridico($idCobro) {
        $DatosCobro= $this->obCon->DevuelveValores("salud_cobros_prejuridicos", "ID", $idCobro);
        if($DatosCobro["TipoCobro"]==1){ //Indica si es prejuridico 1 o 2
            $idFormato=27;  //Formato de calidad para el cobro prejuridico 1
            $Asunto="CLIENTE CON ALTURA DE MORA DE 1 A 29 DÍAS DE ATRASO";
        
        }else{
            $idFormato=28;   //Formato de calidad para el cobro prejuridico 2
            $Asunto="CLIENTE CON ALTURA DE MORA DE MÁS DE 29 DÍAS DE ATRASO";
        }
        
        $fecha=date("Y-m-d");
        $DatosFormatos= $this->obCon->DevuelveValores("formatos_calidad", "ID", $idFormato);
        
        $Documento="$DatosFormatos[Nombre] No. $idCobro";
        
        $this->PDF_Ini("CobroPrejuridico", 8, ""); 
        
        $DatosUsuarios= $this->obCon->DevuelveValores("usuarios", "idUsuarios", $DatosCobro["idUser"]);
        $sql="SELECT cod_enti_administradora,SUM(`valor_neto_pagar`) AS TotalFacturas FROM salud_cobros_prejuridicos_relaciones p"
                . " INNER JOIN salud_archivo_facturacion_mov_generados f ON f.num_factura=p.num_factura "
                . "WHERE p.idCobroPrejuridico='$idCobro' LIMIT 1"; //Busco una factura que corresponda al cobro
        $consulta=$this->obCon->Query($sql);
        $DatosFacturas=$this->obCon->FetchArray($consulta);
        $DatosEPS=$this->obCon->DevuelveValores("salud_eps", "cod_pagador_min", $DatosFacturas["cod_enti_administradora"]); 
        $DatosEmpresaPro=$this->obCon->DevuelveValores("empresapro", "idEmpresaPro", 1);
        $this->PDF_Encabezado($fecha,1, $idFormato, "",$Documento); //encabezado del formato de calidad
        $this->PDF->SetMargins(20, PDF_MARGIN_TOP, 20);
        $this->PDF->SetFont('helvetica', '', 8);
        $html="<br>";
        $html.="
<p style=margin-left:0cm; margin-right:0cm; text-align:justify><span style=font-size:11pt><span style=font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;><strong><span style=font-size:10.0pt><span style=font-family:&quot;Arial&quot;,&quot;sans-serif&quot;>$DatosEmpresaPro[Ciudad], $DatosCobro[Fecha]</span></span></strong><span style=font-size:10.0pt><span style=font-family:&quot;Arial&quot;,&quot;sans-serif&quot;></span></span></span></span></p>

<p style=margin-left:0cm; margin-right:0cm; text-align:justify><span style=font-size:11pt><span style=font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;><strong><span style=font-size:10.0pt><span style=font-family:&quot;Arial&quot;,&quot;sans-serif&quot;>Se&ntilde;or</span></span></strong><span style=font-size:10.0pt><span style=font-family:&quot;Arial&quot;,&quot;sans-serif&quot;>(a)</span></span></span></span></p>

<p style=margin-left:0cm; margin-right:0cm; text-align:justify><span style=font-size:11pt><span style=font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;><span style=font-size:10.0pt><span style=background-color:yellow><span style=font-family:&quot;Arial&quot;,&quot;sans-serif&quot;>$DatosEPS[nombre_completo]</span></span></span></span></span></p>

<p style=margin-left:0cm; margin-right:0cm; text-align:justify><span style=font-size:11pt><span style=font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;><strong><span style=font-size:10.0pt><span style=background-color:yellow><span style=font-family:&quot;Arial&quot;,&quot;sans-serif&quot;>DIRECCI&Oacute;N</span></span></span></strong><span style=font-size:10.0pt><span style=background-color:yellow><span style=font-family:&quot;Arial&quot;,&quot;sans-serif&quot;>:</span></span></span></span></span></p>

<p style=margin-left:0cm; margin-right:0cm; text-align:justify><span style=font-size:11pt><span style=font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;><strong><span style=font-size:10.0pt><span style=font-family:&quot;Arial&quot;,&quot;sans-serif&quot;>REF</span></span></strong><span style=font-size:10.0pt><span style=font-family:&quot;Arial&quot;,&quot;sans-serif&quot;>:&nbsp;&nbsp;&nbsp;$Asunto</span></span></span></span></p>

<p style=margin-left:0cm; margin-right:0cm; text-align:justify><span style=font-size:11pt><span style=font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;><strong><span style=font-size:10.0pt><span style=font-family:&quot;Arial&quot;,&quot;sans-serif&quot;>FACTURA(S) No</span></span></strong><span style=font-size:10.0pt><span style=font-family:&quot;Arial&quot;,&quot;sans-serif&quot;>:&nbsp;<span style=background-color:yellow>SEGÚN RELACIÓN ADJUNTA</span></span></span></span></span></p>

<p style=margin-left:0cm; margin-right:0cm; text-align:justify><span style=font-size:11pt><span style=font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;><strong><span style=font-size:10.0pt><span style=font-family:&quot;Arial&quot;,&quot;sans-serif&quot;>POR TOTAL DE: </span></span></strong><span style=font-size:10.0pt><span style=font-family:&quot;Arial&quot;,&quot;sans-serif&quot;>&nbsp;<span style=background-color:yellow>$ ".number_format($DatosFacturas["TotalFacturas"])."</span></span></span></span></span></p>

<p style=margin-left:0cm; margin-right:0cm; text-align:justify>&nbsp;</p>

";
        
$html.=$DatosFormatos["CuerpoFormato"];        
        $this->PDF_Write("<br>".$html);
        
        
        $this->PDF_Output("CobroPrejuridico_$idCobro");
    }
    /**
     * Genera el PDF de un reporte
     * @param type $idReporte
     */
    public function Reportes_PDF($idReporte,$st,$idUser,$Vector) {
        $DatosFormatos= $this->obCon->DevuelveValores("formatos_calidad", "ID", 32);
        if($idReporte==1){
            $Documento="Glosas Pendientes por Conciliar";
            $html= $this->ReportesHTML_PendientesXConciliar($st);
        }
        if($idReporte==2){
            $Documento="Glosas Pendientes por Contestar";
        }
        if($idReporte==3){
            $Documento="Porcentajes de valores Glosados definidos Eps";
        }
        if($idReporte==4){
            $Documento="Porcentajes de valores Glosados definidos IPS";
        }
        if($idReporte==5){
            $Documento="Reporte 2193";
        }
        if($idReporte==6){
            $Documento="Reporte de concepto de glosas mas frecuente";
        }
        
        $this->PDF_Ini($Documento, 8,""); 
        $this->PDF_Encabezado(date("Y-m-d"),1, 32, "",$Documento); 
        $this->PDF_Write("<br><br><h3 style='text-align:center'>$Documento</h3>");
        
        $Position=$this->PDF->SetY(60);
        $this->PDF_Write($html);
        
        $Position=$this->PDF->GetY();
        if($Position>240){
          $this->PDF_Add();
        }
        $html= $this->FooterHTMLReportes($idUser);
        $this->PDF->SetY(282);
        $this->PDF_Write($html);
        $this->PDF_Output($Documento);
    }
    /**
     * Arma el HTML para visualizar los reportes de glosas pendientes por conciliar
     * @param type $st
     */
    public function ReportesHTML_PendientesXConciliar($st) {
        
        $Back="#CEE3F6";
        $html='<table cellspacing="1" cellpadding="2" border="0"  align="left" >';
            $html.="<tr>";
                $html.='<td>';
                $html.="<strong>CUENTA</strong>";
                $html.="</td>";
                $html.="<td>";
                $html.="<strong>ENTIDAD</strong>";
                $html.="</td>";
                $html.="<td>";
                $html.="<strong>PRESTADOR</strong>";
                $html.="</td>";
                $html.="<td>";
                $html.="<strong>FACTURA</strong>";
                $html.="</td>";
                $html.="<td>";
                $html.="<strong>IDENTIFICACION</strong>";
                $html.="</td>";
                $html.="<td>";
                $html.="<strong>ESTADO</strong>";
                $html.="</td>";
            $html.="</tr>";
            
            $query="SELECT cuenta,factura,nombre_administrador,fecha_factura,cod_prestador, identificacion, descripcion_estado ";
            $consulta= $this->obCon->Query("$query FROM $st");
            $h=0;
            while($DatosRespuestas=$this->obCon->FetchAssoc($consulta)){
                if($h==0){
                    $Back="#f2f2f2";
                    $h=1;
                }else{
                    $Back="white";
                    $h=0;
                }
                
                $html.='<tr align="left" border="1" style="border-bottom: 2px solid #ddd;background-color: '.$Back.';"> ';
                    $html.="<td>";
                    $html.=$DatosRespuestas["cuenta"];
                    $html.="</td>";
                    $html.="<td>";
                    $html.= utf8_encode($DatosRespuestas["nombre_administrador"]);
                    $html.="</td>";
                    $html.="<td>";
                    $html.=$DatosRespuestas["cod_prestador"];
                    $html.="</td>";
                    $html.="<td>";
                    $html.=$DatosRespuestas["factura"];
                    $html.="</td>";
                    $html.="<td>";
                    $html.=$DatosRespuestas["identificacion"];
                    $html.="</td>";
                    $html.="<td>";
                    $html.=$DatosRespuestas["descripcion_estado"];
                    $html.="</td>";
                $html.="</tr>";
            }
        $html.="</tabla>";
        return($html);
    }
    
    public function FooterHTMLReportes($idUser) {
        $DatosUsuario= $this->obCon->DevuelveValores("usuarios","idUsuarios",$idUser);
        $Fecha=date("Y-m-d H:i:s");
        $html="Impreso por $DatosUsuario[Nombre], Fecha: $Fecha";
        return($html);
    }
   //Fin Clases
}
    