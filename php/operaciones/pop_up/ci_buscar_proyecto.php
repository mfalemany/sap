<?php
class ci_buscar_proyecto extends sap_ci
{
        protected $s__filtro;

	//-----------------------------------------------------------------------------------
	//---- cu_proyectos -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

    function conf__cu_proyectos(sap_ei_cuadro $cuadro)
    {
        $cuadro->desactivar_modo_clave_segura();
        if ( isset( $this->s__filtro ) ) {
            $where = $this->dep('fil_proyectos')->get_sql_where();
            $datos = toba::consulta_php('co_proyectos')->get_proyectosByFiltros($where);
            $cuadro->set_datos($datos);
        }

    }



    function conf__fil_proyectos(sap_ei_filtro $filtro)
    {
        if ( isset($this->s__filtro ) ) {
            $filtro->set_datos($this->s__filtro);
        }
    }

    function evt__fil_proyectos__filtrar($datos)
    {
        $this->s__filtro = $datos;
    }

    function evt__fil_proyectos__cancelar()
    {
        
         unset($this->s__filtro);
    }
}
?>
