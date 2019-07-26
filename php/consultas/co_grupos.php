<?php
class co_grupos
{

	/**
	 * Retorna un array con todos los grupos (si no se estableció un filtro) o aquellos que coincidan con el criterio de filtro.
	 * @param  array  $filtro Array de condiciones a filtrar
	 * @return array         Array de grupos
	 */
	function get_grupos($filtro = array())
	{
		$where = array();
		if(isset($filtro['nro_documento_coordinador'])){
			$where[] = 'gr.nro_documento_coordinador = '.quote($filtro['nro_documento_coordinador']);
		}
		if(isset($filtro['id_grupo'])){
			$where[] = 'gr.id_grupo = '.quote($filtro['id_grupo']);
		}
		if(isset($filtro['denominacion'])){
			$where[] = 'gr.denominacion ilike '.quote('%'.$filtro['denominacion'].'%');
		}	
		if(isset($filtro['coordinador'])){
			$where[] = 'per.apellido ilike '.quote('%'.$filtro['coordinador'].'%'). ' OR per.nombres ilike '.quote('%'.$filtro['coordinador'].'%');
		}
		if(isset($filtro['denominacion'])){
			$where[] = 'gr.denominacion ilike '.quote('%'.$filtro['denominacion'].'%');
		}
		if(isset($filtro['id_dependencia'])){
			$where[] = 'gr.id_dependencia = '.quote($filtro['id_dependencia']);
		}
		if(isset($filtro['id_area_conocimiento'])){
			$where[] = 'gr.id_area_conocimiento = '.quote($filtro['id_area_conocimiento']);
		}
		if(isset($filtro['id_categoria'])){
			$where[] = 'gr.id_categoria = '.quote($filtro['id_categoria']);
		}
		if(isset($filtro['solo_inscriptos']) && $filtro['solo_inscriptos'] == 1){
			$where[] = 'gr.fecha_inscripcion is not null';
		}

		$sql = "SELECT gr.*,
						per.apellido||', '||per.nombres AS coordinador,
						dep.nombre AS dependencia,
						ac.nombre AS area_conocimiento,
						cat.categoria
				FROM sap_grupo AS gr
				LEFT JOIN sap_personas AS per ON per.nro_documento = gr.nro_documento_coordinador
				LEFT JOIN sap_dependencia AS dep ON dep.id = gr.id_dependencia
				LEFT JOIN sap_area_conocimiento AS ac ON ac.id = gr.id_area_conocimiento
				LEFT JOIN sap_grupo_categoria AS cat ON cat.id_categoria = gr.id_categoria";

		if(count($where)){
			$sql = sql_concatenar_where($sql,$where);
		}
		return toba::db()->consultar($sql);


	}

	/**
	 * Retorna la denominación del grupo cuyo ID coincide con el recibido como parámetro.
	 * @param  [integer] $id_grupo ID del grupo que se busca
	 * @return [string]           Denominación del grupo
	 */
	function get_grupo_denominacion($id_grupo){
		$sql = "SELECT denominacion FROM sap_grupo WHERE id_grupo = ".quote($id_grupo);
		$resultado = toba::db()->consultar_fila($sql);
		return ($resultado['denominacion']) ? $resultado['denominacion'] : 'Grupo no encontrado';
	}

	/**
	 * Retorna el ID y la denominación de un grupo. Método útil para usar con EF Combo Editable
	 * @param  [string] $criterio patrón de búsqueda
	 * @return [string]           Denominación del/los grupos que coincidan con el patrón buscado
	 */
	function get_grupo_busqueda($criterio)
	{
		$sql = "SELECT id_grupo, denominacion
				FROM sap_grupo 
				WHERE quitar_acentos(denominacion) ILIKE ".quote("%".$criterio."%");
		return toba::db()->consultar($sql);
	}

