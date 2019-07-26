<?php
require_once('consultas/co_comunicaciones.php'); 
class ci_comunicacion_edicion extends sap_ci
{
    protected $imprimir;
    protected $parametro_impresion;

	function conf()
	{
		if ( !$this->get_datos('comunicacion')->get() ) {
			$this->pantalla()->eliminar_evento('eliminar');
			$this->pantalla()->eliminar_evento('imprimir');
			$this->pantalla()->eliminar_evento('imprimir_lectura');
		}else{
     // echo "llega";
			$comunicacion = $this->get_datos('comunicacion')->get();
     // ei_arbol($comunicacion);
			//si la comunicación está cerrada
			if(isset($comunicacion['estado']) && $comunicacion['estado'] == 'C'){
				if( ! $this->en_etapa_modificacion($comunicacion['id'])){
            $this->pantalla()->eliminar_evento('guardar');
					//bloqueo todos los formularios
					$formularios = $this->get_dependencias_clase('form');
					foreach($formularios as $form){
						$this->dep($form)->set_solo_lectura();
						if(strpos(get_class($this->dep($form)),'_ml') !== FALSE){
							$this->dep($form)->desactivar_agregado_filas();  
						}
					}
				}
				$this->pantalla()->eliminar_evento('eliminar');
				$this->pantalla()->eliminar_evento('presentar');
			}
		}

		//if (isset ($this->controlador()->s__comunicacion) && ($this->controlador()->s__comunicacion['id']>0)){
		if (toba::memoria()->get_dato('lectura')=='1'){
			$this->pantalla()->eliminar_evento('eliminar');
        $this->pantalla()->eliminar_evento('guardar');
        $this->pantalla()->eliminar_evento('cancelar');
			$this->pantalla()->eliminar_evento('imprimir');
			toba::memoria()->eliminar_dato('comunicacion');
			toba::memoria()->eliminar_dato('lectura');
		}else{
			$this->pantalla()->eliminar_evento('imprimir_lectura');
		}



	}
    /**
     * Valida si una comunicación se encuentra en etapa de modificacion. Esto es, cuando la comunicación ya se cerró por parte del postulante, y luego fue evaluada, solicitandose modificaciones. En este caso, aunque la comunicación esté cerrada, el postulante puede modificala
     * @param  integer $id_comunicacion Id de la comunicación que se está analizando
     * @return boolean                  Retorna TRUE en caso de que la comunicación se encuentre en condiciones de ser modificada, FALSE en caso contrario
     */
    function en_etapa_modificacion($id_comunicacion)
    {
      //obtengo los detalles de la comunicacion
      $com = toba::consulta_php('co_comunicaciones')->get_comunicacionesByFiltros('c.id = '.quote($id_comunicacion));

      //obtengo los detalles de la convocatoria a la que pertenece
      $convocatoria = toba::consulta_php('co_convocatorias')->get_convocatorias(array('id'=>$com[0]['sap_convocatoria_id']));
      
      //si la convocatoria ya cerró
      if($convocatoria[0]['fecha_hasta'] < date('Y-m-d')){
        //pero todavía está en fecha para editar
        if($convocatoria[0]['limite_edicion'] >= date('Y-m-d')){
          return TRUE;
        }
      }
      return FALSE;
    }
    
    function get_datos($tabla = NULL)
    {
        return ($tabla) ? $this->dep('datos')->tabla($tabla) : $this->dep('datos');
    }

    //-----------------------------------------------------------------------------------
    //---- f_ml_autor -------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function conf__f_ml_autor(sap_ei_formulario_ml $form_ml)
    {
      $form_ml->agregar_notificacion('Agregar un autor por linea. Para agregar lineas utilice el boton con el signo \'+\' de color verde con la leyenda \'Agregar Autor\'','info');
      $datos=$this->get_datos()->tabla('autor')->get_filas();

       return $datos;


    }

    //-----------------------------------------------------------------------------------
    //---- f_ml_palabraclave ------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function conf__f_ml_palabraclave(sap_ei_formulario_ml $form_ml)
    {
        $form_ml->agregar_notificacion('Agregar una palabra clave por linea. Para agregar lineas utilice el boton con el signo \'+\' de color verde con la leyenda \'Agregar palabra clave\'','info');
        return $this->get_datos()->tabla('palabra_clave')->get_filas();
    }

    //-----------------------------------------------------------------------------------
    //---- form_com_cabecera ------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function conf__form_com_cabecera(sap_ei_formulario $form)
    {
        $datos = $this->get_datos('comunicacion')->get();
        if ($datos) {
          $form->set_datos($datos);
        }
        
    }
    //-----------------------------------------------------------------------------------
    //---- Eventos ----------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function evt__cancelar()
    {
        toba::memoria()->eliminar_dato('comunicacion');
        $this->get_datos()->resetear();
        $this->controlador()->set_pantalla('p_comunicaciones');
    }

    function evt__eliminar()
    {

        $this->get_datos()->eliminar();
        toba::memoria()->eliminar_dato('comunicacion');
        $this->controlador()->set_pantalla('p_comunicaciones');

    }

    function evt__presentar()
    {
      $this->get_datos()->tabla('comunicacion')->set(array('estado'=>'C'));
      $this->evt__imprimir();
    }

    function evt__guardar()
    {
        try
        {
            $this->get_datos()->sincronizar();

            $this->get_datos()->resetear();
            toba::notificacion()->agregar('La comunicacion se ha cargado con éxito!','info'); 
            $this->controlador()->set_pantalla('p_comunicaciones');

        }catch (Exception $e){
            toba::notificacion()->agregar('Error en la carga de la Comunicación. ' . $e->getMessage(),'error');

        }
        
    }





