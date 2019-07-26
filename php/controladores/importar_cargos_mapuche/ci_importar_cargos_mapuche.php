<?php
class ci_importar_cargos_mapuche extends sap_ci
{
	//-----------------------------------------------------------------------------------
	//---- form_importar_cargos ---------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__form_importar_cargos__modificacion($datos)
	{
		/*
			PASO 1: Se obtienen todos los cargos del CSV subido
			PASO 2: Se obtienen todos los cargos que NO TIENEN nro_cargo_mapuche (porque son externos y no se recuperan en la nueva carga)
			PASO 3: Se trunca la tabla sap_cargos_persona
			PASO 4: Se hace la inserción de todos los cargos

		*/

		//SI no me subis un PDF no puedo hacer nada capo
		if(!$datos['archivo_cargos']['tmp_name']){
			toba::notificacion()->agregar('No se ha cargado un archivo de cargos, o el mismo no pudo ser leido','error');
			return;
		}

		//Mapeo de los campos del CSV con los campos de la BD local
		$mapeo = array(
			'cuil'              => 'cuil',
			'cargo'             => 'codc_categ',
			'dedicacion'        => 'codc_dedic',
			'fecha_desde'       => 'fec_alta',
			'fecha_hasta'       => 'fec_baja',
			'nro_cargo_mapuche' => 'nro_cargo',
			'dependencia'       => 'codc_uacad',
			'caracter'          => 'codc_carac'
		);

		//Mantiene un registro de la cantidad de cargos que se procesan
		$lineas_procesadas = 0;
		$cargos_nuevos = 0;

		//Contendrá todos los registros que hay que insertar
		$registros = array();

			
		/* ============================================================================ 
		   =========== PASO 1: SE ARMA UN ARRAY CON LOS CARGOS DEL CSV ================ 
		   ============================================================================ */
		$archivo_cargos = fopen($datos['archivo_cargos']['tmp_name'],'r');
		while($cargo = fgetcsv($archivo_cargos)){
			if($lineas_procesadas == 0){
				foreach($mapeo as $campo_bd_local => $campo_csv){
					$posiciones[$campo_csv] = array_search($campo_csv, $cargo);
				}
				$lineas_procesadas++;
				continue;
			}
			
			//se hace el mapeo de todos los campos definidos, independientemente de su posicion en el CSV
			foreach($mapeo as $campo_bd_local => $campo_csv){
				$registro[$campo_bd_local] = $cargo[$posiciones[$campo_csv]];
			}
			
			//agrego al registro el numero de documento
			$registro['nro_documento'] = substr($registro['cuil'],2,8);
			//se quita el cero inicial (si es que tiene)
			if(substr($registro['nro_documento'],0,1) == '0'){
				$registro['nro_documento'] = substr($registro['nro_documento'],1,7);
			}

			//Formateo las fechas que vienen del mapuche
			//$registro['fecha_desde'] = substr($registro['fecha_desde'],6,4)."-".substr($registro['fecha_desde'],3,2)."-".substr($registro['fecha_desde'],0,2);
			//$registro['fecha_hasta'] = substr($registro['fecha_hasta'],6,4)."-".substr($registro['fecha_hasta'],3,2)."-".substr($registro['fecha_hasta'],0,2);

			$registros[] = $registro;
			$lineas_procesadas++;
		}
		/* ============================================================================ 
		   =========== PASO 2: SE OBTIENEN LOS CARGOS EXTERNOS  ======================= 
		   ============================================================================ */

		$cant_externos = 0;
		$externos = toba::consulta_php('co_tablas_basicas')->get_cargos_filtro(array('solo_externos'=>TRUE));
		if(count($externos)){
			foreach ($externos as $cargo) {
				$registros[] = array(
									'nro_documento'     => $cargo['nro_documento'],
									'cargo'             => $cargo['cargo'],
									'dedicacion'        => $cargo['dedicacion'],
									'fecha_desde'       => $cargo['fecha_desde'],
									'fecha_hasta'       => $cargo['fecha_hasta'],
									'dependencia'       => $cargo['dependencia'],
									'caracter'          => $cargo['caracter']
								);
				$lineas_procesadas++;
				$cant_externos++;
			}
		}

		/* ============================================================================ 
		   =========== PASO 3: SE TRUNCA LA TABLA sap_cargos_persona  ================= 
		   ============================================================================ */
		toba::db()->ejecutar('TRUNCATE sap_cargos_persona');

		/* ============================================================================ 
		   =========== PASO 4: SE INSERTAN TODOS LOS CARGOS EN EL ARRAY  ============== 
		   ============================================================================ */

		foreach ($registros as $cargo) {
			if($this->insertar_cargo($cargo)){
				$cargos_nuevos++;
			}
			
		}
		toba::notificacion()->agregar("Se procesaron $lineas_procesadas registros, y se insertaron $cargos_nuevos cargos nuevos. (Se mantuvieron $cant_externos cargos externos ya existentes)",'info');
		

	}

