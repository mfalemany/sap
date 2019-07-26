<?php
class form_proyecto extends sap_ei_formulario
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//Variable que controla cuantas palabras debe contener el resumen (como máximo)
		var max_resumen = 250;

		//obtengo el id del elemento JS del resumen
		id = '#'+{$this->objeto_js}.ef('resumen')._id_form;
		
		//cuando se escriba sobre el resumen, actualizo el contador de palabras
		$(id).on('keyup',function(){
			cant = $(id).prop('value').split(' ').filter(palabra => palabra.length > 0).length;
			$('#cant_palabras_resumen').text(cant);
		});
		//---- Validacion de EFs -----------------------------------
		
		{$this->objeto_js}.evt__resumen__validar = function()
		{
			if(this.ef('resumen').get_estado().split(' ').filter(pal => pal.length > 0).length > max_resumen){
				this.ef('resumen').set_error('Este campo debe tener, como máximo, '+max_resumen+' palabras');
				return false;
			}else{
				return true;
			}
		}

		
		";
	}


}
?>