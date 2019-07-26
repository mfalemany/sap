<?php
require_once('consultas/co_equipos.php'); 

class ci_reporte_grupo_publico extends sap_ci
{
       protected $s__filtro;
	//-----------------------------------------------------------------------------------
	//---- cu_equipos -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cu_equipos(sap_ei_cuadro $cuadro)
	{

            if (isset($this->s__filtro)){
                try{
                    $datos = co_equipos::get_equiposByParametros($this->s__filtro);
                    $cuadro->set_datos($datos);
                } catch (Exception $ex) {
                   $msg = $ex->getMessage();
                   toba::notificacion()->agregar($msg);
                }
            }
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
            $this->s__filtro=$datos;
	}

	function evt__frm_filtro_equipo__cancelar()
	{
            unset($this->s__filtro);
	}

}

?>