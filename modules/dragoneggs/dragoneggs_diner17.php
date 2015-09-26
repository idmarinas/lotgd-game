<?php
function dragoneggs_diner17(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Hara's Bakery");
	output("`n`c`b`@Hara's `^Bakery`b`c`#`n");
	if ($op2==500){
		output("You hand over your money and chow down.  Everything seems pretty bland until you get to the `&4th Tier of the Cake`#.  That's when you feel a surge!");
		output("`n`nYou `\$gain a Permanent Hitpoint`#.  That was WELL worth the cost.");
		$session['user']['maxhitpoints']++;
		$session['user']['hitpoints']=$session['user']['maxhitpoints'];
		debuglog("spent $op2 gold to gain a permanent hitpoint and refresh hitpoints while researching dragon eggs at Hara's Bakery.");
	}elseif ($op2==100){
		if (e_rand(1,2)==1){
			output("You plop down 100 gold for the `&10 cupcake special`# and find you gain a `\$permanent hitpoint`# but your hitpoints are reduced to 10.");
			$session['user']['maxhitpoints']++;
			$session['user']['hitpoints']=10;
			debuglog("spent $op2 gold to gain 1 permanent hitpoint and hitpoints reduced to 10 while researching dragon eggs at Hara's Bakery.");
		}else{
			output("You plop down 100 gold for the `&10 cupcake special`# and find you hitpointshave been refreshed.");
			$session['user']['hitpoints']=$session['user']['maxhitpoints'];
			debuglog("spent $op2 gold to gain refreshed hitpoints while researching dragon eggs at Hara's Bakery.");
		}
	}else{
		output("You eat the cookie and it gets on your face. A young boy in a vest walks by the bakery and sees you.  You hear him yell `@'Ha Ha!'`# as he walks by.");
		output("`n`nYou're `\$hitpoints are refreshed +1`#, but you `&lose a Charm point`#.");
		$session['user']['hitpoints']=$session['user']['maxhitpoints']+1;
		$session['user']['charm']--;
		debuglog("spent $op2 gold to refresh hitpoints +1 but lose a charm point while researching dragon eggs at Hara's Bakery.");
	}
	$session['user']['gold']-=$op2;
	addnav("Return to Hara's Bakery","runmodule.php?module=bakery&op=food");
	villagenav();
}
?>