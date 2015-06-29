<?php
function metalmine_drink(){
	global $session;
	$allprefs=unserialize(get_module_pref('allprefs'));
	addnav("General Store");
	$hps=$session['user']['hitpoints'];
	$max=$session['user']['maxhitpoints'];
	$diff=$max-$hps;
	output("`n`c`b`&General `)Store`0`c`b`n");
	output("You grab the drink and toss it down your gullet...");
	output("`n`nBurning...");
	output("`n`nScorching...");
	output("`n`nYou have a sudden coughing fit...");
	output("`n`nMore Burning...");
	output("`n`nA coyote pops out of nowhere and tells you to 'find your soulmate' and disappears again...");
	output("`n`nAnother round of burning...");
	output("`n`nAnd suddenly, you feel your body healing and your strength returning.");
	$allprefs['drinkstoday']=$allprefs['drinkstoday']+1;
	set_module_pref('allprefs',serialize($allprefs));
	switch(e_rand(1,3)){
		case 1:
			$diff=round($diff/2);
			$session['user']['hitpoints']+=$diff;
			output("You gain `@%s %s`0 and `@one extra turn`0.",$diff,translate_inline($diff>1?"hitpoints":"hitpoint"));
			$session['user']['turns']++;
		break;
		case 2:
			$session['user']['hitpoints']+=$diff;
			output("You gain `@%s %s`0.",$diff,translate_inline($diff>1?"hitpoints":"hitpoint"));
		break;
		case 3:
			$diff=$diff+20;
			$session['user']['hitpoints']+=$diff;
			output("You gain `@%s %s`0.",$diff,translate_inline($diff>1?"hitpoints":"hitpoint"));
			if ($session['user']['turns']>0){
				$session['user']['turns']--;
				output("Unfortunately, you `\$spend one turn`0 coughing.");
			}
		break;
	}
	if (is_module_active("drinks")) increment_module_pref("drunkeness",35,"drinks");
	output("`n`nGrober looks at you with a smile. `Q'See, don't you feel better already?'`0");
	output("`n`nYou give him a tipsy nod and wobble away.");
	addnav("Leave","runmodule.php?module=metalmine&op=enter");
}
?>