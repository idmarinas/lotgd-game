<?php
function metalmine_batup(){
	global $session;
	$allprefs=unserialize(get_module_pref('allprefs'));
	$mineturnset=get_module_setting("mineturnset");
	output("`n`c`b`&Bat `)Cave`c`b`n`0");
	if ($op=="batdown") output("Oh come on, you know you can't resist looking up!`n`n");
	output("You look up and see the ceiling is covered in bats.  Your bright helmet light agitates the bats.");
	output("`n`nYou hussle out of the cave, but a couple of bats follow you.");
	output("For some reason, they decide to follow you around for quite a while.  They may be with you until your next fight!");
	$dkb = round($session['user']['dragonkills']*.1);
	if ($session['user']['race']=="Vampire"){
		apply_buff('batbites', array(
			"startmsg"=>"`4The bats start flying around you and fight by your side.`n",
			"name"=>"`4Helpful Bats",
			"rounds"=>3,
			"wearoff"=>"The last bat finally flies away.",
			"minioncount"=>$session['user']['level'],
			"minbadguydamage"=>0,
			"maxbadguydamage"=>1+$dkb,
			"effectmsg"=>"A `\$bat`) bites for `\${damage}`) hitpoints`^.",
			"effectnodmgmsg"=>"`^The bat gets brushed away.",
			"effectfailmsg"=>"`^The bat gets brushed away.",
		));
	}else{
		apply_buff('batbites', array(
			"startmsg"=>"`4The bats start flying around you and distract you from the fight.`n",
			"name"=>"`4Hindering Bats",
			"rounds"=>3,
			"wearoff"=>"The last bat finally flies away.",
			"minioncount"=>$session['user']['level']+$dkb,
			"mingoodguydamage"=>0,
			"maxgoodguydamage"=>1+$dkb,
			"effectmsg"=>"A `\$bat`) bites you for `\${damage}`) hitpoints`^.",
			"effectnodmgmsg"=>"`^The bat gets brushed away.",
			"effectfailmsg"=>"`^The bat bites you but doesn't hurt you!",
		));
	}
	$usedmts=$allprefs['usedmts'];
	$mineturns=$mineturnset-$usedmts;
	if ($mineturns>0) output("`n`nYou have `^%s Mine %s`0 left.",$mineturns,translate_inline($mineturns>1?"Turns":"Turn"));
	elseif ($session['user']['hitpoints']>0) output("`n`nYou've used up all your `^Mine Turns`0 for the day. It's probably time for you to head out.");
	addnav("Metal Mine");
	if ($usedmts<$mineturnset){
		addnav("Work The Mine More","runmodule.php?module=metalmine&op=work");
		if (get_module_setting("limitloc")<=1) addnav("Travel To a Different Area","runmodule.php?module=metalmine&op=travel");
	}
	addnav("Leave Mine","runmodule.php?module=metalmine&op=leavemine");
	set_module_pref('allprefs',serialize($allprefs));
}
?>