<?php
class ci_subsidios_evaluacion extends sap_ci
{
	protected $s__filtro;

	//-----------------------------------------------------------------------------------
	//---- cuadro_solicitudes -----------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_solicitudes(sap_ei_cuadro $cuadro)
	{
		$filtro = isset($this->s__filtro) ? $this->s__filtro : array();
		$cuadro->set_datos(toba::consulta_php('co_subsidios')->get_solicitudes_evaluacion($filtro));
	}

	function evt__cuadro_solicitudes__seleccion($seleccion)
	{
		$this->datos('evaluacion')->resetear();
		$this->datos('evaluacion')->cargar(array('id_solicitud'=>$seleccion['id_solicitud']));
		switch ($seleccion['tipo_subsidio']) {
			case 'A':
				$this->set_pantalla('pant_eval_congreso');
				break;
			case 'B':
				$this->set_pantalla('pant_eval_estadia');
				break;
		}
	}

	//-----------------------------------------------------------------------------------
	//---- filtro_solicitud -------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro_solicitud(sap_ei_formulario $form)
	{
		//obtengo las convocatorias de subsidios para cargar el ef_combo
		$convocatorias = toba::consulta_php('co_convocatorias')->get_convocatorias(array('aplicable'=>'SUBSIDIOS'));
		foreach($convocatorias as $conv){
			$opciones[$conv['id']] = $conv['nombre'];
		}
		$form->ef('id_convocatoria')->set_opciones($opciones);


		if(isset($this->s__filtro)){
			$form->set_datos($this->s__filtro);
		}
	}

	function evt__filtro_solicitud__filtrar($datos)
	{
		$this->s__filtro = $datos;
	}

	function evt__filtro_solicitud__cancelar()
	{
		unset($this->s__filtro);
	}

	//-----------------------------------------------------------------------------------
	//---- FORMULARIO EVALUACIÓN CONGRESO -----------------------------------------------
	//-----------------------------------------------------------------------------------
	function conf__form_eval_congreso(sap_ei_formulario $form)
	{
		$evaluacion = $this->datos('evaluacion','sap_subsidio_eval_congreso')->get();
        if($evaluacion){
			//asigno los datos al formulario
			$form->set_datos($evaluacion);
        }
		//si la solicitud ya fue evaluada y cerrada, no se puede editar nada
		$solicitud = $this->datos('evaluacion','sap_subsidio_solicitud')->get();


        if(toba::consulta_php('co_subsidios')->recibio_subsidio($solicitud['nro_documento'], $solicitud['id_convocatoria'])){
            $form->agregar_notificacion('Esta persona ya fue beneficiada con un subsidio en la convocatoria inmediata anterior.','warning');
        }


        if( in_array($solicitud['estado'],array('E','D')) ){
			$form->agregar_notificacion('Esta solicitud ya fue evaluada y confirmada. No podrá modificar los puntajes asignados.','error');
			$form->set_solo_lectura();
			$this->pantalla()->eliminar_evento('cerrar_evaluacion');
			$this->pantalla()->eliminar_evento('guardar');
			$this->pantalla()->eliminar_evento('desestimar');
		}
		
	}

