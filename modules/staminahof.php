<?php

function staminahof_getmoduleinfo(){
	$info=array(
		"name"=>"Stamina System - HOF",
		"version"=>"2009-08-12",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"category"=>"Stamina",
		"download"=>"",
	);
	return $info;
}

function staminahof_install(){
	module_addhook("footer-hof");
	return true;
}

function staminahof_uninstall(){
	return true;
}

function staminahof_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "footer-hof":
			addnav("Warrior Rankings");
			addnav("Action Level Rankings","runmodule.php?module=staminahof");
		break;
	}
	return $args;
}

function staminahof_run(){
	global $session;
	page_header("Action Level Rankings");
	require_once "modules/staminasystem/lib/lib.php";
	$actions = get_default_action_list();
	
	// Output navs to each action
	foreach($actions AS $action => $vals){
		addnav("Actions");
		addnav($action,"runmodule.php?module=staminahof&action=".$action."&skip=0");
	}
	
	
	// Now show the HOF
	$hof = httpget('action');
	if ($hof){
		// Get action for each user
		$hofpage = array();
		$pages = 0;
		$numres=0;
		
		$namessql = "SELECT acctid, name FROM " . db_prefix("accounts") . "";
		$namesresult = db_query($namessql);
		
		$staminasql = "SELECT setting,value,userid FROM ".db_prefix("module_userprefs")." WHERE modulename='staminasystem' AND setting='actions'";
		$staminaresult = db_query($staminasql);
		
		$scount = db_num_rows($staminaresult);
		
		for ($i=0;$i<$scount;$i++){
			$row = db_fetch_assoc($staminaresult);
			$actions_array = @unserialize($row['value']);
			$actiondetails = $actions_array[$hof];
			if (!$actiondetails['exp']) continue;
			$hofpage[$row['userid']]['exp'] = $actiondetails['exp'];
			$hofpage[$row['userid']]['lvl'] = $actiondetails['lvl'];
		}
		
		$ncount = db_num_rows($namesresult);
		
		for ($i=0;$i<$ncount;$i++){
			$row = db_fetch_assoc($namesresult);
			$numres++;
			$hofpage[$row['acctid']]['name'] = $row['name'];
			//sorting this later on will overwrite the order, so we save it here
			$hofpage[$row['acctid']]['acctid'] = $row['acctid'];
		}
		
		$pages = ceil($numres/50);
		
		for ($i=0; $i<=$pages; $i++){
			addnav("Pages");
			$page=$i*50;
			addnav(array("Page %s",$i+1),"runmodule.php?module=staminahof&action=$hof&skip=$page");
		}
		
		usort($hofpage,"staminahof_sort");
		
		$dexp = translate_inline("Experience");
		$dname = translate_inline("Name");
		$dlvl = translate_inline("Level");
		$drnk = translate_inline("Rank");
		$style=0;
		output_notl("`n`b`c`@".translate_inline($hof)."`n`n`c`b");
		rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' width='100%'>");
		rawoutput("<tr class='trhead'><td align=center>$dname</td><td align=center>$drnk</td><td align=center>$dlvl</td><td align=center>$dexp</td></tr>");

		for($i=httpget('skip');$i<=httpget('skip')+50;$i++){
			if ($hofpage[$i]){
				$style++;
				if ($hofpage[$i]['name']==$session['user']['name']){
					rawoutput("<tr class='trhilight'><td>");
				}else{
					rawoutput("<tr class='".($style%2?"trdark":"trlight")."'><td align=left>");
				}
				output_notl("%s",$hofpage[$i]['name']);
				rawoutput("</td><td align=center>");
				output_notl("%s",$i+1);
				rawoutput("</td><td align=center>");
				output_notl("%s",$hofpage[$i]['lvl']);
				rawoutput("</td><td align=center>");
				output_notl("%s",number_format($hofpage[$i]['exp']));
				rawoutput("</td></tr>");
			}
		}
		rawoutput("</table>");
	} else {
		output("`0What would you like to see?");
	}
	addnav("Exit");
	addnav("Return to the HOF","hof.php");
	page_footer();
	return true;
}

function staminahof_sort($x, $y){
	if ($x['exp'] == $y['exp']) return 0;
	else if ($x['exp'] < $y['exp']) return 1;
	else return -1;
}
?>