<?php
function metalmine_metalsell(){
	global $session;
	$op2 = httpget('op2');
	$op3 = httpget('op3');
	$marray=translate_inline(array("","`)Iron Ore`0","`QCopper`0","`&Mithril`0"));
	$allprefs=unserialize(get_module_pref('allprefs'));
	output("`n`c`b`&Lily's `)Office`0`c`b`n");
	$sell = httppost('sell');
	$max = floor($allprefs['metal'.$op2]/1000);
	$maximumsell=get_module_setting("maximumsell");
	if ($maximumsell>0){
		$left=$maximumsell-$allprefs['metalsold'];
		if ($max>$left) $max=$left;
	}
	if ($sell < 0) $sell = 0;
	if ($sell >= $max) $sell = $max;
	if ($max < $sell) {
		output("`&Lily`0 looks at you bewildered.");
		output("`&'You know you don't have that much %s!'`n`n",$marray[$op2]);
	}else{
		$cost=$sell * $op3;
		$session['user']['gold']+=$cost;
		$inc=1000*$sell;
		$allprefs['metal'.$op2]=$allprefs['metal'.$op2]-$inc;
		$allprefs['metalsold']=$allprefs['metalsold']+$inc;
		set_module_pref('allprefs',serialize($allprefs));
		increment_module_setting("metalsold".$op2,$inc);
		output("`&Lily gives you `b`^%s gold`&`b in return for`& %s kilograms`0 of %s.",$cost,$sell,$marray[$op2]);
	}
	addnav("Lily's Office");
	addnav("Sell More Metal","runmodule.php?module=metalmine&op=priceguide");
	addnav("Discuss Trading Metal","runmodule.php?module=metalmine&op=trade");
	addnav("Lily's Quests","runmodule.php?module=metalmine&op=lilyquest");
	addnav("Leave Lily's Office","runmodule.php?module=metalmine&op=enter");
}
?>