	function evt__form_eval_congreso__modificacion($datos)
	{
		$this->datos('evaluacion','sap_subsidio_eval_congreso')->set($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- FORMULARIO EVALUACIÓN ESTADÍA ------------------------------------------------
	//-----------------------------------------------------------------------------------
	function conf__form_eval_estadia(sap_ei_formulario $form)
	{
        $evaluacion = $this->datos('evaluacion','sap_subsidio_eval_estadia')->get();
		if($evaluacion){
			$form->set_datos($evaluacion);
        }



		//si la solicitud ya fue evaluada y cerrada, no se puede editar nada
		$solicitud = $this->datos('evaluacion','sap_subsidio_solicitud')->get();

        
        if(toba::consulta_php('co_subsidios')->recibio_subsidio($solicitud['nro_documento'], $solicitud['id_convocatoria'])){
            $form->agregar_notificacion('Esta persona ya fue beneficiada con un subsidio en la convocatoria inmediata anterior.','warning');
        }

        if( in_array($solicitud['estado'],array('E','D')) ){
			$form->agregar_notificacion('Esta solicitud ya fue evaluada y confirmada. No podrá modificar los puntajes asignados.','error');
			$form->set_solo_lectura();
			$this->pantalla()->eliminar_evento('cerrar_evaluacion');
			$this->pantalla()->eliminar_evento('guardar');
			$this->pantalla()->eliminar_evento('desestimar');
        }
	}

	function evt__form_eval_estadia__modificacion($datos)
	{
		$this->datos('evaluacion','sap_subsidio_eval_estadia')->set($datos);
	}

	//-----------------------------------------------------------------------------------

	private function datos($relacion,$tabla='')
	{
		if($tabla){
			return $this->dep($relacion)->tabla($tabla);
		}else{
			return $this->dep($relacion);
		}
	}


	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__guardar()
	{
		$this->datos('evaluacion','sap_subsidio_solicitud')->set(array('evaluador'=>toba::usuario()->get_id()));
		$this->datos('evaluacion')->sincronizar();
		$this->datos('evaluacion')->resetear();
		$this->set_pantalla('pant_seleccion');
	}

	function evt__cerrar_evaluacion()
	{
		//
		// ================ VALIDAR QUE LOS PUNTAJES TENGAN VALORES (SE SACÓ LA OBLIGATORIEDAD)
		//
		//se marca la solicitud como evaluada (No se permiten mas evaluaciones posteriores)
		$this->datos('evaluacion','sap_subsidio_solicitud')->set(array('estado'=>'E'));
		$this->datos('evaluacion')->sincronizar();
		$this->datos('evaluacion')->resetear();
		$this->set_pantalla('pant_seleccion');
	}

	function evt__desestimar()
	{
		//se marca la solicitud como evaluada (No se permiten mas evaluaciones posteriores)
		$solicitud = $this->datos('evaluacion','sap_subsidio_solicitud')->get();
		if($solicitud['tipo_subsidio'] == 'A'){
			$eval = $this->datos('evaluacion','sap_subsidio_eval_congreso')->get();
			
			if(!$eval['observaciones']){
				throw new toba_error('Si marca la solicitud como desestimada, debe completar el campo "Observaciones". Esto permitirá que el solicitante conozca el motivo por el cual fue desestimado.');
			}

			$this->datos('evaluacion','sap_subsidio_eval_congreso')->set(array(
				'cvar_solicitante'      =>0,
				'justif_relac_proyecto' =>0
			));
		}
		if($solicitud['tipo_subsidio'] == 'B'){
			$eval = $this->datos('evaluacion','sap_subsidio_eval_estadia')->get();
			if(!$eval['observaciones']){
				throw new toba_error('Si marca la solicitud como desestimada, debe completar el campo "Observaciones". Esto permitirá que el solicitante conozca el motivo por el cual fue desestimado.');
			}
			$this->datos('evaluacion','sap_subsidio_eval_estadia')->set(array(
				'cvar_solicitante'      =>0,
				'justif_relac_proyecto' =>0,
				'plan_trabajo'          =>0

			));
		}
		$this->datos('evaluacion','sap_subsidio_solicitud')->set(array('estado'=>'D'));
		$this->evt__guardar();



		
		
	}

	function evt__cancelar()
	{
		$this->datos('evaluacion')->resetear();
		$this->set_pantalla('pant_seleccion');
	}

	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function conf__pant_eval_congreso(toba_ei_pantalla $pantalla)
    {
        //se obtienen los datos necesarios para pasarselos al template
        $contenido = $this->get_datos_comunes();

        //se calcula el total solicitado y se lo formatea 
        $contenido['costo_total'] = number_format(($contenido['costo_inscripcion']+$contenido['costo_estadia']+$contenido['costo_pasajes']),2,',','.');

        //se obtiene el template relleno con los datos obtenidos anteriormente
        $template = $this->get_template('A',$contenido);
        
        //se asigna el template a la pantalla y se muestra el formulario de evaluación
        $pantalla->set_template($template."[dep id=form_eval_congreso]");
    }

    

    function conf__pant_eval_estadia(toba_ei_pantalla $pantalla)
    {
        //se obtienen los datos necesarios para pasarselos al template
        $contenido = $this->get_datos_comunes();

        //se calcula el total solicitado y se lo formatea 
        $contenido['costo_total'] = number_format(($contenido['costo_estadia']+$contenido['costo_pasajes']),2,',','.');

        //se obtiene el template relleno con los datos obtenidos anteriormente
        $template = $this->get_template('B',$contenido);
        
        //se asigna el template a la pantalla y se muestra el formulario de evaluación
        $pantalla->set_template($template."[dep id=form_eval_estadia]");
    }

    //-----------------------------------------------------------------------------------
    //---- Funciones internas -----------------------------------------------------------
    //-----------------------------------------------------------------------------------


    //retorna todos los datos comunes a los distintos tipos de subsidios (datos de la solicitud y personales)
    private function get_datos_comunes()
    {
        //si no hay una solicitud seleccionada, no se puede evaluar
        if( ! $this->datos('evaluacion')->esta_cargada()){
            throw new toba_error("Para realizar una evaluación, debe seleccionar una solicitud de la lista");
        }
        //detalles de la solicitud
        $solicitud = $this->datos('evaluacion','sap_subsidio_solicitud')->get();
        //descripciones de cada uno de los campos de la solicitud
        $datos = toba::consulta_php('co_subsidios')->get_resumen_informe($solicitud['id_solicitud']);
        //documentación adjunta a la misma
        $docum = toba::consulta_php('co_subsidios')->get_documentacion_solicitud($solicitud['id_solicitud']);
        //Descripción de la categoría de incentivos
        $cats = array('1'=>'Categoría I','2'=>'Categoría II','3'=>'Categoría III','4'=>'Categoría IV','5'=>'Categoría V');
        
        //cargos que tiene el solicitante
        $cargos = toba::consulta_php('co_personas')->get_cargos_persona($datos['nro_documento']);

        /*  ======== SE FORMATEAN LAS FECHAS ============= */
        $desde = new DateTime($datos['fecha_desde']);
        $datos['fecha_desde'] = $desde->format('d-m-Y');

        $hasta = new DateTime($datos['fecha_hasta']);
        $datos['fecha_hasta'] = $hasta->format('d-m-Y');
        /*  ============================================== */

        /* ========== rutas a archivos del solicitante ================== */
        $ruta = toba::consulta_php('helper_archivos')->ruta_base();
        $ruta_docum = "/documentos/subsidios/convocatorias/".$solicitud['id_convocatoria']."/".$solicitud['tipo_subsidio']."/".$solicitud['nro_documento']."/";
        $ruta_cvar = "/documentos/docum_personal/";
        /* ============================================================== */

        // ========= SE GENERA UN ARRAY CON TODOS LOS DATOS QUE NECESITA EL TEMPLATE ==================*/
        $contenido = $datos;
        $contenido['cats'] = $cats;
        $contenido['cargos'] = $cargos;
        $contenido['docum'] = $docum;
        $contenido['ruta_docum'] = $ruta_docum;
        $contenido['ruta_cvar'] = $ruta_cvar;

        return $contenido;

    }

    private function get_template($tipo_subsidio,$datos)
    {
        $funcion = 'template_'.$tipo_subsidio;
        return $this->$funcion($datos);
    }

    private function template_A($datos)
    {
        extract($datos);
            $template =
        "<div id='contenedor_detalles_subsidio'>
        <div class='centrado' id='titulo_subsidio'>$apellido, $nombres (DNI: $nro_documento)</div>
        <div class='centrado'>Cat. Incentivos: ".$cats[$cat_incentivos]."</div>
        <table id='detalles_subsidio'>
            <div id='cvar'>
                <a href='".$ruta_cvar.$nro_documento."/cvar.pdf' target='_BLANK'>CVar del Solicitante</a>
            </div>
            <tr> 
                <td>
                    <span class='etiqueta'>Tipo de Subsidio:</span>
                    <span class='descripcion'>$tipo_subsidio</span>
                </td>
                <td colspan=2>
                    <span class='etiqueta'>Facultad/Instituto</span>
                    <span class='descripcion'>$dependencia</span>
                </td>
                <td>
                    <span class='etiqueta'>Costo Inscripción</span>
                    <span class='descripcion'>\$$costo_inscripcion</span>
                </td>
            </tr>
            <tr> 
                <td colspan=3>
                    <span class='etiqueta'>Nombre del Congreso:</span>
                    <span class='descripcion'>$nombre_congreso</span>
                </td>
                <td>
                    <span class='etiqueta'>Costo de Estadía</span>
                    <span class='descripcion'>\$$costo_estadia</span>
                </td>
            </tr>
            <tr> 
                <td colspan=3>
                    <span class='etiqueta'>Proyecto</span>
                    <span class='descripcion'>$proyecto</span>
                </td>
                <td>
                    <span class='etiqueta'>Costo de Pasajes</span>
                    <span class='descripcion'>\$$costo_pasajes</span>
                </td>
            </tr>
            <tr> 
                <td>
                    <span class='etiqueta'>Lugar</span>
                    <span class='descripcion'>$lugar</span>
                </td>
                <td>
                    <span class='etiqueta'>Desde</span>
                    <span class='descripcion'>$fecha_desde</span>
                </td>
                <td>
                    <span class='etiqueta'>Hasta</span>
                    <span class='descripcion'>$fecha_hasta</span>
                </td>
                <td>
                    <span class='etiqueta'>Total Solicitado</span>
                    <span class='descripcion'><b class='etiqueta_error'>\$$costo_total</b></span>
                </td>
            </tr>
        </table>
        <div class='colapsable'>
            <div class='boton_colapsar'>Ver abstract</div>
            <div class='contenido resumen'>$abstract</div>
        </div>
        ";

        
        //DOCUMENTACIÓN
        if(count($docum)){
            $template .= "
            <div id='documentacion'>Documentación adjunta:
            <ul>";
            foreach ($docum as $d) {
                $template .= "<li><a href='".$ruta_docum.$d['archivo']."' target='_BLANK'>".$d['descripcion']."</a></li>";
            }
            $template .= "</ul></div>";
        }


        //DOCUMENTACIÓN
        if(count($cargos)){
            $template .= "
            <div id='cargos'>
            <table>
                <caption>CARGOS DECLARADOS POR EL SOLICITANTE</caption>
                <th>Cargo</th><th>Desde</th><th>Hasta</th><th>Facultad/Instituto</th>";

            foreach ($cargos as $c) {
                $desde = new DateTime($c['fecha_desde']);
                $desde = $desde->format('d-m-Y');

                $hasta = new DateTime($c['fecha_hasta']);
                $hasta = $hasta->format('d-m-Y');
                
                $template .= "<tr>
                                <td>".$c['descripcion']."</td>
                                <td>".$desde."</td>
                                <td>".$hasta."</td>
                                <td>".$c['nombre']."</td>
                              </tr>";
            }
            $template .= "</table></div>";
        }

        $template .= "
        <!-- CIERRE DEL CONTENEDOR DETALLES SUBSIDIO -->
        </div>";

        return $template;
    }

    private function template_B($datos)
    {
        extract($datos);
        $template =
        "<div id='contenedor_detalles_subsidio'>
        <div class='centrado' id='titulo_subsidio'>$apellido, $nombres (DNI: $nro_documento)</div>
        <div class='centrado'>Cat. Incentivos: ".$cats[$cat_incentivos]."</div>
        
        <table id='detalles_subsidio'>
            <div id='cvar'>
                <a href='".$ruta_cvar.$nro_documento."/cvar.pdf' target='_BLANK'>CVar del Solicitante</a>
            </div>
            <tr> 
                <td>
                    <span class='etiqueta'>Tipo de Subsidio:</span>
                    <span class='descripcion'>$tipo_subsidio</span>
                </td>
                <td colspan=2>
                    <span class='etiqueta'>Facultad/Instituto</span>
                    <span class='descripcion'>$dependencia</span>
                </td>
                <td>
                    <span class='etiqueta'></span>
                    <span class='descripcion'></span>
                </td>
            </tr>
            <tr> 
                <td colspan=3>
                    <span class='etiqueta'>Institución:</span>
                    <span class='descripcion'>$institucion</span>
                </td>
                <td>
                    <span class='etiqueta'>Costo de Estadía</span>
                    <span class='descripcion'>\$$costo_estadia</span>
                </td>
            </tr>
            <tr> 
                <td colspan=3>
                    <span class='etiqueta'>Proyecto</span>
                    <span class='descripcion'>$proyecto</span>
                </td>
                <td>
                    <span class='etiqueta'>Costo de Pasajes</span>
                    <span class='descripcion'>\$$costo_pasajes</span>
                </td>
            </tr>
            <tr> 
                <td>
                    <span class='etiqueta'>Lugar</span>
                    <span class='descripcion'>$lugar</span>
                </td>
                <td>
                    <span class='etiqueta'>Desde</span>
                    <span class='descripcion'>$fecha_desde</span>
                </td>
                <td>
                    <span class='etiqueta'>Hasta</span>
                    <span class='descripcion'>$fecha_hasta</span>
                </td>
                <td>
                    <span class='etiqueta'>Total Solicitado</span>
                    <span class='descripcion'><b class='etiqueta_error'>\$$costo_total</b></span>
                </td>
            </tr>
        </table>
        <div class='colapsable'>
            <div class='boton_colapsar'>Ver plan de trabajo</div>
            <div class='contenido resumen'>$plan_trabajo</div>
        </div>";
        
        //DOCUMENTACIÓN
        if(count($docum)){
            $template .= "
            <div id='documentacion'>Documentación adjunta:
            <ul>";
            foreach ($docum as $d) {
                $template .= "<li><a href='".$ruta_docum.$d['archivo']."' target='_BLANK'>".$d['descripcion']."</a></li>";
            }
            $template .= "</ul></div>";
        }

        //DOCUMENTACIÓN
        if(count($cargos)){
            $template .= "
            <div id='cargos'>
            <table>
                <caption>CARGOS DECLARADOS POR EL SOLICITANTE</caption>
                <th>Cargo</th><th>Desde</th><th>Hasta</th><th>Facultad/Instituto</th>";

            foreach ($cargos as $c) {
                $desde = new DateTime($c['fecha_desde']);
                $desde = $desde->format('d-m-Y');

                $hasta = new DateTime($c['fecha_hasta']);
                $hasta = $hasta->format('d-m-Y');
                
                $template .= "<tr>
                                <td>".$c['descripcion']."</td>
                                <td>".$desde."</td>
                                <td>".$hasta."</td>
                                <td>".$c['nombre']."</td>
                              </tr>";
            }
            $template .= "</table></div>";
        }

        $template .= "
        <!-- CIERRE DEL CONTENEDOR DETALLES SUBSIDIO -->
        </div>";

        return $template;
    }



    //-----------------------------------------------------------------------------------
    //---- JAVASCRIPT -------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function extender_objeto_js()
    {
        echo "$('.colapsable .boton_colapsar').on('click',function(param){
            $(param.target).next().toggle(300);
        })
        ";
    }


}
?>