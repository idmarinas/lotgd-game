<?php
function orchard_megame(){
	page_header("MightyE's Weapons");
	output("`c`b`&MightyE's Weapons`0`c`b");
	$allprefs=unserialize(get_module_pref('allprefs'));
	$menumb=$allprefs['menumb'];
	$allprefs['meplay']=1;
	$allprefs['menumb']=$allprefs['menumb']+1;
	if ($menumb==0){
		output("You grab for the avocado seed before `!MightyE`0 has a chance to react...`n`n");
		output("or at least so you thought.  You feel a sharp sting as the dagger pierces your hand.");
		output("`n`n\"`#Not today. Come again tomorrow and maybe you'll get my avocado seed.`0\"");
		apply_buff('mecut',array(
			"name"=>"`!MightyE's Cut",
			"rounds"=>8,
			"wearoff"=>"`!The cut from MightyE heals.",
			"atkmod"=>.97,
			"defmod"=>.97,
			"roundmsg"=>"`!The cut on your hand prevents you from fighting at your best.",
		));
	}else{
		output("You stare at the avocado with deep concentration.");
		output("`n`nYour hand shoots out! You look down and and feel the seed in your hand!");
		switch(e_rand($menumb,5)){
			case 1:
			case 2:
			case 3:
			case 4:
				output("`n`nYou turn your hand over to gloat and suddenly realize that there's blood in your hand, not a seed.");
				output("`n`nYou marvel at `!MightyE's`0 speed.");
				output("`n`nWith a sly look, `!MightyE`0 points you to the door. \"`#See you again tomorrow?`0\"");
				apply_buff('mecut',array(
					"name"=>"`!MightyE's Cut",
					"rounds"=>8-$menumb,
					"wearoff"=>"`!The cut from MightyE heals.",
					"atkmod"=>.97,
					"defmod"=>.97,
					"roundmsg"=>"`!The cut on your hand prevents you from fighting at your best.",
				));
			break;
			case 5:
				output("`n`nYou sense that `!MightyE`0 may have grown bored with the game, but you're still proud nonetheless.");
				require_once("modules/orchard/orchard_func.php");
				orchard_findseed();
				$allprefs=unserialize(get_module_pref('allprefs'));
				$allprefs['mespiel']=0;
				$allprefs['meplay']=0;
				$allprefs['menumb']=0;
			break;
		}
	}
	addnav("Return to the Storefront","weapons.php");
	set_module_pref('allprefs',serialize($allprefs));
}
?>