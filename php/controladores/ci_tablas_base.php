<?php
require_once('consultas/co_tablas_basicas.php'); 
class ci_tablas_base extends sap_ci
{
    protected $s__seleccion;
    protected $s__filtro;
    private function get_dt_area() 
    {
        return $this->dep('sap_area_conocimiento');
    }
      private function get_dt_evaluacion() 
    {
        return $this->dep('sap_evaluacion');
    }
      private function get_dt_tipo_beca() 
    {
        return $this->dep('sap_tipo_beca');
    }
      private function get_dt_dependencia() 
    {
        return $this->dep('sap_dependencia');
    }
    private function get_dt_evaluadores() 
    {
        return $this->dep('sap_evaluadores');
    }
	//-----------------------------------------------------------------------------------
	//---- cuadro_area_beca -------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_area_beca(sap_ei_cuadro $cuadro)
	{
            $cuadro->set_datos(co_tablas_basicas::get_areas_conocimiento());
	}

	function evt__cuadro_area_beca__seleccion($seleccion)
	{
             $this->get_dt_area()->cargar($seleccion);

	}

	//-----------------------------------------------------------------------------------
	//---- form_area_beca ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_area_beca(sap_ei_formulario $form)
	{
           if ($this->get_dt_area()->esta_cargada()) {
               return $this->get_dt_area()->get();    
               }
	}

	function evt__form_area_beca__alta($datos)
	{
            $this->get_dt_area()->nueva_fila($datos);
            $this->get_dt_area()->sincronizar();
            $this->get_dt_area()->resetear();
	}

	function evt__form_area_beca__baja()
	{
            $this->get_dt_area()->eliminar_filas();
            $this->get_dt_area()->sincronizar();
            $this->get_dt_area()->resetear();
	}

	function evt__form_area_beca__modificacion($datos)
	{
            $this->get_dt_area()->set($datos);
            $this->get_dt_area()->sincronizar();
            $this->get_dt_area()->resetear();
	}

	function evt__form_area_beca__cancelar()
	{
            $this->get_dt_area()->resetear();
	}


	//-----------------------------------------------------------------------------------
	//---- cuadro_tipo_beca -------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_tipo_beca(sap_ei_cuadro $cuadro)
	{
            $cuadro->set_datos(co_tablas_basicas::get_tipos_beca());
	}
        

	function evt__cuadro_tipo_beca__seleccion($seleccion)
	{
             $this->get_dt_tipo_beca()->cargar($seleccion);
	}

	//-----------------------------------------------------------------------------------
	//---- form_tipo_beca ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_tipo_beca(sap_ei_formulario $form)
	{
            if ($this->get_dt_tipo_beca()->esta_cargada()) {
            return $this->get_dt_tipo_beca()->get();    
            }
	}

	function evt__form_tipo_beca__alta($datos)
	{
            
            $this->get_dt_tipo_beca()->nueva_fila($datos);
            $this->get_dt_tipo_beca()->sincronizar();
            $this->get_dt_tipo_beca()->resetear();
	}

	function evt__form_tipo_beca__baja()
	{
            $this->get_dt_tipo_beca()->eliminar_filas();
            $this->get_dt_tipo_beca()->sincronizar();
            $this->get_dt_tipo_beca()->resetear();
	}

	function evt__form_tipo_beca__modificacion($datos)
	{
            $this->get_dt_tipo_beca()->set($datos);
            $this->get_dt_tipo_beca()->sincronizar();
            $this->get_dt_tipo_beca()->resetear();
	}

