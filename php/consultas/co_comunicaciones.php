<?php
class co_comunicaciones
{
    static function get_comunicacionesByConvocatoriaUsuario($convocatoria_id = '',$usuario_id){
            $sql = "SELECT
                        c.id,
                        titulo,
                        e_mail,
                        resumen,
                        resolucion,
                        telefono,
                        sap_area_beca_id,
                        sap_tipo_beca_id,
                        c.sap_dependencia_id,
                        periodo_hasta,
                        periodo_desde,
                        sap_convocatoria_id,
                        version_impresa,
                        version_modificacion,
                        usuario_id,
                        p.codigo || ' - ' || p.descripcion as proyecto
                    FROM
                        sap_comunicacion c
                    JOIN sap_proyectos p ON p.id=c.proyecto_id
                    WHERE usuario_id= ". quote($usuario_id) . " AND sap_convocatoria_id= " . quote($convocatoria_id); /*  
                    " AND (SELECT ce.sap_evaluacion_id 
                            FROM sap_comunicacion_evaluacion ce 
                            WHERE ce.sap_comunicacion_id = c.id 
                                ORDER BY ce.id DESC LIMIT 1) = 4 ; -- MODIFICAR" ;*/
            //echo nl2br($sql); die;
            return consultar_fuente($sql);
    }
       static function get_comunicacionesByEstadoEvaluacion($evaluacion_id,$area_id,$tipo_beca_id,$estado_convocatoria='',$sap_convocatoria_id = NULL,$estado_comunicacion=NULL){
            //echo "Evaluacion id: ".$evaluacion_id." - Area id: ".$area_id." - Tipo de Beca id:".$tipo_beca_id." - Estado: ".$estado." - Sap Convoc ID:".$sap_convocatoria_id."<br>";
           $sql = "SELECT
                        c.id,
                        c.titulo,
                        c.e_mail,
                        array_to_string(ARRAY_AGG(au.autor ORDER BY au.id ASC) , ',') AS lista_autores,
                        (SELECT ce.observaciones 
                            FROM sap_comunicacion_evaluacion ce 
                            WHERE ce.sap_comunicacion_id=c.id 
                            ORDER BY ce.id DESC limit 1) AS observacion_evaluacion,
                        a.nombre AS area_beca,
                        t.descripcion AS tipo_beca,
                        c.orden_poster,
                        c.evaluador_poster,
                         case when (SELECT ce.sap_evaluacion_id 
						FROM sap_comunicacion_evaluacion ce 
						WHERE ce.sap_comunicacion_id = c.id 
						ORDER BY ce.id DESC LIMIT 1) = 7 -- SELECCIONADA
						then 'EXPOSICION ORAL'
						else ''
						end as exposicion
                    FROM
                        sap_comunicacion c
                    LEFT JOIN sap_area_conocimiento a ON a.id=c.sap_area_beca_id
                    LEFT JOIN sap_tipo_beca t ON t.id=c.sap_tipo_beca_id
                    LEFT JOIN sap_convocatoria co on co.id=c.sap_convocatoria_id
                    LEFT JOIN sap_autor au ON au.sap_comunicacion_id = C .id
                    WHERE 1=1";
           if ($evaluacion_id != ''){
                $sql .= " AND (SELECT ce.sap_evaluacion_id 
                        FROM sap_comunicacion_evaluacion ce 
                        WHERE ce.sap_comunicacion_id = c.id 
                        ORDER BY ce.id DESC LIMIT 1) IN ({$evaluacion_id})";
            }else{
                $sql .= " AND c.id NOT IN (SELECT DISTINCT ce.sap_comunicacion_id 
                                                FROM sap_comunicacion_evaluacion ce)";
            }
            
            if ($area_id != ''){
                $sql .= " AND c.sap_area_beca_id = {$area_id}";
                   
            }
            if ($tipo_beca_id != ''){
                $sql .= " AND c.sap_tipo_beca_id = {$tipo_beca_id}";
                   
            }
             if ($estado_convocatoria != ''){
                $sql .= " AND co.estado=" .  quote($estado_convocatoria);
            }
            if($sap_convocatoria_id){
                $sql .= " AND c.sap_convocatoria_id = " .  $sap_convocatoria_id;   
            }
             if ($estado_comunicacion){
                $sql .= " AND c.estado=" .  quote($estado_comunicacion);
            }
            $sql .= " GROUP BY
                            c.id,
                            c.titulo,
                            c.e_mail,
                            a.nombre,
                            t.descripcion
                       ORDER BY c.orden_poster ASC;";
                    
            return consultar_fuente($sql);
    }
  
    static function actualizarVersionImpresa($comunicacion_id){
            $sql = "UPDATE 
                        sap_comunicacion
                        SET version_impresa=version_modificacion
                    WHERE id={$comunicacion_id};";
                        
            return consultar_fuente($sql);
    }
    static function get_comunicacionesByFiltros($where = ' 1 = 1 '){
            $sql = "SELECT
                            c.id,
                            A .nombre AS area,
                            titulo,
                            c.sap_convocatoria_id,
                            array_to_string(ARRAY_AGG(au.autor ORDER BY au.id ASC) , ',') AS lista_autores,
                            C .e_mail,
                            C .telefono,
                            T .descripcion AS tipo,
                            P.codigo || ' - '|| P.descripcion AS proyecto_descripcion,
                            d.nombre AS dependencia,
                            c.orden_poster,
                            c.version_impresa,
                            c.version_modificacion,
                            case c.estado when 'C' then 'Cerrada' when 'A' then 'Abierta' else 'Sin estado' end as estado
                            
                    FROM sap_comunicacion C
                    JOIN sap_area_conocimiento A ON A . ID = C .sap_area_beca_id
                    JOIN sap_convocatoria conv ON conv.id = c.sap_convocatoria_id
                    JOIN sap_tipo_beca T ON T . ID = C .sap_tipo_beca_id
                    JOIN sap_dependencia d ON d. ID = C .sap_dependencia_id
                    JOIN sap_proyectos P ON P . ID = C .proyecto_id
                    LEFT JOIN sap_autor au ON au.sap_comunicacion_id = C .id
                    WHERE {$where}
                    GROUP BY
                            c.id,
                            A .nombre,
                            titulo,
                            C .e_mail,
                            C .telefono,
                            T .descripcion,
                            P .descripcion,
                            P .codigo,
                            d.nombre
                    ORDER BY c.id DESC";
                    
            return consultar_fuente($sql);
    }
     static function get_comunicacionesConEstadoEvaluacion($usuario_id){
            $sql = "SELECT
                        c.id,
                        a.nombre AS area,
                        titulo,
                        array_to_string(
                                ARRAY_AGG (au.autor ORDER BY au.ID ASC),
                                ','
                        ) AS lista_autores,
                        c.e_mail,
                        c.telefono,
                        t.descripcion AS tipo,
                        p.codigo || ' - ' || p.descripcion AS proyecto_descripcion,
                        d.nombre AS dependencia,
                        c.orden_poster,
                        (SELECT e.nombre 
                            FROM sap_comunicacion_evaluacion ce 
                            JOIN sap_evaluacion e ON e.id=ce.sap_evaluacion_id
                            WHERE ce.sap_comunicacion_id=c.id 
                            ORDER BY ce.id DESC limit 1) AS estado_evaluacion,
                        (SELECT ce.observaciones 
                            FROM sap_comunicacion_evaluacion ce 
                            WHERE ce.sap_comunicacion_id=c.id 
                            ORDER BY ce.id DESC limit 1) AS observacion_evaluacion,
                        (SELECT ce.evaluadores 
                            FROM sap_comunicacion_evaluacion ce 
                            WHERE ce.sap_comunicacion_id=c.id 
                            ORDER BY ce.id DESC limit 1) AS evaluadores
                        
                                         
                       
                FROM
                        sap_comunicacion c
                LEFT JOIN sap_area_conocimiento as a ON a.id = c.sap_area_beca_id
                LEFT JOIN sap_tipo_beca t ON t.id = c.sap_tipo_beca_id
                LEFT JOIN sap_dependencia d ON d.id = c.sap_dependencia_id
                LEFT JOIN sap_proyectos p ON p.id = c.proyecto_id
                LEFT JOIN sap_autor au ON au.sap_comunicacion_id = c.id
                WHERE c.usuario_id={$usuario_id}
                GROUP BY
                        c.id,
                        a.nombre,
                        titulo,
                        c.e_mail,
                        c.telefono,
                        t.descripcion,
                        p.descripcion,
                        p.codigo,
                        d.nombre
                ORDER BY c.id DESC;";
                        //echo nl2br($sql);
            return consultar_fuente($sql);
    }
    
    static function get_comunicacionEvaluaciones($comunicacion_id){
         $sql = "SELECT
                     ce.evaluadores AS evaluadores,
                     ce.observaciones AS observaciones,
                     ce.fecha_hora AS fecha_hora,
                     e.nombre AS evaluacion,
                     ce.usuario_id
                 FROM sap_comunicacion_evaluacion ce
                 JOIN sap_evaluacion e ON e.id=ce.sap_evaluacion_id
                 WHERE ce.sap_comunicacion_id = {$comunicacion_id}
                     ORDER BY ce.id DESC;";

                     

         return consultar_fuente($sql);
    }
      static function get_comunicacionTituloById($comunicacion_id){
         $sql = "SELECT
                    CASE 
                        WHEN (character_length(c.titulo)) > 150 THEN c.id || ' - ' || substr(c.titulo,0,150) || '...'
                    ELSE c.id || ' - ' || c.titulo END as titulo
                FROM sap_comunicacion c
                WHERE c.id = {$comunicacion_id};";
         return consultar_fuente($sql);
    }

    static function get_historial_comunicaciones($id_usuario)
    {
        $sql = "SELECT com.id,
                       com.orden_poster,
                       com.titulo, 
                       area.descripcion area_tipo_beca, 
                       tb.descripcion tipo_beca, 
                       conv.nombre convocatoria,
                       ce.sap_evaluacion_id,
                       eval.nombre evaluacion
                FROM sap_comunicacion as com
                LEFT JOIN sap_area_conocimiento as area on area.id = com.sap_area_beca_id
                LEFT JOIN sap_tipo_beca as tb on tb.id = com.sap_tipo_beca_id
                LEFT JOIN sap_convocatoria as conv on conv.id = com.sap_convocatoria_id
                LEFT JOIN sap_comunicacion_evaluacion as ce on ce.sap_comunicacion_id = com.id and ce.id = (select max(id) from sap_comunicacion_evaluacion where sap_comunicacion_id = com.id)
                LEFT JOIN sap_evaluacion as eval on eval.id = ce.sap_evaluacion_id
                    AND ce.fecha_hora = (SELECT MAX(fecha_hora) 
                                            FROM sap_comunicacion_evaluacion 
                                            WHERE sap_comunicacion_id = com.id)
                WHERE com.usuario_id = ".quote($id_usuario)."
                ORDER BY com.id DESC";
                //echo nl2br($sql);
        return consultar_fuente($sql);
    }

    function abrir_comunicacion($id)
    {
        return toba::db()->ejecutar("UPDATE sap_comunicacion SET estado = 'A' WHERE id = ".quote($id));
    }
    
    function get_reporte_certificados($filtro = NULL)
    {
        if(!$filtro){
            return array();
        }
        $sql = "SELECT nombre AS area,
                        id_comunicacion, 
                        orden_poster, 
                        replace(titulo,chr(10),' ') AS titulo, 
                        nombre_convocatoria,
                        id_convocatoria,
                        evaluacion,
                        es_mejor_trabajo,
                        string_agg(autor,' / ') AS autores
                FROM (
                    SELECT ac.nombre, 
                        com.id as id_comunicacion,
                        eva.sap_evaluacion_id as evaluacion,
                        com.es_mejor_trabajo,
                        com.orden_poster, 
                        com.titulo,
                        com.sap_convocatoria_id as id_convocatoria,
                        conv.nombre as nombre_convocatoria, 
                        CASE au.es_becario 
                            WHEN true THEN au.autor||' (Becario)' 
                            else au.autor end AS autor, 
                        au.es_becario
                    FROM sap_comunicacion_evaluacion AS eva
                    LEFT JOIN sap_comunicacion AS com ON com.id = eva.sap_comunicacion_id
                    LEFT JOIN sap_area_conocimiento AS ac ON ac.id = sap_area_beca_id
                    LEFT JOIN sap_autor AS au ON au.sap_comunicacion_id = com.id
                    LEFT JOIN sap_convocatoria AS conv ON conv.id = com.sap_convocatoria_id ";
                    if(isset($filtro['estado_evaluacion'])){
                        switch ($filtro['estado_evaluacion']) {
                            case 'S':
                                $sql .= " where eva.sap_evaluacion_id = 7 "; 
                                break;
                            case 'A':
                                $sql .= " where eva.sap_evaluacion_id = 5 "; 
                                break;
                            case 'T':
                                $sql .= " where eva.sap_evaluacion_id in (7,5) "; 
                                break;
                            default:
                                $sql .= " where eva.sap_evaluacion_id in (7,5) "; 
                                break;
                        }
                    }else{
                        $sql .= " where eva.sap_evaluacion_id in (7,5) "; 
                    }
        if(isset($filtro['id_convocatoria'])){
            $sql .= "and com.sap_convocatoria_id = ".$filtro['id_convocatoria'];
        }
        if(isset($filtro['id_comunicacion'])){
            $sql .= "and com.id = ".$filtro['id_comunicacion'];
        }
        if(isset($filtro['nro_documento'])){
            $sql .= "and com.usuario_id = ".quote($filtro['nro_documento']);
        }

        
        $sql.=" and eva.fecha_hora = (SELECT max(fecha_hora) FROM sap_comunicacion_evaluacion where sap_comunicacion_id = com.id)
                    order by orden_poster asc, es_becario desc
                    ) AS tmp
                group by 1,2,3,4,5,6,7,8
                order by orden_poster";
        return toba::db()->consultar($sql);
    }


    function get_comunicacion($id)
    {
        $sql = "SELECT * FROM sap_comunicacion WHERE id = ".quote($id);
        return toba::db()->consultar_fila($sql);
    }

    function get_ultima_evaluacion($id_comunicacion)
    {
        $sql = "SELECT com.id, com.fecha_hora, com.sap_evaluacion_id, com.sap_comunicacion_id, com.evaluadores, com.observaciones, com.usuario_id, eva.nombre as evaluacion 
                FROM sap_comunicacion_evaluacion as com
                LEFT JOIN sap_evaluacion as eva ON eva.id = com.sap_evaluacion_id
                WHERE sap_comunicacion_id = ".quote($id_comunicacion)." ORDER BY fecha_hora DESC LIMIT 1";
        return toba::db()->consultar_fila($sql);
    }

    static function get_autores($id_comunicacion)
    {
        $sql = "SELECT autor,es_becario FROM sap_autor WHERE sap_comunicacion_id = ".quote($id_comunicacion)." ORDER BY es_becario DESC, id ";
        return toba::db()->consultar($sql);
    }
 
    
  
    
}
?>