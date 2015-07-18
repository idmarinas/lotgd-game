<?php
	$script = $args['script'];

	$sql = "SELECT * FROM ".db_prefix("skills");
	$result = db_query_cached($sql, "skills-navs");
	$first = false;
	$number = db_num_rows($result);
	$cooldown = get_module_pref("cooldown");
	if (is_module_active("skills")) {
		if ($cooldown > 0) {
			$colorarray = array(1=>"`@",2=>"`2",3=>"`^",4=>"`6",5=>"`q",6=>"`Q",7=>"`$",8=>"`4",9=>"`%",10=>"`5",11=>"`5",12=>"`5",13=>"`5",14=>"`5",15=>"`5");
			if ($cooldown > 15) $cooldown = "`)15+";
			addnav(array("`&Misc Skills (Cooldown: %s%s Rds`&)`0",$colorarray[$cooldown],$cooldown),"");
			for ($i=0;$i<$number;$i++) {
				$row=db_fetch_assoc($result);
				if (eval("return ".$row['requirement'].";")) {
					addnav(array(" `)&#149; %s`0",translate_inline($row['name'])),"", true);
				}
			}
		} else {
			addnav("`&Misc Skills (Cooldown: `#Ready`&)`0","");
			for ($i=0;$i<$number;$i++) {
				$row=db_fetch_assoc($result);
				eval($row['globals']);
				if (eval("return ".$row['requirement'].";")) {
					addnav(array(" %s&#149; %s`0",$row['ccode'],translate_inline($row['name'])),$script."op=fight&skill=$spec&l={$row['skillid']}", true);
				}
			}
		}
	}
?>