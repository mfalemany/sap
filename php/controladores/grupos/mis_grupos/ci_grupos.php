<?php
class ci_grupos extends sap_ci
{
	protected $s__convocatoria;
	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function conf()
	{
		$usuario = toba::usuario()->get_id();
		/* Valido que el usuario logueado tenga:
			- Al menos un cargo docente (mapuche) con mayor dedicacion (semi o exclusiva) o bien, ser investigador de conicet (lista de Damian)
			- Categoría I, II o III de incentivos
			- Un solo grupo (no puede dirigir mas de uno)
		*/
			/*var_dump(toba::consulta_php('co_personas')->es_docente($usuario));
			var_dump(toba::consulta_php('co_personas')->tiene_mayor_dedicacion($usuario));
			var_dump(in_array(toba::consulta_php('co_personas')->get_categoria_incentivos($usuario),array(1,2,3)));
			var_dump(toba::consulta_php('co_personas')->get_categoria_incentivos($usuario));*/
	
		if(! toba::consulta_php('co_grupos')->puede_coordinar($usuario)){
			throw new toba_error('Usted no cumple las condiciones para coordinar un Grupo de Investigación');
		}
		//NO puede dirigir mas de un grupo
		if(toba::consulta_php('co_grupos')->es_coordinador($usuario)){
			$this->pantalla()->eliminar_evento('crear_grupo');
		}
		//Si no hay convocatoria abierta, no puede crear un grupo
		if(! toba::consulta_php('co_convocatorias')->get_convocatorias_vigentes_equipos()){
			$this->pantalla()->eliminar_evento('crear_grupo');	
		}
	}

