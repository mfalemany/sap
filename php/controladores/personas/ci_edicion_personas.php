<?php
class ci_edicion_personas extends sap_ci
{
	//-----------------------------------------------------------------------------------
	//---- form_personas ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function conf__form_persona(sap_ei_formulario $form)
	{
		
		if($this->get_datos('personas','personas')->get()){
			$form->set_datos($this->get_datos('personas','personas')->get());
			$form->set_solo_lectura(array('nro_documento'));
		}else{
			$this->controlador()->pantalla()->eliminar_evento('eliminar');
		}

	}

	function evt__form_persona__modificacion($datos)
	{
		if($datos['archivo_cvar']){
			$carpeta = 'docum_personal/'.$datos['nro_documento']."/";
			toba::consulta_php('helper_archivos')->subir_archivo($datos['archivo_cvar'],$carpeta,'cvar.pdf');
			$datos['archivo_cvar'] = 'cvar.pdf';	
		}else{
			unset($datos['archivo_cvar']);
		}
		$this->get_datos('personas','personas')->set($datos);
		
	}
	//-----------------------------------------------------------------------------------
	//---- ml_cargos --------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml_cargos(sap_ei_formulario_ml $form_ml)
	{
		if($this->get_datos('personas','cargos')->get_filas()){
			$form_ml->set_datos($this->get_datos('personas','cargos')->get_filas());	
		}
	}

	function evt__ml_cargos__modificacion($datos)
	{
		foreach($datos as $indice => $valor){
			switch (substr($datos[$indice]['cargo'],3,1)) {
				case 'E':
					$datos[$indice]['dedicacion'] = 'EXCL';
					break;
				case 'S':
					$datos[$indice]['dedicacion'] = 'SEMI';
					break;
				case '1':
					$datos[$indice]['dedicacion'] = 'SIMP';
					break;
			}
		}
		$this->get_datos('personas','cargos')->procesar_filas($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- ml_cat_incentivos ------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml_cat_incentivos(sap_ei_formulario_ml $form_ml)
	{
		if($this->get_datos('personas','cat_incentivos')->get_filas()){
			$form_ml->set_datos($this->get_datos('personas','cat_incentivos')->get_filas());
		}
	}

	function evt__ml_cat_incentivos__modificacion($datos)
	{
		$this->get_datos('personas','cat_incentivos')->procesar_filas($datos);

	}

	function conf__form_cat_conicet(sap_ei_formulario $form)
	{
		if ($this->get_datos('personas','cat_conicet_persona')->get()) {
			$form->set_datos($this->get_datos('personas','cat_conicet_persona')->get());
		} 
	}

	function evt__form_cat_conicet__modificacion($datos)
	{
		if($datos['id_cat_conicet'] && $datos['lugar_trabajo']){
			$this->get_datos('personas','cat_conicet_persona')->set($datos);
		}else{
			if($datos['id_cat_conicet'] || $datos['lugar_trabajo']){
				throw new toba_error("Si carga una categoría CONICET, debe cargar ambos campos: categoría y lugar de trabajo.");
			}
		}
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro subsidios -------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function conf__cuadro_subsidios(sap_ei_cuadro $cuadro)
	{
		if($this->get_datos('personas','personas')->get()){
			$persona = $this->get_datos('personas','personas')->get();
			$datos = toba::consulta_php('co_subsidios')->get_historial_solicitudes($persona['nro_documento']);
			
			if(count($datos)){
				foreach($datos as $indice => $solicitud){
					$datos[$indice]['otorgado'] = ($solicitud['monto_otorgado']) ? 'SI' : 'NO';	
				}
				
			}
			
			$cuadro->set_datos($datos);
		}
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro participacion UNNE Investiga ------------------------------------------
	//-----------------------------------------------------------------------------------
	function conf__cu_unne_investiga(sap_ei_cuadro $cuadro)
	{
		if($this->get_datos('personas','personas')->get()){
			$persona = $this->get_datos('personas','personas')->get();
			$datos = toba::consulta_php('co_proyectos')->get_participaciones_equipos($persona['nro_documento']);
			$cuadro->set_datos($datos);
		}
	}

	//-----------------------------------------------------------------------------------
	//---- Datos ------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function get_datos($relacion='personas',$tabla=NULL)
	{
		return ($tabla) ? $this->controlador()->dep($relacion)->tabla($tabla) : $this->controlador()->dep($relacion);
	}
	//-----------------------------------------------------------------------------------
	//---- form_usuario -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	/*function conf__form_usuario(sap_ei_formulario $form)
	{
		$persona = $this->get_datos('personas','personas')->get();
		if($persona){
			$persona['usuario']  = $persona['nro_documento'];
			$persona['perfiles'] = toba::consulta_php('co_usuarios')->get_perfiles_usuario_proyecto($persona['nro_documento'],'sap','array');
		}
		$form->set_datos($persona);
		$form->set_solo_lectura(array('usuario'));
	}

	function evt__form_usuario__modificacion($datos)
	{
		
	}*/

}
?>