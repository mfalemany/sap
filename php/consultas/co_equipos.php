<?php
class co_equipos
{
    const CAMPOS = 'e.codigo, e.id,e.denominacion,e.email,e.departamento,e.coordinador,e.lineas_estudio,e.produccion,e.transferencia,e.area_conocimiento_id,e.dependencia_id,e.fecha_inicio,e.convocatoria_id,e.usuario_id';
    
    static function get_equiposByConvocatoriaUsuario($convocatoria_id,$usuario_id){
            $sql = "SELECT " . self::CAMPOS .
                    " FROM
                        sap_equipo e
                    WHERE e.usuario_id= ". quote($usuario_id) . " AND e.convocatoria_id= " . quote($convocatoria_id) . ";";
            return consultar_fuente($sql);
    }

    static function get_equipos_por_usuario($usuario_id){
        $sql = "SELECT  equi.codigo, 
                        equi.id,
                        equi.denominacion,
                        equi.email,
                        equi.departamento,
                        equi.coordinador,
                        equi.lineas_estudio,
                        equi.produccion,
                        equi.transferencia,
                        equi.area_conocimiento_id,
                        area.nombre as area_conocimiento,
                        equi.dependencia_id,
                        dep.nombre as dependencia,
                        equi.fecha_inicio,
                        equi.convocatoria_id,
                        equi.usuario_id
                FROM sap_equipo AS equi
                LEFT JOIN sap_area_conocimiento AS area ON area.id = equi.area_conocimiento_id
                LEFT JOIN sap_dependencia AS dep ON dep.id = equi.dependencia_id
                WHERE equi.usuario_id= ".quote($usuario_id).";";
        return consultar_fuente($sql);
    }

    static function esta_inscripto($id_equipo,$id_convocatoria){
        if(!$id_equipo || !$id_convocatoria){
            return FALSE;
        }
        $sql = "SELECT * FROM sap_equipos_convocatorias 
                WHERE id_equipo = ".quote($id_equipo)."
                AND id_convocatoria = ".quote($id_convocatoria);
        return count(toba::db()->consultar($sql));
    }


  
    static function get_equiposByFiltros($where = ' 1 = 1 '){
           $sql = "SELECT " . self::CAMPOS .
                    " FROM
                        sap_equipo e
                    JOIN sap_area_beca a ON a.id=e.c.sap_area_beca_id
                    JOIN sap_dependencia d ON d.id=c.sap_dependencia_id
                    JOIN sap_convocatoria co ON co.id=c.sap_convocatoria_id
                    JOIN sap_equipo_proyecto ep ON ep.equipo_id=c.proyecto_id
                    WHERE co.fecha_hasta >= current_date
                    AND {$where};";
            return consultar_fuente($sql);
    }
     static function get_equiposByParametros($filtro){
           $sql = 
                "SELECT e.*,
                        -- string_agg(ei.apellido || ', ' || ei.nombre, ' / ') AS lista_integrantes,
                        (SELECT string_agg(ei.apellido || ', ' || ei.nombre, ' / ') AS lista_integrantes FROM sap_equipo_integrante ei WHERE ei.equipo_id=e.id GROUP BY ei.equipo_id),
                        d.nombre as dependencia,
                        a.nombre as area_conocimiento,
                        e.usuario_id as usuario,
                        (SELECT string_agg(p.descripcion, ' / ') AS lista_proyectos FROM sap_proyectos p JOIN sap_equipo_proyecto ep on ep.proyecto_id=p.id WHERE ep.equipo_id=e.id GROUP BY ep.equipo_id),
                        (SELECT string_agg(epc.palabra_clave, ' / ') AS lista_palabras FROM sap_equipo_palabra_clave epc WHERE epc.equipo_id=e.id)
                FROM sap_equipo e
                LEFT JOIN sap_area_conocimiento a ON a.id=e.area_conocimiento_id
                LEFT JOIN sap_dependencia d ON d.id=e.dependencia_id
                LEFT JOIN sap_convocatoria co ON co.id=e.convocatoria_id
                LEFT JOIN sap_equipo_integrante ei ON ei.equipo_id=e.id
                LEFT JOIN sap_personas as per on per.nro_documento = ei.dni
                WHERE 1=1";

            if (isset($filtro['integrante'])){
                $sql .= " AND per.apellido||per.nombres ILIKE ".quote("%".$filtro['integrante']."%") ;
            }
            if (isset($filtro['codigo'])){
                $sql .= " AND e.codigo  ~* " . quote($filtro['codigo']);
            }
            if (isset($filtro['id_convocatoria'])){
                $sql .= " AND exists (SELECT * FROM sap_equipos_convocatorias WHERE id_convocatoria = " . quote($filtro['id_convocatoria'])." AND id_equipo = e.id)";
            }
            if (isset($filtro['area_conocimiento_id'])){
                $sql .= "AND a.id = ".quote($filtro['area_conocimiento_id']);
            }



            $sql .= " GROUP BY e.id,d.nombre,a.nombre,e.usuario_id;";
            return consultar_fuente($sql);
    }

    function get_integrantes_equipo($id_equipo)
    {
        $sql = "SELECT ei.dni as nro_documento, per.apellido||', '||per.nombres as integrante, per.mail
                FROM sap_equipo_integrante AS ei
                LEFT JOIN sap_personas AS per ON per.nro_documento = ei.dni
                WHERE ei.equipo_id = ".quote($id_equipo)."
                ORDER BY per.apellido, per.nombres"; 
        return toba::db()->consultar($sql);
    }

    function get_historial_presentaciones($usuario)
    {
        $sql = "SELECT co.id AS id_convocatoria, 
                        co.nombre AS convocatoria, 
                        eq.denominacion AS equipo, 
                        eq.id AS id_equipo,
                        ec.asistio
                FROM sap_equipos_convocatorias AS ec
                LEFT JOIN sap_equipo AS eq ON ec.id_equipo = eq.id
                LEFT JOIN sap_convocatoria AS co ON co.id = ec.id_convocatoria
                WHERE eq.usuario_id = ".quote($usuario);
        return toba::db()->consultar($sql);
    }

    function get_detalles_certificado($params)
    {
        $params = toba_ei_cuadro::recuperar_clave_fila(4314,$params['fila_safe']);
        if(!is_array($params) || count($params) == 0 || $params['asistio'] != 'S'){
            throw new toba_error('No se pueden determinar los detalles del certificado. No se recibieron los par?etros necesarios o el equipo no estuvo presente');
        }

        $sql = "SELECT co.id AS id_convocatoria,
                        eq.codigo,
                        per.apellido||', '||per.nombres AS director,
                        co.nombre AS convocatoria, 
                        eq.denominacion AS equipo, 
                        eq.id AS id_equipo
                FROM sap_equipo AS eq
                LEFT JOIN sap_equipos_convocatorias AS ec ON ec.id_equipo = eq.id
                LEFT JOIN sap_convocatoria AS co ON co.id = ec.id_convocatoria
                LEFT JOIN sap_personas AS per ON per.nro_documento = eq.usuario_id
                WHERE co.id = ".quote($params['id_convocatoria'])."
                AND eq.id = ".quote($params['id_equipo']);
                //echo nl2br($sql); die;

        return toba::db()->consultar_fila($sql);
    }
  
    
}
?>
