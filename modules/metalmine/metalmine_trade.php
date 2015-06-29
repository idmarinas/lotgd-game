<?php
function metalmine_trade(){
	$allprefs=unserialize(get_module_pref('allprefs'));
	output("`n`c`b`&Lily's `)Office`0`c`b`n");
	$metal1=$allprefs['metal1'];
	$metal2=$allprefs['metal2'];
	$metal3=$allprefs['metal3'];
	$bottom1=floor($metal1/200);
	$bottom2=floor($metal2/200);
	$bottom3=floor($metal3/200);
	if ($bottom1>0 || $bottom2>0|| $bottom3>0)output("`cYou can trade:`c`n");
	else output("`&'Unfortunately, you don't have enough of any metal to make a trade.'");
	if ($bottom1>0) {
		output("`c`^%s units of 100 grams`0 of `)Iron Ore`0`c",$bottom1*2);
		addnav("Trade Metal");
		addnav("2 `)Iron Ore`0 for 1 `QCopper","runmodule.php?module=metalmine&op=trademetal&op2=1&op3=2&op4=$bottom1");
		addnav("2 `)Iron Ore`0 for 1 `&Mithril","runmodule.php?module=metalmine&op=trademetal&op2=1&op3=3&op4=$bottom1");
		addnav("Other");
	}
	if ($bottom2>0) {
		output("`c`^%s units of 100 grams`0 of `QCopper`0`c",$bottom2*2);
		addnav("Trade Metal");
		addnav("2 `QCopper`0 for 1 `)Iron Ore","runmodule.php?module=metalmine&op=trademetal&op2=2&op3=1&op4=$bottom2");
		addnav("2 `QCopper`0 for 1 `&Mithril","runmodule.php?module=metalmine&op=trademetal&op2=2&op3=3&op4=$bottom2");
		addnav("Other");
	}
	if ($bottom3>0) {
		output("`c`^%s units of 100 grams`0 of `&Mithril`0`c",$bottom3*2);
		addnav("Trade Metal");
		addnav("2 `&Mithril`0 for 1 `)Iron Ore","runmodule.php?module=metalmine&op=trademetal&op2=3&op3=1&op4=$bottom3");
		addnav("2 `&Mithril`0 for 1 `QCopper","runmodule.php?module=metalmine&op=trademetal&op2=3&op3=2&op4=$bottom3");
		addnav("Other");
	}
	if ($metal1>=100 || $metal2>=100 || $metal3>=100) addnav("Sell Metal","runmodule.php?module=metalmine&op=priceguide");
	addnav("Lily's Quests","runmodule.php?module=metalmine&op=lilyquest");
	addnav("Leave Lily's Office","runmodule.php?module=metalmine&op=enter");
}
?>