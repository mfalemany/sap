<?php
class ci_subsidios extends sap_ci
{
	protected $s__filtro;

	function conf()
	{
		
		$nro_documento = toba::instancia()->get_info_usuario(toba::usuario()->get_id());
		$dni = $nro_documento['id'];

		toba::consulta_php('co_personas')->asegurar_existencia_usuario($nro_documento);
		

		//si la persona logueada es un administrador, no puede solicitar subsidios
		if(in_array('admin',toba::usuario()->get_perfiles_funcionales())){
			$this->pantalla()->eliminar_evento('agregar');
		}else{
			//elimino el filtro de solicitudes
			$this->dep('filtro')->desactivar_efs(array('solicitante','rendido'));
			//si no es administrador, verifico que tenga categor? de inscentivos
			$persona = toba::consulta_php('co_personas')->get_personas(array('nro_documento'=>$dni));
			//Si no tiene categor? de incentivos, no puede solicitar un subsidio
			if( ! $persona[0]['categoria']){
				$this->pantalla()->eliminar_evento('agregar');
				throw new toba_error("Para poder solicitar un subsidio, es necesario tener una categoría de incentivos. Si usted está categorizado, por favor comuniquese con la SGCyT: cyt.unne@gmail.com");
			}

			//obtengo las convocatorias de subsidios abiertas
			$convs = toba::consulta_php('co_convocatorias')->get_convocatorias_vigentes_subsidios();
			
			if(count($convs)){
				
				
			
				//si esta condicion se cumple, es porque la persona tiene solicitadas la misma cantidad de subsidios, que convocatorias abiertas (NO PUEDE SOLICITAR MAS)
				if(count(toba::consulta_php('co_subsidios')->get_subsidios(array('nro_documento'=>$dni,'lista_convocatorias'=>$convs))) == count($convs)) {
					$this->pantalla()->eliminar_evento('agregar');	
				}	
			}else{
				//si no hay convocatorias abiertas, no se puede solicitar un subsidio
				$this->pantalla()->eliminar_evento('agregar');
			}
		}
		
		
	}

	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro(sap_ei_formulario $form)
	{
		return (isset($this->s__filtro)) ? $this->s__filtro : array();
	}

