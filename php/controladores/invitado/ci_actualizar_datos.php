<?php
class ci_actualizar_datos extends sap_ci
{
	protected $s__credenciales;
	function conf()
	{
		if( ! (isset($_GET['nro_documento']) && isset($_GET['token']))){
			if(!isset($this->s__credenciales)){
				die;
			}
		}else{
			$this->s__credenciales = $_GET;
		}
		//cargo los datos según el nro_documento recibido
		$this->get_datos()->cargar(array('nro_documento'=>$this->s__credenciales['nro_documento']));
		
		//Si la persona no existe, chau tu plata
		if(!$this->get_datos()->esta_cargada()){
			die('No existe la persona indicada');		
		}
	}

	function conf__form_datos_personales(sap_ei_formulario $form)
	{
		$datos = $this->get_datos('personas')->get();
		if($datos){
			//Obtengo el id del campo de aplicacion que le corresponde al subcampo de la persona (para llenar el combo cascada)
			if($datos['id_subcampo_aplicacion']){
				$datos['id_campo_aplicacion'] = toba::consulta_php('co_tablas_basicas')->get_campo_de_subcampo($datos['id_subcampo_aplicacion']);

			}
			$form->set_datos($datos);
		}
	}
	function evt__form_datos_personales__modificacion($datos)
	{
		$this->get_datos('personas')->set($datos);
	}

	function conf__form_cat_conicet(sap_ei_formulario $form)
	{
		//Esta variable no almacena a Macri
		$cat = $this->get_datos('cat_conicet')->get();
		if($cat){
			$form->set_datos($cat);
		}
	}
	function evt__form_cat_conicet__modificacion($datos)
	{
		$this->get_datos('cat_conicet')->set($datos);
	}

	function evt__guardar()
	{
		$this->get_datos()->sincronizar();
		toba::notificacion()->agregar('Muchas gracias por actualizar sus datos! Es muy importante para nosotros, ya que nos permite generar y mantener indicadores de calidad. Ya puede cerrar esta ventana','info');
	}


	function get_datos($tabla = NULL)
	{
		return ($tabla) ? $this->dep('datos')->tabla($tabla) : $this->dep('datos');
	}


}
?>