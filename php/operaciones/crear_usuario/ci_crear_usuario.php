<?php 
require_once('consultas/co_usuarios.php');
class ci_crear_usuario extends toba_ci
{

	protected $s__persona;
	
	//---- Eventos CI -------------------------------------------------------

	function evt__procesar($datos) 
	{
		
	}

	function evt__cancelar() 
	{   
		toba::vinculador()->navegar_a('sap','3509');
	}

	//---- frm_datos_personales -------------------------------------------------------
	
	function conf__frm_datos_personales($form)
	{
		if(isset($this->s__persona)){
			$form->set_datos($this->s__persona);
		}
	}

	function evt__frm_datos_personales__modificacion($datos)
	{
		$this->s__persona = $datos;
		//var_dump(toba::proyecto()->get_parametro('usuario_subclase')); return false;
		//verifico la disponibilidad del nombre de usuario
		if( toba::usuario()->existe_usuario($datos['nro_documento']) ){
			toba::notificacion()->agregar('Ya existe el usuario que intenta crear. Elija la opción: "Olvid&eacute; mi contrase&ntilde;a"');
			//guardo el mensaje para mostrarlo al usuario despues de la redireccion
			return false;
		}else{
			if( $datos['clave'] <> $datos['repetir_clave']) {
				toba::notificacion()->agregar('Las claves ingresadas no coinciden');
				return false;
			}
			if( strlen($datos['clave']) <= 5 ){
				toba::notificacion()->agregar('Debe ingresar una clave de al menos seis caracteres','error');
				return;
			}
		}

		$datos['ayn'] = $datos['apellido'].", ".$datos['nombre'];

		toba::instancia()->get_db()->abrir_transaccion();

		try {
			toba::instancia()->agregar_usuario($datos['nro_documento'], $datos['ayn'], $datos['clave'],array('email'=>$datos['mail']));


			toba::instancia()->vincular_usuario('sap', $datos['nro_documento'], array('becas'));

			//cierro la transaccion
			toba::instancia()->get_db()->cerrar_transaccion();
			$datos = array();

			//guardo el mensaje para mostrarlo al usuario despues de la redireccion
			toba::memoria()->set_dato("mensaje", "Usuario generado con éxito!");

			//verifico si la persona existe en la base de datos local
			$persona = toba::consulta_php('co_personas')->get_personas(array('nro_documento'=>$datos['nro_documento']));

			if( ! $persona){
				//guardo los datos de la persona registrada 
				//Prefiero los datos del WS (si existen)
				$datos_ws =  toba::consulta_php('co_personas')->buscar_en_ws($datos['nro_documento']);
				if(count($datos_ws)){
					$datos['sexo'] = $datos_ws['sexo'];
					$datos['cuil'] = $datos_ws['cuil'];
					$datos['apellido'] = $datos_ws['apellido'];
					$datos['nombres'] = $datos_ws['nombres'];
					$datos['fecha_nac'] = $datos_ws['fecha_nac'];
				}
				toba::consulta_php('co_personas')->nueva_persona($datos);	
				
			}else{
				$persona = $persona[0];
				$datos['ayn'] = $persona['apellido'].', '.$persona['nombres'];
			}
			
			

			toba::vinculador()->navegar_a('sap','3509');

		} catch (toba_error_db $e) {
			if($e->get_sqlstate() == 'db_23505'){
				//cierro la transaccion
				toba::instancia()->get_db()->cerrar_transaccion();

				//guardo el mensaje para mostrarlo al usuario despues de la redireccion
				toba::memoria()->set_dato("mensaje", "Usuario generado con éito!");
				toba::vinculador()->navegar_a('sap','3509');	
			}else{
				toba::instancia()->get_db()->abortar_transaccion(); 
				toba::notificacion()->agregar('Ocurrió un error al intentar generar el usuario. '.$e->get_mensaje());	
			}
			
		}
	}
}

