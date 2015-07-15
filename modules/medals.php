<?php

function medals_getmoduleinfo(){
	$info = array(
		"name"=>"medals System",
		"version"=>"2009-12-15",
		"author"=>"Dan Hall",
		"category"=>"Improbable",
		"download"=>"",
		"prefs"=>array(
			"medals"=>"Player's medals Array,viewonly|array()",
		),
	);
	return $info;
}
function medals_install(){
	module_addhook("bioinfo");
	return true;
}
function medals_uninstall(){
	return true;
}
function medals_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "bioinfo":
			medals_show_medals($args['acctid']);
			break;
		}
	return $args;
}
function medals_run(){
}
function medals_show_medals($acctid=false){
	global $session;
	if (!$acctid){
		$acctid = $session['user']['acctid'];
	}
	$info = unserialize(get_module_pref("medals","medals",$acctid));
	if (!is_array($info)){
		$info=array();
		set_module_pref("medals",serialize($info),"medals",$acctid);
	}
	$count = 0;
	$rc = 0;
	rawoutput("<table border=0 cellpadding=9 cellspacing=0><tr>");
	foreach ($info AS $key => $vals){
		$count++;
		$finalrow = false;
		output_notl("<td><img src=\"images/medals/".$vals['icon']."\" alt=\"".$vals['name']."\" title=\"".$vals['name']."\"></td>",true);
		if ($count==5){
			rawoutput("</tr><tr>");
			$count=0;
			$finalrow=true;
		}
	}
	if ($finalrow){
		rawoutput("</table>");
	} else {
		for ($i=0; $i<(5-$count); $i++){
			rawoutput("<td>&nbsp;</td>");
		}
		rawoutput("</tr></table>");
	}
	//rawoutput("<img src=\"images/medals/background-bottom.png\">");
	output_notl("`n");
}

/*

USAGE
medals_award_medal("skronkypot", "Skronky Extraordinaire", "You knocked over the Skronky Pot!", "medals_skronky.png", "You have received a medal for knocking over the Skronky Pot!  See your Bio page!");

*/

function medals_award_medal($sname, $vname, $desc, $icon, $awardtext=false, $acctid=false){
	global $session;
	if (!$acctid){
		$acctid = $session['user']['acctid'];
	}
	$ach = array("name"=>$vname,"desc"=>$desc,"icon"=>$icon);
	$info = unserialize(get_module_pref("medals","medals"));
	if (!isset($info[$sname])){
		$info[$sname]=$ach;
		if ($awardtext) output_notl("`n%s`n`n",$awardtext);
		set_module_pref("medals",serialize($info),"medals",$acctid);
	}
}

?>
