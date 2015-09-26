<?php
function dragoneggs_diner5(){
	global $session;
	page_header("Hara's Bakery");
	output("`n`c`b`@Hara's `^Bakery`b`c`#`n");
	$chance=e_rand(1,5);
	$level=$session['user']['level'];
	if (($level>5 && $chance<=3) || ($level<=5 && $chance<=2)){
		$session['user']['gold']+=100;
		debuglog("gained 100 gold while researching dragon eggs at Hara's Bakery.");
		output("Trying to be as sneaky as you can, you grab the money and then look very non-chalantly, pretending to study your cup of coffee.");
		output("`n`nNobody noticed! Excellent!");
		addnav("Return to Hara's Bakery","runmodule.php?module=bakery&op=food");
	}else{
		output("You grab the money just as `%Hara's`# foot comes crushing down on you.");
		output("`n`n`%'What are you doing??? That's my money you nasty thief! Get out of here!'`# she exclaims.");
		output("`n`nYou face down the stares of the people in the Diner and your `&charm falls by one`#. You find yourself kicked out of the Bakery.");
		$session['user']['charm']--;
		debuglog("lost a charm while researching dragon eggs at Hara's Bakery.");
	}
	villagenav();
}
?>