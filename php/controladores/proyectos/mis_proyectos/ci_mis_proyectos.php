<?php
class ci_mis_proyectos extends sap_ci
{
	protected $s__seleccion;
	protected $s__volver_a;
	protected $s__evaluaciones;
	protected $s__es_admin;

	public function conf()
	{
		$this->s__es_admin = (in_array('admin',toba::usuario()->get_perfiles_funcionales()));

		//si no es administrador, no tiene sentido que tenga un filtro
		if( ! $this->s__es_admin && $this->pantalla()->existe_dependencia('filtro_proyectos')){
		
			$this->pantalla('pant_seleccion_proyecto')->eliminar_dep('filtro_proyectos');
		}
	}
	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__pant_seleccion_instancia(toba_ei_pantalla $pantalla)
	{
		$this->s__volver_a = 'pant_seleccion_proyecto';
	}

	function conf__pant_informe_evaluacion(toba_ei_pantalla $pantalla)
	{
		$this->s__volver_a = 'pant_seleccion_instancia';
		if(!$this->s__evaluaciones){
			$this->set_pantalla($this->s__volver_a);
			return;
		}
		$archivo = __DIR__.'/template_proyectos.php';
		//echo $this->s__evaluaciones['ruta_template'];
		$template = $this->armar_template_con_logica($archivo, $this->s__evaluaciones);
		$this->pantalla()->set_template($template);
	}
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function evt__nuevo_proyecto()
	{
		$this->dep('ci_detalles_proyecto')->dep('datos')->resetear();
		$this->set_pantalla('pant_detalles_proyecto');
	}

	function evt__volver()
	{
		$this->set_pantalla($this->s__volver_a);
	}

	//-----------------------------------------------------------------------------------
	//---- cu_proyectos -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cu_proyectos(sap_ei_cuadro $cuadro)
	{
		//si es admin, puede ver todos los proyectos (sin filtro). Si no lo es, solo ve los suyos
		$filtro = ($this->s__es_admin) ? array() : array('dirigido_por'=>toba::usuario()->get_id());

		//=====================REVISAR ESTE FILTRO=====================
		$filtro = array('dirigido_por'=>toba::usuario()->get_id());
		// ============================================================

		$cuadro->set_datos(
			toba::consulta_php('co_proyectos')->get_proyectos($filtro)
		);
		
	}

	function evt__cu_proyectos__seleccion($seleccion)
	{
		$this->s__seleccion = $seleccion;
		$this->set_pantalla('pant_seleccion_instancia');
	}


	function evt__cu_proyectos__editar($seleccion)
	{
		$this->dep('ci_detalles_proyecto')->get_datos()->cargar($seleccion);
		$this->set_pantalla('pant_detalles_proyecto');
	}

	//-----------------------------------------------------------------------------------
	//---- cu_instancias ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cu_instancias(sap_ei_cuadro $cuadro)
	{
		$r = toba::consulta_php('co_proyectos')->presentaciones_evaluacion_proyecto($this->s__seleccion['id']);
		//ei_arbol($r);
		$cuadro->set_datos($r);
	}

	function evt__cu_instancias__seleccion($seleccion)
	{
		$evaluaciones = toba::consulta_php('co_proyectos')->get_detalle_evaluaciones_realizadas($seleccion);

		if($evaluaciones){
			$this->s__evaluaciones = $evaluaciones;
			$this->set_pantalla('pant_informe_evaluacion');
		}else{
			toba::notificacion()->agregar('No se registraron evaluaciones para la instancia seleccionada.','info');
		}
	}



	

	

}
?>