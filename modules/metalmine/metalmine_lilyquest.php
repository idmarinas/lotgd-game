<?php
function metalmine_lilyquest(){
	global $session;
	$allprefs=unserialize(get_module_pref('allprefs'));
	output("`n`c`b`&Lily's `)Office`0`c`b`n");
	$lily=$allprefs['lily'];
	$items=translate_inline(array("","`4Rose Quartz Heart`0","`^Citrine Moonstone`0","`QAmber Star Gem`0","`@Four Leaf Clover`0","`!Blue Diamond`0","`5Violet's Horseshoe`0"));
	if ($lily==0){
		$allprefs['lily']=1;
		output("You ask `&Lily`0 if she needs anything from the mine.");
		output("`n`n`&'I'm glad you asked.  I am a collector of peculiar items that are rarely found in the mine.");
		output("Currently, I'm looking for a %s`&. If you find one, please come and see me.  I may have something worth trading for.'",$items[1]);
	}elseif ($allprefs['return']==1){
		output("You happily pull out %s that you found in the mine and show it to `&Lily`0.",translate_inline($lily<>6?"the":""),$items[$lily]);
		output("`n`n`&Lily`0 looks at you with excitement. `&'I can't believe you found it!'");
		output("`0`n`nShe takes %s %s from you and gives you",translate_inline($lily<>6?"the":""),$items[$lily]);
		switch(e_rand(1,24)){
			case 1: case 2: case 3: case 4: case 5: case 6: case 7: case 8: case 9: case 10:
				$gold=100*$lily;
				output("`^%s gold`0.",$gold);
				$session['user']['gold']+=$gold;
			break;
			case 11: case 12: case 13: case 14: case 15:
				$gold=125*$lily;
				output("`^%s gold`0.",$gold);
				$session['user']['gold']+=$gold;
			break;
			case 16: case 17: case 18:
				$gold=150*$lily;
				output("`^%s gold`0.",$gold);
				$session['user']['gold']+=$gold;
			break;
			case 19: case 20: case 21:
				output("a magic potion.  You drink it and `&gain a Charm Point`0!");
				$session['user']['charm']++;
			break;
			case 22: case 23:
				output("`%a gem`0.");
				$session['user']['gems']++;
			break;
			case 24:
				output("a magic potion.  You drink it and `@gain");
				if (get_module_setting("permhps")==1){
					$session['user']['maxhitpoints']++;
					output("a permanent hitpoint`0.");
				}else{
					output("`@25 hitpoints`0!");
					$session['user']['hitpoints']+=25;
				}
			break;
		}
		$allprefs['return']=0;
		$allprefs['lily']=$allprefs['lily']+1;
		if ($allprefs['lily']>=7) $allprefs['lily']=1;
		output("`n`n`&'By the way, if you find %s %s`& in the mine, come back and see me. I may have something else for you.'",translate_inline($allprefs['lily']<>6?"a":""),$items[$allprefs['lily']]);
	}else{
		output("You ask `&Lily`0 about what she wanted you to get her from the mine.");
		output("`n`n`&'Please bring me %s %s`&.  Thank you for trying to find it for me!'`0 she says.",translate_inline($lily<>6?"a":""),$items[$lily]);
	}
	set_module_pref('allprefs',serialize($allprefs));
	addnav("Lily's Office");
	addnav("Discuss Selling Metal","runmodule.php?module=metalmine&op=priceguide");
	addnav("Discuss Trading Metal","runmodule.php?module=metalmine&op=trade");
	addnav("Leave Lily's Office","runmodule.php?module=metalmine&op=enter");
}
?>