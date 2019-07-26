<?php
class form_congreso extends sap_ei_formulario
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		id = {$this->ef('abstract')->objeto_js()}._id_form;
		$('#'+id).on('keydown',function(e){
			var palabras = $('#'+id).prop('value').split(' ').length;
			if(palabras > 500){
				console.log(e.keyCode);
				if(e.keyCode != 8 && e.keyCode != 16){
					alert('Usted ha superado el límite de 500 palabras permitidas');
					e.preventDefault();
				}
			}
		});
		
				
		//---- Validacion de EFs -----------------------------------
		
		{$this->objeto_js}.evt__abstract__validar = function()
		{
			if({$this->objeto_js}.ef('abstract').get_estado().split().length > 500){
				alert('Usted ha superado el límite de 500 palabras permitidas');
				return false;

			}else{
				return true;
			}
		}
		";
	}


}
?>

