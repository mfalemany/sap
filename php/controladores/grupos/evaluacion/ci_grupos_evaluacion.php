<?php
class ci_grupos_evaluacion extends sap_ci
{
	protected $s__filtro;
	protected $s__seleccionado;
	//-----------------------------------------------------------------------------------
	//---- Pantalla de Evaluación -------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function conf__pant_evaluacion(toba_ei_pantalla $pantalla)
	{
		//Utilizo la vaiable de sesion y la elimino
		$id_grupo = $this->s__seleccionado['id_grupo'];

		$datos['grupo'] = toba::consulta_php('co_grupos')->get_detalles_grupo($id_grupo);
		$datos['integrantes'] = toba::consulta_php('co_grupos')->get_integrantes($id_grupo);
		$datos['proyectos'] = toba::consulta_php('co_grupos')->get_proyectos_grupo($id_grupo,FALSE);
		
		//Actividades del grupo
		$datos['extension'] = toba::consulta_php('co_grupos')->get_actividad_extension($id_grupo);	
		$datos['publicacion'] = toba::consulta_php('co_grupos')->get_actividad_publicacion($id_grupo);	
		$datos['transferencia'] = toba::consulta_php('co_grupos')->get_actividad_transferencia($id_grupo);	
		$datos['form_rrhh'] = toba::consulta_php('co_grupos')->get_actividad_form_rrhh($id_grupo);
		$datos['evento'] = toba::consulta_php('co_grupos')->get_actividad_evento($id_grupo);

		//Planes de trabajo
		$datos['planes_trabajo'] = toba::consulta_php('co_grupos')->get_planes_trabajo($id_grupo);

		//URL del template
		$tpl = __DIR__.'/template_evaluacion_grupos.php';
		//Armo el template con los datos y lo asigno a la pantalla
		$tpl = $this->armar_template_con_logica($tpl,$datos);
		$pantalla->set_template($tpl);


		
		/*if( ! $datos['grupo']['id_categoria']){
			$this->dep('form_eval_grupo')->set_solo_lectura();
		}*/
	}



	//-----------------------------------------------------------------------------------
	//---- cu_grupos --------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cu_grupos(sap_ei_cuadro $cuadro)
	{
		$filtro = ($this->s__filtro) ? $this->s__filtro : array();
		$grupos = toba::consulta_php('co_grupos')->get_grupos_a_evaluar($filtro);
		$cuadro->set_datos($grupos);

	}

	function evt__cu_grupos__seleccion($seleccion)
	{
		$this->s__seleccionado = $seleccion;
		//ei_arbol($seleccion); return;
		$this->get_datos('grupo')->cargar(array('id_grupo'=>$seleccion['id_grupo']));
		$this->get_datos('grupo_informe_evaluacion')->cargar($seleccion);
		$this->set_pantalla('pant_evaluacion');
	}

	//-----------------------------------------------------------------------------------
	//---- filtro_grupos ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro_grupos(sap_ei_formulario $form)
	{
		if(isset($this->s__filtro)){
			$form->set_datos($this->s__filtro);
		}
	}

	function evt__filtro_grupos__filtrar($datos)
	{
		$this->s__filtro = $datos;
	}

	function evt__filtro_grupos__cancelar()
	{
		unset($this->s__filtro);
	}

	//-----------------------------------------------------------------------------------
	//---- form_eval_grupo --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_eval_grupo(sap_ei_formulario $form)
	{
		$datos = $this->get_datos('grupo')->get();
		if($datos){
			$form->set_datos($datos);
		}
	}

	function evt__form_eval_grupo__modificacion($datos)
	{
		$this->get_datos('grupo')->set($datos);
		
	}

	//-----------------------------------------------------------------------------------
	//---- form_eval_plan_trabajo -------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_eval_plan_trabajo(sap_ei_formulario $form)
	{
		$datos = $this->get_datos('grupo_informe_evaluacion')->get();
		if($datos){
			$form->set_datos($datos);
		}
	}

	function evt__form_eval_plan_trabajo__modificacion($datos)
	{
		//aquí se asigna el id_grupo e id_convocaotoria
		$datos = array_merge($datos,$this->s__seleccionado);
		$datos['nro_documento_evaluador'] = toba::usuario()->get_id();
		$this->get_datos('grupo_informe_evaluacion')->set($datos);
		
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__guardar()
	{
		$this->get_datos('grupo')->sincronizar();
		$this->get_datos('grupo_informe_evaluacion')->sincronizar();

		$this->get_datos('grupo')->resetear();
		$this->get_datos('grupo_informe_evaluacion')->resetear();
		$this->set_pantalla('pant_seleccion');

	}

	function evt__cancelar()
	{
		$this->get_datos('grupo')->resetear();
		$this->get_datos('grupo_informe_evaluacion')->resetear();
		$this->set_pantalla('pant_seleccion');
	}

	//-----------------------------------------------------------------------------------
	//---- Auxiliares -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function get_datos($tabla = NULL)
	{
		return $this->dep($tabla);
	}
}
?>