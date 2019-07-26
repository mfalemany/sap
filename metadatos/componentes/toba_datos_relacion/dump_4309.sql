------------------------------------------------------------
--[4309]--  Evaluación - ci_evaluacion_proyectos - datos 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'sap', --proyecto
	'4309', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_relacion', --clase
	'20', --punto_montaje
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Evaluación - ci_evaluacion_proyectos - datos', --nombre
	NULL, --titulo
	NULL, --colapsable
	NULL, --descripcion
	'sap', --fuente_datos_proyecto
	'sap', --fuente_datos
	NULL, --solicitud_registrar
	NULL, --solicitud_obj_obs_tipo
	NULL, --solicitud_obj_observacion
	NULL, --parametro_a
	NULL, --parametro_b
	NULL, --parametro_c
	NULL, --parametro_d
	NULL, --parametro_e
	NULL, --parametro_f
	NULL, --usuario
	'2018-06-22 10:08:37', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_datos_rel
------------------------------------------------------------
INSERT INTO apex_objeto_datos_rel (proyecto, objeto, debug, clave, ap, punto_montaje, ap_clase, ap_archivo, sinc_susp_constraints, sinc_orden_automatico, sinc_lock_optimista) VALUES (
	'sap', --proyecto
	'4309', --objeto
	'0', --debug
	NULL, --clave
	'2', --ap
	'20', --punto_montaje
	NULL, --ap_clase
	NULL, --ap_archivo
	'1', --sinc_susp_constraints
	'1', --sinc_orden_automatico
	'1'  --sinc_lock_optimista
);

------------------------------------------------------------
-- apex_objeto_dependencias
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3006', --dep_id
	'4309', --objeto_consumidor
	'4310', --objeto_proveedor
	'sap_proy_pdts_eval', --identificador
	'', --parametros_a
	'', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'6'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3029', --dep_id
	'4309', --objeto_consumidor
	'4328', --objeto_proveedor
	'sap_proy_pdts_informe_eval', --identificador
	'', --parametros_a
	'', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'9'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3004', --dep_id
	'4309', --objeto_consumidor
	'4307', --objeto_proveedor
	'sap_proy_pi_eval', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'4'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3025', --dep_id
	'4309', --objeto_consumidor
	'4324', --objeto_proveedor
	'sap_proy_pi_informe_eval', --identificador
	'', --parametros_a
	'', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'8'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3003', --dep_id
	'4309', --objeto_consumidor
	'2546', --objeto_proveedor
	'sap_proyectos', --identificador
	NULL, --parametros_a
	'1', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'1'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3028', --dep_id
	'4309', --objeto_consumidor
	'4327', --objeto_proveedor
	'sap_proyectos_pdts', --identificador
	'', --parametros_a
	'1', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'3'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3026', --dep_id
	'4309', --objeto_consumidor
	'4325', --objeto_proveedor
	'sap_proyectos_pdts_informe', --identificador
	'', --parametros_a
	'', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'5'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3027', --dep_id
	'4309', --objeto_consumidor
	'4326', --objeto_proveedor
	'sap_proyectos_pi', --identificador
	'', --parametros_a
	'1', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'7'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3024', --dep_id
	'4309', --objeto_consumidor
	'4322', --objeto_proveedor
	'sap_proyectos_pi_informe', --identificador
	'', --parametros_a
	'', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'2'  --orden
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_datos_rel_asoc
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4309', --objeto
	'145', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'2546', --padre_objeto
	'sap_proyectos', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4326', --hijo_objeto
	'sap_proyectos_pi', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'1'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4309', --objeto
	'146', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'2546', --padre_objeto
	'sap_proyectos', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4327', --hijo_objeto
	'sap_proyectos_pdts', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'2'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4309', --objeto
	'157', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'4326', --padre_objeto
	'sap_proyectos_pi', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4307', --hijo_objeto
	'sap_proy_pi_eval', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'3'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4309', --objeto
	'158', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'4326', --padre_objeto
	'sap_proyectos_pi', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4322', --hijo_objeto
	'sap_proyectos_pi_informe', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'4'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4309', --objeto
	'159', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'4322', --padre_objeto
	'sap_proyectos_pi_informe', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4324', --hijo_objeto
	'sap_proy_pi_informe_eval', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'5'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4309', --objeto
	'160', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'4327', --padre_objeto
	'sap_proyectos_pdts', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4310', --hijo_objeto
	'sap_proy_pdts_eval', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'6'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4309', --objeto
	'161', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'4327', --padre_objeto
	'sap_proyectos_pdts', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4325', --hijo_objeto
	'sap_proyectos_pdts_informe', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'7'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4309', --objeto
	'162', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'4325', --padre_objeto
	'sap_proyectos_pdts_informe', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4328', --hijo_objeto
	'sap_proy_pdts_informe_eval', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'8'  --orden
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_rel_columnas_asoc
------------------------------------------------------------
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4309', --objeto
	'157', --asoc_id
	'4326', --padre_objeto
	'2173', --padre_clave
	'4307', --hijo_objeto
	'2108'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4309', --objeto
	'158', --asoc_id
	'4326', --padre_objeto
	'2173', --padre_clave
	'4322', --hijo_objeto
	'2163'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4309', --objeto
	'159', --asoc_id
	'4322', --padre_objeto
	'2162', --padre_clave
	'4324', --hijo_objeto
	'2165'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4309', --objeto
	'160', --asoc_id
	'4327', --padre_objeto
	'2174', --padre_clave
	'4310', --hijo_objeto
	'2122'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4309', --objeto
	'161', --asoc_id
	'4327', --padre_objeto
	'2174', --padre_clave
	'4325', --hijo_objeto
	'2172'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4309', --objeto
	'162', --asoc_id
	'4325', --padre_objeto
	'2171', --padre_clave
	'4328', --hijo_objeto
	'2176'  --hijo_clave
);
