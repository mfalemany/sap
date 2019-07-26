<?php
/**
 * Esta clase fue y ser?generada autom?icamente. NO EDITAR A MANO.
 * @ignore
 */
class sap_autoload 
{
	static function existe_clase($nombre)
	{
		return isset(self::$clases[$nombre]);
	}

	static function cargar($nombre)
	{
		if (self::existe_clase($nombre)) { 
			 require_once(dirname(__FILE__) .'/'. self::$clases[$nombre]); 
		}
	}

	static protected $clases = array(
		'sap_ci' => 'extension_toba/componentes/sap_ci.php',
		'sap_cn' => 'extension_toba/componentes/sap_cn.php',
		'sap_datos_relacion' => 'extension_toba/componentes/sap_datos_relacion.php',
		'sap_datos_tabla' => 'extension_toba/componentes/sap_datos_tabla.php',
		'sap_ei_arbol' => 'extension_toba/componentes/sap_ei_arbol.php',
		'sap_ei_archivos' => 'extension_toba/componentes/sap_ei_archivos.php',
		'sap_ei_calendario' => 'extension_toba/componentes/sap_ei_calendario.php',
		'sap_ei_codigo' => 'extension_toba/componentes/sap_ei_codigo.php',
		'sap_ei_cuadro' => 'extension_toba/componentes/sap_ei_cuadro.php',
		'sap_ei_esquema' => 'extension_toba/componentes/sap_ei_esquema.php',
		'sap_ei_filtro' => 'extension_toba/componentes/sap_ei_filtro.php',
		'sap_ei_firma' => 'extension_toba/componentes/sap_ei_firma.php',
		'sap_ei_formulario' => 'extension_toba/componentes/sap_ei_formulario.php',
		'sap_ei_formulario_ml' => 'extension_toba/componentes/sap_ei_formulario_ml.php',
		'sap_ei_grafico' => 'extension_toba/componentes/sap_ei_grafico.php',
		'sap_ei_mapa' => 'extension_toba/componentes/sap_ei_mapa.php',
		'sap_servicio_web' => 'extension_toba/componentes/sap_servicio_web.php',
		'sap_comando' => 'extension_toba/sap_comando.php',
		'sap_modelo' => 'extension_toba/sap_modelo.php',
		'FPDF' => '3ros/fpdf/fpdf.php',
		'certificado_comunicaciones' => 'generadores_pdf/comunicaciones/certificado.php',
		'certificado_equipos' => 'generadores_pdf/equipos/certificado.php',
		'Informe_evaluaciones_realizadas' => 'generadores_pdf/proyectos/informe_evaluaciones_realizadas.php',
		'Certificado_evaluacion' => 'generadores_pdf/proyectos/certificado_evaluacion.php',
		'Comprobante_insc_grupos' => 'generadores_pdf/grupos/Comprobante_insc_grupos.php'
	);
}
?>