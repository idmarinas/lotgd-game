<?php
function dragonpointviewer_getmoduleinfo(){
	$info = array(
		"name"=>"Dragonpointviewer",
		"version"=>"0.1",
		"author"=>"`2R`@o`ghe`2n `Qvon `2Fa`@lk`genbr`@uch`0",
		"category"=>"Administrative",
		"download"=>"http://www.lotgd.de/downloads/dragonpointviewer.zip"
	);
	return $info;
}

function dragonpointviewer_install(){
	module_addhook("modifyuserview");
	module_addhook("superuser");
	return true;
}

function dragonpointviewer_uninstall(){
	return true;
}

function dragonpointviewer_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "modifyuserview":
			$values=$args['user'];
			$daten=unserialize($values['dragonpoints']);
			if (is_array($daten)) {
				$counted=array_count_values($daten);
				$args['user']['dragonpoints']=$counted;

				if($session['user']['superuser'] & SU_EDIT_USERS) {
					tlschema("user");
					addnav("Operations");
					tlschema();
					addnav("Dragonpoints","runmodule.php?module=dragonpointviewer&user=" .$args['user']['acctid']);
				}
			}
			break;

	}
	return $args;
}

function dragonpointviewer_run() {
	global $session;
	page_header("Dragonpoint-Viewer");
	output('`^`bDragonpoints this player`b`0');

	require_once("lib/superusernav.php");
	superusernav();

	$user=httpget('user');
	addnav("Editor");
	addnav("to the editor","user.php?op=edit&userid=" . $user);
	addnav("refresh","runmodule.php?module=dragonpointviewer&user=" . $user);
	
	$sql="SELECT dragonpoints FROM " . db_prefix("accounts") . " WHERE acctid=" . $user;
	$result=db_query($sql);
	if (db_num_rows($result)>0) {
		$row=db_fetch_assoc($result);
		$daten=unserialize($row['dragonpoints']);
		
		rawoutput("<table border=0 cellspacing=0 cellpadding=0>");
		rawoutput("<tr class='trhead'><td>", true);
		output_notl(translate_inline("DK"));
		rawoutput("</td><td>");
		output_notl(translate_inline("Skill"));
		rawoutput("</td></tr>");
		
		$v = 0;
		foreach($daten as $key=>$value)  {
			$v++;
			rawoutput('<tr class="' . ($v%2?"trlight":"trdark").'"><td>', true);
			output_notl($key);
			rawoutput("</td><td>");
			output_notl($value);
			rawoutput("</td></tr>");
		}
		
		rawoutput("</table");
		

		//Install a summary

		$counted=array_count_values($daten);
		output("`n`n`b`^Summary`b`n");
		rawoutput("<table border=0 cellspacing=0 cellpadding=0>");
		rawoutput("<tr class='trhead'><td>", true);
		output_notl(translate_inline("Skill"));
		rawoutput("</td><td>");
		output_notl(translate_inline("Count"));
		rawoutput("</td></tr>");
		
		$v = 0;
		foreach($counted as $key=>$value)  {
			$v++;
			rawoutput('<tr class="' . ($v%2?"trlight":"trdark").'"><td>', true);
			output_notl($key);
			rawoutput("</td><td>");
			output_notl($value);
			rawoutput("</td></tr>");
		}
		
		rawoutput("</table");

	}


	page_footer();
}
?>