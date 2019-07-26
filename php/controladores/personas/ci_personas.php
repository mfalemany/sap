<?php
class ci_personas extends sap_ci
{
	protected $s__filtro;
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
		unset($this->s__filtro);
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro_personas --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_personas(sap_ei_cuadro $cuadro)
	{
		if(isset($this->s__filtro)){
			$cuadro->set_datos(toba::consulta_php('co_personas')->get_personas($this->s__filtro));	
		}else{
			$cuadro->set_eof_mensaje('Debe establecer algún criterio de filtro para ver resultados.');
		}
		
	}

	function evt__cuadro_personas__seleccion($seleccion)
	{
		$this->dep('personas')->cargar($seleccion);
		$this->set_pantalla('pant_edicion');
	}

	
	
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__guardar()
	{
		try{
			$this->dep('personas')->sincronizar();
			$this->dep('personas')->resetear();
			$this->set_pantalla('pant_seleccion');	
		}catch(toba_error_db $e){
			switch ($e->get_sqlstate()) {
				case 'db_23505':
					toba::notificacion()->agregar('Ocurrió un error al intentar guardar. Posiblemente la persona ingresada ya se encuentra registrada en el sistema');
					break;
				case 'db_23503':
					toba::notificacion()->agregar($e->get_mensaje_motor());
					break;
				default:
					toba::notificacion()->agregar($e->get_sqlstate());
					break;
			}
		}
	}

	function evt__cancelar()
	{
		$this->dep('personas')->resetear();
		$this->set_pantalla('pant_seleccion');
	}

	function evt__agregar()
	{
		$this->dep('personas')->resetear();
		$this->set_pantalla('pant_edicion');
	}

	function evt__eliminar()
	{
		$this->dep('personas')->eliminar_todo();
		$this->dep('personas')->sincronizar();
		$this->dep('personas')->resetear();
		$this->set_pantalla('pant_seleccion');
	}
}
?>