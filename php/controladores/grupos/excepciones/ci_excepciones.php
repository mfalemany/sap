<?php
class ci_excepciones extends sap_ci
{
	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$cuadro->set_datos(toba::consulta_php('co_grupos')->get_excepciones());
	}

	function evt__cuadro__eliminar($datos)
	{
		$this->dep('datos')->resetear();
		$this->dep('datos')->cargar($datos);
		$this->dep('datos')->eliminar_todo();
		$this->dep('datos')->resetear();
	}

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
	}

	//---- Formulario -------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{
		if ($this->dep('datos')->esta_cargada()) {
			$form->set_datos($this->dep('datos')->tabla('sap_grupo_excepcion_dir')->get());
		}
	}

	function evt__formulario__guardar($datos)
	{
		$this->dep('datos')->tabla('sap_grupo_excepcion_dir')->set($datos);
		$this->dep('datos')->sincronizar();
		$this->resetear();
	}

	function evt__formulario__modificacion($datos)
	{
		$this->dep('datos')->tabla('sap_grupo_excepcion_dir')->set($datos);
		$this->dep('datos')->sincronizar();
		$this->resetear();
	}

	function evt__formulario__eliminar()
	{
		$this->dep('datos')->eliminar_todo();
		$this->resetear();
	}

	function evt__formulario__cancelar()
	{
		$this->resetear();
	}

	function resetear()
	{
		$this->dep('datos')->resetear();
	}

	function get_ayn($nro_documento)
	{
		$sql = "SELECT apellido||', '||nombres AS ayn FROM sap_personas WHERE nro_documento = ".quote($nro_documento);
		$persona = toba::db()->consultar_fila($sql);
		return (count($persona)) ? $persona['ayn'] : '';
	}

}

?>