<?php
class form_cambio_clave extends sap_ei_formulario
{
	function extender_objeto_js()
	{
		echo "
		{$this->objeto_js}.evt__guardar = function(){
			if({$this->objeto_js}.ef('clave_nueva').get_estado().length > 0 || {$this->objeto_js}.ef('clave_nueva_repetir').get_estado().length > 0){
				//se valida la coincidencia de claves
				if({$this->objeto_js}.ef('clave_nueva').get_estado() != {$this->objeto_js}.ef('clave_nueva_repetir').get_estado()){
					notificacion.mostrar_ventana_modal('Error en los datos ingresados','Las claves ingresadas no coinciden',100);	
					return false;
				}
				//se valida la longitud de claves
				if({$this->objeto_js}.ef('clave_nueva').get_estado().length < 1){
					notificacion.mostrar_ventana_modal('Error en los datos ingresados','Debe ingresar una clave de al menos seis (6) caracteres',100);	
					return false;	
				}
			}

		}

		/* si el usuario completa el campo 'clave_nueva' 
		   se transforma en obligatorio el campo 'clave_nueva_repetir' */
		
		//obtengo el id del ef
		id = {$this->objeto_js}.ef('clave_nueva')._id_form;
		
		document.getElementById(id).onblur = function(){
			oblig = ({$this->objeto_js}.ef('clave_nueva').get_estado().length > 0) ? true : false;
			{$this->objeto_js}.ef('clave_nueva_repetir').set_obligatorio(oblig);
		}
		";
	}
}

?>