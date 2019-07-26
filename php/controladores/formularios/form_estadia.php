<?php
class form_estadia extends sap_ei_formulario
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		id = {$this->ef('plan_trabajo')->objeto_js()}._id_form;
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
		
		{$this->objeto_js}.evt__plan_trabajo__validar = function()
		{
			if({$this->objeto_js}.ef('plan_trabajo').get_estado().split().length > 500){
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