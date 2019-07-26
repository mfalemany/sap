<?php
class cu_evaluacion extends sap_ei_cuadro
{
	//---- Config. EVENTOS sobre fila ---------------------------------------------------

	function conf_evt__modificar($evento, $fila)
	{
             if (($this->datos[$fila]['estado_evaluacion'] != 'MODIFICAR')) {
            $evento->ocultar();
        }
     

}
}
?>