<?php
require_once('consultas/co_convocatorias.php'); 
require_once('consultas/co_comunicaciones.php'); 
class ci_comunicacion extends sap_ci
{
      public $s__convocatoria;      
      public $s__comunicacion;
      protected $path;
   
    function conf()
    {
		$path = toba::proyecto()->get_www();
        $this->path = $path['path']."img/plantillas_certificados/";
    }


	function ini()
	{
        $this->dep('cuadro')->eliminar_evento('eliminar');

	}
	
	//-----------------------------------------------------------------------------------
	//---- cuadro ------------------------------------------------- ----------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(sap_ei_cuadro $cuadro)
	{
            $datos = co_convocatorias::get_convocatoriasVigentes('BECARIOS');
            $cuadro->set_datos($datos);
	}

	function evt__cuadro__seleccion($seleccion)
	{
            $this->s__convocatoria = $seleccion['id'];
            $this->set_pantalla('p_comunicaciones');
            
	}


	
	//-----------------------------------------------------------------------------------
	//---- cuadro_historial -------------------------------------- ----------------------
	//-----------------------------------------------------------------------------------

	function conf__cu_certificados(sap_ei_cuadro $cuadro)
	{
		$cuadro->set_titulo('Historial de comunicaciones presentadas');
		$cuadro->agregar_columnas(array(array('titulo'=>'Convocatoria','clave'=>'nombre_convocatoria')));
		$cuadro->eliminar_columnas(array('area','autores'));
		$cuadro->desactivar_modo_clave_segura();
		$filtro = array('nro_documento'=>toba::usuario()->get_id());
		$cuadro->set_datos(toba::consulta_php('co_comunicaciones')->get_reporte_certificados($filtro));
	}
	/*function conf__cuadro_historial(sap_ei_cuadro $cuadro)
	{
		$cuadro->desactivar_modo_clave_segura();
        $datos = co_comunicaciones::get_historial_comunicaciones(toba::usuario()->get_id());
        $cuadro->set_datos($datos);
	}*/

	/**
	 * Verifica si existe una plantilla para certificados que corresponda a la comunicaci? seleccionada. En caso de no existir la plantila (no se puede generar el certificado), oculta el evento.
	 * @param  toba_evento_usuario $evt  Evento producido
	 * @param  integer              $fila Fila del cuadro sobre la cual ocurri?el evento
	 * @return void                    
	 */
	/*function conf_evt__cuadro_historial__ver_certificado(toba_evento_usuario $evt, $fila)
	{
		//obtengo el ID de la comunicacion seleccionada
		$id_comunicacion = $evt->get_parametros();
		if(!$id_comunicacion){
			$evt->ocultar();
			return;
		}

		$es_aprobada = FALSE;

		//verifico que la comunicacion tenga estado "Aceptado" o "Seleccionado"
		$evaluacion = toba::consulta_php('co_comunicaciones')->get_ultima_evaluacion($id_comunicacion);

		//si no tiene evaluacion realizadas, no puede tener certificado
		if(count($evaluacion) > 0){
			if($evaluacion['evaluacion'] == 'APROBADA' || $evaluacion['evaluacion'] == 'SELECCIONADA'){
				$es_aprobada = TRUE;
			}
		}

		//obtengo los detalles de esa comunicacion (incluyendo el ID de convocatoria al que pertenece)
		$det_comunicacion = toba::consulta_php('co_comunicaciones')->get_comunicacion($id_comunicacion);

		//ei_arbol($det_comunicacion); die;
		$id = $det_comunicacion['sap_convocatoria_id'];

		

		//si no existe el archivo de plantilla para esta convocatoria, se elimina el evento
		if(file_exists($this->path.$id.'.png') && $es_aprobada){

			$evt->mostrar();
		}else{
			$evt->ocultar();
		}
			
	}
*/
	/*function servicio__ver_certificado()
	{
		//obtengo los parametros del evento
		$params = toba::memoria()->get_parametros();

		//busco los detalles de la comunicaci? elegida
		$datos = array_shift(toba::consulta_php('co_comunicaciones')->get_reporte_certificados(array('id_comunicacion'=>$params['id'])));

		//Obtengo el becario de la lista de autores, y le saco el sufijo "(Becario)"
		$datos['autor'] = array_shift(explode('/',$datos['autores']));
		$datos['autor'] = str_replace(' (Becario)','',$datos['autor']);

		//genero el PDF y lo muestro
		$pdf = new certificado_comunicaciones($datos);
		$pdf->mostrar();
	}*/

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------


