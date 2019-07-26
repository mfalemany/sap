<?php
require_once(toba_dir() . '/php/3ros/ezpdf/class.ezpdf.php');
require_once('consultas/co_tablas_basicas.php'); 
require_once('consultas/co_convocatorias.php'); 
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    function puntos_cm($medida, $resolucion = 72)
    {
        //// 2.54 cm / pulgada
        return ($medida/(2.54))*$resolucion;
        
    }

    /* Recibe una cadena de texto y un tamaño de renglon, y retorna
		un array con renglones de esa longitud (respeta las palabras y no las corta) */
	function string_multilinea($texto, $size)
	{
		//array de retorno
		$renglones = array();

		//obtengo las palabras separadas en un array
		$palabras = explode(" ",$texto);

		$renglon_actual = '';
		foreach($palabras as $indice => $palabra){
			//si el largo de la palabra actual, mas el largo del renglon no supera el maximo, la agrego al renglon
			if( (strlen($renglon_actual) + strlen($palabra)) <= $size){
				if(strlen($renglon_actual) == 0){
					$renglon_actual .= $palabra;	
				}else{
					$renglon_actual .= " ".$palabra;	
				}
				//si es la ultima palabra del array agrego el renglon aunque no se haya llenado
				if($indice == count($palabras)-1){
					$renglones[] = $renglon_actual;
				}
			}else{
				//agrego el renglon lleno al array de renglones
				$renglones[] = $renglon_actual;

				//y evalúo que hacer con la palabra que sobró			
				if($indice == count($palabras) - 1){
					//si es la ultima palabra del array, la agrego directamente al siguiente renglon
					$renglones[] = $palabra;
				}else{
					//si no es la última, la agrego a un nuevo renglon para seguir concatenando en la proxima iteracion
					$renglon_actual = $palabra;
				}
			}

		}
		return $renglones;
		
	}


    toba::memoria()->desactivar_reciclado();
    $font='Helvetica.afm';   
    $font_b='Helvetica-Bold.afm';


    $parametros = toba::memoria()->get_dato('datos');
    //obtengo los detalles de la convocatoria
    $conv = co_convocatorias::get_detalles_convocatoria($parametros['cabecera']['sap_convocatoria_id']);

    $image =  imagecreatefromjpeg(toba::proyecto()->get_path() . '/www/img/logoUNNE_completo_bn.jpg');
    //$image = imagecreatefromgif(toba::proyecto()->get_path() . '/www/img/logo.gif');
    
    $dep=co_tablas_basicas::get_dependencia_nombre($parametros['cabecera']['sap_dependencia_id']);
    $tipo=co_tablas_basicas::get_tipobeca_desc($parametros['cabecera']['sap_tipo_beca_id']);
    $area=co_tablas_basicas::get_area_conocimiento_nombre($parametros['cabecera']['sap_area_beca_id']);
    $orden_poster = $parametros['cabecera']['orden_poster'];
    $proy=  toba::consulta_php('co_proyectos')->get_proyecto($parametros['cabecera']['proyecto_id']);
    //toba::logger()->info('Sergio ' . $proy);
    $titulo=utf8_decode('UNIVERSIDAD NACIONAL DEL NORDESTE');
    $subtitulo= $conv['nombre'].' - '.substr($conv["fecha_desde"],0,4);
    $identificador= $parametros['cabecera']['id'];
    $areabeca=utf8_decode( $area['nombre']);
    $titulotrabajo = $parametros['cabecera']['titulo'];
    $autores = $parametros['autores'];
    $email = $parametros['cabecera']['e_mail'];
    $telefono=$parametros['cabecera']['telefono'];
    $tipobeca= $tipo['descripcion'];
    $resolucion=$parametros['cabecera']['resolucion'];
    $p_desde=$parametros['cabecera']['periodo_desde'] ;
    $p_hasta=$parametros['cabecera']['periodo_hasta'];
    $periodo=  substr($p_desde,8,2) . '-' . substr($p_desde,5,2) . '-' . substr($p_desde,0,4) . '/' . substr($p_hasta,8,2) . '-' . substr($p_hasta,5,2) . '-' . substr($p_hasta,0,4);
    $proyecto = $proy;
    $dependencia=$dep['nombre'];
    $palabraclave=$parametros['palabras'];
    $resumen= $parametros['cabecera']['resumen'];
    $version=$parametros['cabecera']['version_impresa'];
    $x_campo=4.2;
    $x_label=1.3;
    $size_label=9;
    $size_campo=10;
    $incremento_y=0.4;
    //para controlar los saltos de linea
    $size_renglon = 90;
    
    //GENERACION DEL REPORTE
    $pdf = new Cezpdf();
    $pdf->selectFont(toba_dir() . '/php/3ros/ezpdf/fonts/Helvetica.afm');
    $pdf->addImage($image,5 , ($pdf->y - 40), 55, 65);
