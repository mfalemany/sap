<?php
class ci_edicion_subsidio extends sap_ci
{
	protected $s__tipo_subsidio;
	protected $s__estado_inicial;

	function conf()
	{
		if($this->datos('subsidio','congreso')->esta_cargada()){
			$this->s__tipo_subsidio = 'A';
		}
		if($this->datos('subsidio','estadia')->esta_cargada()){
			$this->s__tipo_subsidio = 'B';
		}

		if( ! $this->datos('subsidio','solicitud_subsidio')->esta_cargada()){
			//elimino el evento "eliminar" si se esta cargando una nueva solicitud
			$this->controlador()->pantalla()->eliminar_evento('eliminar');
		}else{
			//verifico si esta vigente la convocatoria de la solicitud elegida, caso contrario, bloqueo todos los formularios
			$solic = $this->datos('subsidio','solicitud_subsidio')->get();
			//obtengo los detalles de la convocatoria
			$conv = toba::consulta_php('co_convocatorias')->get_detalles_convocatoria($solic['id_convocatoria']);
			
			//variable que contendra lo elementos que no se bloquearan en la llamada a "bloquear_modificaciones"
			$excepciones = array();
			
			//si la fecha tope de edicion todavia no llego, el usuario puede modificar la documentacion adjunta
			if($conv['limite_edicion'] > date('Y-m-d')){
				//pero solo cuando el tipo de subsidio es 'A'
				if($this->s__tipo_subsidio == 'A'){
					$excepciones[] = 'ml_documentacion';	
				}
			}

			//si el estado de la solicitud no es Abierto, o la convocatoria no esta abierta, el usuario ya no puede modificar nada
			if($solic['estado'] != 'A' || $conv['estado'] != 'A'){
				$this->bloquear_modificaciones($excepciones);
			}

			//si la fecha de cierre de la convocatoria ya paso, el usuario no puede modificar nada
			if($conv['fecha_hasta'] < date('Y-m-d')){
				$this->bloquear_modificaciones($excepciones);
				$this->agregar_notificacion('La solicitud seleccionada pertenece a una convocatoria que ya no está vigente o se encuentra inactiva. No podrá realizar modificaciones a la solicitud.','error');
			}

		}

		if(isset($this->s__tipo_subsidio)){
			switch ($this->s__tipo_subsidio) {
				case 'A':
					$this->pantalla()->eliminar_tab('pant_estadia');
					break;
				case 'B':
					$this->pantalla()->eliminar_tab('pant_congreso');
					break;
			}
		}else{
			$this->pantalla()->eliminar_tab('pant_congreso');
			$this->pantalla()->eliminar_tab('pant_estadia');
		}
	}

	private function bloquear_modificaciones($excepciones = array())
	{
		$eventos_eliminar = array('eliminar','cerrar','desestimar');
		foreach ($eventos_eliminar as $evento) {
			if($this->controlador()->pantalla()->existe_evento($evento)){
				$this->controlador()->pantalla()->eliminar_evento($evento);
			}
		}

		//si existe algun formulario que no se bloquee, entonces algo se puede modificar
		if(count($excepciones) == 0){
			$this->controlador()->pantalla()->eliminar_evento('guardar');	
		}
		
		//obtengo todos los formularios que dependen del CI, y los pongo como solo lectura
		$forms = $this->get_dependencias_clase('ei_form');
		foreach($forms as $form){
			if( ! in_array($form,$excepciones)){
				//se establece el formulario como solo_lectura
				$this->dep($form)->set_solo_lectura();	

				//si el formulario es multilinea, se desactiva el agregado de filas
				if(strpos(get_class($this->dep($form)),'_ml')){
					$this->dep($form)->desactivar_agregado_filas();
				}
			}
		}
	}

