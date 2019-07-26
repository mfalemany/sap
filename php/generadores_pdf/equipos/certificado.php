<?php 
class Certificado_equipos extends FPDF
{
	function __construct($datos)
	{
		parent::__construct();
		//Formato A4 y Apaisado
		$this->AddPage('Landscape','A4');

		//Agrego la Imagen de fondo
		$path = toba::proyecto()->get_www();
        $path = $path['path']."img/plantillas_certificados/";
	    $plantilla = $path.$datos['id_convocatoria'].".png";
	    $this->Image($plantilla,0,5,300);

		//agrego una fuente importada de Google Fonts
		$this->addFont('elegante','','IMFeDPit28P.php');
		//agrego la fuente de la UNNE
		$this->addFont('unne','','english.php');

		//Encabezado con el nombre de la universidad
		$this->SetFont('unne','',32);
		
		$this->setXY(54,28);
		$this->MultiCell(234,5,'Universidad Nacional del Nordeste',0,'C',false);
		
		//Nombre del grupo
		$this->SetFont('times','BI',20);
		$this->setXY(54,71);
		$this->MultiCell(234,5,ucfirst(str_replace(array('Á','É','Í','Ó','Ú'),array('á','é','í','ó','ú'),$datos['equipo'])),0,'C',false);
		

		//código
		$this->SetFont('times','IB',16);
		$this->setXY(53,89);
		$this->Cell(234,10,$datos['codigo'],0,0,'C',false);

		//Nombre de la Persona
		$this->SetFont('elegante','',18);
		$this->setXY(54,99);
		$this->Cell(234,10,ucwords(strtolower($datos['director'])),0,0,'C',false);

		//Nombre de la Convocatoria
		$this->SetFont('arial','B',14);
		$this->setXY(54,120);
		$this->Cell(234,10,$datos['convocatoria'],0,0,'C',false);
	}

	function mostrar()
	{
		$this->Output('I','certificado.pdf');
	}
}
?>