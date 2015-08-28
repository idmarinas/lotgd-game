<?php 
## Obtener los ID de las ciudades que tienen aserradero
function get_cities_id()
{
	$sql = "SELECT 
			`module_objprefs`.`objid` AS `cityid`,
			`cityprefs`.`cityname` 
		FROM `module_objprefs`
		JOIN `cityprefs` ON `cityprefs`.`cityid` = `module_objprefs`.`objid`
		WHERE 
			`objid` IN (SELECT `cityid` FROM `cityprefs`)
			AND `modulename` = 'lumberyard'
			AND `objtype` = 'city'
			AND `setting` = 'chophere'
			AND `value` = '1'
	";
	
	return db_query($sql);
}