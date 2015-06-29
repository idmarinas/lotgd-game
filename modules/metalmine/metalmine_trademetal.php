<?php
function metalmine_trademetal(){
	$allprefs=unserialize(get_module_pref('allprefs'));
	$metal1=$allprefs['metal1'];
	$metal2=$allprefs['metal2'];
	$metal3=$allprefs['metal3'];
	$op2 = httpget('op2');
	$op3 = httpget('op3');
	$op4 = httpget('op4');
	$marray=translate_inline(array("","`)Iron Ore`0","`QCopper`0","`&Mithril`0"));
	output("`n`c`b`&Lily's `)Office`0`c`b`n");
	$max=floor(floor($allprefs['metal'.$op2]/100)/2)*2;
	if ($op4==1){
		output("You trade `^200 grams`0 of %s in exchange for `^100 grams`0 of %s.",$marray[$op2],$marray[$op3]);
		$allprefs['metal'.$op2]=$allprefs['metal'.$op2]-200;
		$allprefs['metal'.$op3]=$allprefs['metal'.$op3]+100;
		set_module_pref('allprefs',serialize($allprefs));
		$metal1=$allprefs['metal1'];
		$metal2=$allprefs['metal2'];
		$metal3=$allprefs['metal3'];
	}else{
		output("`&'How many 100 gram units of %s `& (Maximum %s, Minimum 2) would you like to trade for %s`&?",$marray[$op2],$max,$marray[$op3]);
		output("By the way, if you chose an odd number of units, I will just assume you meant the lower amount.'");
		output("<form action='runmodule.php?module=metalmine&op=metaltrade&op2=$op2&op3=$op3' method='POST'><input name='sell' id='sell'><input type='submit' class='button' value='Trade'></form>",true);
		addnav("","runmodule.php?module=metalmine&op=metaltrade&op2=$op2&op3=$op3");
	}
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
	if ($metal1>=100 || $metal2>=100 || $metal3>=100) addnav("Sell Metal","runmodule.php?module=metalmine&op=priceguide");
	addnav("Lily's Quests","runmodule.php?module=metalmine&op=lilyquest");
	addnav("Leave Lily's Office","runmodule.php?module=metalmine&op=enter");
}
?>