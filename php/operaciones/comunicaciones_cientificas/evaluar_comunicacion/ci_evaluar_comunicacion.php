<?php
require_once('consultas/co_comunicaciones.php'); 
class ci_evaluar_comunicacion extends sap_ci

{
    public $s__comunicacion;

    function get_dato_tabla()
    {
        return $this->dependencia('comunicacion_evaluacion');
    }
    //-----------------------------------------------------------------------------------
    //---- Configuraciones --------------------------------------------------------------
    //-----------------------------------------------------------------------------------


    function ini__operacion()
    {
        $this->s__comunicacion = toba::memoria()->get_dato('comunicacion');
//        if (isset($this->s__comunicacion) && ($this->s__comunicacion['id']!=0)){
//            $this->get_dato_relacion()->cargar($this->s__comunicacion);

    //            $datos=$this->dep('ci_comunicacion_edicion')->dep('datos')->tabla('comunicacion')->get();
    //            $this->s__convocatoria=$datos['sap_convocatoria_id'];
    //            $this->set_pantalla('p_comunic_edicion');
      // }    

    }
    

    //-----------------------------------------------------------------------------------
    //---- cu_evaluaciones --------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function conf__cu_evaluaciones(sap_ei_cuadro $cuadro)
    {
        $datos = array();
         if (isset($this->s__comunicacion['id'])){
        // $t =$this->get_dato_tabla()->get();
         $datos=  co_comunicaciones::get_comunicacionEvaluaciones($this->s__comunicacion['id']);
<<<<<<< .mine
        $cuadro->set_datos($datos);
        }
||||||| .r3234
        }
=======
>>>>>>> .r3244
        $comunicacion=co_comunicaciones::get_comunicacionTituloById($this->s__comunicacion['id']);
        $cuadro->set_titulo('Evaluaciones de la Comunicación' . ' ' . $comunicacion['0']['titulo'] . '');
        }
        
    }

    //-----------------------------------------------------------------------------------
    //---- frm_evaluacion ---------------------------------------------------------------
    //-----------------------------------------------------------------------------------

//    function conf__frm_evaluacion(sap_ei_formulario $form)
//    {   
//       if ( $this->get_dato_tabla()->esta_cargada()) {
//           $t =$this->get_dato_tabla()->get();
//        }
//
//        return $t;
//    }
    function evt__frm_evaluacion__modificacion($datos)
    {
        $datos['evaluadores'] = implode(' / ',$datos['evaluadores']);
         try
        {
            $datos['usuario_id']=toba::usuario()->get_id();
            $datos['sap_comunicacion_id']=$this->s__comunicacion['id'];
            $this->get_dato_tabla()->nueva_fila($datos);
            $this->get_dato_tabla()->sincronizar();
            $this->get_dato_tabla()->resetear();
            toba::notificacion()->info('La Evaluación de la Comunicación se ha guardado correctamente'); 
            toba::notificacion()->mostrar();    
            toba::notificacion()->vaciar();

            $this->navegar_operacion();
        }
        catch (Exception $e)
        {
            $result = $e;
            toba::notificacion()->agregar('Error en la carga de la Evaluación de la Comunicación. ' . $e->getMessage(),'error'); 
            //$this->navegar_operacion();
        }
    }

    //-----------------------------------------------------------------------------------
    //---- frm_evaluacion ---------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function evt__frm_evaluacion__cancelar()
    {
        $this->get_dato_tabla()->resetear();
        $this->navegar_operacion();
    }

	


      

    //-----------------------------------------------------------------------------------
    //---- frm_comunicacion -------------------------------------------------------------
    //-----------------------------------------------------------------------------------

//    function conf__frm_comunicacion(sap_ei_formulario $form)
//    {
//        if ($this->get_dato_relacion()->esta_cargada()){
//         $t =$this->get_dato_relacion()->tabla('comunicacion');
//         $datos=$t->get();
//         $where = ' c.id=' . $this->s__comunicacion['id'];
//         $datos=  co_comunicaciones::get_comunicacionesByFiltros($where);
//        }
//        return $datos[0];
//    }

    //    function evt__frm_comunicacion__modificacion($datos)
    //    {
    //        
    //        $datos['usuario_id']=toba::usuario()->get_id();
    //        $this->get_dato_relacion()->tabla('comunicacion')->set($datos);
    //        toba::vinculador()->navegar_a(toba::proyecto()->get_id(), 3517);
    //    }


    function navegar_operacion()
    {
        toba::memoria()->eliminar_dato('comunicacion');
        unset($this->s__comunicacion);
        toba::vinculador()->navegar_a(toba::proyecto()->get_id(), 3517);
    }
	

	
	
}
?>