<?php
class ci_proyectos extends sap_ci
{
	protected $s__filtro;
	//-----------------------------------------------------------------------------------
	//---- cuadro_proyecto --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_proyecto(sap_ei_cuadro $cuadro)
	{
		$where = isset($this->s__filtro) ? $this->dep('filtro_proyecto')->get_sql_where() : '1=1';
		$datos = toba::consulta_php('co_proyectos')->get_proyectosByFiltros($where);
		$cuadro->set_datos($datos);
	}

	function evt__cuadro_proyecto__seleccion($seleccion)
	{
		$this->get_datos()->cargar($seleccion);
		$this->set_pantalla('pant_edicion');
	}

	//-----------------------------------------------------------------------------------
	//---- filtro_proyecto --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro_proyecto(sap_ei_filtro $filtro)
	{
		if ( isset($this->s__filtro ) ) {
			$filtro->set_datos($this->s__filtro);
		}
	}

	function evt__filtro_proyecto__filtrar($datos)
	{
		$this->s__filtro=$datos;
	}

	function evt__filtro_proyecto__cancelar()
	{
		unset($this->s__filtro);
	}

	


	//-----------------------------------------------------------------------------------
	//-------------- DATOS --------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function get_datos($relacion = 'datos',$tabla = NULL)
	{
		if($tabla){
			return $this->dep($relacion)->tabla($tabla);
		}else{
			return $this->dep($relacion);
		}
	}

	//-----------------------------------------------------------------------------------
	//-------------- EVENTOS ------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
	function evt__guardar()
	{
		//Guardo los detalles del proyecto para luego buscar el código asignado y mostrarlo al usuario
		$datos = $this->get_datos('datos','sap_proyectos')->get();

		$filtro = array('fecha_desde' => $datos['fecha_desde'],
						'fecha_hasta' => $datos['fecha_hasta'],
						'entidad_financiadora' => $datos['entidad_financiadora'],
						'sap_dependencia_id' => $datos['sap_dependencia_id'],
						'descripcion' => $datos['descripcion'],
						'tipo' => $datos['tipo']);

		try {
			$this->get_datos()->sincronizar();	
			$codigo = toba::consulta_php('co_proyectos')->get_campo(array('codigo'),$filtro);
			//Si el código no fue pasado como parametro, se genera uno y se muestra al usuario
			if( !isset($datos['codigo']) && $codigo){
				toba::notificacion()->agregar('Se ha registrado el proyecto con código <span style="font-size:2.5em;"><b>'.$codigo[0]['codigo'].'</b></span>','info');
			}
			//"Cancelar" solo resetea y direcciona a otra pantalla
			$this->evt__cancelar();
		} catch (Exception $e) {
			toba::notificacion()->agregar('Ocurrió el siguiente error: '.$e->getMessage());
		}
		
		
	}

	function evt__cancelar()
	{
		$this->get_datos()->resetear();
		$this->set_pantalla('pant_seleccion');
	}

	function evt__eliminar()
	{
		$this->get_datos()->eliminar();
		$this->evt__cancelar();
	}

	function evt__agregar()
	{
		$this->get_datos()->resetear();
		$this->set_pantalla('pant_edicion');
	}




}

?>