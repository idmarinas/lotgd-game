<?php
function lumberyard_planttree(){
	global $session;
	
	$loc = city_id();
	$fullsize=get_module_objpref("city",$loc,"fullsize","lumberyard");
	$remainsize=get_module_objpref("city",$loc,"remainsize","lumberyard");
	$lumberturns=get_module_setting("lumberturns");
	$plantneed=get_module_objpref("city",$loc,"plantneed","lumberyard");
	$allprefs=unserialize(get_module_pref('allprefs'));
	$usedlts=$allprefs['usedlts'];
	$remaining=$plantneed-$remainsize;
	
	$module = true;
	$eventtype = '';
	if ($session['user']['turns']<2){
		output("`c`n`b`QT`qhe `QL`qumber `QY`qard`b`c`n`n");
		output("`#'You look too tired to plant trees.");
		output("Why don't you come back when you're feeling a little more energetic?'`n`n");
		addnav("`@Back to the Forest","forest.php");
		addnav("Remind Me of the Rules","runmodule.php?module=lumberyard&op=rules");
		addnav("`@T`7he `@F`7oreman's `@O`7ffice","runmodule.php?module=lumberyard&op=office");
		
		$eventtype = 'tired';
	}elseif ($usedlts>= $lumberturns) {
		output("`c`n`b`QT`qhe `QL`qumber `QY`qard`b`c`n`n");
		output("`#'Thank you for your enthusiasm, but I think you've spent enough time in the `b`QT`qhe `QL`qumber `QY`qard`b`#.'`n`n");
		output("'Please stop by to help tomorrow.'`n`n");
		addnav("`@Back to the Forest","forest.php");
		addnav("Remind Me of the Rules","runmodule.php?module=lumberyard&op=rules");
		addnav("`@T`7he `@F`7oreman's `@O`7ffice","runmodule.php?module=lumberyard&op=office");
		if ($remainsize<$plantneed) output("I think the yard should be ready once we've got `6 %s more trees`# planted.`n`n",$remaining);
		
		$eventtype = 'noturns';
	}elseif ($remainsize>=$fullsize){
		output("`c`n`b`$ Forest Too Full To Plant`b`c`n`n");
		output("`#'Thank you for your enthusiasm.");
		output("However, the forest doesn't need any new trees at this time.'`n`n");
		output("'You can plant more trees once we have a need.'");
		addnav("`@Back to the Forest","forest.php");
		addnav("Cut Trees","runmodule.php?module=lumberyard&op=work");
		addnav("Remind Me of the Rules","runmodule.php?module=lumberyard&op=rules");
		addnav("`@T`7he `@F`7oreman's `@O`7ffice","runmodule.php?module=lumberyard&op=office");
		
		$eventtype = 'forestfull';
	}else{
		if ($remainsize>=$plantneed && get_module_objpref("city",$loc,"cutdown","lumberyard")==1){
			$allprefs['ccspiel']=0;
			set_module_objpref("city",$loc,"cutdown",0, "lumberyard");
			set_module_objpref("city",$loc,"cccount",0, "lumberyard");
			output("`n`c`b`QL`qumber `QY`qard `@Open!`b`c`n");
			output("`^A smiling foreman comes to greet you.");
			output("`#'Well, `b`QT`qhe `QL`qumber `QY`qard`b `#is back in business!'`n`n");
			output("'Are you ready to go work the yard?'`n`n");
			output("There are now %s trees in the forest available to be harvested!`n`n",$remainsize);
			addnav("Lumber Yard","runmodule.php?module=lumberyard&op=enter");
			addnav("`@No, Back to the Forest","forest.php");
			$allprefs=unserialize(get_module_pref('allprefs'));
		}else{
			require_once("modules/lumberyard/lumberyard_func.php");
			lumberyard_planttrees();
			$module = false;
		}
	}
	
	if ($module)
	{
		//-- Activar hook de platar árboles
		modulehook('lumberyard-planttree', array(
			'treesplanted' => 0,
			'event' => $eventtype
		));
	}
}
?>