	function insertar_cargo($cargo)
	{
		//Actualizo el cuil (si existe) y luego lo elimino del array porque no existe ese campo en sap_cargos_persona
		if(isset($cargo['cuil'])){
			//Armo la consulta de actualización del CUIL
			$sql_personas = "UPDATE sap_personas SET cuil = ".quote($cargo['cuil'])." WHERE nro_documento = ".quote($cargo['nro_documento']." WHERE EXISTS (SELECT * FROM sap_personas WHERE nro_documento = ".quote($cargo['nro_documento']).");" );
			
			//Actualizo el CUIL
			try{
				toba::db()->ejecutar($sql_personas);    
			} catch (toba_error_db $e) {
				//echo "Se omite la actualización del CUIL de ".$registro['nro_documento'];
			}
			unset($cargo['cuil']);	
		}

		//armo los campos y valores para el insert
		$campos = implode(',',array_keys($cargo));
		$valores = implode(',',array_values(array_map('quote',array_values($cargo))));

		//Armo la consulta de inserción del cargo
		$sql_cargos = "INSERT INTO sap_cargos_persona ($campos) VALUES ($valores);";

		
		

		//Inserto el cargo
		try {

			toba::db()->ejecutar($sql_cargos);
			return true;
		} catch (toba_error_db $e) {
			return false;
			//echo "Se omite: ".$cargo['cargo']." ( ".$cargo['dedicacion'].") de ".$cargo['nro_documento']." en ".$cargo['dependencia']." por: ".$e->get_mensaje_motor()."<br>";

		}
		

	}

	function evt__form_importar_cargos__modificacion_original($datos)
	{
		// SE PUEDE COMENTAR ESTA LINEA Y AÚN ASÍ FUNCIONA (NO ES NECESARIO BORRAR TODO)
		//limpio la tabla de cargos_persona
		toba::db()->ejecutar('TRUNCATE sap_cargos_persona');
		// =============================================================================
		

		$mapeo = array(
			'cuil'              => 'cuil',
			'cargo'             => 'codc_categ',
			'dedicacion'        => 'codc_dedic',
			'fecha_desde'       => 'fec_alta',
			'fecha_hasta'       => 'fec_baja',
			'nro_cargo_mapuche' => 'nro_cargo',
			'dependencia'       => 'codc_uacad',
			'caracter'          => 'codc_carac'
		);



		//Mantiene un registro de la cantidad de cargos que se procesan
		$lineas_procesadas = 0;
		$cargos_nuevos = 0;
		$cuils_actualizados = 0;

		if($datos['archivo_cargos']['tmp_name']){
			$archivo_cargos = fopen($datos['archivo_cargos']['tmp_name'],'r');
			
			while($cargo = fgetcsv($archivo_cargos)){
				$registro = array();
				if($lineas_procesadas == 0){
					foreach($mapeo as $campo_bd_local => $campo_csv){
						$posiciones[$campo_csv] = array_search($campo_csv, $cargo);
					}
					$lineas_procesadas++;
					continue;
				}
				$lineas_procesadas++;
				//CUIL
				
				//se hace el mapeo de todos los campos definidos, independientemente de su posicion en el CSV
				foreach($mapeo as $campo_bd_local => $campo_csv){
					$registro[$campo_bd_local] = $cargo[$posiciones[$campo_csv]];
				}
				
				//agrego al registro el numero de documento
				$registro['nro_documento'] = substr($registro['cuil'],2,8);
				//se quita el cero inicial (si es que tiene)
				if(substr($registro['nro_documento'],0,1) == '0'){
					$registro['nro_documento'] = substr($registro['nro_documento'],1,7);
				}
				
				//hago una compia del registro
				$tmp = $registro;

				//elimino el cuil, no va en la tabla cargos
				unset($tmp['cuil']);

				
				
				
			}
			toba::notificacion()->agregar("Se insertaron $cargos_nuevos cargos nuevos y se actualizaron $cuils_actualizados números de CUIL",'info');
		}
	}

	function extender_objeto_js()
	{
		echo "
				$('#form_4350_form_importar_cargos_modificacion').on('click',function(){
					toba.inicio_aguardar();
					if({$this->objeto_js}.dep('form_importar_cargos').ef('archivo_cargos').get_estado()){
						$('#form_4350_form_importar_cargos_modificacion').prop('disabled','disabled');	
					}
				});
		";
		/*echo "console.log($('#form_4350_form_importar_cargos'));
				/*$('#form_4350_form_importar_cargos_modificacion').on('click',function(){
					$this.prop('disabled','disabled');
				})*
			;";*/
	}



}
?>