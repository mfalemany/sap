<?php 
class co_personas{

	/**
	 * Se utiliza para obtener un resumen de los datos relevantes para una persona que es director. Por ejemplo, Apellido y nombres completo, categor? de incentivos, cargos.
	 * @param  string $nro_documento N?mero de documento de la persona que se busca
	 * @return array                En caso de encontrar a la persona buscada, retorna un array con todos los detalles. En caso contrario, retorna un array vac?
	 */
	function get_detalles_director($datos)
	{
		$nro_documento = str_replace('.','',$datos['nro_documento']);
		$resultado = $this->buscar_persona($nro_documento);

		$sql = "SELECT cd.descripcion as cargo_descripcion, 
						dep.nombre as dependencia_desc, 
						CASE WHEN current_date BETWEEN cp.fecha_desde AND cp.fecha_hasta THEN 'S'
						ELSE 'N' END AS vigente,
						cp.* 
				FROM sap_cargos_persona AS cp
				LEFT JOIN sap_cargos_descripcion AS cd ON cd.cargo = cp.cargo
				LEFT JOIN sap_dependencia AS dep ON dep.sigla_mapuche = cp.dependencia
				WHERE cp.nro_documento = ".quote($nro_documento);
		$resultado['cargos'] = toba::db()->consultar($sql);
		$resultado['campo'] = $datos['campo'];
		return $resultado;
	}

	function buscar_en_ws($nro_documento)
	{
		$nro_documento = str_replace('.','',$nro_documento);
		$cliente = toba::servicio_web_rest('ws_unne')->guzzle();
		$response = $cliente->get('agentes/'.$nro_documento.'/datoscomedor');
		$datos = rest_decode($response->json());

		$persona = array();
		if(array_key_exists('MAPUCHE',$datos)){
			//obtengo los datos de mapuche (si existen)
			$tmp = $datos['MAPUCHE'][0];
			//separo el apellido y el nombre
			$ayn = explode(',',$tmp['ayn']);
			//asigno todos los valores
			$persona['nro_documento'] = $nro_documento;
			$persona['apellido'] = $ayn[0];
			$persona['nombres']  = $ayn[1];
			$persona['mail'] = NULL; 
			$persona['cuil'] =  $tmp['cuit'];
			$persona['sexo'] = NULL;
			$persona['fecha_nac'] = NULL;
		}
		if(array_key_exists('GUARANI',$datos)){
			//obtengo los datos de mapuche (si existen)
			$tmp = $datos['GUARANI'][0];
			
			//asigno todos los valores
			$persona['nro_documento'] = $nro_documento;
			$persona['apellido'] = $tmp['APELLIDO'];
			$persona['nombres']  = $tmp['NOMBRES'];
			$persona['mail'] = $tmp['EMAIL'];
			$persona['fecha_nac'] = $tmp['FECHA_NAC'];
			$persona['cuil'] = (isset($persona['cuil']) && $persona['cuil']) ? $persona['cuil'] : 'XX-'.$nro_documento.'-X';
			$persona['sexo'] = $tmp['SEXO'];
		}


		if(isset($persona['apellido'])){
			$persona['apellido'] = mb_convert_encoding($persona['apellido'], "LATIN1", "auto");
			$persona['nombres'] = mb_convert_encoding($persona['nombres'], "LATIN1", "auto");

			$persona['apellido'] = ucwords(strtolower($persona['apellido'])) ;
			$persona['nombres'] = ucwords(strtolower($persona['nombres'])) ;
		}

		return $persona;	
	}

	function existe_persona($nro_documento)
	{
		$nro_documento = str_replace('.','',$nro_documento);
		if(!$nro_documento){
			return false;
		}
		if(!$this->existe_en_local($nro_documento)){
			$persona = $this->buscar_en_ws($nro_documento);
			if(count($persona)){
				return ($this->nueva_persona($persona)) ? true : false;
			}else{
				return false;
			}
		}else{
			return true;
		}
	}

	function actualizar_datos($nro_documento)
	{
		//Si no se recibe un DNI no se hace nada
		if(!$nro_documento){
			return;
		}
		$nro_documento = str_replace('.','',$nro_documento);
		//Busco los datos en fuentes externas
		$persona = $this->buscar_en_ws($nro_documento);
		//si encuentro algo, intento actualizar
		if(count($persona)){
			//Esta variable se modifica a TRUE solamente si se encuentra algun campo no vac?
			$actualizar = FALSE;
			//Consulta de actualizacion
			$sql = "UPDATE sap_personas SET ";
			
			//no se actualizan el apellido y el/los nombres			
			if(isset($persona['apellido'])){
				unset($persona['apellido']);
			}
			if(isset($persona['nombres'])){
				unset($persona['nombres']);
			}

			//Recorro todos los campos obtenidos del WS
			foreach($persona as $campo => $valor){
				//Si es no nulo, lo agrego a la consulta
				if($valor){
					if(preg_match('/fecha/',$campo)){
						$tmp = new Datetime(str_replace('/','-',$valor));
						$valor = $tmp->format('Y-m-d');

					}
					$actualizar = TRUE;
					$sql .= $campo." = ".quote($valor).",";
				}
			}
			//Si se agreg?alg?n campo a la consulta, se ejecuta
			if($actualizar){
				//quito la ?ltima coma agregada entre los campos
				$sql = substr($sql,0,strlen($sql)-1);
				//Condidici? de actualizaci?
				$sql .= " WHERE nro_documento = ".quote($nro_documento);
				
				toba::db()->ejecutar($sql);
			}
		}
	}