    function evt__f_ml_autor__modificacion($datos)
    {
//            $autores="";
//              foreach ($datos as $clave=>$valor){
//                
//                $autores = $autores. ' / ' . $valor['autor'];
//            }
//            $autores = substr($autores,2);
//            $this->parametro_impresion['autores']=$autores;
//            $this->get_datos()->tabla('autor')->procesar_filas($datos);
        $this->get_datos()->tabla('autor')->procesar_filas($datos);
    }


    function armarStingAutores($id_comunicacion){
		$retorno = array();
		$autores = co_comunicaciones::get_autores($id_comunicacion);
    foreach ($autores as $clave=>$valor){
      // el/los autor/es debe/n ir primero en la lista
			if( isset($valor['es_becario']) && strtolower($valor['es_becario']) === 'true'){
				array_unshift($retorno,trim($valor['autor']));
				//$salida = trim($valor['autor']).' / '.$salida;	
			}else{
        if(isset($valor['autor'])){
          $retorno[] = trim($valor['autor']);  
        }
				
				//$salida = $salida.' / '.$valor['autor'];
			}
    }
		
		
		return implode(" / ",$retorno); 

	}

    function evt__f_ml_palabraclave__modificacion($datos)
    {
//            
//            foreach ($datos as $clave=>$valor){
//                $palabras = $palabras. ' / ' . $valor['palabra_clave'];
//            }
//            $palabras = substr($palabras,2);
//            
        //$this->parametro_impresion['palabras']=$palabras;
        $this->parametro_impresion['palabras']=$this->armarStingPalabras($datos);
        $this->get_datos()->tabla('palabra_clave')->procesar_filas($datos);
    }
    function armarStingPalabras($palabras)
    {
        $salida="";
        foreach ($palabras as $clave=>$valor){

            $salida = $salida. ' / ' . $valor['palabra_clave'];
        }
        $salida = substr($salida,2);
        return $salida;
    }

    function evt__form_com_cabecera__modificacion($datos)
    {   
    	//si no existe orden de poster asignado
		if( ! $datos['orden_poster']){
			//obtener un orden de poster para esta convocatoria y area de conocimiento
			$datos['orden_poster'] = $this->get_orden_poster($this->controlador()->s__convocatoria,$datos['sap_area_beca_id']);
		}

        //paso a minusculas la direccion de correo
        $datos['e_mail'] = strtolower(trim($datos['e_mail']));

        $datos['usuario_id']=toba::usuario()->get_id();
        $datos['sap_convocatoria_id']=$this->controlador()->s__convocatoria;
        $datos['version_modificacion']+=1;


        $this->parametro_impresion['cabecera']=$datos;
        $this->get_datos()->tabla('comunicacion')->set($datos);
    }

    function evt__imprimir()
    {
      
       $comunicacion = $this->get_datos()->tabla('comunicacion')->get();
       $this->parametro_impresion['autores']=$this->armarStingAutores($comunicacion['id']);
      if($comunicacion['estado'] == 'C'){
        $this->parametro_impresion['estado'] = 'C';
      }
       $this->evt__guardar();

      // $this->get_datos()->sincronizar();
       //$this->get_datos()->resetear();
       co_comunicaciones::actualizarVersionImpresa($this->controlador()->s__comunicacion['id']);
       unset($this->controlador()->s__comunicacion);
       toba::memoria()->eliminar_dato('comunicacion');
       $this->parametro_impresion['cabecera']['version_impresa']= $this->parametro_impresion['cabecera']['version_modificacion'];
       toba::memoria()->set_dato('datos',$this->parametro_impresion );
       toba::vinculador()->navegar_a(toba::proyecto()->get_id(), 3515);


    }

    function evt__imprimir_lectura()
    {       
        $this->parametro_impresion['cabecera']=$this->get_datos()->tabla('comunicacion')->get();
        $palabras= $this->get_datos()->tabla('palabra_clave')->get_filas();
        $this->parametro_impresion['palabras']=$this->armarStingPalabras($palabras);
        $autores=$this->get_datos()->tabla('autor')->get_filas();

        $this->parametro_impresion['autores']=$this->armarStingAutores($this->parametro_impresion['cabecera']['id']);


       unset($this->controlador()->s__comunicacion);
       toba::memoria()->eliminar_dato('comunicacion');
       toba::memoria()->set_dato('datos',$this->parametro_impresion );
       
       toba::vinculador()->navegar_a(toba::proyecto()->get_id(), 3515);

    }

    protected function get_orden_poster($convocatoria,$area_conocimiento){
    	$orden = $this->get_datos()->tabla('comunicacion')->get_orden_poster($convocatoria,$area_conocimiento);
    	return $orden;
    }

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	

	//-----------------------------------------------------------------------------------
	//---- cu_evaluaciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cu_evaluaciones(sap_ei_cuadro $cuadro)
	{
    if(isset($this->controlador()->s__comunicacion)){
      $comunicacion = $this->controlador()->s__comunicacion;
      $evaluaciones = co_comunicaciones::get_comunicacionEvaluaciones($comunicacion['id']);
      $cuadro->set_datos($evaluaciones);      
    }else{
      $this->dep('cu_evaluaciones')->colapsar();
    }

	}

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Eventos ---------------------------------------------
		
		{$this->objeto_js}.evt__presentar = function()
		{
      return confirm('Esta operación establecerá la comunicación como definitiva. Si acepta, no podrá realizar modificaciones a la misma. Asegurese de que todos los datos consignados son correctos antes de aceptar esta operación. Cerrar la comunicación y establecerla como definitiva?');
		}
		";
	}

}
?>