<?php

require_once("common.php");
require_once("lib/http.php");
require_once("lib/titles.php");
require_once("lib/names.php");
require_once("lib/systemmail.php");
require_once("lib/buffs.php");

function jailbreak_getmoduleinfo(){
	$info = array(
		"name"=>"Jail Break",
		"version"=>"2.0",
		"author"=>"Dager - snippets from sixf00t4 and DaveS, debugged by DaveS",
		"category"=>"Jail",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=413",
		"settings"=>array(
			"breaksallowed"=>"Attempts per day,int|1",
			"breakpercent"=>"Chance a break is successful,range,0,100|10",
			"mintake"=>"Minimum Gold to be taken from bank (cannot be 0),int|1000",
			"maxtake"=>"Maximum Gold to be taken from bank,int|10000",
			"gemstake"=>"Number of gems the Sheriff takes if player gets in a fight,int|10",
			"bounty"=>"Dag bounty per level for bank vault collapse,int|350",
			),
		"prefs"=>array(
			"attempts"=>"Attempts made today,int|0",
			),
		"requires"=>array(
			"jail" => "The Jail by Sixf00t4, Niksolo, and Lonny",
			),
		);
		return $info;
}

function jailbreak_install(){
	module_addhook("newday-runonce");
	//added by DaveS
	module_addhook("injail");
	return true;
}

function jailbreak_uninstall(){
	return true;
}

function jailbreak_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "newday-runonce":
	set_module_pref("attempts",0,"jailbreak");
	break;
	//added by DaveS
	case "injail":
		addnav("Examine the Cell","runmodule.php?module=jailbreak&op=enter");
	break;
	}
	return $args;
}

