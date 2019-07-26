<?php
class ci_programas extends sap_ci
{
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__agregar()
	{
		$this->set_pantalla('pant_edicion');
	}

	function evt__cancelar()
	{
		$this->get_datos()->resetear();
		$this->set_pantalla('pant_seleccion');
	}

	function evt__eliminar()
	{
		$this->get_datos()->eliminar();
		$this->get_datos()->resetear();
		$this->set_pantalla('pant_seleccion');
	}

	function evt__guardar()
	{
		$this->get_datos()->sincronizar();
		$this->get_datos()->resetear();
		$this->set_pantalla('pant_seleccion');
	}

	//-----------------------------------------------------------------------------------
	//---- cu_programas -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cu_programas(sap_ei_cuadro $cuadro)
	{
		$cuadro->set_datos(toba::consulta_php('co_programas')->get_programas());
	}

	function evt__cu_programas__seleccion($seleccion)
	{
		$this->get_datos()->cargar($seleccion);
		$this->set_pantalla('pant_edicion');
	}

	//-----------------------------------------------------------------------------------
	//---- form_programa ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_programa(sap_ei_formulario $form)
	{
		if($this->get_datos()->esta_cargada()){
			$form->set_solo_lectura(array('codigo'));
			$form->set_datos($this->get_datos('sap_programas')->get());
		}

	}

	function evt__form_programa__modificacion($datos)
	{
		if(!$datos['codigo']){
			$datos['codigo'] = $this->generar_codigo($datos['unidad_academica']);
		}
		unset($datos['unidad_academica']);
		/* =============== Procesamiento del EF tipo Upload ====================== */
		$ext = explode(".",$datos['archivo_programa']['name']);
		$ext = end($ext);
		$ruta = 'proyectos/'.$datos['codigo'].'/';
		$efs_archivos = array(array('ef'          => 'archivo_programa',
							  		'descripcion' => 'Contenido comprimido de la documentacion del programa',
							  		'nombre'      => 'Contenido.'.$ext)
							);
		toba::consulta_php('helper_archivos')->procesar_campos($efs_archivos,$datos,$ruta);
		/* =============== Procesamiento del EF tipo Upload ====================== */


		$this->get_datos('sap_programas')->set($datos);
	}

	function generar_codigo($ua)
	{
		$ultimo_id = toba::consulta_php('co_programas')->get_ultimo_id($ua);

		//si existe ultimo id, le sumo uno y lo relleno con ceros a la izquierda (siempre logitud 2)
		$id = (isset($ultimo_id['id'])) ? str_pad( ($ultimo_id['id']+1), 2,'0',STR_PAD_LEFT) : '01';
		//Código con formato [año de dos digitos][unidad_academica][letra P][dos numeros]
		//18AP01
		return substr(date('Y'),2,2).strtoupper($ua).'P'.$id;
	}


	function get_datos($tabla=NULL)
	{
		if($tabla){
			return $this->dep('datos')->tabla($tabla);
		}else{
			return $this->dep('datos');
		}
	}

	function ajax__get_ayn($nro_documento, toba_ajax_respuesta $respuesta)
	{
		if(!$nro_documento){
			return '';
		}
		$respuesta->set(toba::consulta_php('co_personas')->get_ayn($nro_documento));
	}

	//-----------------------------------------------------------------------------------
	//---- ml_proyectos -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml_proyectos(sap_ei_formulario_ml $form_ml)
	{
		if($this->get_datos()->esta_cargada()){
			$form_ml->set_datos($this->get_datos('sap_programas_proyectos')->get_filas());
		}
	}

	function evt__ml_proyectos__modificacion($datos)
	{
		$this->get_datos('sap_programas_proyectos')->procesar_filas($datos);
	}

	function get_descripcion_proyecto($id=NULL){
		return toba::consulta_php('co_proyectos')->get_proyecto($id);
	}


	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.dep('form_programa').ef('nro_documento_dir').cuando_cambia_valor(actualizar_director);
			
			function actualizar_director()
			{
				nro_documento = {$this->dep('form_programa')->objeto_js}.ef('nro_documento_dir').get_estado();
				{$this->objeto_js}.ajax('get_ayn',nro_documento, this, mostrar);
			}

			function mostrar(respuesta)
			{
				if(respuesta){
					$('#director_programa').html(respuesta);
					$('#director_programa').css({'color':'green','font-weight':'bold'});
				}else{
					$('#director_programa').html('Persona no encontrada');
					$('#director_programa').css({'color':'red','font-weight':'bold'});
				}
				
			}
		";
	}

	

}
?>