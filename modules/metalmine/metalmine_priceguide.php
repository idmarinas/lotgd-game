<?php
function metalmine_priceguide(){
	global $session;
	$allprefs=unserialize(get_module_pref('allprefs'));
	$turns=get_module_setting("ffs");
	$metal1=$allprefs['metal1'];
	$metal2=$allprefs['metal2'];
	$metal3=$allprefs['metal3'];
	//Here's how Lily calculates how much to pay for metal:
	//About the Game: You can make about 1.2 kilograms for each day you spend in the mine.
	//The pay is based on the number of forest turns you use getting to the mine.
	//1kg is worth (x * number of ffs * level/15 (if set to this) ) gold
	output("`n`c`b`&Lily's `)Office`0`c`b`n");
	if ($turns==0) $turns=1;
	$metalpay=get_module_setting("kilo")*$turns;
	if (get_module_setting("leveladj")==1) $metalpay=round($metalpay*$session['user']['level']/15);
	output("You ask `&Lily`0 what she's paying for metal today.`n`n");
	if ($metal1>=1000 || $metal2>=1000 || $metal3>=1000){
		$levelreq=get_module_setting("levelreq");
		if (($levelreq>1 && $session['user']['level']>=$levelreq) || $levelreq==1){
			$maximumsell=get_module_setting("maximumsell");
			if (($maximumsell>0 && $allprefs['metalsold']<$maximumsell) || $maximumsell==0){
				output("`&'Let's see, for you, I'm willing to pay you `^%s gold`& for each kilogram of metal. What would you like to sell?'",$metalpay);
				if ($maximumsell>0){
					$left=$maximumsell-$allprefs['metalsold'];
					output("`n`n'Remember, you can sell up to `&%s `)%s`^ per day, and",$maximumsell,translate_inline($maximumsell>1?"kilograms":"kilogram"));
					if ($allprefs['metalsold']==0) output("you haven't sold any today yet.'");
					else output("you've already sold `&%s `)%s`^ today; meaning you can only sell `&%s`^ more today.'",$allprefs['metalsold'],translate_inline($allprefs['metalsold']>1?"kilograms":"kilogram"),$left);
					if ($left*1000<$metal1) $metal1=$left*1000;
					if ($left*1000<$metal2) $metal2=$left*1000;
					if ($left*1000<$metal3) $metal3=$left*1000;
				}
				addnav("Sell Metal");
				if ($metal1>=1000) addnav("Sell `)Iron Ore","runmodule.php?module=metalmine&op=sellmetal&op2=1&op3=$metalpay");
				if ($metal2>=1000) addnav("Sell `QCopper","runmodule.php?module=metalmine&op=sellmetal&op2=2&op3=$metalpay");
				if ($metal3>=1000) addnav("Sell `&Mithril","runmodule.php?module=metalmine&op=sellmetal&op2=3&op3=$metalpay");
				addnav("Other");
			}else{
				output("`&'You've sold your maximum `^%s %s`& today already.  Come back tomorrow.'",$maximumsell,translate_inline($maximumsell>1?"kilograms":"kilogram"));
			}
		}else{
			output("`&'You need to be at least level `^%s`& to sell any metal.  Feel free to come back when you've advanced.'",$levelreq);
		}
	}else{
		output("`&'You need metal to sell.  Go to the mine and stop wasting my time,'`0 complains `&Lily`0.",$pay);
	}
	if (get_module_setting("leveladj")==1 || get_module_setting("levelreq")>1 || get_module_setting("maximumsell")>0) output("`&`n`n`b`cCalculation of Metal Reimbursement:`c`b");
	if (get_module_setting("leveladj")==1) output("`nPay for each kilogram sold is based on your current level.  The higher your level, the higher price you'll be able to negotiate for your stone.`n");
	if (get_module_setting("levelreq")>1) output("`nYou will need to be at least `^level %s`& to sell metal.`n",get_module_setting("levelreq"));
	if (get_module_setting("maximumsell")>0) Output("`nYou may sell up to `^%s`& %s of metal per day.`n",get_module_setting("maximumsell"),translate_inline(get_module_setting("maximumsell")>1?"kilograms":"kilogram"));
	addnav("Discuss Trading Metal","runmodule.php?module=metalmine&op=trade");
	addnav("Lily's Quests","runmodule.php?module=metalmine&op=lilyquest");
	addnav("Leave Lily's Office","runmodule.php?module=metalmine&op=enter");
}
?>