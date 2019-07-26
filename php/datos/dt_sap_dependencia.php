<?php
class dt_sap_dependencia extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT id, nombre FROM sap_dependencia ORDER BY nombre";
		return toba::db('sap')->consultar($sql);
	}

}

?>