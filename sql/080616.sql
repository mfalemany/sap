--Agregamos la columna evaluador_poster
ALTER TABLE sap_comunicacion
  ADD COLUMN evaluador_poster character varying(100);
  
--Hacemos una actualizacion masiva a partir de un excel
-- Primero debemos importar el excel y llamarlo como "comunicacion_evaluadores_poster"
 update sap_comunicacion 
set evaluador_poster = (select e.evaluador from comunicacion_evaluadores_poster e where e.id=id limit 1)

-- Controlamos aquellos que no hayan sido actualziados

-- Comunucaciones Publica
http://sistema.cyt.unne.edu.ar/sap/1.0/aplicacion.php?tm=1&ai=sap||3528

-- Grupos Publica
http://sistema.cyt.unne.edu.ar/sap/1.0/aplicacion.php?tm=1&ai=sap||3529