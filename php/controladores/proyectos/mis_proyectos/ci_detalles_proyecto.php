<?php
class ci_detalles_proyecto extends sap_ci
{
	protected $s__duracion;
	protected $s__id_area;
	protected $s__id_area_tematica;
	protected $s__integrantes_inicial;
	protected $s__id_proyecto;
	protected $s__auxiliares;

	function conf()
	{
		$datos = $this->get_datos('proyectos')->get();

		if($datos){
			/* =======================Cargo los registros auxiliares =========================================*/
			if(isset($datos['id'])){
				$this->s__id_proyecto = $datos['id'];
				$funciones = array(
					array('auxiliar' => 'tesistas',   'tabla' => 'sap_proyecto_tesista'),
					array('auxiliar' => 'alumnos',    'tabla' => 'sap_proyecto_alumno'),
					array('auxiliar' => 'becarios',   'tabla' => 'sap_proyecto_becario'),
					array('auxiliar' => 'apoyo',      'tabla' => 'sap_proyecto_apoyo'),
					array('auxiliar' => 'inv_externo','tabla' => 'sap_proyecto_inv_externo')
				);
				foreach($funciones as $funcion){
					if( ! isset($this->s__auxiliares[$funcion['auxiliar']]) || ! $this->s__auxiliares[$funcion['auxiliar']] ){
						$this->s__auxiliares[$funcion['auxiliar']] = toba::consulta_php('co_proyectos')->get_miembros(array('id_proyecto' => $datos['id']),$funcion['tabla']);	
					}
				}
			}else{
				unset($this->s__id_proyecto);
			}
			/* ===============================================================================================*/

			

			if(isset($datos['fecha_desde']) && isset($datos['fecha_hasta'])){
				//Obtengo la duracion del proyecto en base a las fechas desde y hasta
				$this->s__duracion = date('Y',strtotime($datos['fecha_hasta'])) - date('Y',strtotime($datos['fecha_desde']));	
			}
			if(isset($datos['id_subarea'])){
				//obtengo el area del proyecto
				$this->s__id_area = toba::consulta_php('co_proyectos')->get_area(array('id_subarea'=>$datos['id_subarea']));
			}
			if(isset($datos['id_subarea_prioritaria'])){
				//obtengo el area "prioritaria o temática" (area de programas) del proyecto
				$this->s__id_area_tematica = toba::consulta_php('co_programas')->get_area_de_subarea($datos['id_subarea_prioritaria']);
			}
			/* ===================================================================================== */
			// Dependiendo del tipo de proyecto que se esté cargando, se muestran/ocultan los 
			// formularios de PI y PDTS respectivamente
			if($datos['tipo'] == '0'){
				if($this->pantalla()->existe_dependencia('form_detalles_pdts')){
					$this->pantalla()->eliminar_dep('form_detalles_pdts');
					$this->pantalla()->eliminar_dep('ml_instituciones');
					$this->pantalla()->eliminar_dep('ml_agentes_financieros');
				}
			}
			if($datos['tipo'] == 'D'){

				if($this->pantalla()->existe_dependencia('form_detalles_pi')){
					$this->pantalla()->eliminar_dep('form_detalles_pi');
				}	
			}
			/* ===================================================================================== */
		}else{
			unset($this->s__duracion);
			unset($this->s__id_area);
			unset($this->s__id_area_tematica);
			unset($this->s__integrantes_inicial);
			unset($this->s__id_proyecto);
		}

	}

	/* =====================================================================================*/
	/* ============================== EVENTOS ==============================================*/
	/* =====================================================================================*/

	function evt__guardar()
	{
		try {
			if($this->validar_integrantes()){
				
				
				//Sincronizo con fuente
				toba::db()->ejecutar("BEGIN; LOCK TABLE sap_proyectos IN EXCLUSIVE MODE;");
				$this->get_datos()->sincronizar();
				$id = toba::db()->consultar_fila("SELECT max(id) as id FROM sap_proyectos;");
				$this->s__id_proyecto = $id['id'];
				toba::db()->ejecutar("COMMIT;");
				//se registran todos los cambios en las tablas auxiliares
				$this->registrar_cambios_integrantes();
				//Y si todo salió bien...
				toba::notificacion()->agregar('Los datos se guardaron con éxito','info');
				//se elimina la variable de sesión para que en el siguiente pedido de pagina, se carge desde el DT
				unset($this->s__auxiliares);	
			}
		} catch (toba_error_db $e) {
			switch ($e->get_sqlstate()) {
				case 'db_23503':
					$mensaje = "Alguno de los integrantes que está declarando, no tienen su información completa en la solapa 'Recursos Humanos'.";
					break;
				
				default:
					$mensaje = "Ocurrió el siguiente error desconocido: ".$e->get_mensaje();
					break;
			}
			
			toba::notificacion()->agregar($mensaje.$e->get_sqlstate().$e->get_mensaje_motor());
		} catch (Exception $e) {
			toba::notificacion()->agregar('Ocurrió el siguiente problema: '.$e->getMessage());
		}
		
	}

