ALTER TABLE sap_equipo
   ALTER COLUMN produccion TYPE character varying(2000);
ALTER TABLE sap_equipo
   ALTER COLUMN transferencia TYPE character varying(2000);
 
ALTER TABLE public_auditoria.logs_sap_equipo
   ALTER COLUMN produccion TYPE character varying(2000);
ALTER TABLE public_auditoria.logs_sap_equipo
   ALTER COLUMN transferencia TYPE character varying(2000);
 