	//-----------------------------------------------------------------------------------
	//---- form_solicitud ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function conf__form_solicitud(form_solicitud $form)
	{
		//el primer if se cumple si el datos tabla tiene datos cargados. Esto ocurre tanto cuando se esta editando una solicitud previamente guardada, como cuando se esta cargando una nueva solicitud y se vuelve al formulario de solicitud.
		if($this->datos('subsidio','solicitud_subsidio')->get()){
			//este segundo if se cumple solamente cuando se esta editando una solicitud previamente cargada
			if($this->datos('subsidio','solicitud_subsidio')->esta_cargada()){
				$form->set_solo_lectura(array('id_convocatoria','tipo_subsidio','id_dependencia'));
			}
			$datos = $this->datos('subsidio','solicitud_subsidio')->get();
			//datos de la convocatoria elegida
			$conv = toba::consulta_php('co_convocatorias')->get_detalles_convocatoria($datos['id_convocatoria']);
			//asigno los datos
			$form->set_datos($datos);
			//asigno solo la opcion de la convocatoria que eligio la persona			
			$form->ef('id_convocatoria')->set_opciones(array($conv['id']=>$conv['nombre']));
		}else{
			//Si el formulario esta completamente vacio (y tambien el datos tabla)
			$convs = toba::consulta_php('co_convocatorias')->get_convocatorias_vigentes_subsidios();
			foreach($convs as $convocatoria){
				$opciones[$convocatoria['id']] = $convocatoria['nombre']; 
			}
			$form->ef('id_convocatoria')->set_opciones($opciones);
		}
	}

	function evt__form_solicitud__modificacion($datos)
	{
		$this->datos('subsidio','solicitud_subsidio')->set($datos);
		if( ! $datos['tipo_subsidio']){
			throw new toba_error("Debe seleccionar el tipo de subsidio que desea solicitar",'error');
		}else{
			$this->s__tipo_subsidio = $datos['tipo_subsidio'];	
		}
	}

	//-----------------------------------------------------------------------------------
	//---- ml_documentacion -------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml_documentacion(sap_ei_formulario_ml $form_ml)
	{

		if($this->datos('subsidio','docum_solicitud')->get_filas()){
			$datos = $this->datos('subsidio','docum_solicitud')->get_filas();
			$this->s__estado_inicial = $datos;
			$form_ml->set_datos($datos);
		}
	}

	function evt__ml_documentacion__modificacion($datos)
	{
		$ruta = $this->get_ruta();
		$campos = array(array('nombre' => 'descripcion'));
		$this->procesar_archivos($this->s__estado_inicial,$datos,$ruta,$campos,'archivo');
		$this->datos('subsidio','docum_solicitud')->procesar_filas($datos);
	}


	//-----------------------------------------------------------------------------------
	//---- form_datos_personales --------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_datos_personales(form_datos_principales $form)
	{
		if($this->datos('persona','personas')->get()){
			$form->set_datos($this->datos('persona','personas')->get());
			//Se da por cargado el formulario 
			if($form->ef('cuil')->get_estado()){
				$form->set_solo_lectura(array('cuil'));
			
			}
			$form->set_solo_lectura(array('nro_documento'));
		}

	}

