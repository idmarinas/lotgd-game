<?php
	$l = httpget('l'); //type of attack
	$sql = "SELECT * FROM ".db_prefix("skills")." WHERE skillid = $l";
	$result = db_query_cached($sql, "skills-skill-$l");
	$skill = db_fetch_assoc($result);
	$ccode = $skill['ccode'];
	set_module_pref("cooldown",$skill['cooldown']);
	eval($skill['execvalue']);				
	$buffs = unserialize($skill['buffids']);
	require_once("modules/skills/skills_func.php");
	foreach ($buffs as $buffid => $Xactive){
		$buff = get_skills_buff($buffid,$ccode);
		apply_buff("skills-$buffid",$buff);
	}
?>