	function evt__filtro__filtrar($datos)
	{
		$this->s__filtro = $datos;
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__filtro);
	}


	//-----------------------------------------------------------------------------------
	//---- cuadro_subsidios -------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_subsidios(sap_ei_cuadro $cuadro)
	{
		$cuadro->desactivar_modo_clave_segura();

		$filtro = (isset($this->s__filtro)) ? $this->s__filtro : array();

		//si el usuario logueado es administrador puede ver todo, sino, solo las solicitudes propias
		if(in_array('admin',toba::usuario()->get_perfiles_funcionales()) ){
			$cuadro->set_datos(toba::consulta_php('co_subsidios')->get_subsidios($filtro));	
		}else{
			// ------- EVENTOS SOLO PARA EL ADMINISTRADOR ------------
			$cuadro->eliminar_evento('eliminar');
			$cuadro->eliminar_evento('abrir');
			// -------------------------------------------------------

			$filtro['nro_documento'] = toba::usuario()->get_id();
			$cuadro->set_datos(toba::consulta_php('co_subsidios')->get_subsidios($filtro));	
		}
	}



	function conf_evt__cuadro_subsidios__seleccion(toba_evento_usuario $evento, $fila)
	{
		//obtengo el estado de esa solicitud, filtrando con la clave obtenida anteriormente
		$estado = toba::consulta_php('co_subsidios')->get_subsidios(array('id_solicitud'=>$evento->get_parametros()));
		$estado = $estado[0]['estado'];
		//si el estado es Cerrado o el usuario logueado es administrador, no puede modificar la solicitud

		if($estado == 'C'){
			if(in_array('admin',toba::usuario()->get_perfiles_funcionales())){
				$evento->mostrar();
			}else{
				$evento->ocultar();	
			}
		}else{
			$evento->mostrar();
		}
	}
	function conf_evt__cuadro_subsidios__rendir(toba_evento_usuario $evento, $fila)
	{
		$solicitud = toba::consulta_php('co_subsidios')->get_subsidios(array('id_solicitud'=>$evento->get_parametros()));
		$otorgado = $solicitud[0]['otorgado'];
		//si el estado es Cerrado o el usuario logueado es administrador, no puede modificar la solicitud

		if($otorgado == 'SI' && in_array('admin',toba::usuario()->get_perfiles_funcionales())){
			$rendido = toba::consulta_php('co_subsidios')->get_campo(
				'rendido',
				'sap_subsidio_otorgado',
				array('id_solicitud'=>$solicitud[0]['id_solicitud'])
			);
			if($rendido['rendido'] == 'N'){
				$evento->mostrar();
				return;
			}
		}
		$evento->ocultar();
	}

	function evt__cuadro_subsidios__rendir($seleccion)
	{
		if(toba::consulta_php('co_subsidios')->rendir($seleccion['id_solicitud'])){
			toba::notificacion()->agregar('La solicitud se ha marcado como rendida','info');
		}else{
			toba::notificacion()->agregar('Ocurrió un error al intentar marcar la solicitud como rendida','error');
		}
	}

	function evt__cuadro_subsidios__seleccion($seleccion)
	{
		//elimino todo estado anterior
		$this->datos('subsidio')->resetear();
		$this->datos('persona')->resetear();

		//cargo los datos_tabla
		$this->datos('subsidio')->cargar(array('id_solicitud' => $seleccion['id_solicitud']));
		$persona = $this->datos('subsidio','solicitud_subsidio')->get();
		$this->datos('persona')->cargar(array('nro_documento'=> $persona['nro_documento']));
		$this->set_pantalla('pant_edicion');
	}



	function evt__cuadro_subsidios__abrir($seleccion)
	{
		toba::consulta_php('co_subsidios')->abrir_solicitud($seleccion['id_solicitud']);
	}

	function evt__cuadro_subsidios__eliminar($datos){
		$this->datos('subsidio')->cargar($datos);
		$this->datos('subsidio')->eliminar_todo();
		$this->datos('subsidio')->resetear();
	}

	function evt__cuadro_subsidios__ver_comprobante($seleccion)
	{
		$this->datos('subsidio','solicitud_subsidio')->cargar($seleccion);
		$solicitud = $this->datos('subsidio','solicitud_subsidio')->get();
		$this->datos('persona')->cargar(array('nro_documento'=> $solicitud['nro_documento']));
		$this->mostrar_comprobante();

	}

	function conf_evt__cuadro_subsidios__ver_evaluacion(toba_evento_usuario $evento, $fila)
	{
		//obtengo el id de la solicitud
		$id = $evento->get_parametros();
		//obtengo la convocatoria a la que pertenece
		$conv = toba::consulta_php('co_subsidios')->get_campo('id_convocatoria','sap_subsidio_solicitud',array('id_solicitud'=>$id));
		//obtengo el estado de esa convocatoria (CUANDO LA CONVOCATORIA TENGA ESTADO "PUBLICAR" EL SOLICITANTE PODRÁ VER SU EVALUACIÓN)
		$estado_conv = toba::consulta_php('co_convocatorias')->get_campo('estado','sap_convocatoria',array('id'=>$conv['id_convocatoria']));
		if($estado_conv[0]['estado'] != 'P'){
			if( in_array('admin',toba::usuario()->get_perfiles_funcionales())){
				$evento->mostrar();
				return;
			}
		}else{
			$evento->mostrar();
			return;
		}
		$evento->ocultar();
	}

	function evt__cuadro_subsidios__ver_evaluacion($seleccion)
	{
		toba::memoria()->set_dato('id_solicitud',$seleccion['id_solicitud']);
		$this->set_pantalla('pant_evaluacion');
	}

	function evt__cuadro_subsidios__otorgar($seleccion)
	{
		$this->datos('subsidio')->cargar($seleccion);
		$this->set_pantalla('pant_otorgamiento');



	}
	function conf_evt__cuadro_subsidios__otorgar(toba_evento_usuario $evento, $fila)
	{
		if( in_array('admin',toba::usuario()->get_perfiles_funcionales())){
			$evento->mostrar();
		}else{
			$evento->ocultar();
		}
	}

	
	function datos($relacion, $tabla = NULL)
	{
		return ($tabla) ? $this->dep($relacion)->tabla($tabla) : $this->dep($relacion);
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function evt__agregar()
	{

		$this->datos('persona')->cargar(array('nro_documento' => toba::usuario()->get_id()));
		if( ! $this->datos('persona','personas')->get()){
			//busco a la persona logueada
			$persona = toba::consulta_php('co_personas')->buscar_persona(toba::usuario()->get_id());
		
			if($persona){
				$this->datos('persona','personas')->set($persona);
			}
		}

		$this->set_pantalla('pant_edicion');
		
	}

	function evt__cancelar()
	{
		$this->datos('persona')->resetear();
		$this->datos('subsidio')->resetear();
		$this->disparar_limpieza_memoria();
	}

	function evt__eliminar()
	{

		$this->datos('subsidio')->eliminar_todo();
		$this->set_pantalla('pant_seleccion');
	}

	function evt__guardar()
	{
		try {
			$persona = $this->datos('persona','personas')->get();
			$this->datos('subsidio','solicitud_subsidio')->set(array('nro_documento'=>$persona['nro_documento']));
			$this->datos('persona')->sincronizar();
			$this->datos('subsidio')->sincronizar();
			$this->disparar_limpieza_memoria();
			$this->set_pantalla('pant_seleccion');	
		} catch (Exception $e) {
			toba::notificacion()->agregar('Ocurrió un error al intentar guardar: '.$e->getMessage());
		}
		
	}

	

	function evt__cerrar()
	{
		/* ========== VALIDACI? DE LOS DATOS MAS IMPORTANTES ========================*/
		
		//Validaci? de la declaracion de un cargo
		if( ! $this->datos('persona','cargos')->get_filas()){
			$this->dep('ci_edicion')->set_pantalla('pant_datos_personales');
			throw new toba_error("Para cerrar la solicitud, es obligatoria la declaración de al menos un cargo en la solapa 'Información Personal'",0);
		}	

		//Validaci? de la carga de documentaci? para solicitudes de tipo A
		$solicitud = $this->datos('subsidio','solicitud_subsidio')->get();
		

		if($solicitud['tipo_subsidio'] == 'A'){

			//controlo que haya subido alguna documentacion
			if( !$this->datos('subsidio','docum_solicitud')->get_filas()){
				$this->dep('ci_edicion')->set_pantalla('pant_solicitud');
				throw new toba_error("Para cerrar la solicitud, debe adjuntar al menos una documentación.");
			}
			//controlo que haya cargado los datos del congreso
			if( !$this->datos('subsidio','congreso')->get()){
				$this->dep('ci_edicion')->set_pantalla('pant_congreso');
				throw new toba_error("Para cerrar la solicitud, debe completar los datos del congreso.");
			}
		}	

		if($solicitud['tipo_subsidio'] == 'B'){

			if($this->datos('subsidio','docum_solicitud')->get_filas()){

				if(count($this->datos('subsidio','docum_solicitud')->get_filas()) < 2){
					$this->dep('ci_edicion')->set_pantalla('pant_solicitud');
					throw new toba_error("Para cerrar la solicitud, debe adjuntar al menos dos documentaci?es.");
				}
			}else{
				$this->dep('ci_edicion')->set_pantalla('pant_solicitud');
				throw new toba_error("Para cerrar la solicitud, debe adjuntar al menos una documentación.");
			}
			//controlo que haya cargado los datos de la estadia
			if( !$this->datos('subsidio','estadia')->get()){
				$this->dep('ci_edicion')->set_pantalla('pant_estadia');
				throw new toba_error("Para cerrar la solicitud, debe completar los datos de la estadía.");
			}
		}	

		//cierro la solicitud para evitar modificaciones futuras
		$this->datos('subsidio','solicitud_subsidio')->set(array('estado'=>'C'));
		
		$solicitud = $this->datos('subsidio','solicitud_subsidio')->get();

		
		$datos = "";
			
		if($solicitud['tipo_subsidio'] == 'A'){
			$evento = $this->datos('subsidio','congreso')->get();
			$datos .= $evento['costo_inscripcion'].$evento['abstract'];

		}else{
			$evento = $this->datos('subsidio','estadia')->get();
			$datos .= $evento['institucion'].$evento['lugar'];
		}

		$datos .= $solicitud['nro_documento'].
				 $solicitud['tipo_subsidio'].
				 $solicitud['dependencia'].
				 $solicitud['codigo_proyecto'].
				 $evento['costo_estadia'].
				 $evento['costo_pasajes'].
				 $evento['fecha_desde'].
				 $evento['fecha_hasta'];

		$hash_cierre = md5($datos);
		$this->datos('subsidio','solicitud_subsidio')->set(array('hash_cierre'=>$hash_cierre));

		//guardo todos los datos que haya podido realizar el usuario
		$this->evt__guardar();
		
		toba::notificacion()->agregar("La solicitud se ha cerrado con ?ito. Puede imprimir el Comprobante de Solicitud haciendo click en el ícono con el logo PDF",'info');

	}	

	function servicio__ver_comprobante()
	{
		$parametros = toba::memoria()->get_parametros();
		$id = $parametros['id_solicitud'];

		//obtengo todos los datos necesarios para enviar al reporte PDF
		$parametros = toba::consulta_php('co_subsidios')->get_resumen_informe($id);
		
		//Defini una arreglo con las descripciones literales de las categor?s de incentivos y reemplazo.
		$categorias = array('1'=>'Categoría I',
							'2'=>'Categoría II',
							'3'=>'Categoría III',
							'4'=>'Categoría IV',
							'5'=>'Categoría V');
		$parametros['cat_incentivos'] = $categorias[$parametros['cat_incentivos']];


		//guardo todos los datos para el reporte y navego hacia la operaci? que lo genera
		toba::memoria()->set_dato('datos',$parametros);
		toba::vinculador()->navegar_a(toba::proyecto()->get_id(), 3641);
	}


	

	/*function conf_evt__cuadro_subsidios__ver_comprobante(toba_evento_usuario $evento, $fila)
	{
		//obtengo la clave del registro de la fila en cuestion
		$clave = sap_ei_cuadro::recuperar_clave_fila(4199,$fila);
		//obtengo el estado de esa solicitud, filtrando con la clave obtenida anteriormente
		$estado = toba::consulta_php('co_subsidios')->get_subsidios(array('id_solicitud'=>$clave['id_solicitud']));
		$estado = $estado[0]['estado'];
		//si el estado es Cerrado, ya no puedo modifica
		if($estado == 'C'){
			$evento->mostrar();
		}else{
			$evento->ocultar();
		}
	}*/


	function vista_jasperreports(toba_vista_jasperreports $report) 
	{
		//ei_arbol($report); return;

		$path = toba::proyecto()->get_www()['path']."reportes/solicitud_subsidios/sap_solicitudes_subsidio.jasper";
		//$path = toba::proyecto()->get_www()['path']."reportes/solicitud_subsidios";
		//ei_arbol(toba::proyecto()->get_www()['path']."reportes/solicitud_subsidios/"); return;
		$report->set_path_reporte($path);
		$report->set_parametro('desc_solicitud','S','Buen d?!');
		$db = toba::db('sap');
		//nl2br(var_dump($db));
		$report->set_conexion($db);

	}
	

	

	

}
?>