	function get_personas_ef_editable($patron){
		$sql = "SELECT per.nro_documento, 
				coalesce(per.apellido,'Sin apellido')||', '||
				coalesce(per.nombres,'Sin nombre') AS ayn
				FROM sap_personas AS per
				WHERE per.apellido ILIKE ".quote('%'.$patron.'%')."
				OR per.nombres ILIKE ".quote('%'.$patron.'%');
		return toba::db()->consultar($sql);
	}

	function get_personas($filtro = array())
	{
		$where = array();
		if (isset($filtro['nro_documento'])) {
			$filtro['nro_documento'] = str_replace('.','',$filtro['nro_documento']);
			if( ! $this->existe_persona($filtro['nro_documento'])){
				return array();
			}
			$where[] = "per.nro_documento = ".quote($filtro['nro_documento']);
		}
		if (isset($filtro['categoria'])) {
			$where[] = "cat.categoria = ".quote($filtro['categoria']);
		}
		if (isset($filtro['convocatoria'])) {
			$where[] = "cat.convocatoria = ".quote($filtro['convocatoria']);
		}

	   
		if (isset($filtro['apellido'])) {
			$where[] = "quitar_acentos(per.apellido) ILIKE ".quote("%{$filtro['apellido']}%");
		}
	   if (isset($filtro['nombres'])) {
			$where[] = "quitar_acentos(per.nombres) ILIKE ".quote("%{$filtro['nombres']}%");
		}
		
		$sql = "SELECT per.nro_documento,
						per.cuil,
						per.apellido,
						per.nombres,
						per.fecha_nac, 
						per.apellido||','||per.nombres as ayn,
						cat.categoria,
						cat.convocatoria,
						case cat.categoria 
							when 1 then 'Categoría I'
							when 2 then 'Categoría II'
							when 3 then 'Categoría III'
							when 4 then 'Categoría IV'
							when 5 then 'Categoría V'
							else 'No categorizado'
						end as categoria_desc,
						per.archivo_cvar,
						per.mail,
						(SELECT dis.disciplina from sap_disciplinas as dis
							where dis.id_disciplina = per.id_disciplina) as disciplina
				FROM sap_personas AS per
				LEFT JOIN sap_cat_incentivos AS cat ON cat.nro_documento = per.nro_documento
													AND cat.id = (SELECT max(id)
																  FROM sap_cat_incentivos
																  WHERE nro_documento = per.nro_documento)";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		return toba::db()->consultar($sql); 
	}

	function get_ayn($nro_documento)
	{
		$nro_documento = str_replace('.','',$nro_documento);
		if( ! $this->existe_en_local($nro_documento)){
			$datos = $this->buscar_persona($nro_documento);
			if($datos){
				$this->nueva_persona($datos);
			}else{
				return "";
			}
		}
		//Buscar en LOCAL
		//Si no existe, buscar en WS
		$sql = "SELECT apellido||', '||nombres AS ayn FROM sap_personas WHERE nro_documento = ".quote($nro_documento);
		$resultado = toba::db()->consultar_fila($sql);
		return count($resultado) ? $resultado['ayn'] : "";
	}

	/*static function get_ayn($nro_documento)
	{
		//para reutilizar el código del método no estático
		$persona = new co_personas();
		return $persona->get_ayn($nro_documento);
	}*/
	

	protected function existe_en_local($nro_documento)
	{
		$nro_documento = str_replace('.','',$nro_documento);
		$sql = "SELECT * FROM sap_personas WHERE nro_documento = ".quote($nro_documento)." limit 1";	
		$resultado = toba::db()->consultar_fila($sql);
		return ( ! empty($resultado));
	}

	/**
	 * Busca una persona en la base de datos local, si no la encuentra, intenta buscarla en el ws. En caso de no encontrarla, retorna un array vac?
	 * @param  string $dni N?mero de documento de la persona que se intenta encontrar
	 * @return array()				Retorna un array con los datos de la persona o un array vac? en caso de no encontrarla.
	 */
	function buscar_persona($dni)
	{
		$dni = str_replace('.','',$dni);
		$datos = $this->get_personas(array('nro_documento'=>$dni));
		$datos = (count($datos) > 0) ? array_shift($datos) : NULL;
		
		//si se cumple este if es porque la persona no existe en local
		if( ! $datos){
			$datos = $this->buscar_en_ws($dni);
			if(count($datos)){
				$this->nueva_persona($datos);
			}
		}
		return $datos;
	}

