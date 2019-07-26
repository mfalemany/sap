------------------------------------------------------------
--[4466]--  Mis Proyectos - ci_detalles_proyecto - datos 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'sap', --proyecto
	'4466', --objeto
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
	'Mis Proyectos - ci_detalles_proyecto - datos', --nombre
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
	'2019-07-10 13:07:47', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_datos_rel
------------------------------------------------------------
INSERT INTO apex_objeto_datos_rel (proyecto, objeto, debug, clave, ap, punto_montaje, ap_clase, ap_archivo, sinc_susp_constraints, sinc_orden_automatico, sinc_lock_optimista) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'0', --debug
	NULL, --clave
	'2', --ap
	'20', --punto_montaje
	NULL, --ap_clase
	NULL, --ap_archivo
	'0', --sinc_susp_constraints
	'1', --sinc_orden_automatico
	'1'  --sinc_lock_optimista
);

------------------------------------------------------------
-- apex_objeto_dependencias
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3225', --dep_id
	'4466', --objeto_consumidor
	'4479', --objeto_proveedor
	'proy_pdts_institucion', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'7'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3214', --dep_id
	'4466', --objeto_consumidor
	'4469', --objeto_proveedor
	'proy_presupuesto', --identificador
	'', --parametros_a
	'', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'4'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3227', --dep_id
	'4466', --objeto_consumidor
	'4480', --objeto_proveedor
	'proyecto_agente_financiero', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'8'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3232', --dep_id
	'4466', --objeto_consumidor
	'4485', --objeto_proveedor
	'proyecto_alumno', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'11'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3234', --dep_id
	'4466', --objeto_consumidor
	'4487', --objeto_proveedor
	'proyecto_apoyo', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'13'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3231', --dep_id
	'4466', --objeto_consumidor
	'4484', --objeto_proveedor
	'proyecto_becario', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'10'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3212', --dep_id
	'4466', --objeto_consumidor
	'4467', --objeto_proveedor
	'proyecto_integrante', --identificador
	'', --parametros_a
	'', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'2'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3233', --dep_id
	'4466', --objeto_consumidor
	'4486', --objeto_proveedor
	'proyecto_inv_externo', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'12'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3213', --dep_id
	'4466', --objeto_consumidor
	'4468', --objeto_proveedor
	'proyecto_recusacion', --identificador
	'', --parametros_a
	'', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'3'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3230', --dep_id
	'4466', --objeto_consumidor
	'4483', --objeto_proveedor
	'proyecto_tesista', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'9'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3211', --dep_id
	'4466', --objeto_consumidor
	'2546', --objeto_proveedor
	'proyectos', --identificador
	NULL, --parametros_a
	'1', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'1'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3226', --dep_id
	'4466', --objeto_consumidor
	'4327', --objeto_proveedor
	'proyectos_pdts', --identificador
	NULL, --parametros_a
	'1', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'6'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3222', --dep_id
	'4466', --objeto_consumidor
	'4326', --objeto_proveedor
	'proyectos_pi', --identificador
	NULL, --parametros_a
	'1', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'5'  --orden
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_datos_rel_asoc
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'194', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'2546', --padre_objeto
	'proyectos', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4467', --hijo_objeto
	'proyecto_integrante', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'1'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'195', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'2546', --padre_objeto
	'proyectos', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4468', --hijo_objeto
	'proyecto_recusacion', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'2'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'196', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'2546', --padre_objeto
	'proyectos', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4469', --hijo_objeto
	'proy_presupuesto', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'3'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'197', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'2546', --padre_objeto
	'proyectos', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4326', --hijo_objeto
	'proyectos_pi', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'4'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'198', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'2546', --padre_objeto
	'proyectos', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4327', --hijo_objeto
	'proyectos_pdts', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'5'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'199', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'4327', --padre_objeto
	'proyectos_pdts', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4479', --hijo_objeto
	'proy_pdts_institucion', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'6'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'200', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'4327', --padre_objeto
	'proyectos_pdts', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4480', --hijo_objeto
	'proyecto_agente_financiero', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'7'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'201', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'4467', --padre_objeto
	'proyecto_integrante', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4483', --hijo_objeto
	'proyecto_tesista', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'8'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'202', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'4467', --padre_objeto
	'proyecto_integrante', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4484', --hijo_objeto
	'proyecto_becario', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'9'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'203', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'4467', --padre_objeto
	'proyecto_integrante', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4485', --hijo_objeto
	'proyecto_alumno', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'10'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'204', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'4467', --padre_objeto
	'proyecto_integrante', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4486', --hijo_objeto
	'proyecto_inv_externo', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'11'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'205', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'4467', --padre_objeto
	'proyecto_integrante', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4487', --hijo_objeto
	'proyecto_apoyo', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'12'  --orden
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_rel_columnas_asoc
------------------------------------------------------------
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'194', --asoc_id
	'2546', --padre_objeto
	'2467', --padre_clave
	'4467', --hijo_objeto
	'2453'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'195', --asoc_id
	'2546', --padre_objeto
	'2467', --padre_clave
	'4468', --hijo_objeto
	'2458'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'196', --asoc_id
	'2546', --padre_objeto
	'2467', --padre_clave
	'4469', --hijo_objeto
	'2461'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'197', --asoc_id
	'2546', --padre_objeto
	'2467', --padre_clave
	'4326', --hijo_objeto
	'2173'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'198', --asoc_id
	'2546', --padre_objeto
	'2467', --padre_clave
	'4327', --hijo_objeto
	'2174'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'199', --asoc_id
	'4327', --padre_objeto
	'2174', --padre_clave
	'4479', --hijo_objeto
	'2507'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'200', --asoc_id
	'4327', --padre_objeto
	'2174', --padre_clave
	'4480', --hijo_objeto
	'2516'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'201', --asoc_id
	'4467', --padre_objeto
	'2451', --padre_clave
	'4483', --hijo_objeto
	'2520'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'201', --asoc_id
	'4467', --padre_objeto
	'2452', --padre_clave
	'4483', --hijo_objeto
	'2521'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'201', --asoc_id
	'4467', --padre_objeto
	'2453', --padre_clave
	'4483', --hijo_objeto
	'2522'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'202', --asoc_id
	'4467', --padre_objeto
	'2451', --padre_clave
	'4484', --hijo_objeto
	'2526'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'202', --asoc_id
	'4467', --padre_objeto
	'2452', --padre_clave
	'4484', --hijo_objeto
	'2527'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'202', --asoc_id
	'4467', --padre_objeto
	'2453', --padre_clave
	'4484', --hijo_objeto
	'2528'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'203', --asoc_id
	'4467', --padre_objeto
	'2451', --padre_clave
	'4485', --hijo_objeto
	'2532'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'203', --asoc_id
	'4467', --padre_objeto
	'2452', --padre_clave
	'4485', --hijo_objeto
	'2533'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'203', --asoc_id
	'4467', --padre_objeto
	'2453', --padre_clave
	'4485', --hijo_objeto
	'2534'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'204', --asoc_id
	'4467', --padre_objeto
	'2451', --padre_clave
	'4486', --hijo_objeto
	'2537'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'204', --asoc_id
	'4467', --padre_objeto
	'2452', --padre_clave
	'4486', --hijo_objeto
	'2538'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'204', --asoc_id
	'4467', --padre_objeto
	'2453', --padre_clave
	'4486', --hijo_objeto
	'2539'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'205', --asoc_id
	'4467', --padre_objeto
	'2451', --padre_clave
	'4487', --hijo_objeto
	'2543'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'205', --asoc_id
	'4467', --padre_objeto
	'2452', --padre_clave
	'4487', --hijo_objeto
	'2544'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4466', --objeto
	'205', --asoc_id
	'4467', --padre_objeto
	'2453', --padre_clave
	'4487', --hijo_objeto
	'2545'  --hijo_clave
);
