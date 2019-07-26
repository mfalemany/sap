<?php 
class Certificado_comunicaciones extends FPDF
{
	protected $nombre_archivo;

	function __construct($datos,$plantilla)
	{
		parent::__construct();
		//Formato A4 y Apaisado
		$this->AddPage('Landscape','A4');

		//Agrego la Imagen de fondo
		$path = toba::proyecto()->get_www();
        $path = $path['path']."img/plantillas_certificados/";
	    $plantilla = $path.$plantilla.".png";
	    $this->Image($plantilla,0,5,300);

		//agrego una fuente importada de Google Fonts
		$this->addFont('elegante','','IMFeDPit28P.php');
		//agrego la fuente de la UNNE
		$this->addFont('unne','','english.php');

		//Encabezado con el nombre de la universidad
		$this->SetFont('unne','',32);
		$this->Line(55,24,287,24);
		$this->setXY(54,30); //Linea
		$this->MultiCell(234,5,'Universidad Nacional del Nordeste',0,'C',false);
		$this->Line(55,41,287,41); //Linea

		//Tíulo del trabajo
		$this->SetFont('times','BI',13);
		$this->setXY(54,65);
		$this->MultiCell(234,5,ucfirst(str_replace(array('Á','É','Í','Ó','Ú'),array('á','é','í','ó','ú'),$datos['titulo'])),0,'C',false);

		//Nombre de la Persona
		$this->SetFont('elegante','',24);
		$this->setXY(54,88);
		$this->Cell(234,10,ucwords(strtolower($datos['autor'])),0,0,'C',false);
		$this->nombre_archivo = $datos['autor'];


		//Nombre de la Convocatoria
		$this->SetFont('arial','B',14);
		$this->setXY(54,108);
		$this->Cell(234,10,$datos['nombre_convocatoria'],0,0,'C',false);
	}

	function mostrar()
	{
		$this->Output('I',$this->nombre_archivo.'.pdf');
	}
}
?>