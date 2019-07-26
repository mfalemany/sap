<?php
class ci_reporte_grupos extends sap_ci
{
	protected $s__filtro;
	protected $s__seleccion;
	//-----------------------------------------------------------------------------------
	//---- Eventos del CI ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function evt__volver()
	{
		unset($this->s__seleccion);
		$this->set_pantalla('pant_seleccion');
	}
	//-----------------------------------------------------------------------------------
	//---- filtro_grupos ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro_grupos(sap_ei_formulario $form)
	{
		//ei_arbol($this->s__filtro);
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
	//---- cu_grupos --------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function conf__cu_grupos(sap_ei_cuadro $cuadro)
	{
		$filtro = (isset($this->s__filtro)) ? $this->s__filtro : array();
		$cuadro->set_datos(toba::consulta_php('co_grupos')->get_grupos($filtro));
	}
	function evt__cu_grupos__seleccion($seleccion)
	{
		$this->s__seleccion = $seleccion;
		$this->set_pantalla('pant_reporte');
	}

	//-----------------------------------------------------------------------------------
	//---- configuracion de formularios -------------------------------------------------
	//-----------------------------------------------------------------------------------
	function conf__form_grupo(sap_ei_formulario $form)
	{
		if(!isset($this->s__seleccion)){
			$this->set_pantalla('pant_seleccion');
		}
		$datos = toba::consulta_php('co_grupos')->get_grupos($this->s__seleccion);
		$form->set_datos($datos[0]);
	}
	function conf__cu_integrantes(sap_ei_cuadro $cuadro)
	{
		if(!isset($this->s__seleccion)){
			$this->set_pantalla('pant_seleccion');
		}
		$datos = toba::consulta_php('co_grupos')->get_integrantes($this->s__seleccion['id_grupo']);
		$cuadro->set_datos($datos);
	}

	function conf__cu_lineas_investigacion(sap_ei_cuadro $cuadro)
	{
		if(!isset($this->s__seleccion)){
			$this->set_pantalla('pant_seleccion');
		}
		$datos = toba::consulta_php('co_grupos')->get_lineas_investigacion($this->s__seleccion['id_grupo']);
		
		//ajusta el resultado de la consulta php al formato de un form_ml
		foreach($datos as $indice => $linea){
			$datos[$indice] = array('linea_investigacion'=>$linea);
		}

		$cuadro->set_datos($datos);
	}

	function conf__cu_proyectos(sap_ei_cuadro $cuadro)
	{
		if(!isset($this->s__seleccion)){
			$this->set_pantalla('pant_seleccion');
		}
		$datos = toba::consulta_php('co_grupos')->get_proyectos_grupo($this->s__seleccion['id_grupo']);
		$cuadro->set_datos($datos);
	}
	function conf__cu_actividades_extension(sap_ei_cuadro $cuadro)
	{
		if(!isset($this->s__seleccion)){
			$this->set_pantalla('pant_seleccion');
		}
		$datos = toba::consulta_php('co_grupos')->get_actividad_extension($this->s__seleccion['id_grupo']);
		$cuadro->set_datos($datos);
	}
	function conf__cu_publicaciones(sap_ei_cuadro $cuadro)
	{
		if(!isset($this->s__seleccion)){
			$this->set_pantalla('pant_seleccion');
		}
		$datos = toba::consulta_php('co_grupos')->get_actividad_publicacion($this->s__seleccion['id_grupo']);
		$cuadro->set_datos($datos);
	}
	function conf__cu_actividades_transferencia(sap_ei_cuadro $cuadro)
	{
		if(!isset($this->s__seleccion)){
			$this->set_pantalla('pant_seleccion');
		}
		$datos = toba::consulta_php('co_grupos')->get_actividad_transferencia($this->s__seleccion['id_grupo']);
		$cuadro->set_datos($datos);
	}

	function conf__cu_form_rrhh(sap_ei_cuadro $cuadro)
	{
		if(!isset($this->s__seleccion)){
			$this->set_pantalla('pant_seleccion');
		}
		$datos = toba::consulta_php('co_grupos')->get_actividad_form_rrhh($this->s__seleccion['id_grupo']);
		$cuadro->set_datos($datos);
	}

	function conf__cu_eventos(sap_ei_cuadro $cuadro)
	{
		if(!isset($this->s__seleccion)){
			$this->set_pantalla('pant_seleccion');
		}
		$datos = toba::consulta_php('co_grupos')->get_actividad_evento($this->s__seleccion['id_grupo']);
		$cuadro->set_datos($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- auxiliares -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function get_ayn($nro_documento)
	{
		return toba::consulta_php('co_personas')->get_ayn($nro_documento);
	}

	function get_descripcion_proyecto($id)
	{
		return toba::consulta_php('co_proyectos')->get_descripcion($id);

	}

}
?>