<?php
class ci_edicion_grupos extends sap_ci
{
	//-----------------------------------------------------------------------------------
	//---- form_grupo -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_grupo(sap_ei_formulario $form)
	{
		$datos = $this->get_datos('grupo')->get();
		if($datos){
			$form->set_datos($datos);
		}
	}

	function evt__form_grupo__modificacion($datos)
	{
		$this->get_datos('grupo')->set($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- ml_integrantes ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml_integrantes(sap_ei_formulario_ml $form_ml)
	{
		//Obtengo los integrantes del grupo
		$datos = $this->get_datos('grupo_integrante')->get_filas();

		if( ! $this->incluye_el_director($datos) ){
			$nuevo = array('nro_documento'=> toba::usuario()->get_id(),
								'fecha_inicio' => date('Y-m-d'),
								'fecha_fin'    => NULL,
								'id_rol'       => NULL
							);
			//Si el grupo ya está creado
			if($this->get_datos('grupo')->esta_cargada()){
				$this->get_datos('grupo_integrante')->nueva_fila($nuevo);
			}else{
				$form_ml->agregar_registro($nuevo);
			}
		}

		
		$datos = $this->get_datos('grupo_integrante')->get_filas();
		if($datos){
			$form_ml->set_datos($datos);
		}
	}

	function evt__ml_integrantes__modificacion($datos)
	{
		$this->get_datos('grupo_integrante')->procesar_filas($datos);
	}

	private function incluye_el_director($integrantes)
	{	
		foreach($integrantes as $integrante){
			if(trim($integrante['nro_documento']) == toba::usuario()->get_id()){
				return TRUE;
			}
		}
		return FALSE;
	}

	//-----------------------------------------------------------------------------------
	//---- ml_proyectos -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml_proyectos(sap_ei_formulario_ml $form_ml)
	{
		$form_ml->set_titulo('Proyectos de Investigación financiados y gestionados por la SGCyT');
		$datos = $this->get_datos('grupo_proyecto')->get_filas();
		if($datos){
			$form_ml->set_datos($datos);
		}
	}

	function evt__ml_proyectos__modificacion($datos)
	{
		$this->get_datos('grupo_proyecto')->procesar_filas($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- ml_proyectos_externos --------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml_proyectos_externos(sap_ei_formulario_ml $form_ml)
	{
		$datos = $this->get_datos('grupo_proyecto_externo')->get_filas();
		if($datos){
			$form_ml->set_datos($datos);
		}
	}

	function evt__ml_proyectos_externos__modificacion($datos)
	{
		$this->get_datos('grupo_proyecto_externo')->procesar_filas($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- ml_lineas_investigacion ------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml_lineas_investigacion(sap_ei_formulario_ml $form_ml)
	{
		$datos = $this->get_datos('grupo_linea_investigacion')->get_filas();
		if($datos){
			$form_ml->set_datos($datos);
		}

	}

	function evt__ml_lineas_investigacion__modificacion($datos)
	{
		$this->get_datos('grupo_linea_investigacion')->procesar_filas($datos);
	}	


	//-----------------------------------------------------------------------------------
	//---- form_evento ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_evento(sap_ei_formulario_ml $form_ml)
	{
		$filas = $this->get_datos('grupo_evento')->get_filas();
		if($filas){
			$form_ml->set_datos($filas);
		}
	}

	function evt__form_evento__modificacion($datos)
	{
		$this->get_datos('grupo_evento')->procesar_filas($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- form_extension ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_extension(sap_ei_formulario_ml $form_ml)
	{
		$filas = $this->get_datos('grupo_extension')->get_filas();
		if($filas){
			$form_ml->set_datos($filas);
		}
	}

	function evt__form_extension__modificacion($datos)
	{
		$this->get_datos('grupo_extension')->procesar_filas($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- form_publicacion -------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_publicacion(sap_ei_formulario_ml $form_ml)
	{
		$filas = $this->get_datos('grupo_publicacion')->get_filas();
		if($filas){
			$form_ml->set_datos($filas);
		}
	}

	function evt__form_publicacion__modificacion($datos)
	{
		$this->get_datos('grupo_publicacion')->procesar_filas($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- form_rrhh --------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_rrhh(sap_ei_formulario_ml $form_ml)
	{
		$filas = $this->get_datos('grupo_form_rrhh')->get_filas();
		if($filas){
			$form_ml->set_datos($filas);
		}
	}

	function evt__form_rrhh__modificacion($datos)
	{
		$this->get_datos('grupo_form_rrhh')->procesar_filas($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- form_transferencia -----------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_transferencia(sap_ei_formulario_ml $form_ml)
	{
		$filas = $this->get_datos('grupo_transferencia')->get_filas();
		if($filas){
			$form_ml->set_datos($filas);
		}
	}

	function evt__form_transferencia__modificacion($datos)
	{
		$this->get_datos('grupo_transferencia')->procesar_filas($datos);
	}
	//-----------------------------------------------------------------------------------
	//---- Funciones auxiliares ---------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function get_ayn($nro_documento)
	{
		return toba::consulta_php('co_personas')->get_ayn($nro_documento);
	}

	/**
	 * Devuelve la descripcion de un proyecto, determinado por su ID (Carga de descripción del ef_popup)
	 * @param  integer $id_proyecto ID del proyecto a buscar
	 * @return string              Descripcion del proyecto buscado
	 */
	function get_descripcion_proyecto($id_proyecto)
	{
		return toba::consulta_php('co_proyectos')->get_descripcion($id_proyecto);
	}

	function get_datos($tabla = NULL)
	{
		return $this->controlador()->get_datos($tabla);
	}

	function extender_objeto_js()
	{
		echo "
		setTimeout(function(){
			window.focus();
			}, 2000)
		/* ============== LLAMADA DE ATENCIÓN POR ESTADO DE SESIÓN ====================== */
		//Cada 24 minutos: 24 minutos por 60 segundos por 1000 (porque es en milesimas de segundo)
		setInterval(function(){
			alert('Su sesión está por finalizar. Por favor, asegurese de guardar parcialmente los cambios realizados (para no perderlos) y poder continuar');
		}, (24*60*1000) );
		";
	}

	

}
?>

