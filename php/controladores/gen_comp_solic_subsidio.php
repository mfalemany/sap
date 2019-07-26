<?php
//require_once('../../../php/3ros/ezpdf/class.ezpdf.php');
require_once(toba_dir() . '/php/3ros/ezpdf/class.ezpdf.php');
//$parametros = toba::memoria()->get_dato('datos');
   	
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
   

  //ei_arbol(toba::memoria()->get_dato('popup')); return;
    toba::memoria()->desactivar_reciclado();
    $font='Helvetica.afm';   
    $font_b='Helvetica-Bold.afm';


   	$parametros = toba::memoria()->get_dato('datos');

    //ei_arbol($parametros); return;
    $image =  imagecreatefromjpeg(toba::proyecto()->get_path() . '/www/img/logoUNNE_completo_bn.jpg');
    //$image = imagecreatefromgif(toba::proyecto()->get_path() . '/www/img/logo.gif');
    
   
    $size_renglon = 0.7;
    
    //GENERACION DEL REPORTE
    $pdf = new Cezpdf();
    $pdf->selectFont(toba_dir() . '/php/3ros/ezpdf/fonts/Helvetica.afm');
    
    //Logo de la UNNE
    $pdf->addImage($image,50 , $pdf->y-75, 65, 75);

    $pdf->setFontFamily('init','b');
    
    //TITULO
    $y = 28;
    $pdf->addText(puntos_cm(6.3),puntos_cm($y),14,'<b>UNIVERSIDAD NACIONAL DEL NORDESTE</b>'); 
    $y -= $size_renglon;
    $pdf->addText(puntos_cm(5.7),puntos_cm($y),14,'<b>SECRETARÍA GENERAL DE CIENCIA Y TÉCNICA</b>'); 
    $y -= $size_renglon;
    $pdf->addText(puntos_cm(8.5),puntos_cm($y),12,'<b>Solicitud de Subsidio</b>'); 
   

   	
    $size_letra = 10;
	$size_renglon = 0.5;   	
   	$y=25.5;
   	//Linea
   	$pdf->setLineStyle(1,'square');
    $pdf->setStrokeColor(0,0,0);
    $pdf->line(puntos_cm(2), puntos_cm($y), puntos_cm(20), puntos_cm($y));

    $y-=$size_renglon;
    //Datos de la solicitud
   	$pdf->addText(puntos_cm(2),puntos_cm($y),$size_letra,'<b>Convocatoria:</b> '.$parametros['convocatoria']);
   	$y -= $size_renglon;
   	$pdf->addText(puntos_cm(2),puntos_cm($y),$size_letra,'<b>Tipo de Subsidio</b>: '.$parametros['tipo_subsidio']);
   	$y -= $size_renglon;
   	$pdf->addText(puntos_cm(2),puntos_cm($y),$size_letra,'<b>Facultad/Instituto</b>: '.$parametros['dependencia']);
   	$y -= $size_renglon;
   	$pdf->addText(puntos_cm(2),puntos_cm($y),$size_letra,'<b>Proyecto</b>: ');
   	
   	foreach(string_multilinea($parametros['proyecto'],80) as $linea){
   		$pdf->addText(puntos_cm(3.8),puntos_cm($y),$size_letra,$linea);
   		$y-=$size_renglon;	
   	}
   	// ==============================================================================
   	//Linea
   	$pdf->setLineStyle(1,'square');
    $pdf->setStrokeColor(0,0,0);
    $pdf->line(puntos_cm(2), puntos_cm($y), puntos_cm(20), puntos_cm($y));
    // ==============================================================================
    
     
    $y -= $size_renglon;
    $pdf->addText(puntos_cm(9.5),puntos_cm($y),$size_letra,'<b>Solicitante</b>');
    

    $y -= $size_renglon;
    $pdf->addText(puntos_cm(2),puntos_cm($y),$size_letra,'<b>Nro. Documento:</b> '.$parametros['nro_documento']);
    $y -= $size_renglon;
    $pdf->addText(puntos_cm(2),puntos_cm($y),$size_letra,'<b>Apellido y Nombre</b>: '.$parametros['apellido'].', '.$parametros['nombres']);
    $y -= $size_renglon;
    $pdf->addText(puntos_cm(2),puntos_cm($y),$size_letra,'<b>Cat. Incentivos</b>: '.$parametros['cat_incentivos']);

    $y -= $size_renglon;
    // ==============================================================================
   	//Linea
   	$pdf->setLineStyle(1,'square');
    $pdf->setStrokeColor(0,0,0);
    $pdf->line(puntos_cm(2), puntos_cm($y), puntos_cm(20), puntos_cm($y));
    // ==============================================================================

  	 $y -= $size_renglon;
  	 
  	 switch ($parametros['tipo_subsidio']) {
  	 	case 'A':
  	 		$pdf->addText(puntos_cm(9),puntos_cm($y),$size_letra,'<b>Datos del Congreso</b>');	
  	 		$y -= $size_renglon;
  			$pdf->addText(puntos_cm(2),puntos_cm($y),$size_letra,'<b>Nombre del Congreso:</b>');
  			foreach(string_multilinea($parametros['nombre_congreso'],85) as $linea){
		   		$pdf->addText(puntos_cm(6),puntos_cm($y),$size_letra,$linea);
		   		$y-=$size_renglon;	
		   	} 		
  	 		break;
  		case 'B':
  	 		$pdf->addText(puntos_cm(9.5),puntos_cm($y),$size_letra,'<b>Datos de la Estadía</b>');
  	 		$y -= $size_renglon;
  			$pdf->addText(puntos_cm(2),puntos_cm($y),$size_letra,'<b>Institución Estadía</b> '.$parametros['institucion']);
  			$y -= $size_renglon;
  	 		break;
  	 }

    $pdf->addText(puntos_cm(2),puntos_cm($y),$size_letra,'<b>Lugar:</b> '.$parametros['lugar']);
    $y -= $size_renglon;
    $pdf->addText(puntos_cm(2),puntos_cm($y),$size_letra,'<b>Fecha desde:</b> '.$parametros['fecha_desde']);
    
    $pdf->addText(puntos_cm(7),puntos_cm($y),$size_letra,'<b>Fecha hasta:</b> '.$parametros['fecha_hasta']);
    
    $y -= $size_renglon;
    
    $pdf->addText(puntos_cm(2),puntos_cm($y),$size_letra,'<b>Costo pasajes: $</b> '.$parametros['costo_pasajes']);
    
    $pdf->addText(puntos_cm(7),puntos_cm($y),$size_letra,'<b>Costo estadía: $</b> '.$parametros['costo_estadia']);
    if($parametros['tipo_subsidio'] == 'A'){
 		
		$pdf->addText(puntos_cm(12),puntos_cm($y),$size_letra,'<b>Costo Inscripción: $</b> '.$parametros['costo_inscripcion']);
	}
    

    $y -= $size_renglon+0.2;
    $pdf->addText(puntos_cm(2),puntos_cm($y),$size_letra+2,'<b>Total solicitado: $</b> ');
    $pdf->addText(puntos_cm(5.7),puntos_cm($y),$size_letra+2,'<b>'.$parametros['total_solicitado'].'</b>');
    $y -= $size_renglon+0.2;

    $size_renglon = 0.4;

    switch ($parametros['tipo_subsidio']) {
  	 	case 'A':
  	 		
  			$pdf->addText(puntos_cm(2),puntos_cm($y),$size_letra,'<b>Abstract</b>: ');
		   	$y -= $size_renglon;

       //IMPRESION DEL ABSTRACT
        $texto = $parametros['abstract'];
        while(($texto = $pdf->addTextWrap(puntos_cm(2),puntos_cm($y),480,8,$texto,'full')) && $y > 3){
          $y-=$size_renglon;
        }
        break;
      case 'B':
        $pdf->addText(puntos_cm(2),puntos_cm($y),$size_letra,'<b>Plan de trabajo</b>: ');
        $y -= $size_renglon;

       //IMPRESION DEL PLAN DE TRABAJO
        $texto = $parametros['plan_trabajo'];
        while(($texto = $pdf->addTextWrap(puntos_cm(2),puntos_cm($y),480,8,$texto,'full')) && $y > 3){
          $y-=$size_renglon;
        }
        break;

  	 }
  	 $y-=$size_renglon;
  	 $y-=$size_renglon;
  	 $y-=$size_renglon;

    $pdf->addText(puntos_cm(2),puntos_cm($y),$size_letra,'_____________________________________');
    $pdf->addText(puntos_cm(13),puntos_cm($y),$size_letra,'____________________________________');
    $y-=$size_renglon;
    $pdf->addText(puntos_cm(3),puntos_cm($y),$size_letra,'Aval Secretaría de Investigación');
    $pdf->addText(puntos_cm(15.5),puntos_cm($y),$size_letra,'Aval Decanato');

    if( ! $parametros['hash_cierre']){
      $pdf->addText(puntos_cm(5.2),puntos_cm(0.5),15,'Impresión temporal (no válida para presentar)');  
    }else{
      //hash
      $pdf->addText(puntos_cm(6.5),puntos_cm(0.5),8,'Versión definitiva. ID: '.$parametros['hash_cierre']);  
    }
    

			   	 
    $tmp = $pdf->ezOutput(0);
    header('Cache-Control: private');
    header('Content-type: application/pdf');
    header('Content-Length: '.strlen(ltrim($tmp)));
    //header('Content-Disposition: attachment; filename="Comprobante.pdf"');
    header('Pragma: no-cache');
    header('Expires: 0');

	echo ltrim($tmp);


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
   
?>