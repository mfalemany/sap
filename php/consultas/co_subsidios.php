<?php 
class co_subsidios
{
	function abrir_solicitud($id_solicitud)
	{
		$sql = "UPDATE sap_subsidio_solicitud SET estado = 'A', hash_cierre = null WHERE id_solicitud = $id_solicitud";
		toba::db()->ejecutar($sql);
	}

	function get_subsidios($filtro = array())
	{
		$where = array();
		if (isset($filtro['nro_documento'])) {
			$where[] = "sub.nro_documento = ".quote($filtro['nro_documento']);
		}
		if (isset($filtro['solicitante'])) {
			$where[] = "per.apellido||per.nombres ILIKE ".quote("%".$filtro['solicitante']."%");
		}
		if (isset($filtro['rendido'])) {
			if($filtro['rendido'] == 'S'){
				$where[] = "exists (select * from sap_subsidio_otorgado where id_solicitud = sub.id_solicitud and rendido = 'S')";
			}
			if($filtro['rendido'] == 'N'){
				$where[] = "NOT exists (select * from sap_subsidio_otorgado where id_solicitud = sub.id_solicitud and rendido = 'S')";
			}
		}
		if (isset($filtro['id_solicitud'])) {
			$where[] = "sub.id_solicitud = ".quote($filtro['id_solicitud']);
		}
		if (isset($filtro['tipo_subsidio'])) {
			$where[] = "sub.tipo_subsidio ILIKE ".quote("%{$filtro['tipo_subsidio']}%");
		}
		if (isset($filtro['id_dependencia'])) {
			$where[] = "sub.id_dependencia = ".quote($filtro['id_dependencia']);
		}
		if (isset($filtro['convocatoria'])) {
			$where[] = "conv.id = ".$filtro['convocatoria'];
		}
		if (isset($filtro['lista_convocatorias'])) {
			//esta variable contendra una lista separada por comas de todos los IDs de convocatorias
			$convocatorias = "";
			
			foreach($filtro['lista_convocatorias'] as $convocatoria){
				if(strlen($convocatorias)){
					$convocatorias .= ",".$convocatoria['id'];	
				}else{
					$convocatorias .= $convocatoria['id'];	
				}
			}
			$where[] = "conv.id IN (".$convocatorias.")";
		}
		if (isset($filtro['estado'])) {
			$where[] = "sub.estado = ".quote($filtro['estado']);
		}

		
		$sql = "SELECT sub.id_solicitud,
					   sub.nro_documento,
					   per.apellido||', '||per.nombres as ayn,
					   per.cuil,
					   sub.codigo_proyecto as codigo,
					   dep.nombre as unidad_academica,
					   sub.tipo_subsidio,
					   conv.nombre as convocatoria,
					   sub.estado,
					   case sub.estado when 'C' then 'Cerrada' when 'A' then 'Abierta' when 'E' then 'Evaluada' else '' end as estado_desc,
					   case when (select count(*) from sap_subsidio_otorgado where id_solicitud = sub.id_solicitud) > 0 then 'SI' else 'NO' end as otorgado
				FROM sap_subsidio_solicitud AS sub
				LEFT JOIN sap_personas AS per ON per.nro_documento = sub.nro_documento
				LEFT JOIN sap_dependencia AS dep ON dep.id = sub.id_dependencia
				LEFT JOIN sap_convocatoria as conv on conv.id = sub.id_convocatoria";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		return toba::db()->consultar($sql);	
	}

	function get_solicitudes_evaluacion($filtro = array())
	{
		$where = array();
		if (isset($filtro['id_convocatoria'])) {
			$where[] = "sol.id_convocatoria = ".quote($filtro['id_convocatoria']);
		}
		if (isset($filtro['nro_documento'])) {
			$where[] = "sol.nro_documento = ".quote($filtro['nro_documento']);
		}
		if (isset($filtro['apellido'])) {
			$where[] = "per.apellido ILIKE ".quote("%".$filtro['apellido']."%");
		}
		if (isset($filtro['nombres'])) {
			$where[] = "per.nombres ILIKE ".quote("%".$filtro['nombres']."%");
		}
		if (isset($filtro['id_dependencia'])) {
			$where[] = "sol.id_dependencia = ".quote($filtro['id_dependencia']);
		}
		if (isset($filtro['tipo_subsidio'])) {
			$where[] = "sol.tipo_subsidio = ".quote($filtro['tipo_subsidio']);
		}
		if (isset($filtro['otorgado'])) {
			if($filtro['otorgado'] == 'S'){
				$where[] = "EXISTS (select * from sap_subsidio_otorgado WHERE id_solicitud = sol.id_solicitud)" ;	
			}else{
				$where[] = "NOT EXISTS (select * from sap_subsidio_otorgado WHERE id_solicitud = sol.id_solicitud)" ;
			}
			
		}
		$sql = "SELECT sol.id_solicitud,
					   sol.nro_documento,
					   per.apellido||', '||per.nombres as solicitante,
					   dep.nombre as dependencia,

					   case (select categoria 
					        from sap_cat_incentivos 
					        where nro_documento = sol.nro_documento 
					        and convocatoria = (select max(convocatoria) 
					                            from sap_cat_incentivos 
					                            where nro_documento = sol.nro_documento))
						   when 1 then 'Categoría I'
						   when 2 then 'Categoría II'
						   when 3 then 'Categoría III'
						   when 4 then 'Categoría IV'
						   when 5 then 'Categoría V'
						   else 'No categorizado' end as cat_incentivos,
						dep.nombre as unidad_academica,
						sol.tipo_subsidio,
						eva.apellido||', '||eva.nombres as evaluador,
						(SELECT SUM(puntaje) FROM
							(SELECT (cvar_solicitante + justif_relac_proyecto) as puntaje
							FROM sap_subsidio_eval_congreso 
							WHERE id_solicitud = sol.id_solicitud 
							UNION
							SELECT (cvar_solicitante + justif_relac_proyecto + plan_trabajo) as puntaje
							FROM sap_subsidio_eval_estadia
							WHERE id_solicitud = sol.id_solicitud
							) as union_puntajes
						) as puntaje,
						(select case when count(*) > 0 then 'SI' else '' end
						 from sap_subsidio_otorgado where id_solicitud = sol.id_solicitud) as otorgado
				FROM sap_subsidio_solicitud AS sol
				LEFT JOIN sap_dependencia as dep on dep.id = sol.id_dependencia
				LEFT JOIN sap_personas AS per ON per.nro_documento = sol.nro_documento
				LEFT JOIN sap_personas AS eva ON eva.nro_documento = sol.evaluador
				WHERE sol.estado IN ('C','E','D')
				ORDER BY puntaje DESC";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		//echo nl2br($sql);
		return toba::db()->consultar($sql);	
	}

