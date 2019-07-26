<?php 
class Comprobante_insc_grupos extends FPDF
{
	function __construct($datos = array())
	{
		parent::__construct();
		// ei_arbol($datos);
		// return;
		//extract($datos);
		
		//Formato A4 y Apaisado
		$this->AddPage('Portrait','A4');
		
		

		//Agrego el logo de la UNNE
		$path = toba::proyecto()->get_www();
        $path = $path['path']."img/logo_membrete.png";
	    $this->Image($path,10,5,70,25);

	    $this->SetDrawColor(180,180,180);
		$this->Line(10,30,200,30); //Linea gris

		$alto_linea = 7;
		$this->SetFont('Arial','',10);
		$this->setXY(10,33);

		//Se agrega la convocatoria (es el año de la fecha de inscripción) y la fecha de impresión (ambas en un lugar fijo)
		$this->SetTextColor(150,150,150);
		$conv = new DateTime($datos['fecha_inscripcion']);
		$this->Text(170,29,"Convocatoria ".$conv->format('Y'));
		$this->Text(125,287,'Fecha de impresión: '.date('d/m/Y')." - ".date('H:i'));

		$this->SetTextColor(0,0,0);
		$this->MultiCell(0,$alto_linea,'Carta de Presentación - Grupos de Investigación de la Universidad Nacional del Nordeste',0,'C');
		
		//DENOMINACIÓN DEL GRUPO
		$this->saltar(3);

		$this->SetFont('Arial','BU',14);
		$this->MultiCell(0,$alto_linea,$datos['denominacion'],0,'C');
		//DESCRIPCIÓN (SI LA TIENE)
		$this->SetTextColor(100,100,100);
		$this->SetFont('Arial','',10);
		if(isset($datos['descripcion']) && strlen(trim($datos['descripcion']))){
			$this->saltar(1);
			$this->MultiCell(0,$alto_linea-2,$datos['descripcion'],0,'J');	
		}

		
		//Vuelvo el texto a negro
		$this->SetTextColor(0,0,0);
		$alto_linea -= 3;
		$this->setY($this->getY()+6);

		//COORDINADOR DEL GRUPO
		$this->SetFont('Arial','B',10);
		$this->SetFillColor(50,50,75);
		$this->SetTextColor(255,255,255);
		$this->Cell(0,$alto_linea+2,"DETALLES DEL GRUPO",1,1,'C',TRUE);
		

		$y_inicio = $this->getY();
		$y_fin = 20;

		//Vuelvo a los valores por defecto
		$this->SetFillColor(255,255,255);
		$this->SetTextColor(0,0,0);

		
		//COORDINADOR DEL GRUPO
		$this->saltar(2);
		$this->SetFont('Arial','BU',10);
		$this->Cell(45,$alto_linea,"Coordinaror del grupo:",0,0,'R');	
		$this->SetFont('Arial','',10);
		$this->Cell(0,$alto_linea,$datos['coordinador']." (DNI: ".$datos['nro_documento_coordinador'].")",0,1,'L');

		//FACULTAD O INSTITUTO
		$this->saltar(2);
		$this->SetFont('Arial','BU',10);
		$this->Cell(45,$alto_linea,"Facultad/Instituto:",0,0,'R');	
		$this->SetFont('Arial','',10);
		$this->Cell(0,$alto_linea,$datos['dependencia'],0,1,'L');

		//ÁREA DE CONOCIMIENTO
		$this->saltar(2);
		$this->SetFont('Arial','BU',10);
		$this->Cell(45,$alto_linea,"Área de Conocimiento:",0,0,'R');	
		$this->SetFont('Arial','',10);
		$this->Cell(0,$alto_linea,$datos['area_conocimiento'],0,1,'L');

		//LÍNEAS INVESTIGACIÓN
		$this->saltar(2);
		$this->SetFont('Arial','BU',10);
		$this->Cell(45,$alto_linea,"Líneas de Investigación:",0,0,'R');	
		$this->SetFont('Arial','',10);
		$this->MultiCell(0,$alto_linea,implode(' | ',$datos['lineas_investigacion']),0,'L');

		//INICIO DE ACTIVIDADES
		$this->saltar(2);
		$this->SetFont('Arial','BU',10);
		$this->Cell(45,$alto_linea,"Inicio de Actividades:",0,0,'R');	
		$this->SetFont('Arial','',10);
		$fecha = new DateTime($datos['fecha_inicio']);
		$this->Cell(0,$alto_linea,date_format($fecha,'d/m/Y'),0,1,'L');

		//INTEGRANTES
		$this->saltar(2);
		$this->SetFont('Arial','BU',10);
		$this->Cell(45,$alto_linea,"Integrantes:",0,1,'R');
		$this->SetFont('Arial','',10);
		$alto_linea += 1;

		foreach ($datos['integrantes'] as $integrante) {
			$this->setX($this->getX()+45);
			$texto = "- ".$integrante['integrante']." (DNI: ".$integrante['nro_documento'].").";
			$this->Cell(strlen($texto)*1.7,$alto_linea,$texto,0,0,'L');
			$this->SetFont('Arial','B',10);
			$this->Cell(10,$alto_linea," Rol: ",0,0,'L');
			$this->SetFont('Arial','',10);
			$this->Cell(0,$alto_linea,$integrante['rol'],0,1,'L');
		}
		//Rectangulo que encierra toda la info del grupo 
		
		$this->Rect($this->getX(),$y_inicio,190, ($this->getY()-$y_inicio+10) );

		//FIRMAS
		$this->setY($this->getY()+40);
		$this->Line(10,$this->getY(),60,$this->getY()); //Linea gris
		$this->Line(150,$this->getY(),200,$this->getY()); //Linea gris
		$this->setY($this->getY()+2);
		$this->Cell(140,7,'Aval Secretaría Investigación',0,0,'L');
		$this->Cell(30,7,'Aval Decanato o Responsable',0,0,'L');
		
	}

	function saltar($lineas)
	{
		$this->y += $lineas; 
	}

	function mostrar()
	{
		$this->Output('I','Carta Presentación.pdf');
	}
}

?>