<?php
class co_convocatorias
{
    static function get_convocatoriasVigentes($aplicable){
            $vigentes = "SELECT
                            id,
                            nombre,
                            fecha_desde,
                            fecha_hasta,
                            limite_edicion,
                            Case estado 
                                WHEN 'A' THEN 'Activa' 
                                WHEN 'E' THEN 'Activa en Evaluacion'
                                ELSE 'Cerrada' END as estado_desc,
                            aplicable
                        FROM
                            sap_convocatoria
                        WHERE fecha_hasta >= current_date 
                        AND aplicable=". quote($aplicable);
            
            $vencidas_modificables = "SELECT
                            c.id,
                            c.nombre,
                            c.fecha_desde,
                            c.fecha_hasta,
                            c.limite_edicion,	
                            Case c.estado 
                                WHEN 'A' THEN 'Activa' 
                                WHEN 'E' THEN 'Activa en Evaluacion'
                                ELSE 'Cerrada' END as estado_desc,
                            c.aplicable
                        FROM
                            sap_convocatoria as c
                        WHERE c.limite_edicion >= current_date 
                        AND aplicable=". quote($aplicable)." 
                        AND EXISTS (
								SELECT * 
								FROM sap_comunicacion_evaluacion as ce 
								LEFT JOIN sap_comunicacion as co on co.id = ce.sap_comunicacion_id
								WHERE co.usuario_id = ".quote(toba::usuario()->get_id())."
								AND co.sap_convocatoria_id = c.id
								AND sap_evaluacion_id = (
														SELECT id 
														 FROM sap_evaluacion 
														 WHERE nombre = 'A MODIFICAR'
														 )
								AND ce.id = (
										SELECT MAX(id) 
										FROM sap_comunicacion_evaluacion 
										WHERE sap_comunicacion_id = co.id
											)
								);";  
									
            $sql = $vigentes." UNION ".$vencidas_modificables;
            //echo nl2br($sql); die;
            return consultar_fuente($sql);
    }
    static function get_convocatorias($filtro = array()){
            $where = array();
            if(isset($filtro['id'])){
                $where[] = "id = ".quote($filtro['id']);
            }
            if(isset($filtro['aplicable'])){
                $where[] = "aplicable = ".quote($filtro['aplicable']);
            }
            if(isset($filtro['id'])){
                $where[] = "id = ".quote($filtro['id']);
            }
            if(isset($filtro['estado'])){
                $where[] = "estado = ".quote($filtro['estado']);
            }
            if(isset($filtro['vigente'])){
                if($filtro['vigente']){
                    $where[] = "current_date between fecha_desde and fecha_hasta";
                }
            }

            $sql = "SELECT
                            id,
                            nombre,
                            fecha_desde,
                            fecha_hasta,
                            limite_edicion,
                            Case estado 
                                WHEN 'A' THEN 'Activa' 
                                WHEN 'E' THEN 'Activa en Evaluacion'
                                ELSE 'Cerrada' END as estado_desc,
                            aplicable
                        FROM
                            sap_convocatoria
                    ORDER BY id DESC;";
            if (count($where)>0) {
                $sql = sql_concatenar_where($sql, $where);
            }
            return consultar_fuente($sql);
    }
    static function esta_en_evaluacion($convocatoria_id){
        $sql = "SELECT
                    estado 
                    FROM
                    sap_convocatoria WHERE id= {$convocatoria_id};";
        $estado=toba::db()->consultar_fila($sql);
        if ($estado['estado']=='E'){
            return true;
        }else{
            return false;
        }
            
    }

    static function get_detalles_convocatoria($convocatoria_id){
        $sql = "SELECT * FROM sap_convocatoria WHERE id= {$convocatoria_id};";
        return toba::db()->consultar_fila($sql);
    }

    function get_convocatorias_vigentes_subsidios()
    {
        return self::get_convocatorias(array('aplicable'=>'SUBSIDIOS','estado'=>'A','vigente'=>true));
    }

    /**
     * Retorna un ID o listado de IDs de convocatorias del año inmediatamente anterior a la recibida como parámetro
     * @param  [integer] $id_convocatoria El id de una convocatoria
     * @param  [string] $aplicable Si la convocatoria es aplicable solamente a un tipo específico
     * @return [array]                  Convocatoria/s del año inmediatamente anterior
     */
    function get_convocatorias_anterior($id_convocatoria,$aplicable = NULL){
        $sql = "SELECT array_to_string(array_agg(id), ',') AS ids
                FROM sap_convocatoria 
                WHERE extract(year FROM fecha_desde) = (
                    (SELECT extract(year FROM fecha_desde) FROM sap_convocatoria WHERE id = $id_convocatoria) - 1
                ) ";
        if($aplicable){
            $sql = sql_concatenar_where($sql,array("aplicable = '$aplicable'"));
        }

        $resultado = toba::db()->consultar_fila($sql);
        return isset($resultado['ids']) ? $resultado['ids'] : "";
    }


    function get_convocatorias_vigentes_equipos()
    {
        return self::get_convocatorias(array('aplicable'=>'EQUIPOS','estado'=>'A','vigente'=>true));
    }


    function get_convocatorias_subsidios()
    {
        return self::get_convocatorias(array('aplicable'=>'SUBSIDIOS'));
    }

    function get_convocatorias_equipos()
    {
        return self::get_convocatorias(array('aplicable'=>'EQUIPOS'));
    }

    function get_convocatorias_becarios()
    {
        return self::get_convocatorias(array('aplicable'=>'BECARIOS'));
    }

    function get_campo($campo,$tabla,$filtro)
    {
        $where = array();
        if(isset($filtro['id'])){
            $where[] = 'id = '.quote($filtro['id']);
        }
        $sql = "SELECT $campo FROM $tabla";
        if(count($where)){
            $sql = sql_concatenar_where($sql,$where);
        }
        return toba::db()->consultar($sql);
    }



  
}
?>