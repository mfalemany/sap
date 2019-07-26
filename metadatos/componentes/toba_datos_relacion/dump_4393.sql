------------------------------------------------------------
--[4393]--  ci_grupos - datos 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'sap', --proyecto
	'4393', --objeto
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
	'ci_grupos - datos', --nombre
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
	'2018-12-05 13:41:09', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_datos_rel
------------------------------------------------------------
INSERT INTO apex_objeto_datos_rel (proyecto, objeto, debug, clave, ap, punto_montaje, ap_clase, ap_archivo, sinc_susp_constraints, sinc_orden_automatico, sinc_lock_optimista) VALUES (
	'sap', --proyecto
	'4393', --objeto
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
	'3096', --dep_id
	'4393', --objeto_consumidor
	'4381', --objeto_proveedor
	'grupo', --identificador
	NULL, --parametros_a
	'1', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'1'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3135', --dep_id
	'4393', --objeto_consumidor
	'4412', --objeto_proveedor
	'grupo_evento', --identificador
	'', --parametros_a
	'', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'2'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3131', --dep_id
	'4393', --objeto_consumidor
	'4408', --objeto_proveedor
	'grupo_extension', --identificador
	'', --parametros_a
	'', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'4'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3134', --dep_id
	'4393', --objeto_consumidor
	'4411', --objeto_proveedor
	'grupo_form_rrhh', --identificador
	'', --parametros_a
	'', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'6'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3172', --dep_id
	'4393', --objeto_consumidor
	'4387', --objeto_proveedor
	'grupo_informe', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'11'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3097', --dep_id
	'4393', --objeto_consumidor
	'4383', --objeto_proveedor
	'grupo_integrante', --identificador
	'1', --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'3'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3100', --dep_id
	'4393', --objeto_consumidor
	'4386', --objeto_proveedor
	'grupo_linea_investigacion', --identificador
	'1', --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'7'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3098', --dep_id
	'4393', --objeto_consumidor
	'4384', --objeto_proveedor
	'grupo_proyecto', --identificador
	'1', --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'5'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3118', --dep_id
	'4393', --objeto_consumidor
	'4400', --objeto_proveedor
	'grupo_proyecto_externo', --identificador
	'', --parametros_a
	'', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'9'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3132', --dep_id
	'4393', --objeto_consumidor
	'4409', --objeto_proveedor
	'grupo_publicacion', --identificador
	'', --parametros_a
	'', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'8'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'sap', --proyecto
	'3133', --dep_id
	'4393', --objeto_consumidor
	'4410', --objeto_proveedor
	'grupo_transferencia', --identificador
	'', --parametros_a
	'', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'10'  --orden
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_datos_rel_asoc
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4393', --objeto
	'166', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'4381', --padre_objeto
	'grupo', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4383', --hijo_objeto
	'grupo_integrante', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'1'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4393', --objeto
	'168', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'4381', --padre_objeto
	'grupo', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4386', --hijo_objeto
	'grupo_linea_investigacion', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'2'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4393', --objeto
	'170', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'4381', --padre_objeto
	'grupo', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4384', --hijo_objeto
	'grupo_proyecto', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'3'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4393', --objeto
	'176', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'4381', --padre_objeto
	'grupo', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4400', --hijo_objeto
	'grupo_proyecto_externo', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'4'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4393', --objeto
	'182', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'4381', --padre_objeto
	'grupo', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4412', --hijo_objeto
	'grupo_evento', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'5'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4393', --objeto
	'183', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'4381', --padre_objeto
	'grupo', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4408', --hijo_objeto
	'grupo_extension', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'6'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4393', --objeto
	'184', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'4381', --padre_objeto
	'grupo', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4411', --hijo_objeto
	'grupo_form_rrhh', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'7'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4393', --objeto
	'185', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'4381', --padre_objeto
	'grupo', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4409', --hijo_objeto
	'grupo_publicacion', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'8'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4393', --objeto
	'186', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'4381', --padre_objeto
	'grupo', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4410', --hijo_objeto
	'grupo_transferencia', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'9'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'sap', --proyecto
	'4393', --objeto
	'191', --asoc_id
	NULL, --identificador
	'sap', --padre_proyecto
	'4381', --padre_objeto
	'grupo', --padre_id
	NULL, --padre_clave
	'sap', --hijo_proyecto
	'4387', --hijo_objeto
	'grupo_informe', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'10'  --orden
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_rel_columnas_asoc
------------------------------------------------------------
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4393', --objeto
	'166', --asoc_id
	'4381', --padre_objeto
	'2238', --padre_clave
	'4383', --hijo_objeto
	'2251'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4393', --objeto
	'168', --asoc_id
	'4381', --padre_objeto
	'2238', --padre_clave
	'4386', --hijo_objeto
	'2259'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4393', --objeto
	'170', --asoc_id
	'4381', --padre_objeto
	'2238', --padre_clave
	'4384', --hijo_objeto
	'2256'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4393', --objeto
	'176', --asoc_id
	'4381', --padre_objeto
	'2238', --padre_clave
	'4400', --hijo_objeto
	'2305'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4393', --objeto
	'182', --asoc_id
	'4381', --padre_objeto
	'2238', --padre_clave
	'4412', --hijo_objeto
	'2409'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4393', --objeto
	'183', --asoc_id
	'4381', --padre_objeto
	'2238', --padre_clave
	'4408', --hijo_objeto
	'2414'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4393', --objeto
	'184', --asoc_id
	'4381', --padre_objeto
	'2238', --padre_clave
	'4411', --hijo_objeto
	'2420'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4393', --objeto
	'185', --asoc_id
	'4381', --padre_objeto
	'2238', --padre_clave
	'4409', --hijo_objeto
	'2427'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4393', --objeto
	'186', --asoc_id
	'4381', --padre_objeto
	'2238', --padre_clave
	'4410', --hijo_objeto
	'2434'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'sap', --proyecto
	'4393', --objeto
	'191', --asoc_id
	'4381', --padre_objeto
	'2238', --padre_clave
	'4387', --hijo_objeto
	'2262'  --hijo_clave
);
