ALTER TABLE sap_equipo
  ADD COLUMN codigo character varying(10);
  
  
  update sap_equipo e
set codigo=(select a.descripcion || '-' || to_char(e."id",'000')  
from sap_area_conocimiento a where a.id=e.area_conocimiento_id limit 1); 

-- Actaulizacion masiva de orden_poster a partir de un excel que enviaron RRHH
update sap_comunicacion 
set orden_poster= (select orde_poster.orden from orde_poster  
where sap_comunicacion.e_mail=orde_poster.e_mail and orde_poster.e_mail not in ('juanpa_melana@hotmail.com','mercesarmiento@yahoo.com','facun.avila@gmail.com','marisadiazkelen@gmail.com'))

 -- Actualizacion de los codigos de los equipos
 update sap_equipo e
set codigo=(select a.descripcion || '-' || to_char(e."id",'000')  
from sap_area_conocimiento a where a.id=e.area_conocimiento_id limit 1)
where e.codigo is null; 
