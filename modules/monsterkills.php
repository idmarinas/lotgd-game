<?php
function monsterkills_getmoduleinfo(){
	$info = array(
		"name"=>"Keep Track of Monster Kills",
		"author"=>"Sixf00t4",
		"version"=>"20070207",
		"category"=>"Forest",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1200",
		"vertxtloc">"http://www.legendofsix.com/",
		"description"=>"Keeps a running count of monsters killed in the forest",
		"settings"=>array(
			"monsterkills Settings,title",
				"reset"=>"Reset Count on DK?,bool|1",
				"list"=>"How many should be shown in HoF?,int|25",
		),
		"prefs"=>array(
		    "monsterkills user preferences,title",
			"kills"=>"Current monster kills,int|0",
		),
	);
	return $info;
}

function monsterkills_install(){
	module_addhook("battle-victory");
	module_addhook("footer-hof");
	module_addhook("dragonkill");
    return true;
}

function monsterkills_uninstall(){
        return true;
}

function monsterkills_dohook($hookname,$args){
	global $session,$badguy;

    switch($hookname){
		case "dragonkill":
			if(get_module_setting("reset")){
				set_module_pref("kills",0,"monsterkills");
			}
			break;

		case "footer-hof":
			addnav("Warrior Rankings");
			addnav("Most Monster Kills", "runmodule.php?module=monsterkills");

		break;

		case "battle-victory":
			if($args['type']=="forest"){
				increment_module_pref("kills");
			}
		break;
	}
	return $args;
}
function monsterkills_run(){
	page_header("Most Monster Kills");
	$acc = db_prefix("accounts");
	$mp = db_prefix("module_userprefs");
	$sql = "SELECT $acc.name AS name,
		$acc.acctid AS acctid,
		$mp.value AS kills,
		$mp.userid FROM $mp INNER JOIN $acc
		ON $acc.acctid = $mp.userid 
		WHERE $mp.modulename = 'monsterkills' 
		AND $mp.setting = 'kills' 
		AND $mp.value > 0 ORDER BY ($mp.value+0)	
		DESC limit ".get_module_setting("list")."";
	$result = db_query($sql);
	$rank = translate_inline("Kills");
	$name = translate_inline("Name");
	output("`n`b`c`@Most`$ Monster `@Kills`n`n`c`b");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center'>");
	rawoutput("<tr class='trhead'><td align=center>$name</td><td align=center>$rank</td></tr>");
	for ($i=0;$i < db_num_rows($result);$i++){ 
		$row = db_fetch_assoc($result);
		if ($row['name']==$session['user']['name']){
			rawoutput("<tr class='trhilight'><td>");
		}else{
			rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td align=left>");
		}
		output_notl("%s",$row['name']);
		rawoutput("</td><td align=right>");
		output_notl("%s",$row['kills']);
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
	addnav("Back to HoF", "hof.php");
	villagenav();
	page_footer();
}

?>