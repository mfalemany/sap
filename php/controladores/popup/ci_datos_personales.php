<?php
class ci_datos_personales extends sap_ci
{

	function conf()
	{
		$parametros = toba::memoria()->get_parametros();
		if(isset($parametros['nro_documento']) && !$this->get_datos()->esta_cargada()){
			$this->get_datos()->cargar(array('nro_documento' => $parametros['nro_documento']));
		}else{
			if (!$this->get_datos()->esta_cargada()) {
				echo "<script type='text/javascript'>window.close()</script>";
			}
			
		}
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__guardar()
	{
		try {
			$this->get_datos()->sincronizar();
			//Una vez sincronizados los cambios
			toba::consulta_php('co_personas')->marcar_cumplido_cvar($this->get_datos('personas')->get()['nro_documento']);
			$this->get_datos()->resetear();
		} catch (toba_error_db $e) {
			toba::notificacion()->agregar($e->get_mensaje());
		} catch (Exception $e){
			toba::notificacion()->agregar($e->getMessage());
		}
	}

	//-----------------------------------------------------------------------------------
	//---- form_personales --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_personales(sap_ei_formulario $form)
	{
		$datos = $this->get_datos('personas')->get();
		if($datos){
			//Obtengo el campo de aplicación correspondiente al subcampo (si existe). Esto se hace por el esquema de cascadas entre los efs
			if(isset($datos['id_subcampo_aplicacion'])){
				$datos['id_campo_aplicacion'] = toba::consulta_php('co_personas')->get_campo_aplicacion($datos['id_subcampo_aplicacion']);	
			}
			
			$form->set_datos($datos);
		}

	}

	function evt__form_personales__modificacion($datos)
	{
		if($datos['archivo_cvar']){
			$dni = $datos['nro_documento'];
			if( ! toba::consulta_php('helper_archivos')->subir_archivo($datos['archivo_cvar'],"docum_personal/$dni/",'cvar.pdf')){
				throw new Exception('No se ha podido subir el archivo correspondiente al CVar');
			}
			
		}
		//se elimina el DNI para que no de error al intentar alcualizar una clave primaria
		unset($datos['nro_documento']);
		$this->get_datos('personas')->set($datos);

	}

	//-----------------------------------------------------------------------------------
	//---- auxiliares -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function get_datos($tabla=NULL)
	{
		return ($tabla) ? $this->dep('datos')->tabla($tabla) : $this->dep('datos');
	}

	

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		/*echo "
		//---- Eventos ---------------------------------------------
		
		{$this->objeto_js}.evt__volver = function(evento)
		{
			window.close();
		}
		";*/
	}

}
?>