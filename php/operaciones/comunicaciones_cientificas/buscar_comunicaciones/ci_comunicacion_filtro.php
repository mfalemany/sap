<?php
require_once('consultas/co_comunicaciones.php'); 
class ci_comunicacion_filtro extends sap_ci
{
		protected $s__filtro;
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(sap_ei_cuadro $cuadro)
	{

		if ( isset( $this->s__filtro ) ) {
			$where = $this->dep('filtro')->get_sql_where();
			$datos = co_comunicaciones::get_comunicacionesByFiltros($where);
			$cuadro->set_datos($datos);
		}
	}

	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro(sap_ei_filtro $filtro)
	{
		if ( isset($this->s__filtro ) ) {
			$filtro->set_datos($this->s__filtro);
		}

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
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__cuadro__seleccion($seleccion)
	{
			toba::memoria()->set_dato('comunicacion',$seleccion);
			toba::memoria()->set_dato('lectura','1');
			toba::vinculador()->navegar_a(toba::proyecto()->get_id(), 3514);
	}

	function evt__cuadro__evaluar($seleccion)
	{
		toba::memoria()->set_dato('comunicacion',$seleccion);
		toba::vinculador()->navegar_a(toba::proyecto()->get_id(), 3523);
	}

	function evt__cuadro__abrir($seleccion)
	{
		if(toba::db()->ejecutar("UPDATE sap_comunicacion SET estado = 'A' WHERE id = ".quote($seleccion['id']))){
			toba::notificacion()->agregar('Se ha abierto la comunicación correctamente.','info');
		}else{
			toba::notificacion()->agregar('Ocurrió un error al intentar abrir la comunicación');
		}
	}

	
}
?>