<?php
class co_tablas_basicas
{
	
	 static function get_areas_conocimiento(){
			$sql = "SELECT
						id,
						nombre,
						descripcion,
						aplicable,
						prefijo_orden_poster
					 FROM
						sap_area_conocimiento;";
			return consultar_fuente($sql);
			
	}
	  static function get_areas_conocimiento_becarios(){
			$sql = "SELECT
						id,
						nombre,
						descripcion,
						aplicable
					 FROM
						sap_area_conocimiento WHERE aplicable IN ('BECARIOS','AMBOS')
						ORDER BY nombre ASC;";
			return consultar_fuente($sql);
			
	}
	  static function get_areas_conocimiento_equipos(){
			$sql = "SELECT
						id,
						nombre,
						descripcion,
						aplicable
					 FROM
						sap_area_conocimiento WHERE aplicable IN ('EQUIPOS','AMBOS')
					ORDER BY nombre;";
			return consultar_fuente($sql);
			
	}
	static function get_tipos_beca(){
			$sql = "SELECT
						id,
						descripcion
					FROM
						sap_tipo_beca;";
			return consultar_fuente($sql);
	}
	static function get_dependencias($filtro = array()){
		$sql = "SELECT
					id,
					nombre,
					descripcion,
					letra_codigo_proyectos
				FROM
					sap_dependencia
				ORDER BY nombre;";
		if(in_array('con_letra_codigo_proyectos',$filtro)){
			$where[] = 'letra_codigo_proyectos is not null';
			$sql = sql_concatenar_where($sql,$where);
		}		
		return consultar_fuente($sql);
	}
	static function get_dependencias_unne()
	{
		return toba::db()->consultar('SELECT id,nombre FROM sap_dependencia WHERE id_universidad = 1');
	}
	static function get_dependencias_subsidios(){
		return self::get_dependencias(array('con_letra_codigo_proyectos'));
	}
	/**
	 * Retorna un listado de Unidades Acad?icas con sus respectivas letras para la generaci? de c?igos para proyectos y programas.
	 * @return array Array con listados de unidades academicas con sus letras.
	 */
	function get_dependencias_letras_proyectos()
	{
		$sql = "SELECT letra_codigo_proyectos, nombre FROM sap_dependencia WHERE letra_codigo_proyectos IS NOT NULL";
		return toba::db()->consultar($sql);
	}
	
	static function get_dependencias_cargos(){
			$sql = "SELECT
						dep.id,
						uni.sigla||' - '||dep.nombre as nombre,
						dep.descripcion,
						COALESCE(dep.sigla_mapuche,'----') as sigla_mapuche
					FROM sap_dependencia as dep
					LEFT JOIN be_universidades AS uni ON uni.id_universidad = dep.id_universidad
					WHERE (uni.sigla||' - '||dep.nombre) is not null;";
			return consultar_fuente($sql);
	}
	function get_cargos_filtro($filtro = array())
	{

		$where = array();
		if(isset($filtro['sigla_mapuche'])){
			$where[] = 'dep.sigla_mapuche = '.quote($filtro['sigla_mapuche']);
		}
		if(isset($filtro['solo_externos'])){
			$where[] = 'car.nro_cargo_mapuche is null';
		}
		$sql = "SELECT * 
				FROM sap_cargos_persona AS car
				LEFT JOIN sap_dependencia AS dep ON dep.sigla_mapuche = car.dependencia";
		if(count($where)){
			$sql = sql_concatenar_where($sql,$where);
		}
		return toba::db()->consultar($sql);


	}
	
	static function get_evaluaciones(){
			$sql = "SELECT
						id,
						nombre
					FROM
						sap_evaluacion;";
			return consultar_fuente($sql);
	}
	static function get_area_conocimiento_nombre($id){
		   $sql = "SELECT
						nombre
				   FROM
						sap_area_conocimiento
				   WHERE id= {$id};";
			return toba::db()->consultar_fila($sql);
	}
	static function get_tipobeca_desc($id){
			 $sql = "SELECT
						descripcion
					FROM
						sap_tipo_beca
				   WHERE id= {$id};";
			return toba::db()->consultar_fila($sql);
	}
	 static function get_dependencia_nombre($id){
			$sql = "SELECT
						nombre
					FROM
						sap_dependencia
					WHERE id = {$id};";
			return toba::db()->consultar_fila($sql);
	}
	
	

	function get_evaluadores($area_conocimiento = false)
	{

		$sql = "SELECT eva.evaluador, eva.id_area_conocimiento, a_con.descripcion
				FROM sap_evaluadores AS eva
				JOIN sap_area_conocimiento as a_con on a_con.id = eva.id_area_conocimiento";
		if($area_conocimiento !== false && is_numeric($area_conocimiento) ){
			$sql .= " WHERE eva.id_area_conocimiento = $area_conocimiento";
		}
		$sql .= " ORDER BY eva.evaluador";		
		return toba::db()->consultar($sql);

	}