	function get_resumen_informe($id_solicitud)
	{
		$sql = "SELECT tipo_subsidio FROM sap_subsidio_solicitud WHERE id_solicitud = ".$id_solicitud;
		$tipo = toba::db()->consultar_fila($sql);
		$tipo = $tipo['tipo_subsidio'];

		switch ($tipo) {
			case 'A':
				$sql = "SELECT conv.nombre as convocatoria, 
						   'A' as tipo_subsidio,
					       dep.nombre as dependencia,
					       proy.descripcion as proyecto,
					       per.nro_documento,
					       per.apellido,
					       per.nombres,
					       (select categoria 
					        from sap_cat_incentivos 
					        where nro_documento = per.nro_documento 
					        and convocatoria = (select max(convocatoria) 
					                            from sap_cat_incentivos 
					                            where nro_documento = per.nro_documento)) as cat_incentivos,
					        cong.nombre as nombre_congreso,
					        cong.lugar,
					        cong.fecha_desde,
					        cong.fecha_hasta,
					        cong.costo_inscripcion,
					        cong.costo_pasajes,
					        cong.costo_estadia,
					        cong.abstract,
					        (cong.costo_inscripcion+cong.costo_pasajes+cong.costo_estadia) as total_solicitado,
					        sub.hash_cierre
					FROM sap_subsidio_solicitud AS sub
					LEFT JOIN sap_proyectos as proy on proy.codigo = sub.codigo_proyecto
					LEFT JOIN sap_personas AS per ON per.nro_documento = sub.nro_documento
					LEFT JOIN sap_subsidio_congreso as cong on cong.id_solicitud = sub.id_solicitud
					LEFT JOIN sap_dependencia AS dep ON dep.id = sub.id_dependencia
					LEFT JOIN sap_convocatoria as conv on conv.id = sub.id_convocatoria
					where sub.id_solicitud = $id_solicitud";
				break;
			case 'B':
				$sql = "SELECT conv.nombre as convocatoria, 
						       'B' as tipo_subsidio,
						       dep.nombre as dependencia,
						       proy.descripcion as proyecto,
						       per.nro_documento,
						       per.apellido,
						       per.nombres,
						       (select categoria 
						        from sap_cat_incentivos 
						        where nro_documento = per.nro_documento 
						        and convocatoria = (select max(convocatoria) 
						                            from sap_cat_incentivos 
						                            where nro_documento = per.nro_documento)) as cat_incentivos,
						        est.institucion,
						        est.lugar,
						        est.fecha_desde,
						        est.fecha_hasta,
						        est.costo_pasaje as costo_pasajes,
						        est.costo_estadia,
						        est.plan_trabajo,
						        (est.costo_pasaje+est.costo_estadia) as total_solicitado,
						        sub.hash_cierre
						FROM sap_subsidio_solicitud AS sub
						LEFT JOIN sap_proyectos as proy on proy.codigo = sub.codigo_proyecto
						LEFT JOIN sap_personas AS per ON per.nro_documento = sub.nro_documento
						LEFT JOIN sap_subsidio_estadia as est on est.id_solicitud = sub.id_solicitud
						LEFT JOIN sap_dependencia AS dep ON dep.id = sub.id_dependencia
						LEFT JOIN sap_convocatoria as conv on conv.id = sub.id_convocatoria
						where sub.id_solicitud = $id_solicitud";
				break;
		}
		return toba::db()->consultar_fila($sql);
	}

