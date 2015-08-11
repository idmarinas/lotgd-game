<?php
function get_item_buff($buffid) {
	$sql = "SELECT * FROM ".db_prefix("magicitembuffs")." WHERE buffid = $buffid";
	
	$result = db_query_cached($sql, "magicitem-buff-$buffid");
	$buff2 = db_fetch_assoc($result);
	$buff = unserialize($buff2['itembuff']);
	$buff['buffname'] = $buff2['buffname'];

	// Here we'll sanitize the buff a little, so there are no values in it
	// which will actually cause output but which don't have an effect
	$newbuff = array();
	if ($buff['atkmod'] != "0" && $buff['atkmod'] != "1" && $buff['atkmod'] != "") $new_buff['atkmod'] = $buff['atkmod'];
	if ($buff['defmod'] != "0" && $buff['defmod'] != "1" && $buff['defmod'] != "") $new_buff['defmod'] = $buff['defmod'];
	if ($buff['dmgmod'] != "0" && $buff['dmgmod'] != "1" && $buff['dmgmod'] != "") $new_buff['dmgmod'] = $buff['dmgmod'];
	if ($buff['badguyatkmod'] != "0" && $buff['badguyatkmod'] != "1" && $buff['badguyatkmod'] != "") $new_buff['badguyatkmod'] = $buff['badguyatkmod'];
	if ($buff['badguydefmod'] != "0" && $buff['badguydefmod'] != "1" && $buff['badguydefmod'] != "") $new_buff['badguydefmod'] = $buff['badguydefmod'];
	if ($buff['badguydmgmod'] != "0" && $buff['badguydmgmod'] != "1" && $buff['badguydmgmod'] != "") $new_buff['badguydmgmod'] = $buff['badguydmgmod'];
	if ($buff['invulnerable'] == "1") $new_buff['invulnerable'] = 1;
	if ($buff['damageshield'] != "0" && $buff['damageshield'] != "") $new_buff['damageshield'] = $buff['damageshield'];
	if ($buff['regen'] != "0" && $buff['regen'] != "") $new_buff['regen'] = $buff['regen'];
	if ($buff['lifetap'] != "0" && $buff['lifetap'] != "") $new_buff['lifetap'] = $buff['lifetap'];
	if ($buff['minioncount'] != "0" && $buff['minioncount'] != "") {
		$new_buff['minioncount'] = $buff['minioncount'];
		$new_buff['maxbadguydamage'] = $buff['maxbadguydamage'];
		$new_buff['minbadguydamage'] = $buff['minbadguydamage'];
		$new_buff['maxgoodguydamage'] = $buff['maxgoodguydamage'];
		$new_buff['mingoodguydamage'] = $buff['mingoodguydamage'];
	}
	$new_buff['rounds'] = $buff['rounds'];
	if ($buff['startmsg'] != "") $new_buff['startmsg'] = $buff['startmsg'];
	if ($buff['roundmsg'] != "") $new_buff['roundmsg'] = $buff['roundmsg'];
	if ($buff['wearoff'] != "") $new_buff['wearoff'] = $buff['wearoff'];
	if ($buff['effectfailmsg'] != "") $new_buff['effectfailmsg'] = $buff['effectfailmsg'];
	if ($buff['effectnodmgmsg'] != "") $new_buff['effectnodmgmsg'] = $buff['effectnodmgmsg'];
	if ($buff['effectmsg'] != "") $new_buff['effectmsg'] = $buff['effectmsg'];
	$new_buff['allowinpvp'] = $buff['allowinpvp'];
	$new_buff['allowintrain'] = $buff['allowintrain'];
	$new_buff['survivenewday'] = $buff['survivenewday'];
	$new_buff['invulnerable'] = $buff['invulnerable'];
	$new_buff['expireafterfight'] = $buff['expireafterfight'];

	if ($buff['name'] == "") {
		$new_buff['name'] = "";
	} else {
		$new_buff['name'] = $buff['name']."`0";
	}

	while (list($property,$value)=each($new_buff)) {
		$new_buff[$property] = preg_replace("/\\n/", "", $value);
	}

	return $new_buff;
}
?>