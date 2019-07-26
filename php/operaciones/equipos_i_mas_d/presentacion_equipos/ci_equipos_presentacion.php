<?php
require_once('consultas/co_convocatorias.php'); 
require_once('consultas/co_equipos.php'); 

class ci_equipos_presentacion extends sap_ci
{
	public $s__convocatoria;      
	public $s__equipo;
	
	function ini()
	{
		//$this->dep('cu_convocatorias_vigentes')->eliminar_evento('eliminar');
		
	}
	
	function ini__operacion()
	{
//        $this->s__comunicacion = toba::memoria()->get_dato('comunicacion');
//        
//        if (isset($this->s__comunicacion) && ($this->s__comunicacion['id']!=0)){
//                  $this->dep('ci_comunicacion_edicion')->dep('datos')->cargar($this->s__comunicacion);
//                  $datos=$this->dep('ci_comunicacion_edicion')->dep('datos')->tabla('comunicacion')->get();
//                  $this->s__convocatoria=$datos['sap_convocatoria_id'];
//                  $this->set_pantalla('p_comunic_edicion');
//             }   
	}
	//-----------------------------------------------------------------------------------
	//---- cu_convocatorias_vigentes ----------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cu_convocatorias_vigentes(sap_ei_cuadro $cuadro)
	{
		$datos = co_convocatorias::get_convocatoriasVigentes('EQUIPOS');
		$cuadro->set_datos($datos);
	}
	
	function evt__cu_convocatorias_vigentes__seleccion($seleccion)
	{
		$this->s__convocatoria = $seleccion['id'];
		$this->set_pantalla('p_equipos');
	}

	//-----------------------------------------------------------------------------------
	//---- cu_vista_equipos -------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cu_vista_equipos(sap_ei_cuadro $cuadro)
	{
		$cuadro->set_datos(toba::consulta_php('co_equipos')->get_equipos_por_usuario(toba::usuario()->get_id()));
	}

	function evt__cu_vista_equipos__seleccion($seleccion)
	{
		$this->dep('ci_equipo_edicion')->dep('datos')->cargar($seleccion);
		$this->set_pantalla('p_equipo_edicion');
	}

	//-----------------------------------------------------------------------------------
	//---- cu_equipos -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cu_equipos(sap_ei_cuadro $cuadro)
	{
		//$cuadro->desactivar_modo_clave_segura();
		$convocatoria= $this->s__convocatoria ;
		$usuario=toba::usuario()->get_id();
		$datos = co_equipos::get_equipos_por_usuario($usuario);
		$cuadro->set_titulo('Equipo del Usuario: ' . toba::usuario()->get_nombre());
		$cuadro->set_datos($datos);
	}

	
	function evt__cu_equipos__seleccion($seleccion)
	{
		$this->dep('ci_equipo_edicion')->dep('datos')->cargar($seleccion);
		$this->set_pantalla('p_equipo_edicion');
	}
	
	function evt__cu_equipos__inscribir($seleccion)
	{
		if(co_equipos::esta_inscripto($seleccion['id'],$this->s__convocatoria)){
			//este condicional tiene sentido por un bug de toba que no configura el evento correctamente al iniciar la operación
			toba::notificacion()->agregar('Inscripción registrada!','info');
		}else{
			//cargo el DR
			$this->dep('ci_equipo_edicion')->dep('datos')->cargar($seleccion);
			//unset($seleccion['id']);

			//armo el array de ids necesarios para la tabla equipo_convocatoria
			$seleccion['id_equipo'] = $seleccion['id'];
			$seleccion['id_convocatoria'] = $this->s__convocatoria;

			//agrego una nueva fila y sincronizo
			$this->dep('ci_equipo_edicion')->dep('datos')->tabla('equipo_convocatoria')->nueva_fila($seleccion);
			$this->dep('ci_equipo_edicion')->dep('datos')->sincronizar();

			toba::notificacion()->agregar('Inscripción registrada!','info');
		}
	}

	function conf_evt__cu_equipos__inscribir(toba_evento_usuario $evento, $fila)
	{
		$equipo = toba_ei_cuadro::recuperar_clave_fila(2529,$fila);
		if(co_equipos::esta_inscripto($equipo['id'],$this->s__convocatoria)){
			$evento->anular();
		}
	}

	//-----------------------------------------------------------------------------------
	//---- cu_convocatorias_vigentes ----------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cu_hist_presentaciones(sap_ei_cuadro $cuadro)
	{
		$cuadro->set_datos(toba::consulta_php('co_equipos')->get_historial_presentaciones(toba::usuario()->get_id()));
	}
	
	function conf_evt__cu_hist_presentaciones__ver_certificado(toba_evento_usuario $evento, $fila)
	{
		$evento->ocultar();
		$params = toba_ei_cuadro::recuperar_clave_fila(4314,$fila);
		
		//solo se muestra el evento cuando existe el archivo de plantilla, y el equipo se presentó
		if(isset($params['id_convocatoria'])){
			$path = toba::proyecto()->get_www();
			$path = $path['path'];
			if(file_exists($path.'img/plantillas_certificados/'.$params['id_convocatoria'].'.png')){
				if(isset($params['asistio'])){
					if($params['asistio'] == 'S'){
						$evento->mostrar();
					}
				}
			}
		}
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__agregar()
	{
		$this->set_pantalla('p_equipo_edicion'); 
	}

	function evt__cancelar()
	{
		$this->set_pantalla('p_convocatorias');
	}

	function servicio__ver_certificado()
	{
		//obtengo los parametros del evento
		$params = toba::memoria()->get_parametros();

		$datos = toba::consulta_php('co_equipos')->get_detalles_certificado($params);

		//genero el PDF y lo muestro
		$pdf = new certificado_equipos($datos);
		$pdf->mostrar();
	}


	

	

	

}
?>