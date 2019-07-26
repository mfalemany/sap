<?php
require_once('consultas/co_comunicaciones.php'); 
class ci_comunicaciones_estados_evaluacion extends sap_ci
{
      protected $s__filtro;
      
    //-----------------------------------------------------------------------------------
    //---- cu_comunicacion_evaluacion ---------------------------------------------------
    //-----------------------------------------------------------------------------------
    function conf__cu_comunicacion_evaluacion(sap_ei_cuadro $cuadro)
    {
        if (isset($this->s__filtro)){
            /*
            $evaluacion_id = $this->s__filtro['evaluacion_id'];  
            $area_conocimiento_id = $this->s__filtro['area_conocimiento_id'];   
            $tipo_beca_id = $this->s__filtro['tipo_beca_id'];
            */
            //var_dump($this->s__filtro);
            extract($this->s__filtro);
            try {
              
               $datos = co_comunicaciones::get_comunicacionesByEstadoEvaluacion($evaluacion_id,$area_conocimiento_id,$tipo_beca_id,'',$sap_convocatoria_id);
               $cuadro->set_datos($datos);
            } catch (Exception $ex) {
               $msg = $ex->getMessage();
               toba::notificacion()->agregar($msg);
            }
        }
            
    }
	//-----------------------------------------------------------------------------------
	//---- frm_filtro_evaluacion --------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function conf__frm_filtro_evaluacion(sap_ei_formulario $form)
	{
            if (isset($this->s__filtro)) {
                  $form->set_datos($this->s__filtro);
            }
        }
	function evt__frm_filtro_evaluacion__filtrar($datos)
	{
             $this->s__filtro = $datos;
              unset($this->s__base);
	}
	function evt__frm_filtro_evaluacion__cancelar()
	{
            unset($this->s__filtro);
            unset($this->s__base);
	}
}
?>