	function get_documentacion_solicitud($id_solicitud)
	{
		$sql = "select id_documentacion, descripcion, archivo from sap_subsidio_docum_solicitud where id_solicitud = ".quote($id_solicitud);
		return toba::db()->consultar($sql);
	}

	function get_evaluacion($id_solicitud)
	{
		$subsidio = toba::db()->consultar_fila("SELECT tipo_subsidio AS tipo FROM sap_subsidio_solicitud WHERE id_solicitud = ".quote($id_solicitud)." LIMIT 1");
		
		switch ($subsidio['tipo']) {
			case 'A':
				$sql = "SELECT  CASE estado WHEN 'A' then 'Abierta' WHEN 'C' then 'Cerrada' WHEN 'D' then 'Desestimada' WHEN 'E' then 'Evaluada' end as estado,
						        CASE otorgado WHEN 'S' then 'Otorgado' WHEN 'N' then 'No otorgado' else 'En proceso de selección' end as otorgado,
						        sol.id_solicitud,
						        sol.nro_documento,
						        coalesce('$'||oto.monto,'----') as monto,
						        (eva.cvar_solicitante+eva.justif_relac_proyecto) as puntaje,
						        eva.observaciones
						FROM sap_subsidio_solicitud as sol
						RIGHT JOIN sap_subsidio_eval_congreso as eva on eva.id_solicitud = sol.id_solicitud
						LEFT JOIN sap_subsidio_otorgado as oto on oto.id_solicitud = sol.id_solicitud
						WHERE sol.estado <> 'A'
						AND sol.id_solicitud = ".quote($id_solicitud)." LIMIT 1";
				break;
			case 'B':
				$sql = "SELECT  CASE estado WHEN 'A' then 'Abierta' WHEN 'C' then 'Cerrada' WHEN 'D' then 'Desestimada' WHEN 'E' then 'Evaluada' end as estado,
						        CASE otorgado WHEN 'S' then 'Otorgado' WHEN 'N' then 'No otorgado' else 'En proceso de selección' end as otorgado,
						        sol.id_solicitud,
						        sol.nro_documento,
						        coalesce('$'||oto.monto,'----') as monto,
						        (eva.cvar_solicitante+eva.justif_relac_proyecto+plan_trabajo) as puntaje,
						        eva.observaciones
						FROM sap_subsidio_solicitud as sol
						RIGHT JOIN sap_subsidio_eval_estadia as eva on eva.id_solicitud = sol.id_solicitud
						LEFT JOIN sap_subsidio_otorgado as oto on oto.id_solicitud = sol.id_solicitud
						WHERE sol.estado <> 'A'
						AND sol.id_solicitud = ".quote($id_solicitud)." LIMIT 1";
				break;
		}

		return toba::db()->consultar_fila($sql);
	}


	function get_historial_solicitudes($nro_documento)
	{
		if(! $nro_documento){
			return array();
		}
		$sql = "select conv.nombre as convocatoria, 
					sol.tipo_subsidio, 
					coalesce((est.costo_pasaje+est.costo_estadia),(cong.costo_inscripcion+cong.costo_pasajes+cong.costo_estadia)) as total_solicitado,
					case sol.estado when 'A' then 'Abierta' when 'C' then 'Cerrada (sin evaluar)' when 'E' then 'Evaluada' end as estado,
					(select monto from sap_subsidio_otorgado where id_solicitud = sol.id_solicitud) as monto_otorgado,
					case (select rendido from sap_subsidio_otorgado where id_solicitud = sol.id_solicitud) 
						when 'N' then 'NO'
						when 'S' then 'SI' 
						end as rendido
				from sap_subsidio_solicitud as sol
				left join sap_convocatoria as conv on conv.id = sol.id_convocatoria
				left join sap_subsidio_congreso as cong on cong.id_solicitud = sol.id_solicitud
				left join sap_subsidio_estadia as est on est.id_solicitud = sol.id_solicitud
				where nro_documento = ".quote($nro_documento);
		return toba::db()->consultar($sql);
	}

	function rendir($id_solicitud)
	{
		return toba::db()->ejecutar("UPDATE sap_subsidio_otorgado SET rendido = 'S' WHERE id_solicitud = ".quote($id_solicitud));
	}

	function get_campo($campo,$tabla,$filtro)
	{
		$where = array();
		$sql = "SELECT $campo FROM $tabla";
		if(isset($filtro['id_solicitud'])){
			$where[] = 'id_solicitud = '.quote($filtro['id_solicitud']);
		}
		if(count($where)){
			$sql = sql_concatenar_where($sql,$where);
		}
		return toba::db()->consultar_fila($sql);
	}


	function recibio_subsidio($nro_documento, $id_convocatoria){
		
		$anteriores=toba::consulta_php('co_convocatorias')->get_convocatorias_anterior($id_convocatoria,'SUBSIDIOS');
		
		$sql = "SELECT * 
        FROM sap_subsidio_solicitud 
        WHERE nro_documento = '$nro_documento' 
        AND otorgado = 'S'
        AND id_convocatoria in ($anteriores)
        LIMIT 1";

        $resultado = toba::db()->consultar($sql);

        return (count($resultado));
	}
}

?>