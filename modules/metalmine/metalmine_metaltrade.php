<?php
function metalmine_metaltrade(){
	global $session;
	$allprefs=unserialize(get_module_pref('allprefs'));
	$metal1=$allprefs['metal1'];
	$metal2=$allprefs['metal2'];
	$metal3=$allprefs['metal3'];
	$op2 = httpget('op2');
	$op3 = httpget('op3');
	$op4 = httpget('op4');
	$marray=translate_inline(array("","`)Iron Ore`0","`QCopper`0","`&Mithril`0"));
	output("`n`c`b`&Lily's `)Office`0`c`b`n");
	$sell = httppost('sell');
	$sell= floor($sell/2)*2;
	$max = floor(floor($allprefs['metal'.$op2]/100)/2)*2;
	if ($sell < 2) $sell = 0;
	if ($sell >= $max) $sell = $max;
	if ($sell==0){
		output("`&Lily`0 looks at you bewildered.  `&'No, you have to choose to trade at least 200 grams of metal.'");	
	}elseif ($max < $sell) {
		output("`&Lily`0 looks at you bewildered.");
		output("`&'You know you don't have enough %s`& to trade!'`n`n",$marray[$op2]);
	}else{
		$allprefs['metal'.$op2]=$allprefs['metal'.$op2]-(100*$sell);
		$allprefs['metal'.$op3]=$allprefs['metal'.$op3]+(50*$sell);
		output("`&Lily`0 gives you `^%s grams`0 of %s in return for `^%s grams`0 of %s.",$sell*50,$marray[$op3],$sell*100,$marray[$op2]);
		$metal1=$allprefs['metal1'];
		$metal2=$allprefs['metal2'];
		$metal3=$allprefs['metal3'];
	}
	set_module_pref('allprefs',serialize($allprefs));
	$bottom1=floor($metal1/200);
	$bottom2=floor($metal2/200);
	$bottom3=floor($metal3/200);
	if ($metal1>=200){
		addnav("Trade Metal");
		addnav("2 `)Iron Ore`0 for 1 `QCopper","runmodule.php?module=metalmine&op=trademetal&op2=1&op3=2&op4=$bottom1");
		addnav("2 `)Iron Ore`0 for 1 `&Mithril","runmodule.php?module=metalmine&op=trademetal&op2=1&op3=3&op4=$bottom1");
		addnav("Other");
	}
	if ($metal2>=200){
		addnav("Trade Metal");
		addnav("2 `QCopper`0 for 1 `)Iron Ore","runmodule.php?module=metalmine&op=trademetal&op2=2&op3=1&op4=$bottom2");
		addnav("2 `QCopper`0 for 1 `&Mithril","runmodule.php?module=metalmine&op=trademetal&op2=2&op3=3&op4=$bottom2");
		addnav("Other");
	}
	if ($metal3>=200){
		addnav("Trade Metal");
		addnav("2 `&Mithril`0 for 1 `)Iron Ore","runmodule.php?module=metalmine&op=trademetal&op2=3&op3=1&op4=$bottom3");
		addnav("2 `&Mithril`0 for 1 `QCopper","runmodule.php?module=metalmine&op=trademetal&op2=3&op3=2&op4=$bottom3");
		addnav("Other");
	}
	if ($metal1>=1000 || $metal2>=1000 || $metal3>=1000) addnav("Sell Metal","runmodule.php?module=metalmine&op=priceguide");
	addnav("Lily's Quests","runmodule.php?module=metalmine&op=lilyquest");
}
?>