<?php
class ci_reporte_generacion_certificados extends sap_ci
{
	protected $s__filtro;
	private $path;

	function conf()
	{
		$path = toba::proyecto()->get_www();
        $this->path = $path['path']."img/plantillas_certificados/";
	}

	function conf__filtro_certificados(sap_ei_formulario $form)
	{
		$filtro = isset($this->s__filtro) ? $this->s__filtro : NULL;
		$form->set_datos($filtro);
	}

	function evt__filtro_certificados__filtrar($datos)
	{
		$this->s__filtro = $datos;
	}

	function evt__filtro_certificados__cancelar()
	{
		unset($this->s__filtro);
	}

	function conf__cu_certificados(sap_ei_cuadro $cuadro)
	{
		$cuadro->desactivar_modo_clave_segura();
		if(isset($this->s__filtro)){
			$cuadro->set_datos(toba::consulta_php('co_comunicaciones')->get_reporte_certificados($this->s__filtro));
		}else{
			$cuadro->set_eof_mensaje('Debe seleccionar una convocatoria y filtrar para obtener datos');
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
		//La plantilla se nombra así: [id_convocatoria]_[tipo_certificado].png
		if(file_exists($this->path.$params['1'].$tipo_certificado.'.png')){
			$evt->mostrar();
		}else{
			$evt->ocultar();
		}
	}

	protected function mostrar_certificado($params,$plantilla)
	{
		//busco los detalles de la comunicación elegida
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