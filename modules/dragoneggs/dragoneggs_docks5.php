<?php
function dragoneggs_docks5(){
	global $session;
	page_header("The Docks");
	output("`c`b`^The Docks`b`c`7`n");
	if (is_module_active("oceanquest")) $docks="oceanquest";
	else $docks="docks";
	$level=$session['user']['level'];
	$chance=e_rand(1,7);
	if (($level>8 && $chance<=3) || ($level<=8 && $chance<=2)){
		$previous= strpos($session['user']['armor'],"Enchanted ")!==false ? 1 : 0;
		if ($previous==0){//Never had this
			$session['user']['armor']="Enchanted ".$session['user']['armor'];
			$session['user']['armordef']++;
			$session['user']['defense']++;
			output("You pull in a strange glowing jewel.  It's entrancing! It's magical! It's crawling!");
			output("`n`nYou try to throw it on the ground but instead it attaches to your armor.");
			output("`n`nAfter a moment of panic, you realize that it makes your armor even better! How cool!! You've now got `^%s`7.",$session['user']['armor']);
			debuglog("found Enchanted Armor to add 1 to armor while researching dragon eggs at the Docks.");
		}else{
			output("You find a water-logged book.  You try to page through it and realize it probably could have answered a lot of your questions but you can't read most of the text.");
			output("`n`nLuckily, you're able to sell the book and `%gain a gem`7.");
			$session['user']['gems']++;
			debuglog("found a gem while researching dragon eggs at the Docks.");
		}
	}else{
		output("You grab for the item and it grabs you back!!! It's a nasty tentacle!!!`n`n");
		output("You're dragged off the docks and you're close to drowning to death.`n`n");
		output("With a mighty surge you fight back and find yourself safely back on the docks.`n`n");
		if ($session['user']['level']>4 && $session['user']['maxhitpoints']>$session['user']['level']*11){
			output("You `\$lose a permanent hitpoint`7.`n`n");
			$session['user']['maxhitpoints']--;
			debuglog("lost a maxhitpoint, all hitpoints except 1, and all money on hand while researching dragon eggs at the Docks.");
		}else debuglog("lost all hitpoints except 1 and all money on hand while researching dragon eggs at the Docks.");
		$session['user']['gold']=0;
		$session['user']['hitpoints']=1;
		output("You `\$lose all hitpoints except 1`7 and `^lose all your money`7.  You're lucky to escape with your life!");
	}
	addnav("Return to the Docks","runmodule.php?module=$docks&op=docks&op2=enter");
	addnav("Return to the Forest","forest.php");
}
?>