	function puede_coordinar($usuario)
	{
		if($this->esta_exceptuado($usuario)){
			return TRUE;
		}
		if(! toba::consulta_php('co_personas')->es_docente($usuario)){
			return FALSE;
		}
		if( ! (toba::consulta_php('co_personas')->tiene_mayor_dedicacion($usuario) || 
			  toba::consulta_php('co_personas')->get_cargo_conicet($usuario)) ){
			return FALSE;
		}
		$cat = toba::consulta_php('co_personas')->get_categoria_incentivos($usuario);
		if($cat == NULL || ! in_array($cat,array(1,2,3))){
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Retorna un array con todos los detalles del grupo cuyo ID coincide con el argumento recibido. El array contiene la denominación, la descripción, los integrantes, etc.
	 * @param  integer $id_grupo ID del grupo que se busca
	 * @return array           Array con los detalles del grupo
	 */
	function get_detalles_grupo($id_grupo)
	{
		$sql = "SELECT gr.id_grupo, 
					gr.denominacion, 
					gr.descripcion, 
					gr.id_categoria,
					coord.apellido||', '||coord.nombres AS coordinador, 
					gr.nro_documento_coordinador,
					dep.nombre AS dependencia,
					ac.nombre AS area_conocimiento,
					gr.fecha_inicio,
					gr.fecha_inscripcion
				FROM sap_grupo AS gr
				LEFT JOIN sap_personas AS coord ON coord.nro_documento = gr.nro_documento_coordinador
				LEFT JOIN sap_dependencia AS dep ON dep.id = gr.id_dependencia
				LEFT JOIN sap_area_conocimiento AS ac ON ac.id = gr.id_area_conocimiento
				WHERE gr.id_grupo = ".quote($id_grupo);
		return toba::db()->consultar_fila($sql);
	}

	function get_planes_trabajo($id_grupo)
	{
		$sql = "SELECT gi.*, conv.nombre as convocatoria
				FROM sap_grupo_informe AS gi
				LEFT JOIN sap_convocatoria AS conv ON conv.id = gi.id_convocatoria
				WHERE gi.id_grupo = ".quote($id_grupo);
		return toba::db()->consultar($sql);


	}


	/**
	 * Retorna un array con los datos de los integrantes del grupo
	 * @param  integer $id_grupo ID del grupo del cual se quieren obtener los integrantes
	 * @return array           Array de integrantes, con datos básicos como apellido y nombres, nro_documento, etc.
	 */
	function get_integrantes($id_grupo)
	{
		$sql = "SELECT per.apellido,
						per.nombres,
						per.apellido||', '||per.nombres AS integrante, 
						per.nro_documento, 
						inte.fecha_inicio,
						inte.fecha_fin,
						rol.rol,
						rol.id_rol

				FROM sap_grupo_integrante AS inte
				LEFT JOIN sap_personas AS per ON per.nro_documento = inte.nro_documento
				LEFT JOIN sap_grupo_rol AS rol ON rol.id_rol = inte.id_rol
				WHERE inte.id_grupo = ".quote($id_grupo)."
				ORDER BY integrante";
		return toba::db()->consultar($sql);
	}

	function get_proyectos_grupo($id_grupo,$solo_vigentes=FALSE)
	{
		$sql = "SELECT  pr.id,
						pr.id AS id_proyecto,
					pr.codigo, 
					pr.descripcion,
					CASE WHEN pr.fecha_hasta < current_date THEN 'Finalizado' ELSE 'Vigente' END AS estado
				FROM sap_grupo_proyecto AS gp
				LEFT JOIN sap_proyectos AS pr ON pr.id = gp.id_proyecto
				WHERE pr.id_grupo = ".quote($id_grupo)."
				UNION
				SELECT null AS id, null AS id_proyecto, 
					'Externo' AS codigo, 
					denominacion,
					CASE WHEN pe.fecha_hasta < current_date THEN 'Finalizado' ELSE 'Vigente' END AS estado
				FROM sap_proyectos_externos AS pe
				WHERE id_grupo = ".quote($id_grupo);
		return toba::db()->consultar($sql);
	}

	function get_actividad_extension($id_grupo)
	{
		$sql = "SELECT * FROM sap_grupo_extension WHERE id_grupo = ".quote($id_grupo);
		return toba::db()->consultar($sql);
	}
	function get_actividad_publicacion($id_grupo)
	{
		$sql = "SELECT gp.*, tp.tipo_publicacion
				FROM sap_grupo_publicacion AS gp
				LEFT JOIN sap_tipo_publicacion AS tp ON gp.id_tipo_publicacion = tp.id_tipo_publicacion
				WHERE id_grupo = ".quote($id_grupo);
		return toba::db()->consultar($sql);
	}

	function get_actividad_transferencia($id_grupo)
	{
		$sql = "SELECT gt.*, tt.tipo_transferencia, CASE gt.sector WHEN 'R' THEN 'Privado' ELSE 'Público' END AS sector_desc
				FROM sap_grupo_transferencia AS gt
				LEFT JOIN sap_tipo_transferencia AS tt ON gt.id_tipo_transferencia = tt.id_tipo_transferencia
				WHERE id_grupo = ".quote($id_grupo);
		return toba::db()->consultar($sql);
	}
	function get_actividad_form_rrhh($id_grupo)
	{
		$sql = "SELECT gf.*, 
						per.apellido||', '||per.nombres AS persona,
						tf.tipo_formacion,
						CASE WHEN gf.id_entidad_beca IS NULL THEN 'Sin beca' else eb.entidad_beca end as entidad_beca
						
				FROM sap_grupo_form_rrhh AS gf
				LEFT JOIN sap_personas AS per USING(nro_documento)
				LEFT JOIN sap_tipo_form_rrhh AS tf USING (id_tipo_formacion)
				LEFT JOIN sap_entidad_beca AS eb USING(id_entidad_beca)
				WHERE id_grupo = ".quote($id_grupo);
		return toba::db()->consultar($sql);
	}

	function get_actividad_evento($id_grupo)
	{
		$sql = "SELECT evento, anio, 
					CASE alcance 
						WHEN 'R' THEN 'Regional' 
						WHEN 'N' THEN 'Nacional' 
						WHEN 'I' THEN 'Internacional' 
					END AS alcance
				FROM sap_grupo_evento AS ge
				WHERE id_grupo = ".quote($id_grupo);
		return toba::db()->consultar($sql);
	}




	function get_lineas_investigacion($id_grupo)
	{
		$sql = "SELECT linea_investigacion FROM sap_grupo_linea_investigacion WHERE id_grupo = ".quote($id_grupo);
		$resultado = toba::db()->consultar($sql);
		$lineas = array();
		foreach ($resultado as $linea) {
			$lineas[] = $linea['linea_investigacion'];
		}
		return $lineas;
	}


	/**
	 * Devuelve TRUE si la persona ya se encuentra asignada como coordinador de un grupo de investigacion
	 * @param string $nro_documento 
	 * @return boolean
	 */
	function es_coordinador($nro_documento){
		$sql = "SELECT * FROM sap_grupo WHERE nro_documento_coordinador = ".quote($nro_documento)." AND (fecha_fin > now() OR fecha_fin is null) LIMIT 1";
		$resultado = toba::db()->consultar($sql);
		return (count($resultado)) ? TRUE : FALSE;
	}

	/**
	 * Retorna los roles disponibles para integrantes de grupos
	 * @return array Array de roles disponibles
	 */
	function get_roles()
	{
		$sql = "SELECT * FROM sap_grupo_rol";
		return toba::db()->consultar($sql);
	}

	/**
	 * Retorna los ID de los grupos de los cuales una persona es integrante
	 * @param  varchar $nro_documento Número de documento de la persona que se busca
	 * @param  boolea $historico Indica si se deben considerar tambien grupos en los cuales la persona fue integrante (pero ya no)
	 * @param  array $omitir   Array de id_grupo que no se consideran al buscar (sirve para resolver la pregunta "Aparte de este/estos grupo/s, en que otros grupos está?")
	 * @return array                Array que contiene los IDs de los grupos de los cuales la persona es integrante
	 */
	function grupos_es_integrante($nro_documento,$historico = false,$omitir=array()){
		$sql = "SELECT gi.id_grupo,gr.denominacion 
				FROM sap_grupo_integrante AS gi
				LEFT JOIN sap_grupo AS gr ON gr.id_grupo = gi.id_grupo
				WHERE gr.fecha_inscripcion is not null
				AND gi.nro_documento = ".quote($nro_documento);

		if(!$historico){
			$sql .= " AND (gi.fecha_fin > current_date OR gi.fecha_fin is null)"; 
		}
		if($omitir){
			$omitir = implode(',',$omitir);
			$sql .= " AND gi.id_grupo NOT IN (".$omitir.")";
		}
		$grupos = array();
		return toba::db()->consultar($sql);
	}

	/**
	 * Retorna todos un array con todos los grupos que hayan presentado un informe en la última convocatoria
	 * @param  array  $filtro Filtros para la consulta
	 * @return array         Array de grupos a evaluar
	 */
	function get_grupos_a_evaluar($filtro = array())
	{
		//Obtengo la última convocatoria
		$conv = toba::db()->consultar_fila("SELECT max(id) AS id FROM sap_convocatoria WHERE aplicable = 'EQUIPOS'");
		$conv = $conv['id'];

		$where = array();
		if(isset($filtro['id_dependencia'])){
			$where[] = 'gr.id_dependencia = '.quote($filtro['id_dependencia']);
		}
		if(isset($filtro['id_area_conocimiento'])){
			$where[] = 'gr.id_area_conocimiento = '.quote($filtro['id_area_conocimiento']);
		}
		if(isset($filtro['coordinador'])){
			$where[] = '(per.apellido ilike '.quote('%'.$filtro['coordinador'].'%')." OR per.nombres ilike ".quote('%'.$filtro['coordinador'].'%').")";
		}

		$sql = "SELECT $conv AS id_convocatoria,
						gr.*, 
						dep.nombre AS dependencia, 
						ac.nombre AS area_conocimiento,
						per.apellido||', '||per.nombres as coordinador 
				FROM sap_grupo AS gr 
				LEFT JOIN sap_dependencia AS dep ON dep.id = gr.id_dependencia
				LEFT JOIN sap_area_conocimiento AS ac ON ac.id = gr.id_area_conocimiento
				LEFT JOIN sap_personas AS per ON per.nro_documento = gr.nro_documento_coordinador
				WHERE EXISTS (SELECT * FROM sap_grupo_informe WHERE id_convocatoria = $conv AND id_grupo = gr.id_grupo)
				AND gr.fecha_inscripcion IS NOT NULL";
		$sql = sql_concatenar_where($sql,$where);
		return toba::db()->consultar($sql);
	}

	/**
	 * Retorna un valor booleano que indica si el grupo con el ID recibido, ya se encuentra inscripto como grupo en alguna convocatoria (Los grupos se inscriben solo una vex, despues solo presentan planes de trabajo)
	 * @param  integer $id_grupo ID del grupo
	 * @return boolean           
	 */
	function esta_inscripto($id_grupo)
	{
		$sql = "SELECT fecha_inscripcion FROM sap_grupo WHERE id_grupo = ".quote($id_grupo)." LIMIT 1";
		$resultado = toba::db()->consultar_fila($sql);
		return ( isset($resultado['fecha_inscripcion']) && $resultado['fecha_inscripcion'] );
	}

	function get_tipos_extension()
	{
		return toba::db()->consultar("SELECT * FROM sap_tipo_extension");
	}

	function get_tipos_publicacion()
	{
		return toba::db()->consultar("SELECT * FROM sap_tipo_publicacion");
	}
	function get_tipos_transferencia()
	{
		return toba::db()->consultar("SELECT * FROM sap_tipo_transferencia");
	}
	function get_tipos_form_rrhh()
	{
		return toba::db()->consultar("SELECT * FROM sap_tipo_form_rrhh");
	}
	function get_entidades_beca()
	{
		return toba::db()->consultar("SELECT * FROM sap_entidad_beca");
	}

	function get_categorias_grupo()
	{
		return toba::db()->consultar("SELECT * FROM sap_grupo_categoria");
	}

	function get_excepciones()
	{
		$sql = "SELECT per.nro_documento, 
				coalesce(per.apellido,'Sin apellido')||', '||
				coalesce(per.nombres,'Sin nombre') AS ayn, 
				ex.observaciones
				FROM sap_grupo_excepcion_dir AS ex
				LEFT JOIN sap_personas AS per ON per.nro_documento = ex.nro_documento";
		return toba::db()->consultar($sql);
	}

	function esta_exceptuado($nro_documento)
	{
		$sql = "SELECT * FROM sap_grupo_excepcion_dir WHERE nro_documento = ".quote($nro_documento)." LIMIT 1";
		$resultado = toba::db()->consultar_fila($sql);
		return $resultado;
	}


}
?>