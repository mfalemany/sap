<?php
class co_categorias_conicet
{

	function get_categorias_conicet()
	{
		$sql = "SELECT
			cat.id_cat_conicet,
			cat.cat_conicet
		FROM
			be_cat_conicet as cat
		ORDER BY cat_conicet";
		return toba::db()->consultar($sql);
	}

}
?>