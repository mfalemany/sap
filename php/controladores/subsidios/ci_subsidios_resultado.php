<?php
class ci_subsidios_resultado extends sap_ci
{
	
	//-----------------------------------------------------------------------------------
	//---- form_resultado_evaluacion ----------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_resultado_evaluacion(sap_ei_formulario $form)
	{
		$form->set_datos(toba::consulta_php('co_subsidios')->get_evaluacion(toba::memoria()->get_dato('id_solicitud')));
		toba::memoria()->eliminar_dato('id_solicitud');
	 }

}
?>