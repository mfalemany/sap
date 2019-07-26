<?php
require_once('consultas/co_comunicaciones.php'); 

class ci_mis_evaluaciones extends sap_ci
{
    //-----------------------------------------------------------------------------------
    //---- cu_evaluaciones --------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function conf__cu_evaluaciones(sap_ei_cuadro $cuadro)
    {
        
        $usuario=toba::usuario()->get_id();
        //$where='c.usuario_id='.quote($usuario);
        $datos = co_comunicaciones::get_comunicacionesConEstadoEvaluacion(quote($usuario));
        
                //$this->dep('comunicacion')->cargar(array('usuario_id'=>$usuario));
        //$datos= $this->dep('comunicacion')->get_filas();
        //ei_arbol($datos);
        $cuadro->set_datos($datos);
    }

    //-----------------------------------------------------------------------------------
    //---- cu_evaluaciones --------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function evt__cu_evaluaciones__modificar($datos)
    {
        toba::memoria()->set_dato('comunicacion',$datos);
        toba::vinculador()->navegar_a(toba::proyecto()->get_id(), 3514);
    }

   

}
?>