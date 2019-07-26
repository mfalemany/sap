<?php 
class co_usuarios{

	function actualizar_mail($usuario,$mail)
	{
		if(empty($usuario) || empty($mail)){
			return false;
		}
		$sql = "UPDATE apex_usuario SET email = ".quote($mail)." WHERE usuario = ".quote($usuario);
		return toba::instancia()->get_db()->ejecutar($sql);
	}

	function get_datos_usuario($id_usuario)
	{
		return toba::instancia()->get_info_usuario($id_usuario);
	}

	function get_perfiles_usuario_proyecto($usuario,$proyecto = 'sap',$tipo_retorno = 'lista')
	{
		$sql = "SELECT * 
				FROM apex_usuario_proyecto 
				WHERE usuario = ".quote($usuario)." 
				AND proyecto = ".quote($proyecto);
		$perfiles = toba::instancia()->get_db()->consultar($sql);
		if($perfiles){
			foreach ($perfiles as $perfil) {
				$lista[] = $perfil['usuario_grupo_acc'];
			}
			return ($tipo_retorno == 'lista') ? implode('/',$lista) : $lista;
		}
		return ($tipo_retorno == 'lista') ? '' : array();
		
		
	}
	function get_perfiles_proyecto($proyecto = 'sap')
	{
		$sql = "SELECT * 
				FROM apex_usuario_proyecto 
				WHERE proyecto = ".quote($proyecto);
		return toba::instancia()->get_db()->consultar($sql);
	}

	function set_perfiles_usuario($usuario, $perfiles, $proyecto = 'sap')
	{
		$instancia = toba::instancia()->get_db();

		//Elimino todos los perfiles funcionales asignados al usuario
		//$sql = "DELETE FROM apex_usuario_proyecto WHERE usuario = ".quote($usuario)." AND proyecto = ".quote($proyecto);
		//$instancia->ejecutar($sql);




	}
}

	
?>