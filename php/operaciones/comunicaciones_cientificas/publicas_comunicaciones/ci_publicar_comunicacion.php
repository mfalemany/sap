<?php
require_once('consultas/co_comunicaciones.php'); 
require_once('consultas/co_convocatorias.php'); 
require_once('operaciones/funciones_utiles.php'); 

class ci_publicar_comunicacion extends sap_ci
{
          protected $s__filtro;

	//-----------------------------------------------------------------------------------
	//---- cu_comunicacion --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cu_comunicacion(sap_ei_cuadro $cuadro)
	{
            if (isset($this->s__filtro)){
            $evaluacion_id = "5,7";//APROBADA Seleccionada
            $area_conocimiento_id = $this->s__filtro['area_conocimiento_id'];   
            $id_convocatoria = ($this->s__filtro['convocatoria'] == "todas") ? NULL : $this->s__filtro['convocatoria'];
            try {
              
               $datos = co_comunicaciones::get_comunicacionesByEstadoEvaluacion($evaluacion_id,$area_conocimiento_id,'','P',$id_convocatoria);
               $cuadro->set_datos($datos);
            } catch (Exception $ex) {
               $msg = $ex->getMessage();
               toba::notificacion()->agregar($msg);
            }

        }
	}

	//-----------------------------------------------------------------------------------
	//---- frm_filtro -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__frm_filtro(sap_ei_formulario $form)
	{
      /* == Se obtienen todas las convocatorias == */
      $convocatorias = co_convocatorias::get_convocatorias();
      
      $opciones['todas'] = "Todas las convocatorias";
      foreach($convocatorias as $convocatoria){
        $opciones[$convocatoria['id']] = $convocatoria['nombre'];
      }

      $form->ef('convocatoria')->set_opciones(count($opciones) ? $opciones : array());
      /* ===================================================================== */
      
       if (isset($this->s__filtro)) {
            $form->set_datos($this->s__filtro);
      }
	}

	function evt__frm_filtro__filtrar($datos)
	{
              $this->s__filtro = $datos;
              unset($this->s__base);
	}

	function evt__frm_filtro__cancelar()
	{
             unset($this->s__filtro);
	}

	function evt__cu_comunicacion__imprimir($seleccion)
	{
             $this->dep('datos')->cargar($seleccion);
             
            $this->parametro_impresion['cabecera']=$this->dep('datos')->tabla('comunicacion')->get();
            $palabras= $this->dep('datos')->tabla('palabra_clave')->get_filas();
            $this->parametro_impresion['palabras']=  funciones_utiles::armarStingPalabras($palabras);
            $autores=$this->dep('datos')->tabla('autor')->get_filas();
            $this->parametro_impresion['autores']=funciones_utiles::armarStingAutores($autores);
            unset($this->controlador()->s__comunicacion);
            toba::memoria()->eliminar_dato('comunicacion');
            toba::memoria()->set_dato('datos',$this->parametro_impresion );
            toba::vinculador()->navegar_a(toba::proyecto()->get_id(), 3515);
	}

}
?>