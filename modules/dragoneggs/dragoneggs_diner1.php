<?php
function dragoneggs_diner1(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Hara's Bakery");
	output("`n`c`b`@Hara's `^Bakery`b`c`#`n");
	$increase=5*$op2+e_rand(1,5);
	$pay=$op2*50;
	$session['user']['gold']-=$pay;
	$session['user']['hitpoints']+=$increase;
	$chance=e_rand($op2,9);
	output("You hand over your `^%s gold`# and settle down to enjoy some mighty fine pie.",$pay);
	output("After indulging in this heavenly ambrosia, you feel your `\$hitpoints have increased by %s`#.",$increase);
	if ($chance==9){
		output("In addition, your permanent hitpoints have increased by 2!");
		$session['user']['maxhitpoints']+=2;
		debuglog("gained $increase temporary hitpoints and 2 permanent hitpoints for $pay gold while researching dragon eggs at Hara's Bakery.");
	}else{
		debuglog("gained $increase temporary hitpoints for $pay gold while researching dragon eggs at Hara's Bakery.");
	}
	addnav("Return to Hara's Bakery","runmodule.php?module=bakery&op=food");
	villagenav();
}
?>