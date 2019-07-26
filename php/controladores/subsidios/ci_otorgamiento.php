<?php
class ci_otorgamiento extends sap_ci
{
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__otorgar()
	{
		//ei_arbol($this->controlador()->datos('subsidio','sap_subsidio_otorgado')->get()); return;
		$this->controlador()->datos('subsidio','solicitud_subsidio')->set(array('otorgado'=>'S'));
		$this->controlador()->datos('subsidio')->sincronizar();
		$this->controlador()->datos('subsidio')->resetear();
		$this->controlador()->set_pantalla('pant_seleccion');
	}

	function evt__volver()
	{
		$this->controlador()->set_pantalla('pant_seleccion');
	}

	//-----------------------------------------------------------------------------------
	//---- form_otorgamiento ------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_otorgamiento(sap_ei_formulario $form)
	{
		if($this->controlador()->datos('subsidio')->esta_cargada()){
			$form->set_datos($this->controlador()->datos('subsidio','sap_subsidio_otorgado')->get());
		}


	}

	function evt__form_otorgamiento__modificacion($datos)
	{
		$this->controlador()->datos('subsidio','sap_subsidio_otorgado')->set($datos);
	}

}

?>