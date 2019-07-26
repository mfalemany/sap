<?php
class ci_evaluacion extends sap_ci
{
	private $evaluador;
	private $evaluaciones;
	function servicio__generar_comprobante_evaluaciones()
	{
		$filtro = array('nro_documento_evaluador'=>toba::usuario()->get_id());
		$evaluaciones = toba::consulta_php('co_proyectos')->get_evaluaciones_realizadas($filtro);
		$pdf = new Informe_evaluaciones_realizadas($evaluaciones);
		$pdf->mostrar();
		

	}

	function servicio__generar_certificado_eval()
	{
		$datos = array('evaluaciones'=>$this->evaluaciones, 'evaluador'=> $this->evaluador[0]);
		$pdf = new Certificado_evaluacion($datos);
		$pdf->mostrar();
	}
	
	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf()
	{
		$usuario = toba::usuario()->get_id();
		
		$this->evaluaciones = toba::consulta_php('co_proyectos')->
			get_evaluaciones_realizadas(array('nro_documento_evaluador'=>$usuario));
		$this->evaluador = toba::consulta_php('co_personas')->
			get_personas(array('nro_documento'=>$usuario));

		if (!($this->evaluador && $this->evaluaciones)) {
			$this->pantalla()->eliminar_evento('generar_certificado_eval');
			$this->pantalla()->eliminar_evento('generar_comp_evaluaciones');
		}
	}

}
?>