	function evt__cancelar()
	{
		unset($this->s__auxiliares);
		$this->get_datos()->resetear();
		$this->controlador()->set_pantalla('pant_seleccion_proyecto');
	}

	/* =====================================================================================*/
	/* ============================== PANT_FORM_PROYECTO ===================================*/
	/* =====================================================================================*/

	//-----------------------------------------------------------------------------------
	//---- form_proyecto ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_proyecto(form_proyecto $form)
	{
		$datos = $this->get_datos('proyectos')->get();
		
		if($datos){
			//Obtengo el area correspondiente del proyecto
			$datos['id_area'] = $this->s__id_area;
			//Obtengo la duración del proyecto
			$datos['duracion'] = $this->s__duracion;
			//Obtengo el area_tematica correspondiente del proyecto
			$datos['id_area_tematica'] = $this->s__id_area_tematica;

			$form->set_datos($datos);
		}

	}

	function evt__form_proyecto__modificacion($datos)
	{
		//variable de sesion que sirve como base en varios cálculos a lo largo de la carga
		$this->s__duracion = $datos['duracion'];
		//variable que almacena temporalmente el id_area (que no se almacena en datos_tabla)
		$this->s__id_area = $datos['id_area'];
		//variable que almacena temporalmente el id_area_tematica
		$this->s__id_area_tematica = $datos['id_area_tematica'];
		//se calcula la fecha_hasta del proyecto en base a la duración seleccionada
		$datos['fecha_hasta'] = $this->obtener_fecha_hasta($datos['fecha_desde'],$datos['duracion']);

		
		$this->get_datos('proyectos')->set($datos);
	}

	/* =====================================================================================*/
	/* ============================== PANT_INTEGRANTES =====================================*/
	/* =====================================================================================*/

	//-----------------------------------------------------------------------------------
	//---- ml_integrantes ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml_integrantes(sap_ei_formulario_ml $form_ml)
	{
		$this->s__integrantes_inicial = $this->get_datos('proyecto_integrante')->get_filas();
		if($this->s__integrantes_inicial){
			$form_ml->set_datos($this->s__integrantes_inicial);
		}
	}

	function evt__ml_integrantes__modificacion($datos)
	{
		/* En este punto, hay que re-generar todos los registros de las tablas auxiliares: 
		* proyecto_tesista, proyecto_becario, proyecto_alumno, proyecto_inv_externo, proyecto_apoyo
		*/
		
		$this->existen_duplicados($datos);
		$this->get_datos('proyecto_integrante')->procesar_filas($datos);
		//$this->regenerar_auxiliares_integrante($datos);
	}

	function evt__ml_integrantes__pedido_registro_nuevo()
	{
		//seteo la fecha de inicio del proyecto como fecha desde para los integrantes
		$datos = $this->get_datos('proyectos')->get();
		if($datos){
			$this->dep('ml_integrantes')->set_registro_nuevo(array('fecha_desde'=>$datos['fecha_desde']));	
		}
	}

	function conf_evt__ml_integrantes__editar_info(toba_evento_usuario $evento, $fila)
	{
		$filas = $this->dep('ml_integrantes')->get_datos();
		$indice_filas = array_column($filas,'x_dbr_clave');
		$indice = array_search($fila,$indice_filas);
		
		if($indice !== FALSE){
			if(isset($filas[$indice]['nro_documento']) && $filas[$indice]['nro_documento']){
				$evento->vinculo()->agregar_parametro('nro_documento',$filas[$indice]['nro_documento']);
				$evento->mostrar();
				return;
			}
		}
		$evento->ocultar();
	}

	/* =====================================================================================*/
	/* ============================== PANT_DETALLES ========================================*/
	/* =====================================================================================*/

