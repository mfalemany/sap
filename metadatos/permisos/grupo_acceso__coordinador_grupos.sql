
------------------------------------------------------------
-- apex_usuario_grupo_acc
------------------------------------------------------------
INSERT INTO apex_usuario_grupo_acc (proyecto, usuario_grupo_acc, nombre, nivel_acceso, descripcion, vencimiento, dias, hora_entrada, hora_salida, listar, permite_edicion, menu_usuario) VALUES (
	'sap', --proyecto
	'coordinador_grupos', --usuario_grupo_acc
	'Coordinador de Grupos de I+D', --nombre
	NULL, --nivel_acceso
	'Coordinador de Grupos de I+D', --descripcion
	NULL, --vencimiento
	NULL, --dias
	NULL, --hora_entrada
	NULL, --hora_salida
	NULL, --listar
	'0', --permite_edicion
	NULL  --menu_usuario
);

------------------------------------------------------------
-- apex_usuario_grupo_acc_item
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'sap', --proyecto
	'coordinador_grupos', --usuario_grupo_acc
	NULL, --item_id
	'1'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'sap', --proyecto
	'coordinador_grupos', --usuario_grupo_acc
	NULL, --item_id
	'2'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'sap', --proyecto
	'coordinador_grupos', --usuario_grupo_acc
	NULL, --item_id
	'3521'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'sap', --proyecto
	'coordinador_grupos', --usuario_grupo_acc
	NULL, --item_id
	'3522'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'sap', --proyecto
	'coordinador_grupos', --usuario_grupo_acc
	NULL, --item_id
	'3538'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'sap', --proyecto
	'coordinador_grupos', --usuario_grupo_acc
	NULL, --item_id
	'3668'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'sap', --proyecto
	'coordinador_grupos', --usuario_grupo_acc
	NULL, --item_id
	'3669'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'sap', --proyecto
	'coordinador_grupos', --usuario_grupo_acc
	NULL, --item_id
	'3670'  --item
);
--- FIN Grupo de desarrollo 0
