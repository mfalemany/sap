<?php
class co_proyectos
{
	const CAMPOS = 'p.id,p.codigo,p.descripcion,p.fecha_desde,p.fecha_hasta,p.entidad_financiadora,p.director,p.nro_documento_dir,p.co_director,p.sub_director';
	
	  static function get_ProyectosEquipos($filtro=array()){
		$where = array();
		
		if(isset($filtro['codigo_proyecto'])){
			$where[] = "p.codigo = ".quote($filtro['codigo_proyecto']); 
		}
		if(isset($filtro['descripcion'])){
			$where[] = "p.descripcion ILIKE ".quote("%".$filtro['descripcion']."%"); 
		}
		if(isset($filtro['director'])){
			$where[] = "p.director ILIKE ".quote("%".$filtro['director']."%"); 
		}
			$sql = "SELECT " . self::CAMPOS . 
							",e.denominacion AS equipo_denominacion
								,e.coordinador,
								e.codigo AS codigo_equipo
			FROM sap_proyectos p 
			LEFT JOIN sap_equipo_proyecto ep ON ep.proyecto_id=p.id
			LEFT JOIN sap_equipo e ON e.id=ep.equipo_id
			WHERE 1=1
			ORDER BY p.codigo ASC;";//" GROUP BY e.id,d.nombre,a.nombre,e.usuario_id;";
		if(count($where)){
			$sql = sql_concatenar_where($sql,$where);
		}	
		return consultar_fuente($sql);
	}

	function get_proyectos_busqueda($criterio)
	{
		$sql = "SELECT id,codigo, descripcion 
				FROM sap_proyectos 
				WHERE descripcion ILIKE ".quote("%".$criterio."%")." 
				AND '".date('Y-m-d')."' BETWEEN fecha_desde AND fecha_hasta 
				AND entidad_financiadora ILIKE '%Sec. Gral.%'";
		return toba::db()->consultar($sql);
	}
	function get_proyectos_busqueda_todos($criterio)
	{
		$sql = "SELECT id,codigo, descripcion 
				FROM sap_proyectos 
				WHERE descripcion ILIKE ".quote("%".$criterio."%");
		return toba::db()->consultar($sql);
	}

	function get_descripcion_proyecto($codigo)
	{
		$sql = "SELECT descripcion FROM sap_proyectos WHERE codigo = ".quote($codigo)." LIMIT 1";
		$resultado = toba::db()->consultar_fila($sql);
		return $resultado['descripcion'];

	}
	function get_descripcion($id_proyecto){
		$sql = "SELECT descripcion FROM sap_proyectos WHERE id = ".quote($id_proyecto)." LIMIT 1";
		$resultado = toba::db()->consultar_fila($sql);
		return $resultado['descripcion'];
	}

	function get_participaciones_equipos($nro_documento)
	{
		$sql = "select distinct conv.nombre as convocatoria,
						e.codigo||': '||e.denominacion as equipo, 
						ei.condicion, 
						(select '(DNI: '||nro_documento||') '||nombres||' '||apellido from sap_personas where nro_documento = e.usuario_id) as cargado_por
				from sap_equipos_convocatorias as ec
				left join sap_equipo as e on e.id = ec.id_equipo
				left join sap_equipo_integrante as ei on ei.equipo_id = e.id
				left join sap_equipo_proyecto ep on ep.equipo_id = e.id
				left join sap_convocatoria as conv on conv.id = ec.id_convocatoria
				where replace(ei.dni,'.','') = ".quote($nro_documento);
		return toba::db()->consultar($sql);
	}

