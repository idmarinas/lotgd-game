<?php
function dragoneggs_heidi21(){
	global $session;
	page_header("Heidi's Place");
	output("`c`b`&Heidi, the Well-wisher`b`c`7`n");
	output("Always interested in drinking strange drinks that are sitting around bubbling away, you grab a glass and drink it down.`n`n");
	if (isset($session['bufflist']['debilitate'])) $chance=5;
	else $chance=e_rand(1,5);
	if (($session['user']['level']>8 && $chance<5) || ($session['user']['level']<9 && $chance<4)){
		output("Eww! It tastes horrible! You can't believe it.  It makes your stomach churn.");
		if ($session['user']['turns']>6){
			output("Unfortunately, it leaves you debilitated for the rest of the day.");
			output("However, you `@Gain 2 Permanent hitpoints`7.");
			apply_buff('debilitate',array(
				"name"=>translate_inline("Debilitation"),
				"rounds"=>500,
				"atkmod"=>.4,
				"defmod"=>.4,
			));
			$session['user']['turns']=0;
			$session['user']['maxhitpoints']+=2;
			debuglog("received debilitate buff, lost $turns turns, and gained 2 permanent hitpoints while researching dragon eggs at Heidi's Place.");
		}else{
			output("Luckily, the sensation passes quickly.  You find yourself a bit stronger! You have an extra `@10 hitpoints`7.");
			$session['user']['hitpoints']+=10;
			debuglog("gained 10 temporary hitpoints while researching dragon eggs at Heidi's Place.");
		}
	}else{
		if (e_rand(1,2)==1){
			output("Nothing happens.");
		}else{
			output("Eww! It tastes horrible! You can't believe it.  It makes your stomach churn.");
			output("Luckily, the sensation passes quickly.  You find yourself a bit stronger! You have an extra `@2 hitpoints`7.");
			$session['user']['hitpoints']+=2;
			debuglog("gained 2 temporary hitpoints while researching dragon eggs at Heidi's Place.");
		}
	}
	addnav("Return to Heidi's Place","runmodule.php?module=heidi");
	villagenav();
}
?>