	function evt__form_datos_personales__modificacion($datos)
	{

		if( ! $datos['archivo_cvar']){
			unset($datos['archivo_cvar']);
		}else{
			$persona = $this->datos('persona','personas')->get();
			$carpeta = 'docum_personal/'.$persona['nro_documento']."/";
			toba::consulta_php('helper_archivos')->subir_archivo($datos['archivo_cvar'],$carpeta,'cvar.pdf');
			$datos['archivo_cvar'] = 'cvar.pdf';
		}
		$this->datos('persona','personas')->set($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- ml_cargos --------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml_cargos(sap_ei_formulario_ml $form_ml)
	{

		//ei_arbol($this->datos('persona','personas')->get_filas());
		if($this->datos('persona','cargos')->get_filas()){
			$form_ml->set_datos($this->datos('persona','cargos')->get_filas());	
		}
	}

	function evt__ml_cargos__modificacion($datos)
	{

		$this->datos('persona','cargos')->procesar_filas($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- ml_cat_incentivos ------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml_cat_incentivos(sap_ei_formulario_ml $form_ml)
	{
		if($this->datos('persona','cat_incentivos')->esta_cargada()){
			$form_ml->set_datos($this->datos('persona','cat_incentivos')->get_filas());
		}
		$form_ml->desactivar_agregado_filas();
		$form_ml->set_solo_lectura();

	}

	function evt__ml_cat_incentivos__modificacion($datos)
	{
	}

	//-----------------------------------------------------------------------------------
	//---- form_congreso ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_congreso(sap_ei_formulario $form)
	{
		if($this->datos('subsidio','congreso')->get()){
			$form->set_datos($this->datos('subsidio','congreso')->get());
		}
	}

	function evt__form_congreso__modificacion($datos)
	{
		//Validación de fechas desde y hasta
		if($datos['fecha_desde']>$datos['fecha_hasta']){
			throw new toba_error('La fecha "Desde" debe ser anterior o igual a la fecha "Hasta"','La fecha desde no puede ser mayor a la fecha hasta. Por favor, verifique los datos ingresados en ambos campos','Fechas no válidas');
		}
		//Validación de longitud del abstract (debe tener un mínimo de 300 palabras)
		if(str_word_count($datos['abstract']) < 300){
			throw new toba_error('El campo abstract debe tener un mínimo de 300 palabras. Solo se ingresaron '.str_word_count($datos['abstract']),null,'La longitud del abstract no es suficiente');
		}


		$this->datos('subsidio','congreso')->set($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- form_estadia -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_estadia(sap_ei_formulario $form)
	{
		if($this->datos('subsidio','estadia')->get()){
			$form->set_datos($this->datos('subsidio','estadia')->get());
		}
	}

	function evt__form_estadia__modificacion($datos)
	{
		$this->datos('subsidio','estadia')->set($datos);
	}

	

	

	

	

	function ajax__buscar_datos_ws($nro_documento = NULL, toba_ajax_respuesta $respuesta)
	{
		if( ! $nro_documento){
			return array();
		}
		$respuesta->set(toba::consulta_php('co_personas')->buscar_en_ws($nro_documento));

	}




	function extender_objeto_js()
	{
		echo "
			console.log({$this->objeto_js}.dep('form_solicitud'));
			/*var tipo = {$this->objeto_js}.dep('form_solicitud').ef('tipo').get_estado();
			
			switch(tipo) {
				case 'A':
					{$this->objeto_js}.ocultar_tab('pant_estadia');
					{$this->objeto_js}.mostrar_tab('pant_congreso');
					break;
				case 'B':
					{$this->objeto_js}.ocultar_tab('pant_estadia');
					{$this->objeto_js}.mostrar_tab('pant_congreso');
					break;
				default:
					{$this->objeto_js}.ocultar_tab('pant_estadia');
					{$this->objeto_js}.ocultar_tab('pant_congreso');
			}*/
			";
	}

	function datos($relacion,$tabla = NULL)
	{
		return $this->controlador()->datos($relacion, $tabla);
	}

	/**
	 * Esta funcion procesa los archivos involucrados en un formulario ML. Por cada linea pasada al ML, esta funcion procesa si se trata de un Alta, Baja o Modificacion, y en consecuencia, Sube, Modifica o Elimina archivos vinculados a cada linea. Recibe como parametros el estado inicial del ML, el estado luego de la moficiacion, la ruta donde se almacenaran los archivos y los nombres de los campos del ML que se utilizaran para darle el nombre a cada archivo
	 * @param  array $estado_inicial_ml     Estado del ML al cargar el formulario
	 * @param  array $estado_actual_ml      Estado del ML luego de que el usuario realiza cambios
	 * @param  string $ruta                 Ruta donde se almacenaran/eliminaran los archivos involucrados
	 * @param  array $campos_nombre_archivo Campos del ML que se utilizan para generar el nombre del archivo subido. Ejemplo: $campos = array(
						array('nombre' => 'anio_ingreso'),
						array('nombre' => 'anio_egreso', 'defecto' => 'Actualidad'),
						array('nombre' => 'institucion'),
						array('nombre' => 'cargo')
						);
						El array del ejemplo genera un archivo llamado: 2002-2007-UNNE-titular.pdf
	 * @param  string $nombre_input         Nombre del ef_upload que contiene el/los archivos subidos
	 * @return void                        
	 */
	protected function procesar_archivos($estado_inicial_ml,&$estado_actual_ml,$ruta,$campos_nombre_archivo,$nombre_input)
	{
		
		//para cada linea del ML
		foreach($estado_actual_ml as $fila => $item){
			//se genera el nombre del archivo
			$nombre = '';
			foreach($campos_nombre_archivo as $campo){
				//agrega un guion medio entre cada palabra del nombre
				if(strlen($nombre)>0){
					$nombre .= "-";
				}
				$nombre .=  ($item[$campo['nombre']]) ? $item[$campo['nombre']] : $campo['defecto'];
			}
			$nombre .= '.pdf'; 

			// =========== ALTA Y MODIFICACIoN ===============
			
			if($item['apex_ei_analisis_fila'] == 'A' || $item['apex_ei_analisis_fila'] == 'M'){
				if($item[$nombre_input]){
					//en el caso de una modificacion, se elimina el archivo previo
					if(isset($estado_inicial_ml)){
						if($estado_inicial_ml[$nombre_input]){
							toba::consulta_php('helper_archivos')->eliminar_archivo($ruta,$estado_inicial_ml[$nombre_input]);	
						}
					}
					
					//se sube el nuevo archivo
					if( ! toba::consulta_php('helper_archivos')->subir_archivo($item[$nombre_input],$ruta,utf8_encode($nombre))){
						//se utiliza substr y strlen para quitar el ".pdf" al final del nombre de la actividad
						toba::notificacion()->agregar("No se pudo subir la documentacion probatoria correspondiente a la actividad: ".substr($nombre,0,(strlen($nombre)-4) ) );
					}

					$estado_actual_ml[$fila][$nombre_input] = $nombre;
					//ei_arbol($estado_actual_ml[$fila][$nombre_input]);
				}else{
					//si no se subio un archivo, pero se modifico la descripcion, se modifica el nombre del archivo
					if(file_exists($ruta.utf8_encode($estado_inicial_ml[$fila][$nombre_input])) && is_file($ruta.utf8_encode($estado_inicial_ml[$fila][$nombre_input]) )){
						rename($ruta.utf8_encode($estado_inicial_ml[$fila][$nombre_input]),$ruta.utf8_encode($nombre) );
						$estado_actual_ml[$fila][$nombre_input] = $nombre;
					}else{
						//esta linea sirve para que el formulario (cuando no se define un nuevo archivo) no pise el estado anterior
						unset($estado_actual_ml[$fila][$nombre_input]);		
					}
					
				}
			}
				
			//si se esta dando de baja un registro, se busca su nombre de archivo y se lo elimina tambien
			if($item['apex_ei_analisis_fila'] == 'B'){
				foreach($estado_inicial_ml as $linea){
					if($linea['x_dbr_clave'] == $fila){
						$archivo = $ruta.utf8_encode($linea[$nombre_input]);
						toba::consulta_php('helper_archivos')->eliminar_archivo($archivo);
					}
				}
			}
		}
	}

	//genera y retorna la ruta donde se guardaran los archivos para la solicitud en curso
	function get_ruta(){
		$detalles = $this->datos('subsidio','solicitud_subsidio')->get();
		$conv = $detalles['id_convocatoria'];
		$tipo_subsidio = $detalles['tipo_subsidio'];
		$nro_documento = toba::usuario()->get_id();
		return 'subsidios/convocatorias/'.$conv."/".$tipo_subsidio."/".$nro_documento."/";
	}

}


?>