	function get_proyectos($filtro = NULL)
	{
		$sql = "SELECT id, codigo || ' - ' || descripcion AS cod_desc, codigo, descripcion FROM sap_proyectos";
		
		$where = array();
		
		if(is_array($filtro)){
			if(isset($filtro['dirigido_por'])){
				//Se mantiene la columna nro_documento_dir y nro_documento_codir por compatibilidad
				
				/* Hay que generar todos los registros en la tabla sap_proyecto_integrante con todas las personas que figuran en 
				* los campos nro_documento_dir, nro_documento_codir y nro_documento_subdir. Despues, identificar todos los 
				* módulos y consultas que hacen referencia a esos campos, y luego recien eliminarlas, para dar paso al nuevo 
				* esquema de dos tablas separadas 
				*/
				$where[] = "(nro_documento_dir = ".quote($filtro['dirigido_por'])." OR nro_documento_codir = ".quote($filtro['dirigido_por']).") OR (id IN (SELECT id_proyecto 
												FROM sap_proyecto_integrante 
												WHERE nro_documento = ".quote($filtro['dirigido_por'])."
												AND id_funcion IN (SELECT id_funcion 
																	FROM sap_proyecto_integrante_funcion 
																	WHERE identificador_perfil IN ('D','C')
																)
												)
											)";
			}
			
