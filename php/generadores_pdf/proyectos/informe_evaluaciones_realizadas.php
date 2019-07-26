<?php 
class Informe_evaluaciones_realizadas extends FPDF
{
	protected $nombre_archivo;
	protected $path;
	protected $x;
	protected $y;

	function __construct($datos)
	{
		parent::__construct();
		//Formato A4 y Vertical
		$this->AddPage('Portrait','A4');
		$this->SetAutoPageBreak(true,30);

		$ancho_linea = 5;

		//ENCABEZADOS DE LA TABLA
		$this->SetFillColor(200,200,200);
		$this->SetFont('arial','B',7);
		$this->setXY($this->x,$this->y);
		$this->Cell(20,$ancho_linea,'CÓDIGO',1,0,'C',true);
		$this->Cell(30,$ancho_linea,'TIPO',1,0,'C',true);
		$this->Cell(100,$ancho_linea,'DESCRIPCIÓN',1,0,'C',true);
		$this->Cell(20,$ancho_linea,'EVALUACIÓN',1,0,'C',true);
		$this->Cell(20,$ancho_linea,'FECHA',1,1,'C',true);
		//$this->y += $ancho_linea;

		
		//DETALLES DE LA TABLA
		$this->SetFont('arial','',7);
		foreach ($datos as $eval) {
			$this->setX($this->x);
			$this->Cell(20,$ancho_linea,$eval['codigo'],1,0,'C',false);
			$this->Cell(30,$ancho_linea,$eval['tipo'],1,0,'C',false);
			$this->Cell(100,$ancho_linea,ucfirst(strtolower($eval['descripcion'])),1,0,'L',false);
			$this->Cell(20,$ancho_linea,$eval['evaluacion'],1,0,'C',false);
			$fecha_formato = new Datetime($eval['fecha_eval']);
			$fecha_formato = $fecha_formato->format('d/m/Y');
			$this->Cell(20,$ancho_linea,$fecha_formato,1,1,'C',false);
			//$this->y += $ancho_linea;
		}
		
	}

	function Header()
	{
		$path = toba::proyecto()->get_www();
        $path = $path['path']."img/logo_membrete.png";
		$this->Image($path,19,13,65,25);
		$this->SetFont('arial','B',14);
		$this->setXY(10,40);
		$this->Cell(190,10,'INFORME DE EVALUACIONES REALIZADAS',1,0,'C',false);	
		$this->SetFont('arial','',7);
		$this->x = 10;
		$this->y = 54;
		
	}	

	function Footer()
	{
		$this->SetXY(120,-20);
		$this->Cell(70,5,'_________________________________________',0,0,'C',false);
		$this->SetXY(84,-10);
		$this->Cell(40,5,'Página '.$this->PageNo(),0,0,'C',false);
	}
	function mostrar()
	{
		$this->Output('I','Evaluaciones.pdf');
	}
}
?>