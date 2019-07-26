<?php
class ci_buscar_personas extends sap_ci
{
	protected $s__filtro;

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function evt__nueva_persona()
	{
		$this->set_pantalla('pant_edicion');
	}
	function evt__volver()
	{
		unset($this->s__filtro);
		$this->set_pantalla('pant_seleccion');
		$this->pantalla()->eliminar_evento('nueva_persona');
	}
	function evt__guardar()
	{
		try {
			$persona = $this->datos()->get();
			$this->datos()->sincronizar();	
			$this->datos()->resetear();
			toba::notificacion()->agregar('Persona guardada!','info');
			$this->s__filtro = array('nro_documento'=>$persona['nro_documento']);
			$this->set_pantalla('pant_seleccion');
		} catch (toba_error_db $e) {
			toba::notificacion()->agregar('Ocurrió un error al intentar guardar. Es posible que el DNI que intenta guardar ya se encuentre registrado.');
		}
		
		
	}

	//-----------------------------------------------------------------------------------
	//---- cu_personas ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cu_personas(sap_ei_cuadro $cuadro)
	{
		$cuadro->desactivar_modo_clave_segura();
		if(!isset($this->s__filtro)){
			$cuadro->set_eof_mensaje('Debe establecer algún criterio de filtro para ver resultados.');
		}else{
			$personas = toba::consulta_php('co_personas')->get_personas($this->s__filtro);
			if(count($personas)){
				$cuadro->set_datos($personas);
			}else{
				toba::notificacion()->agregar('No se encontraron personas que coincidan con los criterios establecidos. Por favor, intente nuevamente con otros criterios de busqueda o haga click en el botón \'Nueva persona\' para agregar una nueva.','info');
				$this->pantalla()->agregar_evento('nueva_persona');
			}
		}
	}
	/*function evt__cu_personas__seleccion($datos)
	{
		//ei_arbol($datos);
	}*/

	//-----------------------------------------------------------------------------------
	//---- filtro_personas --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro_personas(sap_ei_formulario $form)
	{
		if(isset($this->s__filtro)){
			$form->set_datos($this->s__filtro);
		}
	}

	function evt__filtro_personas__filtrar($datos)
	{
		$this->s__filtro = $datos;
	}

	function evt__filtro_personas__cancelar()
	{
		unset($this->s__datos);
	}

	//-----------------------------------------------------------------------------------
	//---- form_persona -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_persona(sap_ei_formulario $form)
	{
		$form->desactivar_efs(array('archivo_cvar'));
	}

	function evt__form_persona__modificacion($datos)
	{
		$this->datos()->set($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- Extensión Javascript ---------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function extender_objeto_js()
	{
		/*echo "
			if(typeof {$this->objeto_js}.dep('form_persona') != 'undefined'){
				{$this->objeto_js}.dep('form_persona').ef('nro_documento').cuando_cambia_valor('validar_documento()');	
			}

			function validar_documento(){
				nro_documento = {$this->objeto_js}.dep('form_persona').ef('nro_documento').get_estado();
				{$this->objeto_js}.ajax('existe_documento',nro_documento,this,function(existe){
					if(existe){
						alert('El número de documento ingresado ya existe. Vuelva a la pantalla anterior para buscar la persona, y luego seleccione la misma en los resultados.');
					}
				})
			}
		";*/
	}

	function ajax__existe_documento($nro_documento, toba_ajax_respuesta $respuesta)
	{
		$respuesta->set(toba::consulta_php('co_personas')->existe_persona($nro_documento));
	}

	function datos()
	{
		return $this->dep('personas');
	}

	




}
?>