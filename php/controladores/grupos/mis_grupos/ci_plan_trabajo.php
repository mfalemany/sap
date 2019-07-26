<?php
class ci_plan_trabajo extends sap_ci
{
	protected $s__convocatoria;
	
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__guardar()
	{
		$this->get_datos()->sincronizar();
		$this->get_datos()->resetear();
		$this->controlador()->set_pantalla('pant_seleccion');
	}

	function evt__cancelar()
	{
		$this->get_datos()->resetear();
		$this->controlador()->set_pantalla('pant_seleccion');
	}

	function evt__cerrar()
	{
		if( ! $this->validar_plan_trabajo()){
			throw new toba_error('Debe cargar al menos uno de los campos con su plan de trabajo','warning');
		}
		//Cuando se cierra la presentación, se asigna fecha de inscripción (si no tenía). Esto sucede solo una vez, cuando el grupo se inscribe por primera vez (presenta su primer plan de trabajo)
		$grupo = $this->get_datos('grupo')->get();
		if( ! $grupo['fecha_inscripcion']){
			$this->get_datos('grupo')->set(array('fecha_inscripcion'=>date('Y-m-d')));	
		}
		$this->get_datos('grupo_informe')->set(array('fecha_presentacion'=>date('Y-m-d'),'estado'=>'C'));
		$this->get_datos()->sincronizar();
		toba::notificacion()->agregar('Se ha cerrado con éxito la presentación','info');
		$this->controlador()->set_pantalla('pant_seleccion');
	}

	//-----------------------------------------------------------------------------------
	//---- form_plan_trabajo ------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_plan_trabajo(sap_ei_formulario $form)
	{
		$datos = $this->get_datos('grupo_informe')->get();
		
		if($datos){
			$form->set_datos($datos);
		}	
	}

	function evt__form_plan_trabajo__modificacion($datos)
	{
		$this->get_datos('grupo_informe')->set($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- Auxiliares -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------	
	function get_datos($tabla = NULL)
	{
		return ($tabla) ? $this->controlador()->get_datos($tabla) : $this->controlador()->get_datos();

		
	}

	function validar_plan_trabajo()
	{
		$datos = $this->get_datos('grupo_informe')->get();
		foreach ($this->dep('form_plan_trabajo')->get_nombres_ef() as $campo) {
			if(isset($datos[$campo]) && $datos[$campo]){
				return TRUE;
			}
		}
		return FALSE;
	}

}
?>