	//-----------------------------------------------------------------------------------
	//---- form_detalles_pi -------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_detalles_pi(sap_ei_formulario $form)
	{
		$datos = $this->get_datos('proyectos_pi')->get();
		if($datos){
			$form->set_datos($datos);
		}
	}

	function evt__form_detalles_pi__modificacion($datos)
	{
		$this->get_datos('proyectos_pi')->set($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- form_detalles_pdts -----------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_detalles_pdts(sap_ei_formulario $form)
	{
		$datos = $this->get_datos('proyectos_pdts')->get();
		if($datos){
			$form->set_datos($datos);
		}
	}

	function evt__form_detalles_pdts__modificacion($datos)
	{
		$this->get_datos('proyectos_pdts')->set($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- ml_instituciones -------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml_instituciones(sap_ei_formulario_ml $form_ml)
	{
		$datos = $this->get_datos('proy_pdts_institucion')->get_filas();
		if($datos){
			$form_ml->set_datos($datos);
		}
	}

	function evt__ml_instituciones__modificacion($datos)
	{
		$this->get_datos('proy_pdts_institucion')->procesar_filas($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- ml_agentes_financieros -------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml_agentes_financieros(sap_ei_formulario_ml $form_ml)
	{
		$datos = $this->get_datos('proyecto_agente_financiero')->get_filas();
		if($datos){
			$form_ml->set_datos($datos);
		}
	}

	function evt__ml_agentes_financieros__modificacion($datos)
	{
		$this->get_datos('proyecto_agente_financiero')->procesar_filas($datos);
	}

	/* =====================================================================================*/
	/* ============================== PANT_RECURSOS_HUMANOS ================================*/
	/* =====================================================================================*/

	//-----------------------------------------------------------------------------------
	//---- ml_tesistas ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml_tesistas(sap_ei_formulario_ml $form_ml)
	{
		$this->configurar_formulario($form_ml,'P','tesistas');	
	}

	

	function evt__ml_tesistas__modificacion($datos)
	{
		foreach($datos as $tesista){
			$this->s__auxiliares['tesistas'][$tesista['nro_documento']] = array(
				'nro_documento' => $tesista['nro_documento'],
				'carrera'       => $tesista['carrera'],
				'institucion'   => $tesista['institucion'],
				'anio_inicio'   => $tesista['anio_inicio']
			);
		}
	}

	//-----------------------------------------------------------------------------------
	//---- ml_becarios ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml_becarios(sap_ei_formulario_ml $form_ml)
	{
		$this->configurar_formulario($form_ml,'B','becarios');
	}

	function evt__ml_becarios__modificacion($datos)
	{
		foreach($datos as $becario){
			$this->s__auxiliares['becarios'][$becario['nro_documento']] = array(
				'nro_documento' => $becario['nro_documento'],
				'id_tipo_beca'  => $becario['id_tipo_beca'],
				'anio_fin'      => $becario['anio_fin'],
				'anio_inicio'   => $becario['anio_inicio']
			);
		}
	}

	//-----------------------------------------------------------------------------------
	//---- ml_alumnos -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml_alumnos(sap_ei_formulario_ml $form_ml)
	{
		$this->configurar_formulario($form_ml,'A','alumnos');
	}

	function evt__ml_alumnos__modificacion($datos)
	{
		foreach($datos as $alumno){
			$this->s__auxiliares['alumnos'][$alumno['nro_documento']] = array(
				'nro_documento'  => $alumno['nro_documento'],
				'id_carrera'     => $alumno['id_carrera'],
				'porc_mat_aprob' => $alumno['porc_mat_aprob']
			);
		}
	}

	//-----------------------------------------------------------------------------------
	//---- ml_inv_externos --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml_inv_externos(sap_ei_formulario_ml $form_ml)
	{
		$this->configurar_formulario($form_ml,'X','inv_externo');
	}

	function evt__ml_inv_externos__modificacion($datos)
	{
		foreach($datos as $inv_externo){
			$this->s__auxiliares['inv_externo'][$inv_externo['nro_documento']] = array(
				'nro_documento'  => $inv_externo['nro_documento'],
				'institucion'     => $inv_externo['institucion'],
				'cargo_docente'     => $inv_externo['cargo_docente']
			);
		}
	}

	//-----------------------------------------------------------------------------------
	//---- ml_apoyo ---------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml_apoyo(sap_ei_formulario_ml $form_ml)
	{
		$this->configurar_formulario($form_ml,'T','apoyo');
	}

	function evt__ml_apoyo__modificacion($datos)
	{
		foreach($datos as $apoyo){
			$this->s__auxiliares['apoyo'][$apoyo['nro_documento']] = array(
				'nro_documento'  => $apoyo['nro_documento'],
				'id_tipo_apoyo'     => $apoyo['id_tipo_apoyo'],
				'id_dependencia'     => $apoyo['id_dependencia']
			);
		}
	}

	/* =====================================================================================*/
	/* ============================== PANT_NECES_PRESUP ====================================*/
	/* =====================================================================================*/

	//-----------------------------------------------------------------------------------
	//---- form_necesidades_presup ------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_necesidades_presup(sap_ei_formulario_ml $form_ml)
	{
		$datos = $this->get_datos('proy_presupuesto')->get_filas();
		if($datos){
			$form_ml->set_datos($datos);
		}
	}

	function evt__form_necesidades_presup__modificacion($datos)
	{
		$this->get_datos('proy_presupuesto')->procesar_filas($datos);
	}

	/* =====================================================================================*/
	/* ============================== PANT_PLAN TAREAS =====================================*/
	/* =====================================================================================*/

	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__pant_plan_tareas(toba_ei_pantalla $pantalla)
	{
		if( ! $this->get_datos('proyecto_obj_especifico')->hay_cursor()){
			$this->pantalla()->eliminar_dep('ml_tareas');
		}
		$template =  "<table width='100%'>
						<caption style='font-size:1.2em; font-weight:bold; background-color: #575b98; color:white; padding: 4px 0px;'>Cada objetivo específico está compuesto por una o varias tareas que lo componen. Una vez declarado cada objetivo específico, debe detallar dichas tareas, haciendo click en el botón 'Ver tareas relacionadas'.</caption>";
		$template .= "<tbody><tr><td>[dep id=ml_obj_especificos]</td></tr>";
		$template .= ($pantalla->existe_dependencia('ml_tareas')) ? "<tr style='margin-top:25px;'><td>[dep id=ml_tareas]</td></tr>" : "";
		$template .= "</tbody></table>";
		$pantalla->set_template($template);
		
	}

	//-----------------------------------------------------------------------------------
	//---- ml_obj_especificos -----------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml_obj_especificos(sap_ei_formulario_ml $form_ml)
	{
		$datos = $this->get_datos('proyecto_obj_especifico')->get_filas();
		if($datos){
			$form_ml->set_datos($datos);
		}
		$form_ml->agregar_notificacion('Indicar la secuencia de metas parciales o hitos que indican el alcance de los objetivos especificos propuestos','info');
	}

	function evt__ml_obj_especificos__modificacion($datos)
	{
		$this->get_datos('proyecto_obj_especifico')->procesar_filas($datos);
	}
	function evt__ml_obj_especificos__seleccion($datos)
	{
		$this->get_datos('proyecto_obj_especifico')->set_cursor($datos);
	}

	function get_obj_especificos()
	{
		if(isset($this->s__id_proyecto)){
			return toba::consulta_php('co_proyectos')->get_obj_especificos($this->s__id_proyecto,TRUE);
		}
	}

	//-----------------------------------------------------------------------------------
	//---- ml_tareas --------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml_tareas(sap_ei_formulario_ml $form_ml)
	{
		$datos = $this->get_datos('obj_especifico_tarea')->get_filas();
		if($datos){
			$form_ml->set_datos($datos);
			$objetivo = $this->get_datos('proyecto_obj_especifico')->get();
			$form_ml->set_titulo('Tareas relacionadas con el objetivo: '.$objetivo['obj_especifico']);
		}
		$form_ml->agregar_notificacion('Indicar la secuencia de actividades para el logro de este objetivo específico','info');
	}

	function evt__ml_tareas__modificacion($datos)
	{
		$this->get_datos('obj_especifico_tarea')->procesar_filas($datos);
		$this->get_datos('proyecto_obj_especifico')->resetear_cursor();
	}

	function evt__ml_tareas__cancelar()
	{
		$this->get_datos('proyecto_obj_especifico')->resetear_cursor();
	}

	/* =====================================================================================*/
	/* ============================== PANT_CRONOGRAMA ======================================*/
	/* =====================================================================================*/
	function conf__pant_cronograma(toba_ei_pantalla $pantalla)
	{
		//Numeros ordinales para la generación de cuadro cronograma
		$ordinal = array('1'=>'Primer','2'=>'Segundo','3'=>'Tercer','4'=>'Cuarto');
		
		$objetivos = toba::consulta_php('co_proyectos')->get_objetivos_tiempos($this->s__id_proyecto);

		if(!$objetivos){
			return; //si no se han definido tiempos, esto no tiene sentido.
		}

		$objs_tiempos = array();
		foreach($objetivos as $objetivo){
			$objs_tiempos[$objetivo['anio']][$objetivo['semestre']][] = $objetivo['id_obj_especifico'];
		}
		$anios_proyecto = $this->get_anios_proyecto();
		
		$datos = array(
			'duracion'       => $this->s__duracion,
			'ordinal'        => $ordinal,
			'objetivos'      => $objetivos,
			'objs_tiempos'   => $objs_tiempos,
			'anios_proyecto' => $anios_proyecto
		);
		//ei_arbol($datos);

		$template = __DIR__."/template_cronograma.php";
		$cronograma = $this->armar_template_con_logica($template,$datos);
		$template = "";
		foreach ($pantalla->get_lista_dependencias() as $dependencia) {
			$template .= "[dep id=$dependencia]";
		}
		$pantalla->set_template($template.$cronograma);

	}

	//-----------------------------------------------------------------------------------
	//---- ml_cronograma ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml_cronograma(sap_ei_formulario_ml $form_ml)
	{
		$datos = $this->get_datos('obj_especifico_tiempo')->get_filas();
		if($datos){
			$form_ml->set_datos($datos);
		}

	}

	function evt__ml_cronograma__modificacion($datos)
	{
		$this->get_datos('obj_especifico_tiempo')->procesar_filas($datos);
	}

	/* =====================================================================================*/
	/* ============================== JAVASCRIPT ===========================================*/
	/* =====================================================================================*/

	function extender_objeto_js()
	{
		echo "
			//LLAMADA DE ATENCIÓN PARA QUE NO SE LE CIERRE LA SESIÓN
			//Cada 15 minutos: 15 minutos por 60 segundos por 1000 (porque es en milesimas de segundo)
			setInterval(function(){
				alert('Asegurese de guardar, al menos, parcialmente los cambios que vaya realizando. En caso contrario, si su sesión finaliza por inactividad, podría perder toda la información cargada hasta el momento.');
			}, (15*60*1000) );
		";
	}

	/* =====================================================================================*/
	/* ============================== AUXILIARES DEL CI ====================================*/
	/* =====================================================================================*/
	/**
	 * Comportamiento comun a los formularios de carga de tablas auxiliares de integrantes. Todos los formularios de este tipo tienen el mismo comportamiento. Modifican el estado de la variable de sesión $this->s__auxiliares, en sus distintas dimensiones
	 * @param  toba_ei_formulario_ml &$formulario          Formulario que se está editando
	 * @param  string $identificador_perfil Letra que identifica el perfil que tiene el integrante
	 * @param  string $auxiliar             String que representa la dimensión que hay que modificar del array $this->s__auxiliares
	 * @return void                       
	 */
	function configurar_formulario(&$formulario,$identificador_perfil,$auxiliar)
	{
		$integrantes = $this->get_datos('proyecto_integrante')->get_filas();
		$funcion = toba::consulta_php('co_proyectos')->get_funcion($identificador_perfil);
		$miembros = array_filter($integrantes,function($integrante) use ($funcion){
			return ($integrante['id_funcion'] == $funcion['id_funcion']);
		});
		//Agrego todos los miembros nuevos... los que ya estaban no se modifican
		foreach($miembros as $miembro){
			if( ! isset($this->s__auxiliares[$auxiliar][$miembro['nro_documento']])){
				$this->s__auxiliares[$auxiliar][$miembro['nro_documento']] = $miembro;
			}
		}
		//borro todos los miembros que estaban y ya no figuran
		if(isset($this->s__auxiliares[$auxiliar])){
			foreach ($this->s__auxiliares[$auxiliar] as $nro_documento => $miembro) {
				if( ! in_array($nro_documento,array_column($miembros,'nro_documento')) ){
					unset($this->s__auxiliares[$auxiliar][$nro_documento]);
				}
			}
			$formulario->set_datos($this->s__auxiliares[$auxiliar]);
		}
	}

	function get_datos($tabla = NULL){
		return ($tabla) ? $this->dep('datos')->tabla($tabla) : $this->dep('datos');
	}
	function get_ayn($nro_documento)
	{
		return toba::consulta_php('co_personas')->get_ayn($nro_documento);
	}
	/**
	 * Funcion que recibe la fecha de inicio del proyecto y la duración en años (un numero entero), y retorna una fecha en formato String que representa la fecha de finalización del proyecto
	 * @return [String] [Fecha de finalización del proyecto]
	 */
	function obtener_fecha_hasta($fecha_desde,$duracion)
	{
		//Cálculo de la fecha de finalización del proyecto
		$fecha_desde = new DateTime($fecha_desde);
		$intervalo = new DateInterval('P'.$duracion.'Y'); //P2Y o P4Y dependiendo de la duracion
		return $fecha_desde->add($intervalo)->format('Y-m-d');
	}

	/**
	 * Valida todas las condiciones que debe cumplir un proyecto en relación a sus integrantes. Por ejemplo, que exista un director (y solo uno), etc.
	 * @return [boolean] [Verdadero en caso de cumplir con todas las condiciones]
	 */
	function validar_integrantes()
	{
		$integrantes = $this->get_datos('proyecto_integrante')->get_filas();
		//Matriz de validaciones que se hacen a los integrantes
		$perfiles_validacion = array(
										'D'=>array('unico'=>TRUE,'obligatorio'=>TRUE),
										'C'=>array('unico'=>TRUE,'obligatorio'=>FALSE),
										'S'=>array('unico'=>TRUE,'obligatorio'=>FALSE),
										'I'=>array('unico'=>FALSE,'obligatorio'=>FALSE)
									);
		try {
			/** ACÁ VAN TODAS LAS VALIDACIONES DE INTEGRANTES 
			* Todas las llamadas a funciones deben lanzar una excepción en caso de no cumplirse
			*/

			$this->validar_perfiles($integrantes,$perfiles_validacion);	

			return TRUE;
		} catch (Exception $e) {
			toba::notificacion()->agregar($e->getMessage(),'warning');
			return FALSE;
		}
	}

	/**
	 * Valida las condiciones que deben cumplir los integrantes del proyecto en relación a los perfiles que cumplen. Por ejemplo, solo puede haber (y debe haber) un director, mientras que otros perfiles como el subdirector y el codirector, son opcionales, pero en caso de existir, tambien deben ser únicos.
	 * @param  array $integrantes         Integrantes declarados por el usuario
	 * @param  array $perfiles_validacion Array que contiene los perfiles y las condiciones de cada uno
	 * @return void                      Si bien esta funcion no retorna ningún valor, en caso de error, lanza una excepcion de tipo Exception
	 */
	function validar_perfiles($integrantes,$perfiles_validacion)
	{
		//El array $perfiles, contiene la distribucion de funciones, es decir:
		// -El perfil 1, aparece 1 vez
		// -El perfil 2, aparece 1 vez
		// -El perfil 7 aparece 6 veces, etc.
		$perfiles = array_count_values(array_column($integrantes, 'id_funcion'));

		foreach ($perfiles_validacion as $identificador_perfil => $condiciones) {
			//Obtengo el ID de funcion que corresponde al perfil
			$funcion = toba::consulta_php('co_proyectos')->get_funcion($identificador_perfil);	

			//Es obligatorio?
			if($condiciones['obligatorio']){
				// Existe?
				if( ! array_key_exists($funcion['id_funcion'], $perfiles)){
					throw new Exception("Debe existir un integrante que tenga asigada la función de {$funcion['funcion']}", 1);
				}
			}
			//Debería ser unico?
			if($condiciones['unico']){
				//Es realmente único?
				
				if( isset($perfiles[$funcion['id_funcion']]) && $perfiles[$funcion['id_funcion']] > 1){
					throw new Exception("No puede existir mas de un integrante con la función de {$funcion['funcion']}", 1);
				}
			}
		}
	}

	/**
	 * Por cada vez que se guarda el proyecto (se ejecuta el método evt__guardar()), este método se encarga de regenerar todos los registros de las tablas auxiliares de integrantes. Esto se debe a que, durante la carga, el usuario puede realizar modificaciones en las funciones de las personas, lo que hace que esa persona deje de existir en una tabla, y aparezca como nuevo en otra. Para evitar gestionar todas esas modificaciones, cuando el usuario guarda el proyecto, se elimina todo estado anterior y se vuelven a generar con los detalles que haya guardado el usuario. Durante la carga, las modificaciones realizadas se mantienen en la variable de sesion $this->s__auxiliares.
	 * @return vid 
	 */
	function registrar_cambios_integrantes()
	{
		if( !isset($this->s__auxiliares)){
			return;
		}
		if(isset($this->s__id_proyecto)){
			toba::consulta_php('co_proyectos')->eliminar_auxiliares($this->s__id_proyecto);
		}
		$funciones = array(
			array('auxiliar'=>'tesistas',   'tabla'=>'sap_proyecto_tesista',    'identificador_perfil'=>'P'),
			array('auxiliar'=>'becarios',   'tabla'=>'sap_proyecto_becario',    'identificador_perfil'=>'B'),
			array('auxiliar'=>'alumnos',    'tabla'=>'sap_proyecto_alumno' ,    'identificador_perfil'=>'A'),
			array('auxiliar'=>'inv_externo','tabla'=>'sap_proyecto_inv_externo','identificador_perfil'=>'X'),
			array('auxiliar'=>'apoyo',      'tabla'=>'sap_proyecto_apoyo',      'identificador_perfil'=>'T')
		);

		foreach($funciones as $funcion){
			//obtengo el ID de la funcion
			$perfil = toba::consulta_php('co_proyectos')->get_funcion($funcion['identificador_perfil']);
			if(! array_key_exists($funcion['auxiliar'], $this->s__auxiliares)){
				continue;	
			}
			foreach($this->s__auxiliares[$funcion['auxiliar']] as $elementos){

				$campos = array('id_proyecto','id_funcion');
				$valores = array($this->s__id_proyecto,$perfil['id_funcion']);
				foreach($elementos as $campo => $valor){
					$campos[] = $campo;
					$valores[] = quote($valor);
				}
				$campos = implode(',',$campos);
				$valores = implode(',',$valores);
				toba::db()->ejecutar("INSERT INTO {$funcion['tabla']} ($campos) VALUES ($valores)");
			}
		}
	}

	/**
	 * Valida las condiciones de unicidad entre los integrantes. Un integrante puede estar definido mas de una vez, pero debe tener funciones distintas
	 * @param  array $integrantes Arreglo de integrantes cargados por el usuario
	 * @return void              Si bien esta funcion no devuelve ningun valor, en caso de error lanza una excepcion de tipo toba_error
	 */
	function existen_duplicados($integrantes)
	{
		$mensajes = array();
		//Busco los nro_documento repetidos en la lista
		$duplicados = array_filter(array_count_values(array_column($integrantes, 'nro_documento')),function($num){
			return $num > 1;
		});
		//recorro cada uno de los integrantes que aparecen dos veces o mas
		foreach ($duplicados as $nro_documento => $cantidad) {
			//"use" hace que la variable "nro_documento" esté en el scope de la funcion que recibe array_filter()
			$ocurrencias = array_filter($integrantes,function($integrante) use ($nro_documento){
				return (isset($integrante['nro_documento'])) ? ($integrante['nro_documento'] == $nro_documento) : FALSE;
			});
			
			$iguales = array_filter(array_count_values(array_column($ocurrencias,'id_funcion')),function($num){
				return $num > 1;
			});
			if(count($iguales)){
				$mensajes[] = 'El integrante '.$this->get_ayn($nro_documento)." se declaró dos veces con la misma función";
			}
			
		}
		if(count($mensajes)){
			$mensaje = "Se encontraron los siguientes problemas:<br>".implode("<br>",$mensajes);
			throw new toba_error($mensaje,'Los integrantes pueden declararse mas de una vez, pero deben tener funciones distintas. Por ejemplo, un integrante puede declararse como estudiante de grado (durante un periodo determinado), y declararse nuevamente como tesista de posgrado (en otro periodo posterior).','Integrantes duplicados');
		}

	}

	/**
	 * Retorna un array que contiene los años en los cuales tiene vigencia el proyecto. Esto depende de la fecha de inicio y de la duración declarados por el usuario
	 * @return array Arreglo con los años de duración del proyecto
	 */
	function get_anios_proyecto()
	{
		$opciones = array();
		$proyecto = $this->get_datos('proyectos')->get();
		$anio_inicio = date('Y',strtotime($proyecto['fecha_desde']));
		for($i=0; $i<$this->s__duracion; $i++){
			$opciones[] = array('anio'=>($anio_inicio+$i));
		}
		return $opciones;
	}

}
?>