//    $pdf->y = ($pdf->y -50);
    $pdf->setFontFamily('init','b');
    //TITULO
    $pdf->addText(puntos_cm(5.7),puntos_cm(28),14,'<b>' . $titulo . '</b>'); 
    $pdf->addText(puntos_cm(5.5),puntos_cm(27.5),12,'<b>' . $subtitulo . '</b>'); 
   
//    $y = $pdf->y;
//    $y2 = ( $y - 2 );
    $pdf->setLineStyle(1,'square');
    $pdf->setStrokeColor(0,0,0);
    $pdf->line(puntos_cm(1), puntos_cm(27), puntos_cm(20), puntos_cm(27));
    
    //Cuerpo
    $y=26.5;
    /* ORDEN POSTER */
    $pdf->addText(puntos_cm($x_label),puntos_cm($y),$size_label,'<b>Orden de Poster</b>');  
    $pdf->addText(puntos_cm($x_campo),puntos_cm($y),$size_campo,$orden_poster);  
    /* IDENTIFICADOR */
    $y-=$incremento_y;
    $pdf->addText(puntos_cm($x_label),puntos_cm($y),$size_label,'<b>Identificador</b>');  
    $pdf->addText(puntos_cm($x_campo),puntos_cm($y),$size_campo,$identificador);  
    /* VERSION */
    $pdf->addText(puntos_cm($x_label+4.3),puntos_cm($y),$size_label,'<b>Versión</b>'); 
    $pdf->addText(puntos_cm($x_campo+3),puntos_cm($y),$size_campo,$version);  
    /* AREA DE BECA */
    $y-=$incremento_y;
    $pdf->addText(puntos_cm($x_label),puntos_cm($y),$size_label,'<b>Area de Beca:</b>'); 
    $pdf->addText(puntos_cm($x_campo),puntos_cm($y),$size_campo,$areabeca);  
    $y-=$incremento_y;
    $pdf->addText(puntos_cm($x_label),puntos_cm($y),$size_label,'<b>Título del Trabajo:</b>'); 
	
	//se imprime el titulo controlando el salto de linea segun su longitud
	foreach(string_multilinea($titulotrabajo,$size_renglon) as $renglon){
		$pdf->addText(puntos_cm($x_campo),puntos_cm($y),$size_campo, $renglon);
		$y-=$incremento_y;
	} 

   
   $y-=$incremento_y;
   
    /* ********************* PROBLEMA: NO HACÍA SALTO DE LINEA ***************** */
    $pdf->addText(puntos_cm($x_label),puntos_cm($y),$size_label,'<b>Autores:</b>'); 
    $autores = explode("/",$autores);
        foreach($autores as $indice => $autor){
            //si el campo autor tiene algo (no es vacio o espacios)
            if( strlen(str_replace(" ","",$autor)) > 0 ){
                $pdf->addText(puntos_cm($x_campo),puntos_cm($y),$size_campo,"- ".trim($autor) );
                $y-=$incremento_y;        
            }
    }
  
    /* ************************************************************************* */

    $y-=$incremento_y;
    $pdf->addText(puntos_cm($x_label),puntos_cm($y),$size_label,'<b>Email:</b>'); 
    $pdf->addText(puntos_cm($x_campo),puntos_cm($y),$size_campo,$email); 
    $pdf->addText(puntos_cm($x_label+8.5),puntos_cm($y),$size_label,'<b>Tél.:</b>'); 
    $pdf->addText(puntos_cm($x_label+9.5),puntos_cm($y),$size_campo,$telefono);   
    $pdf->addText(puntos_cm($x_label+13),puntos_cm($y),$size_label,'<b>Período:</b>'); 
    
    $pdf->addText(puntos_cm($x_label+14.5),puntos_cm($y),$size_campo,$periodo); 
    $y-=$incremento_y;
    $pdf->addText(puntos_cm($x_label),puntos_cm($y),$size_label,'<b>Tipo de Beca:</b>'); 
    $pdf->addText(puntos_cm($x_campo),puntos_cm($y),$size_campo,$tipobeca); 
    $pdf->addText(puntos_cm($x_label+8.5),puntos_cm($y),$size_label,'<b>Res.:</b>'); 
    $pdf->addText(puntos_cm($x_label+9.5),puntos_cm($y),$size_campo,$resolucion); 
    $y-=$incremento_y;


    
    $pdf->addText(puntos_cm($x_label),puntos_cm($y),$size_label,'<b>Proyecto:</b>');
    //se imprime el nombre del proyecto controlando el salto de linea segun su longitud
	foreach(string_multilinea($proyecto,$size_renglon) as $renglon){
		$pdf->addText(puntos_cm($x_campo),puntos_cm($y),$size_campo, $renglon);
		$y-=$incremento_y;
	} 


    $y-=$incremento_y;
    $pdf->addText(puntos_cm($x_label),puntos_cm($y),$size_label,'<b>Lugar de Trabajo:</b>'); 
    $pdf->addText(puntos_cm($x_campo),puntos_cm($y),$size_campo,$dependencia); 
    $y-=$incremento_y;
    $pdf->addText(puntos_cm($x_label),puntos_cm($y),$size_label,'<b>Palabras Clave:</b>'); 
    $pdf->addText(puntos_cm($x_campo),puntos_cm($y),$size_campo,trim($palabraclave));
    $y-=$incremento_y * 0.8;
    $pdf->line(puntos_cm(1), puntos_cm($y), puntos_cm(20), puntos_cm($y));
    $y-=$incremento_y*0.8;
    $pdf->addText(puntos_cm($x_label),puntos_cm($y),$size_label,'<b>Resumen:</b>');
    $y-=$incremento_y * 0.2;
    //$pdf->addText(puntos_cm($x_label),puntos_cm($y),$size_campo,$resumen); 
    $pdf->y=  puntos_cm($y);
    $opciones = array('justification' => 'full');
    //addTextWrap($x,$y,$width,$size,$text,$justification='left',$angle=0,$test=0)
    $pdf->ezText($resumen, 8, $opciones);
    //seteo y
    $y=4;
    $pdf->setLineStyle(1,'square');
    $pdf->setStrokeColor(0,0,0);
    $pdf->line(puntos_cm(1),  puntos_cm($y), puntos_cm(20),  puntos_cm($y));
    $pdf->ezText("\n",1);
    $y=2.5;
    $pdf->setLineStyle(1,'square','miter',array(2,2));
    $pdf->setStrokeColor(0,0,0);
    $pdf->line(puntos_cm(1),  puntos_cm($y), puntos_cm(20),  puntos_cm($y));
    $y=2.1;
    $pdf->addText(puntos_cm(2),puntos_cm($y),$size_label,'Firmas:');  
    $pdf->addText(puntos_cm(4),puntos_cm($y),$size_label,'Becario');  
    $pdf->addText(puntos_cm(7),puntos_cm($y),$size_label,'Co-Autor');  
    $pdf->addText(puntos_cm(10),puntos_cm($y),$size_label,'Co-Autor');  
    $pdf->addText(puntos_cm(13),puntos_cm($y),$size_label,'Director de Beca'); 
    $pdf->addText(puntos_cm(17),puntos_cm($y),$size_label,'Director de Proyecto');  
    
    //si la solicitud se encuentra cerrada, se imrpime la leyenda "Versión definitiva"
    if(isset($parametros['estado']) && $parametros['estado'] == 'C'){
        $pdf->addText(puntos_cm(8),puntos_cm(0.5),$size_label,'Versión definitiva para presentar');   
    }else{
        $pdf->addText(puntos_cm(8),puntos_cm(0.5),$size_label,'Versión no válida para presentar.');   
    }
    
    
    $tmp = $pdf->ezOutput(0);
    header('Cache-Control: private');
    header('Content-type: application/pdf');
    header('Content-Length: '.strlen(ltrim($tmp)));
    header('Content-Disposition: attachment; filename="Comprobante.pdf"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
//$documento_pdf = $pdf->ezOutput();
//$fichero = fopen('prueba.pdf','wb');
//fwrite ($fichero, $documento_pdf);
//fclose ($fichero);

   echo ltrim($tmp);
   
?>