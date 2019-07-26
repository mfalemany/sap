<?php
	ini_set("display_errors",1);
	ini_set("session.gc_maxlifetime",7200);
	//Valido si se está utilizando Internet Explorer
	$ua = htmlentities($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES, 'UTF-8');
	if (preg_match('~MSIE|Internet Explorer~i', $ua) || (strpos($ua, 'Trident/7.0; rv:11.0') !== false) || preg_match('/Edge/i',$ua) ) {
		echo "<h1 style='background-color: #8e3d3d;color: #FFF;padding: 10px 0px 10px 30px;text-align: center;text-shadow: 1px 1px 2px #333;'>
			Lo sentimos, pero no est&aacute; admitido el uso del Sistema SAP en Internet Explorer (o EDGE) debido a numerosos problemas que pueden afectar su experiencia de usuario. Por favor, utilice otro navegador (Google Chrome, Mozilla Firefox, Opera, etc.)
		</h1>";
		die;
	}

	# Proyecto
	define('apex_pa_proyecto', 'sap');

	// Para que no pida login en cada inicio
	define("apex_pa_validacion_debug",1);

	# Ejecuta con metadatos compilados
	#define('apex_pa_metadatos_compilados', 1);

	# Deshabilita el autologin
	#define("apex_pa_validacion_debug", 0);

	# Activa el logger en modo 'debug'. Para modo info pasar a '6'
	define('apex_pa_log_archivo', true);
	define('apex_pa_log_archivo_nivel', 7);

	//--------------------------------------------------------------------------
	//------ Invocacion del nucleo del toba ------------------------------------
	//--------------------------------------------------------------------------
	if (isset($_SERVER['TOBA_DIR'])) {
		$dir = $_SERVER['TOBA_DIR'].'/php'; 
		$separador = (substr(PHP_OS, 0, 3) == 'WIN') ? ';.;' : ':.:';
		ini_set('include_path', ini_get('include_path'). $separador . $dir);
		require_once('nucleo/toba_nucleo.php');
		
		toba_nucleo::instancia()->acceso_web();	
		
	} else {
		die("Es necesario definir la variable 'TOBA_DIR' en el archivo de configuracion de apache
				(Utilize la directiva 'SetEnv')");
	}
?>