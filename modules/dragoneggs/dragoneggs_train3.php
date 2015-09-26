<?php
function dragoneggs_train3(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Bluspring's Warrior Training");
	output("`c`b`7Bluspring's Warrior Training`b`c");
	if ($op2==1){
		output("Anticipating some easy riches, you sign for the gold.");
		$chance=e_rand(1,9);
		$level=$session['user']['level'];
		if (($level>10 && $chance<=4) || ($level<=10 && $chance<=3)){
			output("The master hands over `^550 gold`7 and you walk away with a smile on your face.");
			$session['user']['gold']+=550;
			debuglog("gained 550 gold by researching at Bluspring's Warrior Training.");
		}else{
			output("You try to collect but another master notices the transaction. `#'Hey! That's not you!'`7 she says.  Before you have a chance to leave the sheriff arrives.");
			if (is_module_active("djail")){
				if (get_module_pref("deputy","djail")==1){
					output("`n`nThe sheriff pulls you aside. `@'Listen, since you're a deputy I'm not going to haul you away.  However, I am going to revoke your deputy position.'");
					output("`n`n`7He takes your badge and sends you on your way. You feel sad, but at least you got away with the money!");
					set_module_pref("deputy",0,"djail");
					$session['user']['gold']+=550;
					debuglog("gained 550 gold but lost their position as deputy by researching at the Bluspring's Warrior Training.");
				}else{
					output("`n`nThe sheriff hauls you off to jail. Do not pass Go. Do not collect `^550 gold`7.");
					if (is_module_active("jail")) $jail="jail";
					else $jail="djail";
					addnav("Continue","runmodule.php?module=$jail");
					debuglog("was sent to jail from Bluspring's Warrior Training.");
					set_module_pref("injail",1,$jail);
					blocknav("village.php");
					blocknav("train.php");
				}
			}else{
				output("`n`nThe sheriff 'Roughs you up' a bit and leaves you with just `\$1 hitpoint`7.");
				$session['user']['hitpoints']=1;
				debuglog("lost all hitpoints but 1 by researching at Bluspring's Warrior Training.");
			}
		}
	}else{
		output("You explain that there must be a mistake and the clerk thanks you for pointing it out.");
		if (e_rand(1,2)==1){
			output("You feel proud about your honesty and `&gain a charm point`7.");
			$session['user']['charm']++;
			debuglog("gained a charm point by researching at Bluspring's Warrior Training.");
		}
	}
	addnav("Continue at the Bluspring's Warrior Training","train.php");
	villagenav();
}
?>