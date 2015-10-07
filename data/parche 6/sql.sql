/* Borrar referencia al alineamiento para las criaturas */
DELETE FROM `module_objprefs` WHERE `modulename` = 'alignment' AND `objtype` = 'creatures';
