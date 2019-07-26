<?php 
class Certificado_evaluador extends FPDF
{
	function __construct()
	{
		parent::__construct();
		//Formato A4 y Apaisado
		$this->AddPage('Landscape','A4');

		//Agrego la Imagen de fondo
		$path = toba::proyecto()->get_www();
        $path = $path['path']."img/";
	    $plantilla = $path."8.png";
	    $this->Image($plantilla,0,5,300);
	}    

	function mostrar()
	{
		$this->Output('I','certificado.pdf');
	}
}
?>