			if(count($where)){
				$sql = sql_concatenar_where($sql,$where);
			}
		}else{
			if(strlen(trim($filtro)) == 0 || $filtro == NULL){
				return array();
			}
			$filtro = quote("%{$filtro}%");
			$sql .= " WHERE descripcion ILIKE {$filtro} OR codigo ILIKE {$filtro} ;";	
		}
		return toba::db()->consultar($sql);
	}
		
	function get_proyecto($id_proyecto = NULL)
	{
		
		$sql = "SELECT id
					   ,codigo || ' - ' || descripcion as descripcion
						FROM sap_proyectos
						WHERE id = ".quote($id_proyecto);
		$resultado = toba::db()->consultar_fila($sql);
		return ($resultado) ? $resultado['descripcion'] : 'El proyecto no existe';
	}

	function externa_proyecto_descripcion($id_proyecto)
	{
	   
		$id = quote($id_proyecto);
		$sql = "SELECT codigo || ' - ' || descripcion AS v_proyecto_descripcion
						FROM sap_proyectos
						WHERE id = $id";
		$resultado = toba::db()->consultar_fila($sql);

		if (! empty($resultado))
		{
			return $resultado;
		}
	}
	
	
	function get_proyectoVigentes($id_proyecto = NULL)
	{
		if (! isset($id_proyecto))
		{
			return array();
		}
		
		$id = quote($id_proyecto);
		$sql = "SELECT id
					   ,codigo || ' - ' || descripcion as descripcion
						FROM sap_proyectos
						WHERE id = $id AND fecha_hasta <=current_date()";
		$resultado = toba::db()->consultar_fila($sql);

		if (! empty($resultado))
		{
			return $resultado['descripcion'];
		}
	}
	function get_proyectosByFiltros($where = ' 1 = 1 ')
	{
		$sql = "SELECT
						p.id,   
						p.codigo,
						p.descripcion,
						case 
							when char_length(p.descripcion) > 65 then substring(p.descripcion,0,65)||'(...)'
							else p.descripcion 
							end AS descripcion_corta,
						codigo || ' - ' || descripcion as codigo_descripcion,
						p.fecha_desde,
						p.fecha_hasta,
						case 
							when char_length(p.entidad_financiadora) > 40 then substring(p.entidad_financiadora,0,40)||'(...)'
							else p.entidad_financiadora 
							end AS entidad_financiadora,
						p.director,
						p.co_director,
						p.sub_director,
						p.archivo_proyecto,
						p.tipo
					FROM
						sap_proyectos p
					WHERE {$where};";
							 
			return consultar_fuente($sql);
	}


	function get_proyectos_evaluar($filtro = array()) 
	{
		$where = array();
		if(isset($filtro['tiempo_inicio'])){
			
			if($filtro['tiempo_inicio'] == 'nuevos'){
				$from = "SELECT proy.id, proy.descripcion, proy.codigo, proy.fecha_desde, proy.fecha_hasta, proy.nro_documento_dir, proy.tipo, proy.sap_area_conocimiento_id
				FROM sap_proyectos AS proy
				    --La fecha actual tiene que estar entre la fecha de presentación y el 30 de abril del año siguiente
				    WHERE current_date 
				       	between proy.fecha_desde and date(concat(extract(year from proy.fecha_desde),'-04-30'))
				    UNION
				    SELECT 0 AS id, prog.denominacion AS descripcion, prog.codigo, prog.fecha_desde, prog.fecha_hasta, prog.nro_documento_dir, 'C' AS tipo, null as sap_area_conocimiento_id
				    FROM sap_programas AS prog
				    --La fecha actual tiene que estar entre la fecha de presentación y el 30 de abril del año siguiente
				    WHERE current_date 
				    	between prog.fecha_desde and date(concat(extract(year from prog.fecha_desde),'-04-30'))";

				$evaluado_sql = "SELECT count(*) AS evaluaciones
						FROM sap_proy_pi_eval
						WHERE id_proyecto = proy.id
						AND nro_documento_evaluador = ".quote(toba::usuario()->get_id())."
						UNION
						SELECT count(*) AS evaluaciones
						FROM sap_proy_pdts_eval
						WHERE id_proyecto = proy.id
						and nro_documento_evaluador = ".quote(toba::usuario()->get_id())."
				        UNION
				        SELECT count(*) AS evaluaciones
						FROM sap_programa_eval AS eval
						LEFT JOIN sap_programas AS prog ON prog.codigo = eval.id_programa
						WHERE eval.id_programa = proy.codigo
						and nro_documento_evaluador = ".quote(toba::usuario()->get_id())."
						) AS tmp";
			}
			if($filtro['tiempo_inicio'] == 'anteriores'){
				$from = "SELECT proy.id, proy.descripcion, proy.codigo, proy.fecha_desde, proy.fecha_hasta, proy.nro_documento_dir, proy.tipo, proy.sap_area_conocimiento_id
				    FROM sap_proyectos_pi_informe AS inf
				    LEFT JOIN sap_proyectos_pi AS pi ON pi.id_proyecto = inf.id_proyecto
				    LEFT JOIN sap_proyectos AS proy ON proy.id = pi.id_proyecto 
					--La fecha actual tiene que estar entre la fecha de presentación y el 30 de abril del año siguiente
				    WHERE current_date 
				    	between inf.fecha_presentacion and date(concat(extract(year from current_date)+1,'-04-30'))
				    UNION
				    SELECT proy.id, proy.descripcion, proy.codigo, proy.fecha_desde, proy.fecha_hasta, proy.nro_documento_dir, proy.tipo, proy.sap_area_conocimiento_id	
				    FROM sap_proyectos_pdts_informe AS inf
				    LEFT JOIN sap_proyectos_pdts AS pdts ON pdts.id_proyecto = inf.id_proyecto
				    LEFT JOIN sap_proyectos AS proy ON proy.id = pdts.id_proyecto 
					--La fecha actual tiene que estar entre la fecha de presentación y el 30 de abril del año siguiente
				    WHERE current_date 
				    	between inf.fecha_presentacion and date(concat(extract(year from current_date)+1,'-04-30'))
				    UNION
				    SELECT 0 AS id, prog.denominacion AS descripcion, prog.codigo, prog.fecha_desde, prog.fecha_hasta, prog.nro_documento_dir, 'C' AS tipo, null as sap_area_conocimiento_id 
				    FROM sap_programa_informe AS inf
				    LEFT JOIN sap_programas AS prog ON prog.codigo = inf.id_programa
				    --La fecha actual tiene que estar entre la fecha de presentación y el 30 de abril del año siguiente
				    WHERE current_date 
				    	between inf.fecha_presentacion and date(concat(extract(year from current_date)+1,'-04-30'))";

				$evaluado_sql = "SELECT count(*) AS evaluaciones
						FROM sap_proy_pi_informe_eval as eval
						LEFT JOIN sap_proyectos_pi_informe as inf ON inf.id_informe = eval.id_informe
						WHERE inf.id_proyecto = proy.id
						AND nro_documento_evaluador = ".quote(toba::usuario()->get_id())."
						--Este ultimo AND verifica si ya evaluó el último informe presentado (y no alguno anterior)
						AND inf.id_informe = (select MAX(id_informe) FROM sap_proyectos_pi_informe WHERE id_proyecto = proy.id)
						UNION
						SELECT count(*) AS evaluaciones
						FROM sap_proy_pdts_informe_eval as eval
						LEFT JOIN sap_proyectos_pdts_informe as inf ON inf.id_informe = eval.id_informe
						WHERE inf.id_proyecto = proy.id
						AND nro_documento_evaluador = ".quote(toba::usuario()->get_id())."
						--Este ultimo AND verifica si ya evaluó el último informe presentado (y no alguno anterior)
						AND inf.id_informe = (select MAX(id_informe) FROM sap_proyectos_pdts_informe WHERE id_proyecto = proy.id)
				        UNION
				        SELECT count(*) AS evaluaciones
						FROM sap_programa_informe_eval as eval
						LEFT JOIN sap_programa_informe as inf ON inf.id_informe = eval.id_informe
						WHERE inf.id_programa = proy.codigo
						and nro_documento_evaluador = ".quote(toba::usuario()->get_id())."
						--Este ultimo AND verifica si ya evaluó el último informe presentado (y no alguno anterior)
						AND inf.id_informe = (select MAX(id_informe) FROM sap_programa_informe WHERE id_programa = proy.codigo)
						) AS tmp";
			}
		}

		$sql = "SELECT proy.id, 
					codigo, 
					substr(proy.descripcion,1,100)||'(...)' AS descripcion, 
					nro_documento_dir, 
					fecha_desde, 
					fecha_hasta,
					tipo,
					ac.nombre AS area_conocimiento,
					CASE proy.tipo WHEN '0' THEN 'PI' WHEN 'D' THEN 'PDTS' WHEN 'C' THEN 'Programa' END AS tipo_descripcion,
					(SELECT nombre FROM sap_dependencia WHERE letra_codigo_proyectos = substr(codigo,3,1) limit 1) AS unidad_academica,
					(SELECT CASE WHEN sum(evaluaciones) > 0 THEN 'S' ELSE 'N' END FROM ($evaluado_sql) AS evaluado,
					(SELECT apellido||', '||nombres FROM sap_personas WHERE nro_documento = proy.nro_documento_dir) AS director
				FROM ($from) AS proy
				LEFT JOIN sap_area_conocimiento AS ac ON ac.id = sap_area_conocimiento_id
				WHERE tipo <> '9'
				ORDER BY codigo ASC";
		if(isset($filtro['tipo'])){
			$where[] = "tipo = ".quote($filtro['tipo']);
		}
		if(isset($filtro['codigo'])){
			$where[] = "codigo ilike ".quote($filtro['codigo']);
		}
		if(count($where)){
			$sql = sql_concatenar_where($sql,$where);
		}
		//echo nl2br($sql);
		return toba::db()->consultar($sql);
	}

	function get_detalles_director($dni)
	{
		$sql = "SELECT per.apellido||', '||per.nombres AS ayn,
					per.nro_documento AS dni,
					CASE cat.categoria 
						WHEN 1 THEN 'Categoría I'
						WHEN 2 THEN 'Categoría II'
						WHEN 3 THEN 'Categoría III'
						WHEN 4 THEN 'Categoría IV'
						WHEN 5 THEN 'Categoría V'
						ELSE 'No Categorizado'
					END AS cat,
					per.archivo_cvar
				FROM sap_personas AS per
				LEFT JOIN sap_cat_incentivos AS cat ON cat.nro_documento = per.nro_documento
					AND cat.convocatoria = (SELECT MAX(convocatoria) FROM sap_cat_incentivos WHERE nro_documento = per.nro_documento)
				WHERE per.nro_documento = ".quote($dni);
		return toba::db()->consultar_fila($sql);
	}

	function get_informes_proyecto($filtro = array())
	{
		$where = array();
		if(isset($filtro['tipo']) && isset($filtro['id'])){
			switch ($filtro['tipo']) {
				case '0':
					//ARMAR LAS CONSULTAS QUE LLENEN EL CUADRO DE SELECCION DE INFORME.
					$sql = "SELECT inf.id_informe, 
									pr.id, pr.codigo,
									pr.tipo,
									'PI' AS tipo_desc,
									pr.descripcion,
									inf.fecha_presentacion
							FROM sap_proyectos_pi AS pri
							LEFT JOIN sap_proyectos AS pr ON pr.id = pri.id_proyecto
							LEFT JOIN sap_proyectos_pi_informe AS inf ON inf.id_proyecto = pri.id_proyecto";
					$where[] = 'pr.id = '.quote($filtro['id']);
					break;
				case 'D':
					$sql = "SELECT inf.id_informe, 
									pr.id, pr.codigo,
									pr.tipo,
									'PDTS' AS tipo_desc,
									pr.descripcion,
									inf.fecha_presentacion
							FROM sap_proyectos_pdts AS prd
							LEFT JOIN sap_proyectos AS pr ON pr.id = prd.id_proyecto
							LEFT JOIN sap_proyectos_pdts_informe AS inf ON inf.id_proyecto = prd.id_proyecto";
					$where[] = 'pr.id = '.quote($filtro['id']);
					break;
				case 'C':
					$sql = "SELECT inf.id_informe, 
									0 as id, 
									pr.codigo, 
									'C' AS tipo,
									'Programa' AS tipo_desc, 
									pr.denominacion AS descripcion,
									inf.fecha_presentacion
							FROM sap_programa_informe AS inf
							LEFT JOIN sap_programas AS pr ON inf.id_programa = pr.codigo";
							
					$where[] = 'pr.codigo = '.quote($filtro['id']);
					break;
			}
		}
		if($sql){
			$sql .= " ORDER BY inf.fecha_presentacion DESC"; 
			return toba::db()->consultar(sql_concatenar_where($sql,$where));	
		}
	}

	/* SE UTILIZA PARA EL REPORTE DE EVALUACIONES REALIZADAS (PDF) */
	function get_evaluaciones_realizadas($filtro = array())
	{
		if(!isset($filtro['nro_documento_evaluador'])){
			$filtro['nro_documento_evaluador'] = toba::usuario()->get_id();
		}
		$where = array();
		//Esta consulta es muy larga, pero es una union de seis consultas cortas
		$sql[] = "
					/* EVALUACION PI*/
					select 
				    pr.codigo as codigo,
				    case when (length(pr.descripcion) > 75) then substr(pr.descripcion,1,75)||'(...)' else pr.descripcion end as descripcion, 
				    case(result_final_evaluacion) 
				    	when 'N' then 'No aprobado' 
				    	when 'B' then 'Bueno' 
				    	when 'M' then 'Muy Bueno' 
				    	when 'E' then 'Excelente'  
				    	else result_final_evaluacion end as evaluacion,
				    fecha_eval,
				    'PI' as tipo
				from sap_proy_pi_eval as ev
				left join sap_proyectos as pr on pr.id = ev.id_proyecto
				where nro_documento_evaluador = ".quote($filtro['nro_documento_evaluador'])."
				--se obtienen todas las evaluaciones del usuario actual, de la ultima convocatoria
				and id_proyecto in (
					select id from sap_proyectos where convocatoria_anio = (select max(convocatoria_anio) from sap_proyectos)
				)";
		$sql[] = "
				/* EVALUACION PI INFORME */
				select 
				    pr.codigo as codigo,
				    case when (length(pr.descripcion) > 75) then substr(pr.descripcion,1,75)||'(...)' else pr.descripcion end as descripcion, 
				    case(satisfactorio) when 'S' then 'Satisfactorio' else 'No satisfactorio' end as evaluacion,
				    fecha_eval,
				    'Informe de PI' as tipo
				from sap_proy_pi_informe_eval ev
				left join sap_proyectos_pi_informe as inf on inf.id_informe = ev.id_informe
				left join sap_proyectos as pr on pr.id = inf.id_proyecto
				where nro_documento_evaluador = ".quote($filtro['nro_documento_evaluador']);
		
		$sql[] = "
				/* EVALUACION PDTS */
				select 
				    pr.codigo as codigo,
				    case when (length(pr.descripcion) > 75) then substr(pr.descripcion,1,75)||'(...)' else pr.descripcion end as descripcion, 
				    case(result_final_evaluacion) 
				        when 'E' then 'Excelente'
				        when 'M' then 'Muy bueno'
				        when 'B' then 'Bueno'
				        when 'N' then 'No aprobado'
				        else result_final_evaluacion
				    end as evaluacion,
				    fecha_eval,
				    'PDTS' as tipo
				from sap_proy_pdts_eval as ev
				left join sap_proyectos as pr on pr.id = ev.id_proyecto
				where nro_documento_evaluador = ".quote($filtro['nro_documento_evaluador'])."
				--se obtienen todas las evaluaciones del usuario actual, de la ultima convocatoria
				and id_proyecto in (
					select id from sap_proyectos where convocatoria_anio = (select max(convocatoria_anio) from sap_proyectos))";

		$sql[] = "
				/* EVALUACION INFORME PDTS */
				select 
				    pr.codigo as codigo,
				    case when (length(pr.descripcion) > 75) then substr(pr.descripcion,1,75)||'(...)' else pr.descripcion end as descripcion, 
				    case(satisfactorio) when 'S' then 'Satisfactorio' else 'No satisfactorio' end as evaluacion,
				    fecha_eval,
				    'Informe de PDTS' as tipo
				from sap_proy_pdts_informe_eval ev
				left join sap_proyectos_pdts_informe as inf on inf.id_informe = ev.id_informe
				left join sap_proyectos as pr on pr.id = inf.id_proyecto
				where nro_documento_evaluador = ".quote($filtro['nro_documento_evaluador']);

		$sql[] = "
				/* EVALUACION PROGRAMA */
				select 
				    prog.codigo as codigo,
				    case when (length(prog.denominacion) > 75) 
				        then substr(prog.denominacion,1,75)||'(...)' 
				        else prog.denominacion end as descripcion, 
				    case(result_final_evaluacion) 
				        when 'E' then 'Excelente'
				        when 'M' then 'Muy bueno'
				        when 'B' then 'Bueno'
				        when 'N' then 'No aprobado'
				        else result_final_evaluacion
				    end as evaluacion,
				    fecha_eval,
				    'Programa' as tipo
				from sap_programa_eval as ev
				left join sap_programas as prog on prog.codigo = ev.id_programa
				where ev.nro_documento_evaluador = ".quote($filtro['nro_documento_evaluador'])."
				--se obtienen todas las evaluaciones del usuario actual, de la ultima convocatoria
				and ev.id_programa in (select codigo from sap_programas where convocatoria_anio = (select max(convocatoria_anio) from sap_programas))";

		$sql[] = "
				/* EVALUACION INFORME PROGRAMA */
				select 
				    prog.codigo as codigo,
				    case when (length(prog.denominacion) > 75) 
				        then substr(prog.denominacion,1,75)||'(...)' 
				        else prog.denominacion end as descripcion, 
				    case(satisfactorio) when 'S' then 'Satisfactorio' else 'No satisfactorio' end as evaluacion,
				    fecha_eval,
				    'Informe de Programa' as tipo
				from sap_programa_informe_eval as ev
				left join sap_programa_informe as inf on inf.id_informe = ev.id_informe
				left join sap_programas as prog on prog.codigo = inf.id_programa
				where ev.nro_documento_evaluador = ".quote($filtro['nro_documento_evaluador']);
				
		
		$sql_completo = implode(' union ',$sql);
		
		//echo nl2br($sql_completo);die;		
		return toba::db()->consultar($sql_completo);
	}

	function get_detalle_evaluaciones_realizadas($filtro)
	{
		if(!isset($filtro['id_proyecto'])){
			return array();
		}
		//obtengo los detalles del proyecto
		$proyecto = toba::consulta_php('co_proyectos')->get_proyectosByFiltros('id = '.$filtro['id_proyecto']);
		$proyecto = $proyecto[0];
		if($proyecto['tipo'] == '9'){
			return array(); //No se evaluan proyectos externos
		}
		$tipos = array('0'=>'pi','D'=>'pdts'); 
		//Si es la presentacion inicial tengo que buscar en una tabla, si es un informe en otra
		if($filtro['fecha_presentacion'] == $proyecto['fecha_desde']){
			$sql = "SELECT eval.*, proy.tipo, 'inicial' AS instancia, per.nombres||' '||per.apellido AS evaluador 
					FROM sap_proy_{$tipos[$proyecto['tipo']]}_eval AS eval
					LEFT JOIN sap_proyectos AS proy ON proy.id = eval.id_proyecto
					LEFT JOIN sap_personas AS per ON per.nro_documento = eval.nro_documento_evaluador
					WHERE id_proyecto = ".quote($filtro['id_proyecto']);
		}else{
			$sql = "SELECT eval.*,proy.tipo, 'informe' AS instancia, per.nombres||' '||per.apellido AS evaluador
					FROM sap_proyectos_{$tipos[$proyecto['tipo']]}_informe AS inf
					LEFT JOIN sap_proy_{$tipos[$proyecto['tipo']]}_informe_eval AS eval USING (id_informe)
					LEFT JOIN sap_proyectos AS proy ON proy.id = inf.id_proyecto
					LEFT JOIN sap_personas AS per ON per.nro_documento = eval.nro_documento_evaluador
					WHERE inf.id_informe = ".quote($filtro['id_informe'])." AND inf.fecha_presentacion = ".quote($filtro['fecha_presentacion']);

		}
		return toba::db()->consultar($sql);
	}



	function get_campo($campos,$filtro = array())
	{
		foreach($filtro as $campo => $valor){
			$where[] = $campo." = ".quote($valor);
		}
		$sql = "SELECT ".implode(',',$campos)." FROM sap_proyectos";
		if(count($where)){
			$sql = sql_concatenar_where($sql,$where);
		}
		return toba::db()->consultar($sql);
	}

	function es_vigente($id_proyecto)
	{
		$resultado = $this->get_campo(array('fecha_hasta'),array('id'=>$id_proyecto));
		if(count($resultado)){
			return ($resultado[0]['fecha_hasta'] >= date('Y-m-d'));
		}
	}

	function dirige_proyectos($nro_documento)
	{
		$sql = "SELECT COUNT(*) AS cantidad FROM sap_proyectos WHERE (nro_documento_dir = ".quote($nro_documento)." OR nro_documento_codir = ".quote($nro_documento).")";
		$resultado = toba::db()->consultar_fila($sql);
		return ($resultado['cantidad'] > 0);
	}

	/**
	 * Retorna todas las instancias de evaluación que se presentaron para un proyecto especifico. Retorna tanto la instancia de presentación inicial, como las distintas instancias de avance.
	 * @return array Array con las instancias de avances presentados por el proyecto
	 */
	function presentaciones_evaluacion_proyecto($id_proyecto){
		$sql = "SELECT *, extract(year from fecha_desde) as anio_desde, 
						  extract(year from fecha_presentacion) as anio_presentacion,
						  extract(year from fecha_hasta) as anio_hasta
				FROM (
					SELECT fecha_desde AS fecha_presentacion, id as id_proyecto,0 as id_informe
					FROM sap_proyectos
					WHERE id = ".quote($id_proyecto)."
					UNION
					SELECT fecha_presentacion, id_proyecto,id_informe
					FROM sap_proyectos_pi_informe
					WHERE id_proyecto = ".quote($id_proyecto)."
					UNION
					SELECT fecha_presentacion, id_proyecto,id_informe
					FROM sap_proyectos_pdts_informe
					WHERE id_proyecto = ".quote($id_proyecto)."
					) AS instancias
				LEFT JOIN sap_proyectos AS proy ON proy.id = instancias.id_proyecto";
		
		$presentaciones = toba::db()->consultar($sql);
		foreach ($presentaciones as &$presentacion) {
			$presentacion['instancia'] = $this->determinar_instancia_evaluacion($presentacion);	
		}
		return $presentaciones;
	}

	private function determinar_instancia_evaluacion($presentacion)
	{
		$dif = $presentacion['anio_presentacion'] - $presentacion['anio_desde'];
		$duracion = $presentacion['anio_hasta'] - $presentacion['anio_desde'];
		switch ($dif) {
			case 0:
				return 'Inicial';
				break;
			case 1:
				return 'Primer Seguimiento';
				break;
			case 2:
				return 'Primer avance';
				break;
			case 3:
				return 'Segundo Seguimiento';
				break;
			case 4:
				return ($presentacion['anio_presentacion'] == ($presentacion['anio_hasta']+1) ) ? 'Informe final' : 'Segundo Avance';
				break;
			case ($dif > 4):
				if($presentacion['anio_presentacion'] == ($presentacion['anio_hasta']+1) ){
					return 'Informe final';
				}else{
					if( ($dif % 2) > 0){
						return 'Seguimiento';
					}else{
						return 'Avance';
					}
				}
				break;
			default:
				return 'No determinada';
				break;
		}
	}

	// =============================================================================
	// ====================== AREAS DE PROYECTOS ===================================
	// =============================================================================
	function get_areas_proyectos()
	{
		return toba::db()->consultar("SELECT id_area, area FROM sap_proyecto_area");
	}
	function get_area($filtro)
	{
		$where = array();
		$sql = "SELECT id_area
				FROM sap_proyecto_subarea";
		if(isset($filtro['id_proyecto'])){
			$where[] = "id_subarea = (SELECT id_subarea 
									  FROM sap_proyectos 
									  WHERE id = ".quote($filtro['id_proyecto']).")";
		}
		if(isset($filtro['id_subarea'])){
			$where[] = 'id_subarea = '.quote($filtro['id_subarea']);
		}
		if(count($where)){
			$sql = sql_concatenar_where($sql,$where);
		}
				
		$resultado = toba::db()->consultar_fila($sql);	
		return $resultado['id_area'];
	}
	function get_subareas_proyecto($id_area = NULL)
	{
		$sql = "SELECT id_area, id_subarea, subarea FROM sap_proyecto_subarea";
		$sql .= ($id_area) ? " WHERE id_area = ".quote($id_area) : "";
		return toba::db()->consultar($sql);
	}

	

	// ====================== OBJETIVOS SOCIO-ECONÓMICOS ===========================
	function get_objetivos_socioeconomicos()
	{
		return toba::db()->consultar("SELECT * FROM sap_objetivo_socioeconomico");
	}
	// ====================== RUBROS DE NECESIDADES PRESUPUESTARIAS ================
	function get_presupuesto_rubros()
	{
		return toba::db()->consultar("SELECT * FROM sap_proy_presupuesto_rubro");
	}
	// ========================= FUNCIÓN DE LOS INTEGRANTES ========================
	function get_funciones_integrantes()
	{
		return toba::db()->consultar('SELECT * FROM sap_proyecto_integrante_funcion WHERE activo = \'1\' ORDER BY funcion ');
	}
	// ========================= PERFILES ========================
	function get_funcion($identificador_perfil)
	{
		return toba::db()->consultar_fila("SELECT id_funcion,funcion FROM sap_proyecto_integrante_funcion WHERE identificador_perfil = ".quote($identificador_perfil));
	}
	// ========================= TABLAS AUXILIARES ========================
	function eliminar_auxiliares($id_proyecto)
	{
		toba::db()->ejecutar("DELETE FROM sap_proyecto_tesista WHERE id_proyecto = ".quote($id_proyecto));
		toba::db()->ejecutar("DELETE FROM sap_proyecto_becario WHERE id_proyecto = ".quote($id_proyecto));
		toba::db()->ejecutar("DELETE FROM sap_proyecto_alumno WHERE id_proyecto = ".quote($id_proyecto));
		toba::db()->ejecutar("DELETE FROM sap_proyecto_inv_externo WHERE id_proyecto = ".quote($id_proyecto));
		toba::db()->ejecutar("DELETE FROM sap_proyecto_apoyo WHERE id_proyecto = ".quote($id_proyecto));
	}


	function get_miembros($filtro,$tabla)
	{
		$where = array();
		if(isset($filtro['id_proyecto'])){
			$where[] = "id_proyecto = ".quote($filtro['id_proyecto']);
		}
		$sql = "SELECT * FROM $tabla";
		if(count($where)){
			$sql = sql_concatenar_where($sql,$where);
		}
		$resultado = toba::db()->consultar($sql);
		foreach($resultado as $miembro){
			$r[$miembro['nro_documento']] = $miembro; 
		}
		return isset($r) ? $r : array();
	}

}
?>