	static function eliminar_evaluadores()
	{
		$sql = "DELETE FROM sap_evaluadores";
		return toba::db()->ejecutar($sql);
	}

	
	function get_cargos()
	{
		$sql = "select cargo, descripcion from sap_cargos_descripcion where activo = 'S'";
		return toba::db()->consultar($sql);
	}

	//retorna todos los a?s en los que se registr?al menos una convocatoria
	function get_anios_convocatorias()
	{   
		$sql = "SELECT DISTINCT convocatoria FROM sap_cat_incentivos ORDER BY convocatoria DESC";
		return toba::db()->consultar($sql);
	}

	function get_disciplinas($filtro = array())
	{
		$sql = "SELECT * FROM sap_disciplinas";
        return toba::db()->consultar($sql);
	}
	
	function get_categorias_conicet()
	{
		$sql = "SELECT * FROM be_cat_conicet";
		return toba::db()->consultar($sql);
	}

	function get_campos_tabla($campos,$tabla,$where=NULL,$limit=NULL)
	{   
		$campos = (is_array($campos)) ? implode(',',$campos) : $campos;
		$sql = "SELECT $campos FROM $tabla";
		$sql = ($where) ? $sql." where ".$where : $sql;
		$sql = ($limit) ? $sql." LIMIT ".$limit : $sql;
		return toba::db()->consultar($sql);
	}
	function get_localidad_provincia_pais($id_localidad)
	{
		$sql = "SELECT loc.localidad||' - '||prov.provincia||' - '||pai.pais as localidad
				FROM be_localidades as loc
				LEFT JOIN be_provincias as prov on prov.id_provincia = loc.id_provincia
				LEFT JOIN be_paises as pai ON pai.id_pais = prov.id_pais
				WHERE loc.id_localidad = ".quote($id_localidad);
		$resultado = toba::db()->consultar_fila($sql);
		return $resultado['localidad'];
	}
	function buscar_localidad($patron)
	{
		$sql = "SELECT id_localidad, 
						loc.localidad||' - '||prov.provincia||' - '||pai.pais AS localidad 
				FROM be_localidades as loc
				LEFT JOIN be_provincias as prov on prov.id_provincia = loc.id_provincia
				LEFT JOIN be_paises as pai ON pai.id_pais = prov.id_pais
				WHERE localidad ilike ".quote('%'.$patron.'%');
		return toba::db()->consultar($sql);
	}
	function get_niveles_academicos()
	{
		return toba::db()->consultar("select * from be_niveles_academicos order by orden ASC");
	}

	function get_campos_aplicacion($filtro = array())
	{
		$where = array();
		$sql = "SELECT * FROM sap_campos_aplicacion";
		if(isset($filtro['id_campo_aplicacion'])){
			$where[] = "id_campo_aplicion = ".quote($filtro['id_campo_aplicacion']);
		}
		if(count($where)){
			$sql = sql_concatenar_where($sql,$where);
		}
		return toba::db()->consultar($sql);
	}
	
	function get_campo_de_subcampo($id_subcampo)
	{
		$sql = "SELECT id_campo_aplicacion FROM sap_subcampos_aplicacion WHERE id_subcampo_aplicacion = ".quote($id_subcampo)." LIMIT 1";
		$resultado = toba::db()->consultar_fila($sql);
		return $resultado['id_campo_aplicacion'];
	}

	function get_subcampos_aplicacion($id_campo = NULL)
	{
		$where = ($id_campo) ? " WHERE id_campo_aplicacion = ".quote($id_campo) : "";
		$sql = "SELECT * FROM sap_subcampos_aplicacion";

		return toba::db()->consultar($sql.$where);
	}

	function get_carreras($filtro=array())
	{
		$where = array();
		if (isset($filtro['carrera'])) {
			$where[] = "car.carrera ILIKE ".quote("%{$filtro['carrera']}%");
		}
		if (isset($filtro['id_dependencia'])) {
			$where[] = "dep.id_dependencia = ".$filtro['id_dependencia'];
		}
		if (isset($filtro['id_carrera'])) {
			$where[] = "car.id_carrera = ".$filtro['id_carrera'];
		}

		$sql = "SELECT DISTINCT
			car.id_carrera,
			car.carrera,
			car.cod_araucano
		FROM be_carreras as car
		ORDER BY car.carrera";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		return toba::db()->consultar($sql);
	}

	function get_carreras_editable($patron = "")
	{
		return $this->get_carreras(array('carrera'=>$patron));
	}

	function carrera_descripcion($id)
	{
		$carrera = toba::db()->consultar_fila('SELECT carrera FROM be_carreras WHERE id_carrera = '.quote($id));
		return $carrera['carrera'];
	}
}
?>