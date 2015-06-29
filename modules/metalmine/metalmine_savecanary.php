<?php
function metalmine_savecanary(){
	global $session;
	$allprefs=unserialize(get_module_pref('allprefs'));
	$canary=$allprefs['canary'];
	output("`n`c`b`&Metal `)Mine`c`b`n`0");
	output("You can't leave `^%s`0 behind! You take the chance to run back and save your faithful friend.`n`n",$canary);
	if (is_module_active("alignment")){
		output("It is an `@act of goodness`0, and your alignment improves because of it.");
		increment_module_pref("alignment",+1,"alignment");
	}
	switch(e_rand(1,4)){
		case 1:
			output("You grab `^%s`0 and make a dash for the exit.",$canary);
		break;
		case 2:
			output("You grab `^%s`0 and make a dash for the exit.  As you grab the cage, you feel a falling rock cut deeply into your hand.`n`n",$canary);
			if ($session['user']['hitpoints']>25){
				output("You lose `\$25 hitpoints`0.");
				$session['user']['hitpoints']-=25;
			}elseif ($session['user']['hitpoints']>1){
				output("You lose `\$all your hitpoints except 1`0.");
				$session['user']['hitpoints']==1;
			}else output("You're lucky the rock doesn't kill you, as your adrenaline keeps you going.");
		break;
		case 3:
			output("As you get closer to the cage, you see a section of the cave crush the cage.  You see a flash of yellow and hope that `^%s`0 was able to fly out, but you can't find your friend.",$canary);
			output("`n`nYou feel hopeful that `^%s`0 got away, but you're not sure and your doubt makes you sad. You no longer have a canary.",$canary);
			$allprefs['canary']="";
			set_module_pref('allprefs',serialize($allprefs));
			apply_buff('sadcanary',array(
				"name"=>"`^Canary Sadness",
				"rounds"=>5,
				"wearoff"=>"`^Your sadness over losing your canary passes.",
				"atkmod"=>.9,
				"roundmsg"=>"`^Your grief over loosing your canary weakens you.",
			));
		break;
		case 4:
			output("As you grab the cage and head for the exit, you notice something strange.");
			output("You look closely and notice that a `%gem`0 has fallen into `^%s's`0 cage!",$canary);
			output("`n`nYou make a quick dash to the exit.");
			$session['user']['gems']++;
		break;
	}
	addnav("Continue","runmodule.php?module=metalmine&op=emergencyleave");
}
?>