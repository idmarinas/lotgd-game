<?php

require_once "modules/staminasystem/lib/lib.php";

popup_header("Your Stamina statistics");

$stamina = get_module_pref("stamina");
$daystamina = 2000000;
$redpoint = get_module_pref("red");
$amberpoint = get_module_pref("amber");
$redpct = get_stamina(0);
$amberpct = get_stamina(1);
$greenpct = get_stamina(2);

$greentotal = round((($daystamina - $redpoint - $amberpoint)/$daystamina)*100);
$ambertotal = round(((($daystamina - $redpoint)/$daystamina)*100) - $greentotal);
$redtotal = (100 - $greentotal) - $ambertotal;

$greenwidth = (($greentotal / 100) * $greenpct);
$amberwidth = (($ambertotal / 100) * $amberpct);
$redwidth = (($redtotal / 100) * $redpct);

$greendarkwidth = $greentotal - $greenwidth;
$amberdarkwidth = $ambertotal - $amberwidth;
$redwdarkidth = $redtotal - $redwidth;

$totalwidth = $greenwidth + $amberwidth + $redwidth;

if ($totalwidth > 100) $greenwidth -= ($totalwidth -100);

rawoutput("<div class='staminabar show'>
			<div class='progress-staminabar progress-staminabar-red' style='width: $redwidth%;'></div>
			<div class='progress-staminabar progress-staminabar-dark-red' style='width: $redwdarkidth%;'></div>
			<div class='progress-staminabar progress-staminabar-amber' style='width: $amberwidth%;'></div>
			<div class='progress-staminabar progress-staminabar-dark-amber' style='width: $amberdarkwidth%;'></div>
			<div class='progress-staminabar progress-staminabar-green' style='width: $greenwidth%;'></div>
			<div class='progress-staminabar progress-staminabar-dark-green' style='width: $greendarkwidth%;'></div>
		</div>");

output("Total Stamina: %s / %s | Amber point: %s | Red point: %s",number_format($stamina), number_format($daystamina), number_format($amberpoint), number_format($redpoint));

output("`n`nHere is the nitty-gritty of your Stamina statistics.  The most important value is the total cost, over there on the right.  If there's anything in the Buff column, something's temporarily affecting the cost of performing that action (negative numbers are good!).  More details follow after the stats.`n`n");

$act = get_player_action_list();

$layout = array();
$row = array();
foreach($act as $key => $value)
{
	$class = ($value['class'] != '' ? $value['class'] : 'Other');
	$layout[] = $class . ',title';
	$layout[] = $class;
	$row[$class][$key] = $value; 
}

action_showform(array_unique($layout), $row);

$bufflist = unserialize(get_module_pref("buffs", "staminasystem"));

output_notl("`n`n`b".translate_inline("Action Buffs")."`b:`n");

if (is_array($bufflist) && count($bufflist) > 0 && isset($bufflist)){
	foreach ($bufflist AS $key => $vals){
		if ($vals['name']){
			if ($vals['rounds'] > 0){
				output("`0%s (%s rounds left)`n",$vals['name'],$vals['rounds']);
			} else {
				output("`0%s`n",$vals['name']);
			}
			$numbuffs++;
		}
	}
} else {
	output("None.");
}

output("`n`nRemember, using the Stamina system is easy - just keep in mind that the more you do something, the better you get at it.  So if you do a lot of the things you enjoy doing the most, the game will let you do more of those things each day.  All of the statistics you see above can help you fine-tune your character, but honestly, 99%% of the time you needn't worry about the statistics and mechanics - they're only there for when you're curious!`n`nAll Bonuses and Penalties are cleared at the start of each game day.`n`n");

popup_footer();

function action_showform($layout,$row)
{
	global $session;
 	static $showform_id=0;
 	static $title_id=0;
	if (is_array($layout) && empty($layout))
	{
		rawoutput("<table>");
		rawoutput("<tr><td>".translate_inline("No actions found")."</td></tr>");
		rawoutput("</table>");
		
		return;
	}
	
 	$showform_id++;
 	$formSections = array();
	$returnvalues = array();
	
	rawoutput("<table>");
	rawoutput("<tr><td><div id='showFormSection$showform_id'></div></td></tr>");
	rawoutput("<tr><td>");
	$i = 0;
	foreach ($layout as $key=>$val) {
		$pretrans = 0;
		if ($keypref !== false) $keyout = sprintf($keypref, $key);
		else $keyout = $key;
		if (is_array($val)) {
			$v = $val[0];
			$info = explode(",", $v);
			$val[0] = $info[0];
			$info[0] = $val;
		} else {
			$info = explode(",",$val);
		}
		if (is_array($info[0])) {
			$info[0] = $info[0];
		} else {
			$info[0] = $info[0];
		}
		if (isset($info[1])) $info[1] = trim($info[1]);
		else $info[1] = "";
		
		//Titulo
		if ($info[1]=="title")
		{
		 	$title_id++;
		 	$formSections[$title_id] = translate_inline($info[0]);
		 	rawoutput("<table id='showFormTable$title_id' cellpadding='2' cellspacing='0'>");
			$i=0;
		}
		//Items agrupados según categoría
		else
		{	
			rawoutput("<tr><td>");
			show_actions($row[$info[0]], $itemI);
			rawoutput("</td></tr><td>");
		}
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
	if ($showform_id==1){
		$startIndex = (int)httppost("showFormTabIndex");
		if ($startIndex == 0){
			$startIndex = 1;
		}
		if (isset($session['user']['prefs']['tabconfig']) &&
				$session['user']['prefs']['tabconfig'] == 0) {
		} else {
		 	rawoutput("
		 	<script language='JavaScript'>
		 	function prepare_form(id){
		 		var theTable;
		 		var theDivs='';
		 		var x=0;
		 		var weight='';
		 		for (x in formSections[id]){
		 			theTable = document.getElementById('showFormTable'+x);
		 			if (x != $startIndex ){
			 			theTable.style.visibility='hidden';
			 			theTable.style.display='none';
			 			weight='';
			 		}else{
			 			theTable.style.visibility='visible';
			 			theTable.style.display='inline-table';
			 			weight='color: yellow;';
			 		}
			 		theDivs += \"<div id='showFormButton\"+x+\"' class='trhead' style='\"+weight+\"float: left; cursor: pointer; cursor: hand; padding: 5px; border: 1px solid #000000;' onClick='showFormTabClick(\"+id+\",\"+x+\");'>\"+formSections[id][x]+\"</div>\";
		 		}
		 		theDivs += \"<div style='display: block;'>&nbsp;</div>\";
				theDivs += \"<input type='hidden' name='showFormTabIndex' value='$startIndex' id='showFormTabIndex'>\";
		 		document.getElementById('showFormSection'+id).innerHTML = theDivs;
		 	}
		 	function showFormTabClick(formid,sectionid){
		 		var theTable;
		 		var theButton;
		 		for (x in formSections[formid]){
		 			theTable = document.getElementById('showFormTable'+x);
		 			theButton = document.getElementById('showFormButton'+x);
		 			if (x == sectionid){
		 				theTable.style.visibility='visible';
		 				theTable.style.display='inline-table';
		 				theButton.style.fontWeight='normal';
		 				theButton.style.color='yellow';
						document.getElementById('showFormTabIndex').value = sectionid;
		 			}else{
		 				theTable.style.visibility='hidden';
		 				theTable.style.display='none';
		 				theButton.style.fontWeight='normal';
		 				theButton.style.color='';
		 			}
		 		}
		 	}
		 	formSections = new Array();
			</script>");
		}
	}
	if (isset($session['user']['prefs']['tabconfig']) &&
			$session['user']['prefs']['tabconfig'] == 0) {
	} else {
		rawoutput("<script language='JavaScript'>");
		rawoutput("formSections[$showform_id] = new Array();");
		foreach ($formSections as $key=>$val) {
			rawoutput("formSections[$showform_id][$key] = '".addslashes($val)."';");
		}
		rawoutput("
		prepare_form($showform_id);
		</script>");
	}
	rawoutput("</td></tr></table>");

	return $returnvalues;
}

//** Mostrar un item
function show_actions($act)
{
	$action = translate_inline("Action");
	$experience = translate_inline("Experience");
	$cost = translate_inline("Natural Cost");
	$buff = translate_inline("Buff");
	$total = translate_inline("Total");
	rawoutput("<table class='stamina'><tr><th>$action</th><th>$experience</th><th>$cost</th><th>$buff</th><th>$total</th></tr>");
	
	foreach($act AS $key => $values){
		$lvlinfo = stamina_level_up($key);
		$nextlvlexp = round($lvlinfo['nextlvlexp']);
		$nextlvlexpdisplay = number_format($nextlvlexp);
		$currentlvlexp = round($lvlinfo['currentlvlexp']);
		$currentlvlexpdisplay = number_format($currentlvlexp);
		$cost = $values['naturalcost'];
		$level = $values['lvl'];
		$exp = $values['exp'];
		$mincost = $values['mincost'];
		$costwithbuff = stamina_calculate_buffed_cost($key);
		$modifier = $costwithbuff - $cost;
		$bonus = "None";
		if ($modifier < 0) {
			$bonus = "`@".number_format($modifier)."`0";
		} elseif ($modifier > 0) {
			$bonus = "`\$".number_format($modifier)."`0";
		};
		
		//current exp - current lvl exp / current exp - nextlvlexp
		
		$expforlvl = $nextlvlexp - $currentlvlexp;
		$expoflvl = $exp - $currentlvlexp;
		
		if ($values['lvl']<100){
			$pct = ($expoflvl / $expforlvl) * 100;
		}
		
		rawoutput("<tr><td>");
		output("`^$key`0 Lv %s", $level);
		rawoutput("</td><td>");
		$exp = number_format($exp);
		
		if ($values['lvl']<100){
			rawoutput("<div class='progressbar'>
				<div class='progress-progressbar progress-progressbar-progress' style='width: $pct%;'></div>
				<div class='progress-text'>$exp / $nextlvlexpdisplay</div>
			</div>");
		} else {
			output_notl("`4`b".translate_inline("Top Level!")."`b`0");
		}
		rawoutput("</td><td>");
		$cost = number_format($cost);
		output_notl("$cost");
		rawoutput("</td><td>");
		output_notl("$bonus");
		rawoutput("</td><td>");
		$costwithbuff = number_format($costwithbuff);
		output_notl("`Q`b$costwithbuff`b`0");
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
}
?>