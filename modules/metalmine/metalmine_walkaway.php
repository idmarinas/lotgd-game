<?php
function metalmine_walkaway(){
	global $session;
	$op2 = httpget('op2');
	$op3 = httpget('op3');
	$mineturnset=get_module_setting("mineturnset");
	$marray=translate_inline(array("","`)Iron Ore`0","`QCopper`0","`&Mithril`0"));
	$allprefs=unserialize(get_module_pref('allprefs'));
	output("`n`c`b`&Metal `)Mine`c`b`n`0");
	$sql = "SELECT acctid,name,gold,sex FROM ".db_prefix("accounts")." WHERE acctid='$op2'";
	$res = db_query($sql);
	$row = db_fetch_assoc($res);
	$name = $row['name'];
	$id = $row['acctid'];
	$sex = $row['sex'];
	$gold= $row['gold'];
	//if alignment isn't installed, make the other player randomly evil/neutral/good
	$chance=0;
	if ($op3=="") $chance=e_rand(1,3);
	//neutral person walks away
	if ($op3=="neutral") output("With indifference to the jerk for hitting you with the pickaxe, you decide to walk away.");
	//good person (or non-alignment players) turns the other cheek
	else output("You are a good person.  You turn the other cheek and walk away like your parents taught you.");
	//bad guy is evil (or chance = 1) and picks a fight
	if (get_module_pref("alignment","alignment",$id)<get_module_setting("evilalign","alignment") || $chance==1){
		output("As you're walking away, `^%s`0 taps you on the shoulder.`n`n`#'Hey Chicken... I think you got blood on my Pickaxe and didn't apologize.'",$name);
		output("`n`n`0Despite your best efforts, it looks like you're going to have to fight.");
		addnav("Fight","runmodule.php?module=metalmine&op=player&op2=$id");
	}else{
		//bad guy is good (or chance = 2) gives some metal to the other player
		if (get_module_pref("alignment","alignment",$id)>get_module_setting("goodalign","alignment") || $chance==2){
			$metal=e_rand(1,3);
			$grams=round(1000/$mineturnset);
			output("As you walk away, `^%s`0 taps you on the shoulder.`n`n`#'I'm very sorry about that.  Can I make it up to you?'`0",$name);
			output("`n`nBefore you get a chance to say anything, `^%s`0 is handing you `^%s grams`0 of %s. `#'I hope this makes up for your injury.'",$name,$grams,$marray[$metal]);
			output("`n`n`0You take the precious metal and mention that it was `3'barely a scratch'`0 and `3'not to mention it'`0.");
				$allprefs['metal'.$metal]=$allprefs['metal'.$metal]+$grams;
				$allprefs['metalhof']=$allprefs['metalhof']+$grams;
				set_module_pref('allprefs',serialize($allprefs));
		//bad guy is neutral (or chance = 3) and walks away
		}else output("You walk away and avoid a potentially dangerous fight. That was probably a smart idea.");
		$usedmts=$allprefs['usedmts'];
		$mineturns=$mineturnset-$usedmts;
		if ($mineturns>0) output("`n`nYou have `^%s Mine %s`0 left.",$mineturns,translate_inline($mineturns>1?"Turns":"Turn"));
		elseif ($session['user']['hitpoints']>0) output("`n`nYou've used up all your `^Mine Turns`0 for the day. It's probably time for you to head out.");
		if ($usedmts<$mineturnset){
			addnav("Work The Mine More","runmodule.php?module=metalmine&op=work");
			if (get_module_setting("limitloc")<=1) addnav("Travel To a Different Area","runmodule.php?module=metalmine&op=travel");
		}
		addnav("Leave Mine","runmodule.php?module=metalmine&op=leavemine");
		//let's send a letter to the badguy to let them know they almost got into a fight
		require_once("lib/systemmail.php");
		$subj = array("`^Fight Avoided");
		$body = array("`0Level heads prevailed after an accident in the Metal Mine almost resulted in a full-out fight.  %s`0 avoided a fight with you by turning the other cheek and walking away.",$session['user']['name']);
		systemmail($id,$subj,$body);
	}
}
?>