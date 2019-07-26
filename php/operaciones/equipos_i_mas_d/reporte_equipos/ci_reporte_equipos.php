<?php 
class ci_reporte_equipos extends sap_ci
{
	protected $s__filtro;
	//-----------------------------------------------------------------------------------
	//---- cu_grupos --------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cu_grupos(sap_ei_cuadro $cuadro)
	{
            $filtro = (isset($this->s__filtro)) ? $this->s__filtro : array();
            try{
                $datos = toba::consulta_php('co_equipos')->get_equiposByParametros($filtro);
                $cuadro->set_datos($datos);
            } catch (Exception $ex) {
               $msg = $ex->getMessage();
               toba::notificacion()->agregar($msg);
            }
	}

	function evt__cu_grupos__eliminar($datos)
	{
		$this->controlador()->dep('datos')->cargar($datos);
		$this->controlador()->dep('datos')->eliminar();
		$this->controlador()->dep('datos')->resetear();
	}

	function evt__cu_grupos__seleccion($datos)
	{
		$this->controlador()->dep('datos')->cargar($datos);
		$this->set_pantalla('pant_edicion');
	}

	//-----------------------------------------------------------------------------------
	//---- cu_integrantes_equipo --------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function conf__cu_integrantes_equipo(sap_ei_cuadro $cuadro)
	{
		$datos = $this->controlador()->dep('datos')->tabla('sap_equipo')->get();
		
		//consulta temporal para actualizar los datos
		$integrantes = toba::consulta_php('co_equipos')->get_integrantes_equipo($datos['id']);
		foreach($integrantes as $integrante){
			if(!$integrante['mail']){
				toba::consulta_php('co_personas')->actualizar_datos($integrante['nro_documento']);	
			}
		}
		
		$cuadro->set_datos(toba::consulta_php('co_equipos')->get_integrantes_equipo($datos['id']));

	}

	//-----------------------------------------------------------------------------------
	//---- frm_filtro_equipo ------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__frm_filtro_equipo(sap_ei_formulario $form)
	{
            if (isset($this->s__filtro)) {
                $form->set_datos($this->s__filtro);
            }
	}

	function evt__frm_filtro_equipo__filtrar($datos)
	{
            $this->s__filtro = $datos;
	}

	function evt__frm_filtro_equipo__cancelar()
	{
            unset ($this->s__filtro);
	}





	function evt__volver()
	{
		$this->controlador()->dep('datos')->resetear();
		$this->set_pantalla('pant_seleccion');
	}
}

?>