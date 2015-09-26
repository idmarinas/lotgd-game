<?php
function dragoneggs_diner15(){
	global $session;
	page_header("Hara's Bakery");
	output("`n`c`b`@Hara's `^Bakery`b`c`#`n");
	if ($session['user']['gold']>=12000){
		output("You hand over the `^12,000 dollars`# from the money you have on hand.");
		$session['user']['gold']-=12000;
		debuglog("bought an Obsidian Sword (Attack 18) for 12000 gold by researching at Hara's Bakery.");
	}else{
		$gold=$session['user']['gold'];
		$pay=12000-$session['user']['gold'];
		output("You hand over `^%s gold`# from your wallet and sign a withdrawal slip for `^%s gold`# from your bank account.",$gold,$pay);
		$session['user']['gold']-=$gold;
		$session['user']['goldinbank']-=$pay;
		debuglog("bought an Obsidian Sword (Attack 18) for $gold gold and $pay goldinbank by researching at Hara's Bakery.");
	}
	output("`n`nAfter taking a couple swings with your new sword, you feel a new power! Grrrr!!!");
	$session['user']['attack']-=$session['user']['weapondmg'];
	$session['user']['weapon']="`1Obsidian Sword`0";
	$session['user']['weaponvalue']=12000;
	$session['user']['weapondmg'] = 18;
	$session['user']['attack']+=18;
	addnav("Return to Hara's Bakery","runmodule.php?module=bakery&op=food");
	villagenav();
}
?>