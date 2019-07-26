<?php
class dt_sap_comunicacion extends sap_datos_tabla
{
	function get_orden_poster($convocatoria,$area_conocimiento){
		$sql = "SELECT orden_poster as ultimo_id
				FROM sap_comunicacion
				WHERE sap_convocatoria_id = $convocatoria
				AND sap_area_beca_id = $area_conocimiento
				AND orden_poster IS NOT NULL
				ORDER BY id DESC
				LIMIT 1";
		$resultado = toba::db('sap')->consultar($sql);
		
		//evalúo si existe algún orden de poster anterior cargado
		if($resultado[0]['ultimo_id']){
			//divido el orden de poster para obtener el valor numerico (sin prefijo)
			$ultimo_id = explode('-',$resultado[0]['ultimo_id']);
			$ultimo_id = intval($ultimo_id[1]); 	
		}else{
			$ultimo_id = NULL;
		}
		
		//obtengo el prefijo para el area de conocimiento seleccionada
		$prefijo = toba::db('sap')->consultar("SELECT descripcion, prefijo_orden_poster FROM sap_area_conocimiento WHERE id = $area_conocimiento LIMIT 1");
		//si no existe prefijo, se toma la descripcion completa
		$prefijo = ($prefijo[0]['prefijo_orden_poster']) ? $prefijo[0]['prefijo_orden_poster'] : $prefijo[0]['descripcion'];
		
		//armo el orden de poster con el formato deseado
		$id = ($ultimo_id) ? sprintf("%'03d", ($ultimo_id+1) ) : '001';
		return $prefijo."-".$id;


	}
}

?>