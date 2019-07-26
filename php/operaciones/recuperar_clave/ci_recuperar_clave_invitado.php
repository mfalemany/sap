<?php
require_once('consultas/co_usuarios.php');
class ci_recuperar_clave_invitado extends toba_ci
{
	protected $s__datos;

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__recuperar()
	{
		$persona = toba::usuario()->existe_usuario($this->s__datos['nro_documento']);
		if( ! $persona){
			toba::notificacion()->agregar('No existe ninguna persona registrada con el número de documento indicado.');
			return;
		}
		
		//datos del servidor y del proyecto (para la redirección)
		//$url_proyecto = toba::proyecto()->get_www()['url'];
		//$servidor = "http://" . $_SERVER['SERVER_NAME'];

		// Clave aleatoria
		$clave = toba::usuario()->generar_clave_aleatoria(8);
		
		//obtengo los detalles del usuario (ayn, mail, etc)
		$datos_usuario = toba::instancia()->get_info_usuario($this->s__datos['nro_documento']);
		
		if( empty($datos_usuario['email']) ){
			toba::notificacion()->agregar('El usuario indicado no posee una dirección de correo asociada. Por favor, comuniquese con la Secretaría General de Ciencia y Técnica');
			return;
		}
		$cuerpo = "<h2 style='background-color: #3a42a7; color:#FFEEEE; padding:3px 0px 3px 10px;'>Solicitud de Cambio de Contraseña - SAP</h2>
		Se ha solicitado la recuperación de contraseña para el usuario <b>".$datos_usuario['id']."</b> (".$datos_usuario['nombre']."). <br> Puede inciar sesión con la siguiente clave: <span style='color:#FF3333; font-weight: bold;'>".$clave."</span>  (<a href='http://sistema.cyt.unne.edu.ar'>Iniciar sesión ahora</a>)";
		

		


		$mail = new toba_mail($datos_usuario['email'],'Recuperación de Contraseña',$cuerpo);
		$mail->set_configuracion_smtp('instalacion');
		$mail->set_remitente('RR.HH. SGCyT');
		$mail->set_reply('rhcyt@unne.edu.ar');
		$mail->set_html();
		
		// Mando el e-mail
		try {
			$mail->ejecutar();
			
			//guardo el mensaje para mostrarlo al usuario despues de la redireccion
			toba::memoria()->set_dato("mensaje", "Su clave ha sido enviada a la siguiente dirección: ".$datos_usuario['email'].". Si no lo encuentra, por favor revise el buzón de spam.");

			//le cambio la clave al usuario
			toba::usuario()->set_clave_usuario($clave,$datos_usuario['id']);
			//navego al inicio
			toba::vinculador()->navegar_a('sap','3509');
		} catch (toba_error $e) {
			toba::memoria()->set_dato('mensaje', 'Error al intentar enviar la clave por mail. Consulte con el administrador del sistema.' . $e->get_mensaje());
			//navego al inicio
			toba::vinculador()->navegar_a('sap','3509');
		}
			
	
	}

	function evt__cancelar(){
		toba::vinculador()->navegar_a('sap','3509');
	}


	//-----------------------------------------------------------------------------------
	//---- frm_documento ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__frm_documento__modificacion($datos)
	{
		$this->s__datos = $datos;
	}

	function conf__frm_documento(sap_ei_formulario $form)
	{
		if(isset($this->s__datos)){
			$form->set_datos($this->s__datos);
		}
	}

}
?>