	function evt__agregar()
	{
            $this->set_pantalla('p_comunic_edicion'); 
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro_comunicaciones --------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_comunicaciones(sap_ei_cuadro $cuadro)
	{
            $convocatoria= $this->s__convocatoria ;
            
            $datos = co_comunicaciones::get_comunicacionesByConvocatoriaUsuario ($convocatoria, toba::usuario()->get_id());
            $cuadro->set_titulo('Comunicaciones Presentadas por: ' . toba::usuario()->get_nombre());
            $cuadro->set_datos($datos);
	}

	function evt__cuadro_comunicaciones__seleccion($seleccion)
	{
            $this->s__comunicacion= $seleccion;
            $this->dep('ci_comunicacion_edicion')->dep('datos')->cargar($seleccion);
            $this->set_pantalla('p_comunic_edicion');
            
	}

	//-----------------------------------------------------------------------------------
	//---- conf del CI --------- --------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function ini__operacion()
	{

                $this->s__comunicacion = toba::memoria()->get_dato('comunicacion');
                //var_dump($this->s__comunicacion);
                if (isset($this->s__comunicacion) && ($this->s__comunicacion['id']!=0)){
                     $this->dep('ci_comunicacion_edicion')->dep('datos')->cargar($this->s__comunicacion);
                     $datos=$this->dep('ci_comunicacion_edicion')->dep('datos')->tabla('comunicacion')->get();
                     $this->s__convocatoria=$datos['sap_convocatoria_id'];
                     $this->set_pantalla('p_comunic_edicion');
                } 
            
           
           
        }



	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__p_comunicaciones(toba_ei_pantalla $pantalla)
	{   
            $en_evaluacion=  co_convocatorias::esta_en_evaluacion($this->s__convocatoria);
            if ($en_evaluacion){
                $pantalla->eliminar_evento('agregar');
            }
	}


	function evt__p_comunicaciones__entrada()
	{
		/* Se eval?a si la convocatoria seleccionada est?cerrada. De ser as?
		   el usuario solo podr?modificar comunicaciones anteriores en estado 
		   de evaluacion, pero no podr?agregar nuevas comunicaciones. 
		*/
		$detalles = co_convocatorias::get_detalles_convocatoria($this->s__convocatoria);
		if(isset($detalles['fecha_hasta']) ) {
			if(date($detalles['fecha_hasta']) < date('Y-m-d')){
				$this->pantalla()->eliminar_evento('agregar');
			}
		}
		
		
	}



	/**
	 * Verifica si existe una plantilla para certificados que corresponda a la comunicaci? seleccionada. En caso de no existir la plantila (no se puede generar el certificado), oculta el evento.
	 * @param  toba_evento_usuario $evt  Evento producido
	 * @param  integer              $fila Fila del cuadro sobre la cual ocurri?el evento
	 * @return void                    
	 */
	function conf_evt__cu_certificados__ver_certificado_participacion(toba_evento_usuario $evt, $fila)
	{
		$this->configurar_eventos($evt,$fila,'_participacion');
	}

	function servicio__ver_certificado_participacion()
	{
		//obtengo los parametros del evento
		$params = toba::memoria()->get_parametros();
		$plantilla = $params['id_convocatoria']."_participacion";
		$this->mostrar_certificado($params,$plantilla);
	}

	function conf_evt__cu_certificados__ver_certificado_seleccionado(toba_evento_usuario $evt, $fila)
	{
		$parametros = explode('||',$evt->get_parametros());
		//si la evaluacion es "seleccionada"
		if($parametros[2] == 7){
			$this->configurar_eventos($evt,$fila,'_seleccionado');	
		}else{
			$evt->ocultar();
		}
		
	}

	function servicio__ver_certificado_seleccionado()
	{
		//obtengo los parametros del evento
		$params = toba::memoria()->get_parametros();
		$plantilla = $params['id_convocatoria']."_seleccionado";
		$this->mostrar_certificado($params,$plantilla);
	}

	function conf_evt__cu_certificados__ver_certificado_mejor_trabajo(toba_evento_usuario $evt, $fila)
	{
		$parametros = explode('||',$evt->get_parametros());
		//Si la comunicacion es "Mejor Trabajo"
		if($parametros[3] == 'S'){
			$this->configurar_eventos($evt,$fila,'_mejor_trabajo');	
		}else{
			$evt->ocultar();
		}
	}

	function servicio__ver_certificado_mejor_trabajo()
	{
		//obtengo los parametros del evento
		$params = toba::memoria()->get_parametros();
		$plantilla = $params['id_convocatoria']."_mejor_trabajo";
		$this->mostrar_certificado($params,$plantilla);
	}

	protected function configurar_eventos(&$evt,$fila,$tipo_certificado)
	{
		$params = explode('||',$evt->get_parametros());
		//obtengo el ID de la comunicacion seleccionada
		$id_comunicacion = $params[0];

		//si no existe el archivo de plantilla para esta convocatoria, se elimina el evento
		//La plantilla se nombra as? [id_convocatoria]_[tipo_certificado].png
		if(file_exists($this->path.$params['1'].$tipo_certificado.'.png')){
			$evt->mostrar();
		}else{
			$evt->ocultar();
		}
	}

	protected function mostrar_certificado($params,$plantilla)
	{
		//busco los detalles de la comunicaci? elegida
		$datos = array_shift(toba::consulta_php('co_comunicaciones')->get_reporte_certificados(array('id_comunicacion'=>$params['id_comunicacion'])));

		//Obtengo el becario de la lista de autores, y le saco el sufijo "(Becario)"
		$datos['autor'] = array_shift(explode('/',$datos['autores']));
		$datos['autor'] = str_replace(' (Becario)','',$datos['autor']);

		//genero el PDF y lo muestro
		$pdf = new certificado_comunicaciones($datos,$plantilla);
		$pdf->mostrar();
	}

}
?>