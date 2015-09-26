<?php
function dragoneggs_sanctum15(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Order of the Inner Sanctum");
	output("`n`c`b`&Order of the Inner Sanctum`b`c`7");
	if ($op2==3){
		if ($session['user']['gold']+$session['user']['goldinbank']<500 && $session['user']['gems']<2){
			output("`#'I can't afford that!'`7 you tell the finely dressed man.");
		}else{
			output("`#'I don't want to pay that!`7 you tell the finely dressed man.");
		}
		output("`n`n`&'Oh, I see,'`7 he says. `&'Well, then you're kicked out.'");
		output("`n`n`7He chants a couple of quiet words and you watch your tatoo fade away.");
		set_module_pref("member",0,"sanctum");
		debuglog("was dismissed form the Inner Sanctum for not paying dues while researching dragon eggs at the Order of the Inner Sanctum.");
	}else{
		if ($op2==2){
			$session['user']['gems']-=2;
			output("You give the `%2 gems`7 to the finely dressed man.");
			debuglog("paid 2 gems to keep membership in the Order by researching at the Inner Sanctum.");
		}else{
			if ($session['user']['gold']<500){
				output("Not having enough money on hand, the finely dressed man prepares a bank withdrawal slip that he just 'happened' to have in his pocket for you.");
				if ($session['user']['gold']<1){
					output("`n`nYou fill in `^500 gold`7 on the slip and hand it to the finely dressed man.");
					$session['user']['goldinbank']-=500;
					debuglog("paid 500 gold from the bank to keep membership in the Order by researching at the Inner Sanctum.");
				}else{
					$inbank=500-$session['user']['gold'];
					output("`n`nYou fill in `^%s gold`7 on the slip and hand over the rest of the money from your cash on hand to the finely dressed man.",$inbank);
					$session['user']['goldinbank']-=$inbank;
					$session['user']['gold']=0;
					debuglog("paid 500 gold from the bank and money on hand to keep membership in the Order by researching at the Inner Sanctum.");
				}
			}else{
				output("You hand over the `^500 gold`7 and the finely dressed man gives you a receipt.");
				$session['user']['gold']-=500;
				debuglog("paid 500 gold to keep membership in the Order by researching at the Inner Sanctum.");
			}
		}
		output("`n`n`&'It looks like everything is in order,'`7 he says.`n`nIt looks like you're still a member!");
		addnav("Return to the Order","runmodule.php?module=sanctum");
	}
	villagenav();
}
?>