	function armar_insert($datos = array(),$tabla="")
	{
		if(count($datos) == 0 || $tabla == ''){
			return FALSE;
		}
		$campos = "";
		$valores = "";
		foreach ($datos as $campo => $valor) {
			if($valor){
				//si es un campo tipo fecha, lo formateo
				if(preg_match('/fecha/',$campo)){
					$tmp = new Datetime(str_replace('/','-',$valor));
					$valor = $tmp->format('Y-m-d');
				}
				$campos .= $campo.",";
				$valores .= quote($valor).",";	
			}
		}
		$campos = substr($campos,0,$campos-1);
		$valores = substr($valores,0,$valores-1);
		return "INSERT INTO $tabla ($campos) VALUES ($valores)";

	}

	function nueva_persona($datos)
	{
		if( ! array_key_exists('nro_documento',$datos) || ! $datos['nro_documento']){
			return false;
		}
		$datos['nro_documento'] = str_replace('.','',$datos['nro_documento']);
		$sql = $this->armar_insert($datos,'sap_personas');
		return toba::db()->ejecutar($sql);
	}

	function asegurar_existencia_usuario($datos_usuario)
	{   
		if( ! $this->get_personas(array('nro_documento' => $datos_usuario['id'])) ){
			$datos_usuario['id'] = str_replace('.','',$datos_usuario['id']);
			$this->nueva_persona(array(
									   'nro_documento' => $datos_usuario['id'],
									   'mail'		  => $datos_usuario['email']
									   )
								);
		}

	}

	function get_cargos_persona($nro_documento,$solo_vigentes=FALSE)
	{
		$nro_documento = str_replace('.','',$nro_documento);
		$sql = "SELECT car.cargo, 
						car.dedicacion, 
						car.fecha_desde, 
						car.fecha_hasta, 
						car.dependencia, 
						dep.nombre, 
						des.descripcion 
			FROM sap_cargos_persona AS car 
			LEFT JOIN sap_dependencia AS dep ON dep.sigla_mapuche = car.dependencia
			LEFT JOIN sap_cargos_descripcion AS des ON des.cargo = car.cargo
			WHERE car.nro_documento = ".quote($nro_documento);
			if($solo_vigentes){
				$sql .=  " AND fecha_hasta >= current_date";
			}
			
		return toba::db()->consultar($sql);
	}

	function tiene_mayor_dedicacion($nro_documento)
	{
		$nro_documento = str_replace('.','',$nro_documento);
		$cargos = $this->get_cargos_persona($nro_documento,TRUE);
		$mayores = array('SEMI','EXCL');
		foreach ($cargos as $cargo) {
			//Si tiene un cargo de mayor dedicacion, y está vigente
			if(in_array($cargo['dedicacion'],$mayores) && date($cargo['fecha_hasta']) > date('Y-m-d')){
				return TRUE;	
			}
		}
		return FALSE;
	}

	function es_docente($nro_documento)
	{
		$nro_documento = str_replace('.','',$nro_documento);
		return (count($this->get_cargos_persona($nro_documento))) ? TRUE : FALSE;
	}

	function get_categoria_incentivos($nro_documento)
	{
		$nro_documento = str_replace('.','',$nro_documento);
		$sql = "SELECT categoria 
				FROM sap_cat_incentivos 
				WHERE nro_documento = ".quote($nro_documento)."
				ORDER BY convocatoria DESC
				LIMIT 1";
		$resultado = toba::db()->consultar_fila($sql);
		if(count($resultado)){
			return $resultado['categoria'];
		}else{
			return FALSE;
		}
	}

	function es_incentivado($nro_documento)
	{
		$nro_documento = str_replace('.','',$nro_documento);
		return ($this->get_categoria_incentivos($nro_documento)) ? true : false;
	}

	function get_cargo_conicet($nro_documento){
		$nro_documento = str_replace('.','',$nro_documento);
		$sql = "SELECT id_cat_conicet FROM be_cat_conicet_persona WHERE nro_documento = ".quote($nro_documento);
		$resultado = toba::db()->consultar_fila($sql);
		return $resultado['id_cat_conicet'];
	}

	function get_campo_aplicacion($id_subcampo)
	{
		$sql = "SELECT id_campo_aplicacion FROM sap_subcampos_aplicacion WHERE id_subcampo_aplicacion = ".quote($id_subcampo);
		$resultado = toba::db()->consultar_fila($sql);
		return $resultado['id_campo_aplicacion'];

	}

	/**
	 * Actualiza la tabla sap_personas, y marca como cumplido el CVar (en el campo "archivo_cvar" establece el valor "cvar.pdf")
	 * @param  [String] $nro_documento [Nro. de documento de la persona que carga su CVar]
	 * @return [integer]                [Cantidad de registros afectados por la consulta (siempre debería ser uno o cero)]
	 */
	function marcar_cumplido_cvar($nro_documento)
	{
		$sql = "UPDATE sap_personas SET archivo_cvar = 'cvar.pdf' WHERE nro_documento = ".quote($nro_documento);
		return toba::db()->ejecutar($sql);
	}

}
?>