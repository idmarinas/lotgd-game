<?php

function serverbalance_getmoduleinfo(){
$info = array(
	"name"=>"Serverbalance",
	"version"=>"1.0",
	"author"=>"`2Oliver Brendel",
	"override_forced_nav"=>true,
	"category"=>"Administrative",
	"download"=>"http://dragonprime.net/dls/serverbalance.zip",
	);
	return $info;
}

function serverbalance_install(){
	module_addhook("superuser");
	module_addhook("dk-preserve");
	return true;
}

function serverbalance_uninstall(){
	output_notl("`n`c`b`QServerbalance Module - Uninstalled`0`b`c");
	return true;
}

function serverbalance_dohook($hookname, $args){
	global $session;
	switch ($hookname) {
		case "superuser":
			if ($session['user']['superuser'] & SU_MEGAUSER) {
				addnav("Mechanics");
				addnav("Serverbalance","runmodule.php?module=serverbalance");
			}
			break;
		case "dk-preserve":
			$dk=$session['user']['dragonkills'];
			$time=$session['user']['age'];
			$timestat=get_module_objpref("Stats",$dk,"Time");			
			if ($timestat) {
				$timestat=($timestat+$time)/2; //yes, sure, this is not the arithmetic average...but it costs less time to calculate.
			} else {
				$timestat=$time;
			}
			set_module_objpref("Stats",$dk,"Time",$timestat);
			$wealth=$session['user']['gold'];
			$wealthstat=get_module_objpref("Stats",$wealth,"Wealth");			
			if ($wealthstat) {
				$wealthstat=($wealthstat+$wealth)/2; //yes, sure, this is not the arithmetic average...but it costs less time to calculate.
			} else {
				$wealthstat=$wealth;
			}
			set_module_objpref("Stats",$dk,"Wealth",$wealthstat);
			$people=get_module_objpref("All",$dk,"Players")+1;
			set_module_objpref("All",$dk,"Players",$people);
			break;	
	}
	return $args;
}

function serverbalance_run(){
	global $session;
	$op = httpget('op');
	require_once("./lib/superusernav.php");
	superusernav();
	page_header("Serverbalance");
	addnav("Refresh","runmodule.php?module=serverbalance");
	addnav("Clear Stats","runmodule.php?module=serverbalance&op=clear");
	switch ($op) {
		case "clear":
			$sql="DELETE FROM ".db_prefix("module_objprefs")." WHERE modulename='serverbalance' AND objtype='Stats';";
			$result=db_query($sql);
			if ($result) {
				output("Stats cleared.");
			} else {
				output("An error happened.");
			}
			break;
		default:
			$i=0;
			$sql="SELECT a.value as time, b.value as wealth,a.objid as dk
						FROM  ".db_prefix("module_objprefs")."  AS b 
						LEFT JOIN  ".db_prefix("module_objprefs")." AS a 
						ON a.objid = b.objid WHERE a.modulename='serverbalance' AND a.objtype='Stats' AND
						b.modulename='serverbalance' AND b.objtype='Stats' AND
						a.setting='Time' AND
						a.setting<>b.setting";
			$result = db_query($sql);
			rawoutput("<table border='0' cellpadding='2' cellspacing='0'>");
			rawoutput("<tr class='trhead'><td>". translate_inline("Dk-#")."</td><td>".translate_inline("Dragonage avg")."</td><td>".translate_inline("Gold avg")."</td><td>". translate_inline("#Players")."</td></tr>");
			while ($row=db_fetch_assoc($result)) {
				rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
				output_notl($row['dk']);
				rawoutput("</td><td>");
				output_notl($row['time']);
				rawoutput("</td><td>");
				output_notl($row['wealth']);
				rawoutput("</td><td>");
				output_notl(get_module_objpref("All",$row['dk'],"Players"));
				rawoutput("</td></tr>");
				$i++;
				}
		break;
	}
	rawoutput("</table>");
	page_footer();

}
?>