function jailbreak_run(){
	global $session;
	$attempts = get_module_pref("attempts");
	$perc = get_module_setting("breakpercent");
	$allowed = get_module_setting("breaksallowed");
	//reference the sheriff from jail.php by DaveS
	$sheriff = get_module_setting("sheriffname","jail");
	$op = httpget("op");
	
	page_header("Jail Break");
	switch($op){
	case "enter":
		if ($attempts < $allowed){
			output("`@You notice the loose tile on the floor of your cell. You cautiously and quietly move it from its place, only to discover `4a hole in the ground!`n`n");
			output("`@Curiousity gets the best of you, and you reach your hand down into the shallow hole. You feel something cold and metallic touch your fingertips. Boldly, you grasp the object and bring it slowly to the surface. It's a `4metal digging pick`@!`n`n");
			output("`@What will you do?");
			addnav("Dig a tunnel","runmodule.php?module=jailbreak&op=break");
		}else{
			output("`n`n`@You have already tried to escape today! Looks like you have to tough it out.");
		}
		addnav("Back to Jail","runmodule.php?module=jail");
	break;
	case "break":
		output("`@You start to tunnel your way out of your cell.. or so you try. You soon realize that this is really hard work, and it will require you to use `33`@ turns to complete your task.`n`n");
		if ($session['user']['turns'] >= 3) addnav("Dig it, baby!","runmodule.php?module=jailbreak&op=dig");
		else output("`4You don't have enough turns!");
		addnav("Forget it","runmodule.php?module=jail");
	break;
	case "dig":
		$session['user']['turns'] -= 3;
		set_module_pref("attempts",1,"jailbreak");
		output("`@You work quickly and efficiently on your tunnel. After `33`@ turns you can see daylight...");
		addnav("Go towards the light","runmodule.php?module=jailbreak&op=daylight");
	break;
	case "daylight":
		output("`4`bYOU'RE FREE!!!`b`n`n");
		if ($perc >= e_rand(1,100)) {
			output("Nobody even knows you escaped from jail. You better keep this on the down low, unless you want a hefty bounty.");
			$newtitle = "ExCon";
			$newname = change_player_title($newtitle);
			$session['user']['title'] = $newtitle;
			$session['user']['name'] = $newname;
			set_module_pref("injail",0,"jail");
			addnav("Village","village.php");
		} else {
			switch(e_rand(1,8)){
				case 1:
					$playername=$session['user']['name'];
					$accntid=$session['user']['acctid'];
					$mingold=1000;
					$minlevel=4;
					$sql = "SELECT acctid,goldinbank FROM ".db_prefix("accounts")." WHERE goldinbank>$mingold and level>$minlevel and acctid!=$accntid";
					$result = db_query($sql);
					for ($i=0;$i<db_num_rows($result);$i++){
						$row = db_fetch_assoc($result);
						$victim=$row['acctid'];
						$robper=((e_rand(1,30))*(0.01));  
						$takengold=round(($row['goldinbank'])*($robper));
						$sql2 = "UPDATE " . db_prefix("accounts") . "  SET goldinbank=goldinbank-$takengold WHERE acctid = $victim";
						db_query($sql2);
						$id = $victim;
						$subj = sprintf("`^Bank Vault Collapse!");
						$body = sprintf("`^%s`6 dug under the bank when trying to escape from jail. The vault collapsed and you lost `^%s `6of your gold!",$playername, $takengold);
						systemmail($id,$subj,$body);
					}
					//translation friendly by DaveS
					addnews("`%%s`5 caused the bank vault to collapse while trying to escape from jail!",$session['user']['name']);
					output("`@...or so you thought. It turns out you took a wrong turn in your tunnel. You ended up below the bank!`n`n");
					output("`@You have compromised the bank's foundation, and as a result, the bank floor collapses!`n`n");
					output("`4You are hit by a falling ton of gold coins and sharp gems for `\$10,038,378 `4damage!");
					output("`n`nYou also end up `^losing all your gold`4.");
					addnav("Daily news","news.php");
					addnav("To the shades","shades.php");
					$session['user']['alive'] = false;
					$session['user']['hitpoints'] = 0;
					$session['user']['gold'] = 0;
					if (is_module_active('alignment')) set_module_pref("alignment",get_module_pref("alignment","alignment")-3, "alignment");
					if (is_module_active('dag')){
						$bounty=(get_module_setting("bounty"))*($session['user']['level']);
						$setdate = time();
						$sql = "INSERT INTO ". db_prefix("bounty") . " (amount, target, setter, setdate) VALUES ($bounty,$accntid,0,'".date("Y-m-d H:i:s",$setdate)."')";
						db_query($sql);
						output("`n`n`5Dag`@ takes hears about your `4evil`@ deed and raises a bounty of `^%s gold`@ against you.",$bounty);
					}
				break;
				case 2:
					output("`@..or so you think. You've dug right into `3%s's`@ chambers! This can't be good. You've interrupted his reading, and he looks at you with a wild stare.",$sheriff);
					output("Enraged, %s lunges at you!",$sheriff);
					addnav("Uh oh","runmodule.php?module=jailbreak&op=fightsheriff");
				break;
				case 3:
					$gemstake=get_module_setting("gemstake");
					output(".. or so you think. It seems you've dug a tad too south. You've ended up in the shades!`n`n");
					output("`4\"`3How funny.. I never felt myself die,`4\"`3 you think to yourself.");
					$session['user']['alive'] = false;
					$session['user']['hitpoints'] = 0;
					$session['user']['gold'] = 0;
					addnav("Return to Shades","shades.php");
					addnews("%s`^ dug a tunnel and escaped from jail -- a tunnel to the Shades!",$session['user']['name']);
					set_module_pref("injail",0,"jail");
				break;
				case 4:
					output("`@You reach up towards the daylight, and pull yourself out of your stinking tunnel.");
					output("`n`n`@Only to realize, you're back where you started! Ugh, if only you didn't waste all of your energy digging the previous tunnel.. you might've been able to try again!");
					output("`n`n`1And to make it even worse, you're all dirty and smelly.`n`n`4Oh well.");
					output("`n`n`@You `&lose two charm`@.");
					$session['user']['charm']-=2;
					addnav("Back to your cell","runmodule.php?module=jail");
				break;
				case 5:
					output("`@..and you reach up, pulling yourself out of your stinky tunnel.`n`n");
					output("`@You're back in the village, hooray!`n`n");
					output("`@You feel lighter, somehow, and different than how you normally would.");
					output("`n`n`@Oh well, who cares.. you're free!!!");
					set_module_pref("injail",0,"jail");
					$session['user']['gold'] = 3;
					$newtitle = "Escapee";
					$newname = change_player_title($newtitle);
					$session['user']['title'] = $newtitle;
					$session['user']['name'] = $newname;
					addnews("%s`^ escaped from prison! Be on the lookout!",$session['user']['name']);
					apply_buff('escape',array(
						"name"=>"`#Escapee",
						"rounds"=>20,
						"wearoff"=>"`#You're no longer paranoid about escaping",
						"atkmod"=>0.7,
						"defmod"=>0.7,
						"roundmsg"=>"`%You're too paranoid about being caught to concentrate on battle",
					));
					addnav("Village","village.php");
				break;
				case 6:
					set_module_pref("injail",0,"jail");
					output("`@You reach upwards and pull yourself out of your tunnel, breathing in the fresh `bfree`b air.");
					output("`n`n`@It feels alot cooler, though. You don't recall it being so chilly in the free world. You look around you and suddenly realize.. `b`4You forgot your pants in the tunnel`b`@.`n`n");
					if ($session['user']['turns']>3){
						output("`@You had to spend another `2three`@ turns looking");
						$session['user']['turns']-=3;
					}elseif ($session['user']['turns']>0){
						output("`@You had to spend the rest of your turns looking");
						$session['user']['turns']=0;
					}else{
						$session['user']['turns']=0;
						output("`@You try to go back to look");
					}
					output("for your pants back in the tunnel.. `band you still cant find them`b`n`n");
					output("`@Not only did you lose your pants, but your wallet with all your `^gold`@ and `%gems`@, too!");
					output("`n`n`#You're so embarrassed.. good thing this isn't in the news.");
					output("`n`n`@You `&lose four charm`@.");
					$session['user']['charm']-=4;
					$session['user']['gold']=0;
					$session['user']['gems']=0;
					if (is_module_active('dag')){
						$accntid=$session['user']['acctid'];
						$bounty=(get_module_setting("bounty"))*($session['user']['level']);
						$setdate = time();
						$sql = "INSERT INTO ". db_prefix("bounty") . " (amount, target, setter, setdate) VALUES ($bounty,$accntid,0,'".date("Y-m-d H:i:s",$setdate)."')";
						db_query($sql);
						output("`n`n`5Dag`@ sees you running around without proper attire and raises a bounty of `^%s gold`@ against you.",$bounty);
					}
					//added missing user name by DaveS
					addnews("%s`^ was seen escaping from prison.. without their pants!",$session['user']['name']);;
					addnav("Freedom!","village.php");
				break;
				case 7:
					set_module_pref("injail",0,"jail");
					addnav("Plunder, baby, Plunder","runmodule.php?module=jailbreak&op=rob");
					addnav("Sneak out empty-handed","village.php");
					output("`@..or so you think. You've actually dug a hole `4into the bank's vault`@!! You find yourself surrounded by countless `%gems`@ and `^gold coins`@, ripe for the taking.`n`nLooking around, you can see that there is no guard on duty and you could easily escape with your pockets full of riches.");
				break;
				case 8:
					$session['user']['hitpoints']=1;
					$playername=$session['user']['name'];
					output("`@...or so you thought. You've actually dug into the Inn's kitchen and struck a `4gas pipe`@!!!!!`n`n`4The gas pipe explodes, setting the inn ablaze and singeing you badly.`@`n`nIt's only a matter of seconds before the `#LOGD Fire Squad`@ arrives at the scene to put out the fire. While the Inn and it's employees were all safe, it seems that every person who had rented a room that night was blown to bits. Way to go.");
					output("`n`n`#The authorities come looking for the cause of the fire and %s`#",$sheriff);
					$chance=e_rand(1,2);
					if ($chance==1){
						output("notices you standing in the crowd.  `n`n`b`\$You're  hauled back to jail!`b`0");
						addnews("`@The Inn was blown up when %s`@ tried to escape from jail!",$playername);
						addnav("Back to your cell","runmodule.php?module=jail");
					}else{
						output("doesn't recognize you! `bYou get away!`b");
						set_module_pref("injail",0,"jail");
						addnav("Village","village.php");
						addnews("`4Authorities are on the lookout for an escaped convict who blew up the Inn.  %s`4 is wanted for questioning.",$playername);
					}
					$sql = "SELECT acctid FROM ".db_prefix("accounts")." WHERE boughtroomtoday>0";
					$result = db_query($sql);
					for ($i=0;$i<db_num_rows($result);$i++){
						$row = db_fetch_assoc($result);
						$victim=$row['acctid'];
						$sql2 = "UPDATE " . db_prefix("accounts") . "  SET hitpoints=0, alive=0 WHERE acctid = $victim";
						db_query($sql2);
						$id = $victim;
						$subj = sprintf("`4Fire at the Inn!");
						$body = sprintf("`^%s`^ caused an explosion in the Inn and all the guests that were sleeping there were killed.  Unfortunately, this means you.",$playername);
						systemmail($id,$subj,$body);
					}
					if (is_module_active('dag')){
						$accntid=$session['user']['acctid'];
						$bounty=(get_module_setting("bounty"))*($session['user']['level']);
						$setdate = time();
						$sql = "INSERT INTO ". db_prefix("bounty") . " (amount, target, setter, setdate) VALUES ($bounty,$accntid,0,'".date("Y-m-d H:i:s",$setdate)."')";
						db_query($sql);
						output("`n`n`5Dag`@ takes note of your presence and raises a bounty of `^%s gold`@ against you.",$bounty);
					}
				break;
				}
			}
		break;
	}
	if ($op=="fightsheriff"){
		output("%s punishes you mercilessly.",$sheriff);
		output("`n`n`@Oh well. The Sheriff takes all your `^gold`@");
		if ($session['user']['gems']>$gemstake) {
			output(" and `%10 gems`@");
			$session['user']['gems']-=$gemstake;
		}
		if ($session['user']['gems']<$gemstake && $session['user']['gems']<0) {
			output(" and `%all of your gems`@");
			$session['user']['gems']=0;
		}
		output("and adds it to the Sheriff Retirement Fund.");
		addnews("%s tried to break out of jail, but ended up in %s's house!",$session['user']['name'],$sheriff);
		$session['user']['alive'] = false;
		$session['user']['hitpoints'] = 0;
		$session['user']['gold'] = 0;
		addnav("To the Shades","shades.php");
		if (is_module_active('alignment')) set_module_pref("alignment",get_module_pref("alignment","alignment")-5, "alignment");
	}
	if($op=="rob"){
		output("`@You make your way towards the vault walls, lined with unimaginable wealth. There's enough money here to feed `!Apro's`@ mom for two days! The only question is.. how much will you take?`n`n");
		output("`@How much `^gold`@ would you like to steal?`n");
		output("<form action='runmodule.php?module=jailbreak&op=robit' method='POST'><input name='take' id='take'><input type='submit' class='button' value='Plunder'></form>",true);
		output("<script language='javascript'>document.getElementById('input').focus();</script>",true);
		addnav("","runmodule.php?module=jailbreak&op=robit");
		addnav("Sneak out quietly","village.php");
	}
	if($op=="robit"){
		$take=abs((int)$_POST['take']);
		$mintake = get_module_setting("mintake");
		$maxtake = get_module_setting("maxtake");
		if($take <= 0) $take = 0;
		$num = e_rand($mintake,$maxtake);
		if($take >= $num){
			output("`@You fill your pockets with `^%s gold`@, and sneak out of the bank.",$take);
			output("`n`n`@But on your way out, your pockets give under the weight of all the gold.`n`n All of your gold spills across the vault floor, alerting the workers. You are immediately outnumbered, clubbed, and taken back to jail. That's what you get for being greedy!");
			set_module_pref("injail",1,"jail");
			$session['user']['gold']=0;
			addnav("To your cell.. again","runmodule.php?module=jail");
		}elseif($take < $num && $take>=$mintake/2){
			output("`@You line your pockets with `^%s gold`@, and sneak out of the bank unnoticed. This exhilarating experience makes you excited and ready to take on the day, giving you `32 forest fights`@.",$take);
			$session['user']['gold']=$session['user']['gold']+$take;
			$session['user']['turns']+=2;
			addnav("Freedom","village.php");
		}elseif($take < $mintake/2 && $take>0) {
			output("`@You line your pockets with `^%s gold`@, and sneak out of the bank unnoticed.",$take);
			output("`n`n`@But really man, you had the whole bank vault open to you and you only took `^%s gold`@?! That's ridiculous. Stop being a wussy and live a little. The whole bank was yours for the taking man, THE WHOLE BANK! Gah!",$take);
			output("`n`n`4You have not gained any turns or gold as a result of your wussy behaviour. How you like them apples, suckah?!`n`n--Administration");
			addnav("Sulk away","village.php");
		}elseif($take==0){
			output("`@Wussy. You `^lose all your gold`@. `n`nMaybe you'll take some next time, or at least pick the right option. I mean jeez, who honestly decides to steal gold `4`bwith two options to leave`b`@ and then decides to steal `^0 gold`@.`n`n Honestly, what where you thinking? Who were you trying to trick? Gosh, what a loser.`n`n`4With Love,`n--Administration");
			addnav("Sulk away","village.php");
			$session['user']['gold']=0;
		}else{
			addnav("Freedom","village.php");
		}
	}
page_footer();
}
?>