	function evt__form_tipo_beca__cancelar()
	{
            $this->get_dt_tipo_beca()->resetear();
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro_dependencia -----------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_dependencia(sap_ei_cuadro $cuadro)
	{
            $cuadro->set_datos(co_tablas_basicas::get_dependencias());
	}

	function evt__cuadro_dependencia__seleccion($seleccion)
	{
             $this->get_dt_dependencia()->cargar($seleccion);
	}

	//-----------------------------------------------------------------------------------
	//---- form_dependencia -------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_dependencia(sap_ei_formulario $form)
	{
              if ($this->get_dt_dependencia()->esta_cargada()) {
               return $this->get_dt_dependencia()->get();    
               }
	}

	function evt__form_dependencia__alta($datos)
	{
            $this->get_dt_dependencia()->nueva_fila($datos);
            $this->get_dt_dependencia()->sincronizar();
            $this->get_dt_dependencia()->resetear();
	}

	function evt__form_dependencia__baja()
	{
            $this->get_dt_dependencia()->eliminar_filas();
            $this->get_dt_dependencia()->sincronizar();
            $this->get_dt_dependencia()->resetear();
	}

	function evt__form_dependencia__modificacion($datos)
	{
            $this->get_dt_dependencia()->set($datos);
            $this->get_dt_dependencia()->sincronizar();
            $this->get_dt_dependencia()->resetear();
	}

	function evt__form_dependencia__cancelar()
	{
             $this->get_dt_dependencia()->resetear();
	}

  
	//-----------------------------------------------------------------------------------
	//---- cu_evaluacion ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cu_evaluacion(sap_ei_cuadro $cuadro)
	{
             $cuadro->set_datos(co_tablas_basicas::get_evaluaciones());
	}

	function evt__cu_evaluacion__seleccion($seleccion)
	{
              $this->get_dt_evaluacion()->cargar($seleccion);
	}

	//-----------------------------------------------------------------------------------
	//---- frm_evaluacion ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__frm_evaluacion(sap_ei_formulario $form)
	{
             if ($this->get_dt_evaluacion()->esta_cargada()) {
               return $this->get_dt_evaluacion()->get();    
               }
	}

	function evt__frm_evaluacion__alta($datos)
	{
            $this->get_dt_evaluacion()->nueva_fila($datos);
            $this->get_dt_evaluacion()->sincronizar();
            $this->get_dt_evaluacion()->resetear();
            
	}

	function evt__frm_evaluacion__baja()
	{
            $this->get_dt_evaluacion()->eliminar_fila();
            $this->get_dt_evaluacion()->sincronizar();
            $this->get_dt_evaluacion()->resetear();
	}

	function evt__frm_evaluacion__modificacion($datos)
	{
            $this->get_dt_evaluacion()->set($datos);
            $this->get_dt_evaluacion()->sincronizar();
            $this->get_dt_evaluacion()->resetear();
            
	}

	function evt__frm_evaluacion__cancelar()
	{
            $this->get_dt_evaluacion()->resetear();
            
	}

	//-----------------------------------------------------------------------------------
	//---- cu_evaluadores ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cu_evaluadores(sap_ei_cuadro $cuadro)
	{
		//$datos = co_tablas_basicas::get_evaluadores();

		$cuadro->set_datos(co_tablas_basicas::get_evaluadores());
	}

	function evt__cu_evaluadores__seleccion($seleccion)
	{
		$this->get_dt_evaluadores()->cargar($seleccion);
	}

	//-----------------------------------------------------------------------------------
	//---- frm_evaluadores --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__frm_evaluadores(sap_ei_formulario $form)
	{
		if($this->get_dt_evaluadores()->esta_cargada()){

			$form->set_datos($this->get_dt_evaluadores()->get());
		}
	}

	function evt__frm_evaluadores__alta($datos)
	{
		$this->get_dt_evaluadores()->nueva_fila($datos);
        $this->get_dt_evaluadores()->sincronizar();
        $this->get_dt_evaluadores()->resetear();
	}

	function evt__frm_evaluadores__baja()
	{
		$this->get_dt_evaluadores()->eliminar_filas();
        $this->get_dt_evaluadores()->sincronizar();
        $this->get_dt_evaluadores()->resetear();
	}

	function evt__frm_evaluadores__modificacion($datos)
	{
		$this->get_dt_evaluadores()->set($datos);
        $this->get_dt_evaluadores()->sincronizar();
        $this->get_dt_evaluadores()->resetear();
	}

	function evt__frm_evaluadores__cancelar()
	{
		$this->get_dt_evaluadores()->resetear();
	}


	//-----------------------------------------------------------------------------------
	//---- cu_areas_programas -----------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cu_areas_programas(sap_ei_cuadro $cuadro)
	{
		$cuadro->set_datos(toba::consulta_php('co_programas')->get_areas());
	}

	function evt__cu_areas_programas__seleccion($seleccion)
	{
		$this->dep('sap_programas_areas')->cargar($seleccion);
	}

	//-----------------------------------------------------------------------------------
	//---- form_areas_programas ---------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_areas_programas(sap_ei_formulario $form)
	{
		if($this->dep('sap_programas_areas')->esta_cargada()){
			$form->set_datos($this->dep('sap_programas_areas')->get());
		}
	}

	function evt__form_areas_programas__alta($datos)
	{
		$this->dep('sap_programas_areas')->nueva_fila($datos);
		$this->dep('sap_programas_areas')->sincronizar();
		$this->dep('sap_programas_areas')->resetear();	
	}

	function evt__form_areas_programas__baja()
	{
		$this->dep('sap_programas_areas')->set($datos);
		$this->dep('sap_programas_areas')->eliminar();
		$this->dep('sap_programas_areas')->resetear();	
	}

	function evt__form_areas_programas__modificacion($datos)
	{
		$this->dep('sap_programas_areas')->set($datos);
		$this->dep('sap_programas_areas')->sincronizar();
		$this->dep('sap_programas_areas')->resetear();	
	}

	function evt__form_areas_programas__cancelar()
	{
		$this->dep('sap_programas_areas')->resetear();
	}

	//-----------------------------------------------------------------------------------
	//---- cu_subareas_programas --------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cu_subareas_programas(sap_ei_cuadro $cuadro)
	{
		$cuadro->set_datos(toba::consulta_php('co_programas')->get_subareas());
	}

	function evt__cu_subareas_programas__seleccion($seleccion)
	{
		$this->dep('sap_programas_subareas')->cargar($seleccion);
	}

	//-----------------------------------------------------------------------------------
	//---- form_subareas_programas ------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_subareas_programas(sap_ei_formulario $form)
	{
		if($this->dep('sap_programas_subareas')->esta_cargada()){
			$form->set_datos($this->dep('sap_programas_subareas')->get());
		}
	}

	function evt__form_subareas_programas__alta($datos)
	{
		$this->dep('sap_programas_subareas')->nueva_fila($datos);
		$this->dep('sap_programas_subareas')->sincronizar();
		$this->dep('sap_programas_subareas')->resetear();	
	}

	function evt__form_subareas_programas__baja()
	{
		$this->dep('sap_programas_subareas')->set($datos);
		$this->dep('sap_programas_subareas')->eliminar();
		$this->dep('sap_programas_subareas')->resetear();	
	}

	function evt__form_subareas_programas__modificacion($datos)
	{
		$this->dep('sap_programas_subareas')->set($datos);
		$this->dep('sap_programas_subareas')->sincronizar();
		$this->dep('sap_programas_subareas')->resetear();	
	}

	function evt__form_subareas_programas__cancelar()
	{
		$this->dep('sap_programas_subareas')->set($seleccion);
	}

	


	//-----------------------------------------------------------------------------------
	//---- EVENTOS DEL CI ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__eliminar_evaluadores()
	{
		co_tablas_basicas::eliminar_evaluadores();
	}

}
?>