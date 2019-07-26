<?php 
class co_programas
{
	
	function get_subareas($filtro = array())
	{
		$where = array();
		if(isset($filtro['id_subarea'])){
			$where[] = 'sub.id_subarea = '.quote($filtro['id_subarea']);
		}
		if(isset($filtro['id_area_tematica'])){
			$where[] = 'area.id_area_tematica = '.quote($filtro['id_area_tematica']);
		}
		if(isset($filtro['activo'])){
			$where[] = 'sub.activo = '.quote($filtro['activo']);
		}
		$sql = 'SELECT sub.id_subarea,sub.subarea, area.id_area_tematica,area.area_tematica, sub.activo as sub_activo, area.activo as area_activo 
				FROM sap_programas_subareas AS sub
				LEFT JOIN sap_programas_areas AS area ON sub.id_area_tematica = area.id_area_tematica
				';
		if(count($where)){
			$sql = sql_concatenar_where($sql,$where);
		}
		return toba::db()->consultar($sql);
	}

	function get_areas($filtro = array())
	{
		$where = array();
		if(isset($filtro['id_area_tematica'])){
			$where[] = 'area.id_area_tematica = '.quote($filtro['id_area_tematica']);
		}
		if(isset($filtro['activo'])){
			$where[] = 'area.activo = '.quote($filtro['activo']);
		}
		$sql = 'SELECT * FROM sap_programas_areas as area';
		if(count($where)){
			$sql = sql_concatenar_where($sql,$where);
		}
		return toba::db()->consultar($sql);
	}

	function get_subareas_activas($filtro=array()){
		$filtro['activo'] = 'S';
		return $this->get_subareas($filtro);
	}

	function get_areas_activas($filtro=array()){
		$filtro['activo'] = 'S';
		return $this->get_areas($filtro);
	}

	function get_programas($filtro=array())
	{
		$where = array();
		if(isset($filtro['codigo'])){
			$where[] = 'prog.codigo = '.quote($filtro['codigo']);
		}
		$sql = "SELECT
					prog.codigo,
					prog.denominacion,
					prog.nro_documento_dir,
					per.apellido||', '||per.nombres as director,
					prog.fecha_desde,
					prog.fecha_hasta,
					prog.resol_acreditacion,
					sub.subarea,
					prog.id_subarea
				FROM sap_programas AS prog	
				LEFT JOIN sap_programas_subareas AS sub ON prog.id_subarea = sub.id_subarea
				LEFT JOIN sap_personas AS per ON per.nro_documento = prog.nro_documento_dir
				ORDER BY prog.codigo";
		if(count($where)){
			$sql = sql_concatenar_where($sql,$where);
		}
		return toba::db()->consultar($sql);
	}

	function get_subareas_combo($id_area)
	{
		return $this->get_subareas_activas(array('id_area_tematica'=>$id_area));
	}

	function get_area_de_subarea($subarea)
	{
		$sql = "SELECT id_area_tematica FROM sap_programas_subareas WHERE id_subarea = ".quote($subarea);
		$resultado = toba::db()->consultar_fila($sql);
		return $resultado['id_area_tematica'];
	}

	/**
	 * Retorna un ID que será utilizado para la creación del código de un nuevo programa.
	 * @param  character $ua   Letra que indica, dentro del código, a que unidad academica pertenece
	 * @return integer       Último ID registrado en la base, para este año, y esa unidad academica
	 */
	function get_ultimo_id($ua)
	{
		$anio = substr(date('Y'),2,2);
		return toba::db()->consultar_fila("SELECT substr(codigo,5,2) as id FROM sap_programas WHERE codigo ILIKE '".$anio.strtoupper($ua)."P%' ORDER BY codigo DESC LIMIT 1");
	}
}
?>