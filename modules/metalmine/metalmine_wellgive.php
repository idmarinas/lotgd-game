<?php
function metalmine_wellgive(){
	global $session;
	$allprefs=unserialize(get_module_pref('allprefs'));
	$mineturnset=get_module_setting("mineturnset");
	addnav("Metal Mine");
	output("`n`c`b`&Metal `)Mine`c`b`n`0");
	increment_module_pref("alignment",+1,"alignment");
	output("You decide that you will add a wish of your own to the pile.");
	$chance=e_rand(1,5);
	if ($session['user']['gold']<=0){
		output("Unfortunately, you don't have any gold.");
		if ($chance==1){
			output("Luckily the `%Under the Well Troll`0 sees you and has pity on you.  He gathers`^ 67 gold together`0 and gives it to you.");
			$session['user']['gold']+=67;
		}
	}elseif ($session['user']['gold']>=300){
		output("You toss a piece of gold in and make a wish...");
		if ($chance==1){
			output("Your gold piece ends up hitting the `%Under the Well Troll`0 in the head.  In order to avoid a fight, you pay him `^300 gold`0.");
			$session['user']['gold']-=300;
		}else{
			output("But remember, for a wish to come true you can't tell me what it is.");
			output("Since I can't tell what your wish was, I can't make it come true.  Isn't this a Catch 22?");
			output("Oh well!");
			$session['user']['gold']--;
		}
	}else{
		output("You toss in a piece of gold to make a wish");
		if ($chance==1){
			output("and you accidentally throw in your gold pouch!");
			$session['user']['gold']=0;
		}else{
			output("and the gold bounces around harmlessly.  Perhaps there is no magic here.");
			$session['user']['gold']--;
		}
	}
	$usedmts=$allprefs['usedmts'];
	$mineturns=$mineturnset-$usedmts;
	if ($mineturns>0) output("`n`nYou have `^%s Mine %s`0 left.",$mineturns,translate_inline($mineturns>1?"Turns":"Turn"));
	elseif ($session['user']['hitpoints']>0) output("`n`nYou've used up all your `^Mine Turns`0 for the day. It's probably time for you to head out.");
	if ($usedmts<$mineturnset){
		addnav("Work The Mine More","runmodule.php?module=metalmine&op=work");
		if (get_module_setting("limitloc")<=1) addnav("Travel To a Different Area","runmodule.php?module=metalmine&op=travel");
	}
	addnav("Leave Mine","runmodule.php?module=metalmine&op=leavemine");
}
?>