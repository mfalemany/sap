<?php
 
class tp_tehuelche_logon extends toba_tp_logon 
{

    /*function pre_contenido()
	{
		echo "<div class='login-titulo'>". toba_recurso::imagen_proyecto('logo-tehuelche-web.png',true);
		echo "<div>versión ".toba::proyecto()->get_version()."</div>";
		echo "</div>";
		echo "\n<div align='center' class='cuerpo'>\n";		
	}*/	
	
	function post_contenido()
	{	
		echo "<div class='caja-recuperar-password'>";
		$vinculo = toba::vinculador()->crear_vinculo('sap','3537', array(), array('validar'=>false));
        echo "<p><a href='$vinculo'>Olvid&eacute; mi contrase&ntilde;a</a></p>";
		echo "</div>";
		echo "<div class='caja-nuevo-usuario'>";
		$vinculo = toba::vinculador()->crear_vinculo('sap','3536', array(), array('validar'=>false));
		echo "<p><a href='$vinculo'>Registrarme como nuevo usuario</a></p>";
		echo "</div>";
	}
	
}