	//Se deshabilitan todos los formularios y eventos si no hay una convocatoria abierta
	function conf__pant_edicion(toba_ei_pantalla $pantalla)
	{
		$esta_inscripto = FALSE;
		$grupo = $this->get_datos('grupo')->get();
		if(isset($grupo['id_grupo'])){
			$esta_inscripto = toba::consulta_php('co_grupos')->esta_inscripto($grupo['id_grupo']);
		}
			
		//Si no existe una convocatoria de grupos, no se puede modificar nada (se bloquea todo)
		if( (! toba::consulta_php('co_convocatorias')->get_convocatorias_vigentes_equipos()) || $esta_inscripto){
			//formularios que hay que bloquear
			$deps = $this->dep('ci_edicion_grupos')->get_dependencias_clase('form');
			//pero solo en la pantalla de edicion de grupos
			$deps_pantalla = $this->dep('ci_edicion_grupos')->pantalla('pant_grupo')->get_lista_dependencias();
			//Se recorren las dependencias de la pantalla, y para las que corresponda, se bloquean
			foreach($deps as $dep){
				if( in_array($dep,$deps_pantalla)){
					$objeto = $this->dep('ci_edicion_grupos')->dep($dep);
					//Se pone en modo solo lectura
					if(method_exists($objeto, 'set_solo_lectura')){
						$objeto->set_solo_lectura();
						//y si es un formulario multilinea, se desactiva el agregado de filas
						if(get_class($objeto) == 'sap_ei_formulario_ml'){
							$objeto->desactivar_agregado_filas();
						} 
					}
				}
			}
			//Se elimina el evento guardar (y se advierte al usuario)
			$pantalla->eliminar_evento('guardar');
			$pantalla->agregar_notificacion('No podrá modificar la información contenida en esta pantalla: no existe una convocatoria abierta o el grupo ya confirmó su plan de trabajo','warning');
		}
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function evt__crear_grupo()
	{
		$this->get_datos()->resetear();
		$this->set_pantalla('pant_edicion');
	}

	function evt__guardar()
	{
		$this->validar_condiciones_grupo();
		$this->get_datos('grupo')->set(array('nro_documento_coordinador'=>toba::usuario()->get_id()));
		try {
			$this->get_datos()->sincronizar();
			$this->get_datos()->resetear();	
			$this->set_pantalla('pant_seleccion');
		} catch (toba_error_db $e) {
			toba::notificacion()->agregar($e->get_mensaje_motor(),'warning');
		} catch(Exception $e){
			toba::notificacion()->agregar($e);
		}

		$this->set_pantalla('pant_seleccion');
		
		
		
	}
	function evt__volver()
	{
		$this->get_datos()->resetear();
		$this->set_pantalla('pant_seleccion');
	}

	//-----------------------------------------------------------------------------------
	//---- cu_grupos --------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cu_grupos(sap_ei_cuadro $cuadro)
	{
		$cuadro->desactivar_modo_clave_segura();
		$datos = toba::consulta_php('co_grupos')->get_grupos(array('nro_documento_coordinador'=>toba::usuario()->get_id() ) );
		
		$cuadro->set_datos($datos) ;
	}

	function evt__cu_grupos__seleccion($seleccion)
	{
		$this->get_datos()->cargar($seleccion);
		$this->set_pantalla('pant_edicion');
	}
	
	function conf_evt__cu_grupos__inscribir(toba_evento_usuario $evento, $fila)
	{
		//Si no existe una convocatoria de grupos se elimina el evento
		$conv = toba::consulta_php('co_convocatorias')->get_convocatorias_vigentes_equipos();
		$id_grupo = $evento->get_parametros();
		if( ! $conv){
			$evento->ocultar();
		}else{
			//conservo el ID de la convocatoria vigente
			$this->s__convocatoria = $conv[0]['id'];
			
			//Si el grupo no está inscripto, debe ver boton de "inscribir"
			if(toba::consulta_php('co_grupos')->esta_inscripto($id_grupo)){
				$evento->ocultar();
			}else{
				$evento->mostrar();
			}
		}
	}
	function evt__cu_grupos__inscribir($seleccion)
	{

		$this->get_datos()->cargar($seleccion);
		
		//Se setea la convocatoria y se carga el DT correspondiente
		$seleccion['id_convocatoria'] = $this->s__convocatoria;
		$this->get_datos('grupo_informe')->cargar($seleccion);

		if(!$this->get_datos('grupo_informe')->get_filas()){
			$this->get_datos('grupo_informe')->nueva_fila($seleccion);
		}
		$this->get_datos('grupo_informe')->set_cursor(0);

		$this->validar_condiciones_grupo();
		$this->set_pantalla('pant_informes');	
	}
	
	function conf_evt__cu_grupos__comprobante(toba_evento_usuario $evento, $fila)
	{
		//Si no existe una convocatoria de grupos se elimina el evento
		$conv = toba::consulta_php('co_convocatorias')->get_convocatorias_vigentes_equipos();
		$id_grupo = $evento->get_parametros();
		//conservo el ID de la convocatoria vigente
		$this->s__convocatoria = $conv[0]['id'];
		
		//Si el grupo no está inscripto, debe ver boton de "inscribir"
		if(toba::consulta_php('co_grupos')->esta_inscripto($id_grupo)){
			$evento->mostrar();
		}else{
			$evento->ocultar();
		}
	}

	function servicio__generar_comprobante(){
		$params = toba::memoria()->get_parametros();
		//$clave = toba_ei_cuadro::recuperar_clave_fila('2948',$params['fila']);
		$this->generar_comprobante($params);
		//validar si existe el archivo, sino, hay que generarlo.
	}

	function generar_comprobante($params)
	{
		if(!count($params) || !isset($params['id_grupo'])){
			return;
		}
		$datos = toba::consulta_php('co_grupos')->get_detalles_grupo($params['id_grupo']);
		$datos['integrantes'] = toba::consulta_php('co_grupos')->get_integrantes($params['id_grupo']);
		$datos['lineas_investigacion'] = toba::consulta_php('co_grupos')->get_lineas_investigacion($params['id_grupo']);
		$reporte = new Comprobante_insc_grupos($datos);
		$reporte->mostrar();

	}



	
	function validar_condiciones_grupo()
	{
		//Validar que el grupo tenga al menos tres docentes investigadores (con una categoría de incentivos, cualquiera)
		//Validar que cada integrante no participe en mas de dos grupos
		$this->validar_integrantes();

		//Validar que se haya declarado al menos un proyecto vigente
		$this->validar_proyectos();
		//valido que se haya cargado al menos una linea de investigación
		if( ! $this->get_datos('grupo_linea_investigacion')->get_filas()){
			throw new toba_error('No se declararon líneas de investigación','Debe cargar al menos una linea de investigación en el la parte inferior de este formulario','Faltan datos del grupo');
		}
		
	}

	

	//-----------------------------------------------------------------------------------
	//---- Funciones de validación ------------------------------------------------------
	//-----------------------------------------------------------------------------------

	/**
	 * Determina si el grupo que se intenta guardar cumple con la condición de cantidad de integrantes
	 * @return void
	 */
	private function validar_integrantes()
	{
		$integrantes = $this->get_datos('grupo_integrante')->get_filas();

		//El grupo debe estar compuesto por al menos tres integrantes
		if(count($integrantes) < 3){
			throw new toba_error("El grupo debe estar conformado por al menos tres integrantes");
		}
		
		$nros_documento = array();
		$incentivados = 0;
		$docentes = 0;
		foreach($integrantes as $fila => $integrante){
			//Valido si hay repetidos
			if(in_array($integrante['nro_documento'],$nros_documento)){
				throw new toba_error("Existen personas repetidas en la lista de integrantes del grupo (fila ".($fila+1).")");
			}else{
				$nros_documento[] = $integrante['nro_documento'];
			}

			if(toba::consulta_php('co_personas')->es_docente($integrante['nro_documento'])) {
				$docentes++;
			}
			
			/*
			//Cuento la cantidad de docentes incentivados que forman parte del grupo
			if(toba::consulta_php('co_personas')->es_incentivado($integrante['nro_documento'])) {
				$incentivados++;
			}

			*/
			// =================================================================================
			//ESTA LINEA HAY QUE ELIMINAR. TEMPORAL PARA QUE NO CONTROLE DOCENTES INCENTIVADOS
			$incentivados = 3;
			// =================================================================================
			
			//verifico si el integrante ya no forme parte de dos grupos YA INSCRIPTOS (sin incluír el actual). Los grupos registrados que no se hayan inscripto, no cuentan.
			$id_actual = $this->get_datos('grupo')->get();
			$omitir = (isset($id_actual['id_grupo'])) ? array($id_actual['id_grupo']) : array();
			
			$grupos = toba::consulta_php('co_grupos')->grupos_es_integrante($integrante['nro_documento'],false,$omitir);
			if(count($grupos) > 1 ){
				//obtengo un detalle de los grupos en los cuales está incluida la persona (para mostrar)
				$det_grupos = array_reduce($grupos,'self::concatenar');
				throw new toba_error("El integrante de la fila ".($fila+1)." ya forma parte de dos grupos. Una persona puede formar parte de, como máximo, dos grupos. Grupos de los cuales forma parte: \n <ul>".$det_grupos."</ul>",FALSE,'Integrantes no válidos');
			}
			
		}
		//El grupo debe tener al menos tres docentes con categoría de incentivos (cualquiera)
		if($incentivados < 3){
			throw new toba_error("El grupo debe estar conformado por al menos tres integrantes con Categoría de Incentivos");
		}
		//El grupo debe tener al menos tres docentes con categoría de incentivos (cualquiera)
		if($docentes < 3){
			throw new toba_error("El grupo debe estar conformado por al menos tres integrantes docentes");
		}
		
	}

	static function concatenar($acum,$item){
		$acum .= "<li>".$item['denominacion']."</li>";
		return $acum; 
	}

	/**
	 * Valida que el grupo haya declarado al menos un proyecto vigente
	 * @return boolean Indica si existen proyectos vigentes
	 */
	private function validar_proyectos()
	{
		$proyectos = $this->get_datos('grupo_proyecto')->get_filas();
		
		foreach ($proyectos as $proyecto) {
			if(toba::consulta_php('co_proyectos')->es_vigente($proyecto['id_proyecto'])){
				return true;
			}
		}
		throw new toba_error("El grupo debe declarar al menos un proyecto vigente. Todos los proyectos declarados están finalizados.");
	}


	function get_datos($tabla = NULL)
	{
		return ($tabla) ? $this->dep('datos')->tabla($tabla) : $this->dep('datos');
	}



}
?>