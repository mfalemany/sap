<?php
class ci_edicion_proyecto extends sap_ci
{
	function conf()
	{
		//se asigna el template a la pantalla
		$template = file_get_contents(__DIR__.'/template_proyecto.php');
		$this->pantalla()->set_template($template);
	}

	//-----------------------------------------------------------------------------------
	//---- form_proyecto ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_proyecto(sap_ei_formulario $form)
	{
		if ($this->get_datos()->esta_cargada()) {
			$form->set_datos($this->get_datos('datos','sap_proyectos')->get());
			$form->desactivar_efs(array('sap_dependencia_id','tipo'));
			$form->set_solo_lectura(array('codigo'));
		}else{
			if($form->existe_ef('director')){
				$form->desactivar_efs(array('director'));	
			}
			$anio = date("Y") + 1;
			$form->ef('tipo')->set_estado("0");
			$form->ef('fecha_desde')->set_estado($anio."-01-01");
			$form->ef('fecha_hasta')->set_estado(($anio+3)."-12-31");
			$form->ef('entidad_financiadora')->set_estado("Sec. Gral. de Ciencia y Técnica - Universidad Nacional del Nordeste");
			
		}
	}

	function evt__form_proyecto__modificacion($datos)
	{
		//Si no se asign?el c?igo a mano, se auto-genera uno
		$datos['director'] = toba::consulta_php('co_personas')->get_ayn($datos['nro_documento_dir']);
		
		/* =============== Procesamiento del EF tipo Upload ====================== */
		
		$ext = explode(".",$datos['archivo_proyecto']['name']);
		$ext = end($ext);
		$ruta = 'proyectos/'.$datos['codigo'].'/';
		$efs_archivos = array(array('ef'          => 'archivo_proyecto',
							  		'descripcion' => 'Contenido comprimido de la documentacion del proyecto',
							  		'nombre'      => 'Contenido.'.$ext)
							);

		toba::consulta_php('helper_archivos')->procesar_campos($efs_archivos,$datos,$ruta);
		/* =============== Procesamiento del EF tipo Upload ====================== */
		if(!$datos['codigo']){
			unset($datos['codigo']);
		}
		$this->get_datos('datos','sap_proyectos')->set($datos);
	}

	//-----------------------------------------------------------------------------------
	//-------------- DATOS --------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function get_datos($relacion = 'datos',$tabla = NULL)
	{
		return $this->controlador()->get_datos($relacion,$tabla);
	}
	

	function extender_objeto_js()
	{
		$objeto_js = $this->dep('form_proyecto')->objeto_js;
		echo "
			//cambio de valor en el campo DNI DIRECTOR
			{$objeto_js}.ef('nro_documento_dir').cuando_cambia_valor('get_detalles_director({$objeto_js}.ef(\'nro_documento_dir\').get_estado(),\'director\')');

			//cambio de valor en el campo DNI CODIRECTOR
			{$objeto_js}.ef('nro_documento_codir').cuando_cambia_valor('get_detalles_director({$objeto_js}.ef(\'nro_documento_codir\').get_estado(),\'codirector\')');

			//cambio de valor en el campo DNI SUBDIRECTOR
			{$objeto_js}.ef('nro_documento_subdir').cuando_cambia_valor('get_detalles_director({$objeto_js}.ef(\'nro_documento_subdir\').get_estado(),\'subdirector\')');

			//PARA LA CARGA INICIAL (NO SOLO CUANDO SE PIERDE EL FOCO)
			if({$objeto_js}.ef('nro_documento_dir').get_estado().length > 0){
				get_detalles_director({$objeto_js}.ef('nro_documento_dir').get_estado(),'director');
			}
			if({$objeto_js}.ef('nro_documento_dir').get_estado().length > 0){
				get_detalles_director({$objeto_js}.ef('nro_documento_codir').get_estado(),'codirector');
			}
			if({$objeto_js}.ef('nro_documento_dir').get_estado().length > 0){
				get_detalles_director({$objeto_js}.ef('nro_documento_subdir').get_estado(),'subdirector');
			}

			function get_detalles_director(nro_documento,campo)
			{
				if(nro_documento.length == 0){
					return;
				}
				datos = {'nro_documento':nro_documento,'campo':campo};
				{$this->objeto_js}.ajax('get_detalles_director',datos,this,llenar_datos_director);
			}

			function llenar_datos_director(respuesta)
			{
				$('#'+respuesta.campo+'_dni').html('');
				$('#'+respuesta.campo+'').html('');
				$('#'+respuesta.campo+'_cat_inc').html('');

				//datos personales
				if(respuesta.apellido){
					$('#'+respuesta.campo+'_dni').html(respuesta.nro_documento);
					$('#'+respuesta.campo+'').html(respuesta.apellido+', '+respuesta.nombres);
				}else{
					$('#'+respuesta.campo).html('No encontrado');
				}


				//categoría de incentivos
				if(respuesta.categoria_desc){
					$('#'+respuesta.campo+'_cat_inc').html(respuesta.categoria_desc+' (Convocatoria '+respuesta.convocatoria+')');	
				}
				
				//vac? el listado de cargos para volver a llenarlo
				$('#'+respuesta.campo+'_cargos').empty();

				if(respuesta.cargos.length > 0){
					$.each(respuesta.cargos,function(indice,cargo){
						if(cargo.vigente == 'S'){
							clase_css = 'cargo_vigente';
						}else{
							clase_css = '';
						}
						$('#'+respuesta.campo+'_cargos').append('<li class=\''+clase_css+'\'>'+cargo.cargo_descripcion+' ('+cargo.dependencia_desc+') - Periodo: '+cargo.fecha_desde+' - '+cargo.fecha_hasta+'</li>');
					})
				}
				
			}";
	}

	function ajax__get_detalles_director($nro_documento, toba_ajax_respuesta $respuesta){
		$resultado = toba::consulta_php('co_personas')->get_detalles_director($nro_documento);
		$respuesta->set($resultado);

	}
}

?>