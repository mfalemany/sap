<?php
class ci_equipo_edicion extends sap_ci
{
    function conf()
    {
        $vigentes = toba::consulta_php('co_convocatorias')->get_convocatorias_vigentes_equipos();
        if(count($vigentes) == 0){
            foreach($this->get_dependencias_clase('form') as $dependencia){
                $this->dep($dependencia)->set_solo_lectura();

                if(get_class($this->dep($dependencia)) == 'sap_ei_formulario_ml'){
                    $this->dep($dependencia)->desactivar_agregado_filas();
                }
            }
            $this->pantalla()->eliminar_evento('guardar');
            $this->pantalla()->eliminar_evento('eliminar');
        }
       

    }

    function get_depDatos()
    {
        return $this->dependencia('datos');
    }
    //-----------------------------------------------------------------------------------
    //---- fml_equipo_proyecto ----------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function conf__fml_equipo_proyecto(sap_ei_formulario_ml $form_ml)
    {
        return $this->get_depDatos()->tabla('proyecto')->get_filas(); 
    }

    function evt__fml_equipo_proyecto__modificacion($datos)
    {
       $this->get_depDatos()->tabla('proyecto')->procesar_filas($datos); 
    }

    //-----------------------------------------------------------------------------------
    //---- fml_integrantes --------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function conf__fml_integrantes(sap_ei_formulario_ml $form_ml)
    {
        $datos=$this->get_depDatos()->tabla('integrante')->get_filas();
        return $datos;
           
    }

    function evt__fml_integrantes__modificacion($datos)
    {
         $this->get_depDatos()->tabla('integrante')->procesar_filas($datos);
    }

    //-----------------------------------------------------------------------------------
    //---- fml_palabra ------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function conf__fml_palabra(sap_ei_formulario_ml $form_ml)
    {
        return $this->get_depDatos()->tabla('palabra_clave')->get_filas();
    }

    function evt__fml_palabra__modificacion($datos)
    {
        $this->get_depDatos()->tabla('palabra_clave')->procesar_filas($datos);
        
    }

    //-----------------------------------------------------------------------------------
    //---- frm_equipo -------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function conf__frm_equipo(sap_ei_formulario $form)
    {   
        if ( $this->get_depDatos()->esta_cargada()) {
            $t =$this->get_depDatos()->tabla('equipo');
            $datos=$t->get();
            return $datos;
        }
        
    }
//
    function evt__frm_equipo__modificacion($datos)
    {
        $datos['usuario_id']=toba::usuario()->get_id();
        $datos['convocatoria_id']=$this->controlador()->s__convocatoria;
        $this->get_depDatos()->tabla('equipo')->set($datos);
    }

    //-----------------------------------------------------------------------------------
    //---- Eventos ----------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function evt__guardar()
    {
        try
          {
            $this->get_depDatos()->sincronizar();
            $this->get_depDatos()->resetear();
            toba::notificacion()->agregar('El equipo y sus proyectos se han guardado exitosamente','info');
            $this->controlador()->set_pantalla('p_equipos'); 
            

        }
        catch (toba_error $e)
        {
          $result = $e;
          toba::notificacion()->agregar('Error en la carga. ' . $e,'error'); 

        }
    }

    function evt__cancelar()
    {
        $this->get_depDatos()->resetear();
        $this->controlador()->set_pantalla('p_convocatorias');
    }

    function evt__eliminar()
    {
        $this->get_depDatos()->eliminar();
        $this->controlador()->set_pantalla('p_equipos');
    }
    
     function get_proyecto_descripcion($id)
      {
          return toba::consulta_php('co_proyectos')->get_proyecto($id);
      }

}
?>