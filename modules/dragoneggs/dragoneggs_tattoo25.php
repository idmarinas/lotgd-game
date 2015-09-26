<?php
function dragoneggs_tattoo25(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Tattoo Parlor");
	output("`c`b`&Petra, the Ink Artist`b`c`7");
	if ($session['user']['gold']<$op3){
		output("Not having enough money on hand, you make a quick run to the bank to get enough for the transaction.");
		if ($session['user']['gold']<1){
			$session['user']['goldinbank']-=$op3;
			output("You hand over the `^%s gold`7 from your bank account.",$op3);
			$debug="$op3 gold from their bank account";
		}else{
			$inbank=$op3-$session['user']['gold'];
			output("You hand over all your money on hand and `^%s gold`7 from your bank account.",$inbank);
			$session['user']['goldinbank']-=$inbank;
			$gold=$session['user']['gold'];
			$session['user']['gold']=0;
			$debug="$inbank gold from their bank account and $gold gold from onhand";
		}
	}else{
		$session['user']['gold']-=$op3;
		output("You hand over the `^%s gold`7 from your cash-on-hand.",$op3);
		$debug="$op3 gold from onhand";
	}
	output_notl("`n`n");
	output("You feel like you've made a good bargain.");
	if ($op2==1) output("You `%gain 1 gem`7.");
	else output("You `%gain %s gems`7.",$op2);
	$session['user']['gems']+=$op2;
	debuglog("bought $op2 gems for ".$debug." while researching dragon eggs at the Tattoo Parlor.");
	addnav("Return to the Tattoo Parlor","runmodule.php?module=petra");
	villagenav();
}
?>