<?php
class sap_ci extends toba_ci
{
	private $dias = array(0=>'domingo',1=>'lunes',2=>'martes',3=>'miércoles',4=>'jueves',5=>'viernes',6=>'sábado');
	private $meses = array(1=>'enero',2=>'febrero',3=>'marzo',4=>'abril',5=>'mayo',6=>'junio',7=>'julio',8=>'agosto',9=>'septiembre',10=>'octubre',11=>'noviembre',12=>'diciembre');
	/**
	 * =============================================================================
	 * ESTE MÉTODO HAY QUE ELIMINAR Y DEJAR "ARMAR TEMPLATE CON LOGIA" 
	 * =============================================================================
	 */
	function armar_template($param)
	{
		//ei_arbol($param);
		$template = file_get_contents($param['ruta_template']);
		$this->recorrer_datos($template, $param);
		return $template;
	}

	private function recorrer_datos(&$template, $datos)
	{
		foreach($datos as $clave => $valor){
			if(is_array($valor)){
				$this->recorrer_datos($template,$valor);
			}else{
				$template = str_replace('{{'.strtoupper($clave).'}}',$valor,$template);	
			}
		}
	}
	/**
	 * =============================================================================
	 * ELIMINAR HASTA ACA 
	 * =============================================================================
	 */


	//ESTA ES LA FUNCIÓN QUE TIENE QUE QUEDAR
	function armar_template_con_logica($archivo,$datos)
	{	
		ob_start();
		include $archivo;
		return ob_get_clean();
	}

	protected function get_dia($dia)
	{
		return $this->dias[$dia];
	}
	protected function get_mes($mes)
	{
		return $this->meses[$mes];
	}
	protected function get_fecha_texto($time)
	{
		$dia = $this->get_dia(date('N',$time));
		$mes = $this->get_mes(date('n',$time));
		return $dia.' '.date('d').' de '.$mes.' de '.date('Y');
	}

	protected function fecha_dmy($fecha_ymd)
	{
		$fecha = new Datetime($fecha_ymd);
		return $fecha->format('d-m-Y');
	}
}
?>