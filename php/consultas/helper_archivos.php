<?php

class helper_archivos
{
	function subir_archivo($detalles = array(),$carpeta,$nombre_archivo)
	{
		$nombre_archivo = str_replace(array('/','%','\\','/',':','*','?','<','>','|'), '-', $nombre_archivo);
		if(!count($detalles)){
			return;
		}
		
		if( ! is_dir($this->ruta_base().$carpeta)){
			if( ! mkdir($this->ruta_base().$carpeta,0777,TRUE)){
				throw new toba_error('No se puede crear el directorio '.$carpeta.' en el directorio navegable del servidor. Por favor, pongase en contacto con el administrador del sistema');
				return false;
			}
		}
		if(substr($carpeta, strlen($carpeta)-1,1) == '/'){
			$carpeta = substr($carpeta,0,strlen($carpeta)-1);
		}
		return move_uploaded_file($detalles['tmp_name'], $this->ruta_base().$carpeta."/".$nombre_archivo);
	}

	function eliminar_archivo($archivo)
	{
		unlink($archivo);
	}
	function eliminar_archivos_usuario($nro_documento)
	{

	}

	function procesar_campos($efs_archivos,&$datos_form,$ruta)
	{
		foreach($efs_archivos as $archivo){
			if(array_key_exists($archivo['ef'], $datos_form)){
				if($datos_form[$archivo['ef']]){
					if( ! $this->subir_archivo($datos_form[$archivo['ef']],utf8_encode($ruta),$archivo['nombre'])){
						toba::notificacion()->agregar('No se pudo cargar el archivo '.$archivo['descripcion'].'. Por favor, intentelo nuevamente. Si el problema persiste, pongase en contacto con la Secretaría General de Ciencia y Técnica');
					}else{
						$datos_form[$archivo['ef']] = $archivo['nombre'];
					}
				}else{
					unset($datos_form[$archivo['ef']]);
				}	
			}
		}
	}

	function ruta_base()
	{
		return '/mnt/datos/cyt/';
	}

}

?>