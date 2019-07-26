<?php
require_once('consultas/co_convocatorias.php'); 
class ci_convocatorias extends sap_ci
{
    protected $s__seleccion;

    function resetear()
    {
        $this->get_dt_convocatoria()->resetear();
        $this->set_pantalla('p_seleccion');
        if (isset($this->s__seleccion)) {
            unset($this->s__seleccion);
        }
    }
    private function get_dt_convocatoria() 
    {
        return $this->dep('dt_convocatoria');
    }

    //-----------------------------------------------------------------------------------
    //---- Eventos ----------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function evt__agregar()
    {
        $this->set_pantalla('p_edicion');

    }

    //-----------------------------------------------------------------------------------
    //---- cuadro_convocatorias ---------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function conf__cuadro_convocatorias(sap_ei_cuadro $cuadro)
    {
        $datos = co_convocatorias::get_convocatorias();
        $cuadro->set_datos($datos);
    }

    function evt__cuadro_convocatorias__seleccion($seleccion)
    {
        $this->s__seleccion = $seleccion;
        $this->set_pantalla('p_edicion');
    }

    function evt__cuadro_convocatorias__eliminar($seleccion)
    {
        $t = $this->get_dt_convocatoria();
        $t->cargar($seleccion);
        $t->eliminar_filas(false);
        $t->sincronizar();
        $this->resetear();
    }

    //-----------------------------------------------------------------------------------
    //---- form_convocatoria ------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function conf__form_convocatoria(sap_ei_formulario $form)
    {
        if (isset($this->s__seleccion)) {
            $t = $this->get_dt_convocatoria();
            $t->cargar($this->s__seleccion);
            return $t->get();
        }
    }

    function evt__form_convocatoria__alta($datos)
    {
        $t = $this->get_dt_convocatoria();
        $t->nueva_fila($datos);
        try{
            $t->sincronizar();
            $this->resetear();
        }catch(toba_error $e){
            toba::notificacion()->agregar('Error insertando');
            toba::logger()->error($e->getMessage());
        }
    }

    function evt__form_convocatoria__baja()
    {
        if (isset($this->s__seleccion)) {
            $t = $this->get_dt_convocatoria();
            $t->eliminar_filas(false);
            $t->sincronizar();
            $this->resetear();
        }
    }

    function evt__form_convocatoria__modificacion($datos)
    {
         if (isset($this->s__seleccion)) {
            $t = $this->get_dt_convocatoria();
            $t->set($datos);
            $t->sincronizar();
            $this->resetear();
        }
    }

    function evt__form_convocatoria__cancelar()
    {
         $this->resetear();        
    }

}
?>