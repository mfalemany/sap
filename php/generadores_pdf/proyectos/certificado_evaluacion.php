<?php 
class Certificado_evaluacion extends FPDF
{
	function __construct($datos)
	{
		parent::__construct();
		// ei_arbol($datos);
		// return;
		extract($datos);
		
		//Formato A4 y Apaisado
		$this->AddPage('Landscape','A4');

		//Agrego la Imagen de fondo
		$path = toba::proyecto()->get_www();
        $path = $path['path']."img/";
	    $plantilla = $path."CertificadoEval2018.jpg";
	    $this->Image($plantilla,0,0,300);

		//agrego una fuente importada de Google Fonts
		$this->addFont('elegante','','JacquesFrancois-Regular.php');
		//agrego la fuente de la UNNE
		//$this->addFont('unne','','english.php');
		//agrego la fuente comÃƒÂºn
		$this->addFont('lobster','','Lobster-Regular.php');

		//Encabezado con el nombre de la universidad
		$this->SetFont('lobster','',28);
		$nombre = ucwords(strtolower($evaluador['apellido'].", ".$evaluador['nombres']));
		if (strlen($nombre) > 30) {
			$nom=explode(" ", $evaluador['nombres']);
			$nombre = ucwords(strtolower($evaluador['apellido'].", ".$nom[0]));
		}
		if (is_numeric($evaluador['nro_documento'])) {
			$dni = number_format($evaluador['nro_documento'],0,',','.');
		}
		$this->setXY(60,72);
		$this->Cell(234,10,$nombre." - DNI Nº: ".$dni,0,0,'C',false);
	
		//Detalle de la evaluaci? y disciplina
		// $tipo = '';
		// switch($datos[0]['tipo']){
		// 	case 'PI':
		// 		$tipo = 'Proyectos de Investigación';
		// 	break;
		// 	case 'PDTS':
		// 		$tipo = 'Proyectos de Desarrollo T. y S.';
		// 	break;
		// 	case 'Programa':
		// 		$tipo = 'Programas de I. y D.';
		// 	break;
		// }
		
		$this->SetFont('times','BI',20);
		$this->setXY(60,100);
		$this->MultiCell(234,9,
			"Proyectos de I+D+i o Informes de la Convocatoria 2018\nen la disciplina: \""
			.$evaluador['disciplina']."\"",0,'C',false);
		
/*
		//c?igo
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
		$this->Cell(234,10,$datos['convocatoria'],0,0,'C',false);*/

	}

	function mostrar()
	{
		$this->Output('I','certificado.pdf');
	}
}
?>