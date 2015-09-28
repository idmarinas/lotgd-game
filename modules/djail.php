<?php
function djail_getmoduleinfo(){
	$info = array(
		"name"=>"Dragon Eggs Jail",
		"author"=>"Sixf00t4, Lonny, RPGee. DaveS Re-write",
		"version"=>"1.0",
		"category"=>"Dragon Expansion", 
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1362",
		"settings"=>array(
			"Dragon Eggs Jail Settings, title",
			"Note: This should be pretty low if jail.php is installed because it is much less useful,note",
			"deputycost"=>"How many lodgepoints does the deputy position cost?,int|100",
			"daysdep"=>"How many days may a player be deputy if no DK?,int|25",
			"Dragon Eggs Jail Settings (Valid if jail.php is NOT installed), title",
			"oneloc"=>"Does the Jail only show in one village?, bool|0",
			"jailloc"=>"Where does the Jail appear?, location|".getsetting("villagename", LOCATION_FIELDS),
			"sheriffname"=>"Name of sheriff?, text|Andy Griffith",
			"baildk"=>"Bail price per DK?, int|300",
			"baillvl"=>"Bail per level?, int|300",
			"maxbail"=>"Max bail price?, int|5000",
			"chance"=>"Chance to encounter sheriff in the forest:,range,0,100,5|75",
		),
		"prefs"	=> array(
			"Dragon Eggs Jail Preferences, title",
			"deputy"=>"Is the player a deputy?,bool|0",
			"daysdeputy"=>"How many days has the player been a deputy?,int|0",
			"wasdeputy"=>"Was the player recently a deputy?,bool|0",
			"sheriff"=>"Seen the sheriff today?,bool|0",
			"Dragon Eggs Jail Preferences (Valid if jail.php is NOT installed), title",
			"injail"=>"Is player in jail?, bool|0",
			"playerloc"=>"Where did they log off?, viewonly",
		),
		"requires"=>array(
			"dragoneggs"=>"1.0|Dragon Eggs Expansion by DaveS",
		),
	);
	return $info;
}
function djail_chance() {
	global $session;
	if (get_module_pref("sheriff","djail")==1 || $session['user']['dragonkills']<get_module_setting("mindk","dragoneggs")) $ret=0;
	else $ret= get_module_setting('chance','djail');
	return $ret;
}
function djail_install(){
	module_addhook("village");
	module_addhook("newday");
	module_addhook("dragonkill");
	module_addhook("newday-runonce");
	module_addhook("changesetting");
	module_addhook("footer-jail");
	module_addeventhook("forest","require_once(\"modules/djail.php\"); 
	return djail_chance();");
	return true;
}
function djail_uninstall(){
return true;
}
function djail_dohook($hookname, $args){
	global $session;
	require("modules/djail/dohook/$hookname.php");
	return $args;
}
function djail_runevent($type){
	global $session;
	set_module_pref("sheriff",1);
	if (is_module_active("jail")){
		$jail="jail";
		$chance=e_rand(4,6);
	}else{
		$jail="djail";
		$chance=e_rand(1,3);
	}
	if ($chance==1){
		if (get_module_pref("deputy")==0){
			output("You encounter the sheriff who recognizes you from the wanted posters! Looks like today is not your lucky day. You're going to jail.");
			$user = $session['user']['name'];
			addnews("The sheriff arrested %s in the forest!", $user);
			set_module_pref('injail',1,$jail);
			addnav("To Jail", "runmodule.php?module=$jail");
			if ($session['user']['superuser'] &~ SU_DOESNT_GIVE_GROTTO){
				addnav("Superuser");
				addnav("Newday", "newday.php");
			}
		}else{
			output("You encounter the sheriff who is about to arrest you on some trumped up charges.  You pull out your `@'Deputy'`0 badge and show it to him.  He takes out a note pad and tells you that although he's not going to arrest you, he's going to take one day off the rest of your term.");
			output("`n`nYou feel lucky to have avoided getting jailed.  Good thing you're the Deputy!");
			increment_module_pref("daysdeputy",1);
		}
	}elseif($chance==4){
		if (get_module_pref("deputy")==0){
			output("You encounter the sheriff who recognizes you from the wanted posters! Looks like today is not your lucky day. You're going to jail.`n`n");
			output("You plead your case, explaining that there must be some mistake.`n`n");
			if ($session['user']['gold']>=200){
				output("After slipping the sheriff `^200 gold`0 he lets you go on your way.");
				debuglog("lost 200 gold to the sheriff in the forest.");
				$session['user']['gold']-=200;
			}elseif ($session['user']['turns']>1){
				output("`@2 turns later`0, he lets you go on your way.");
				$session['user']['turns']-=2;
				debuglog("lost 2 turns to the sheriff in the forest.");
			}elseif ($session['user']['gems']>0){
				output("After slipping the sheriff `%a gem`0 he lets you go on your way.");
				$session['user']['gems']--;
				debuglog("lost 1 gem to the sheriff in the forest.");
			}else{
				output("A crowd starts to gather as you grovel and plead.  The sheriff lets you go, but you have `&lost 2 charm`0 from all the grovelling.");
				$session['user']['charm']-=2;
				addnews("%s was seen grovelling in front of the sheriff in the forest.",$session['user']['name']);
				debuglog("lost 2 charm to the sheriff in the forest.");
			}
		}else{
			output("You encounter the sheriff who is about to arrest you on some trumped up charges.  You pull out your `@'Deputy'`0 badge and show it to him.  He takes out a note pad and tells you that although he's not going to arrest you, he's going to take one day off the rest of your term.");
			output("`n`nYou feel lucky to have avoided getting jailed.  Good thing you're the Deputy!");
			increment_module_pref("daysdeputy",1);
		}
	}elseif(e_rand(1,25)==1 && get_module_pref("deputy")==1){
		output("You encounter the sheriff who sees that you're not doing your job.");
		output("`n`n`@'What are you doing slacking off?? With a work ethic like that, I don't need your help anymore.  You're fired!'");
		output("`n`nYou lose your job as deputy.");
		set_module_pref("deputy",0);
		require_once("lib/forest.php");
		forest(true);
	}else{
		output("You encounter the sheriff who is searching through the forest for evil-doers.  You smile as he passes you by.");
		require_once("lib/forest.php");
		forest(true);
	}
}
function dinjailnav(){
	global $session;
	if (is_module_active("jail")==0){
		$injail = get_module_pref('injail');
		if ($injail == 0) addnav ("Take your things and go","runmodule.php?module=djail");
		addnav("Twiddle your thumbs", "runmodule.php?module=djail&op=twiddle"); 
		addnav("Go to Sleep", "runmodule.php?module=djail&op=sleep"); 
		addnav("Pay Bond", "runmodule.php?module=djail&op=paybond"); 
		addnav("Ask for some soup - 1 gem", "runmodule.php?module=djail&op=soup"); 
		if ($session['user']['superuser'] &~ SU_DOESNT_GIVE_GROTTO){
			addnav("Superuser");
			addnav("Newday", "newday.php");
		}
	}
}
function djail_run(){
	global $session;
	page_header(array("The Jail of %s", $session['user']['location']));
	$op = httpget('op');
	$op2= httpget('op2');
	$op3= httpget('op3');
	$jaillocation= translate_inline("`7The Jail");
	$sheriffname=get_module_setting('sheriffname');
	$baillvl=get_module_setting('baillvl');
	$baildk=get_module_setting('baildk');
	if (is_module_active("jail")) $jail="jail";
	else $jail="djail";
	if ($session['user']['location'] == $jaillocation){
		$session['user']['location'] = get_module_pref('playerloc');
		set_module_pref('playerloc', "");
	}
	if ($op==""){
		output("`c`b`^The Jail of %s`b`c`2",$session['user']['location']);
		$injail = get_module_pref('injail');
		set_module_pref("village", $session['user']['location']);
		if($session['user']['alive'] == 0) redirect("shades.php");
		$active=0;
		if($injail == 1){
			$session['user']['restorepage'] = "runmodule.php?module=djail&op=wakeup";
			output("`2You are in your jail cell and there is nothing to do.`n`n");
			require_once("lib/commentary.php");
			addcommentary();
			viewcommentary("djail","Whine about being in jail",20,"whines");
			dinjailnav();
		}else{
			output(" `2You wonder into the jail and spot %s`2 sitting at a desk.`n`n", $sheriffname);
			$sql = "SELECT ".db_prefix("module_userprefs").".value, ".db_prefix("accounts").".name FROM " . db_prefix("module_userprefs") . "," . db_prefix("accounts") . " WHERE acctid = userid AND modulename = 'djail' AND setting = 'deputy' AND value > 0";
			$result = db_query($sql);
			$count = db_num_rows($result);
			if ($count>0){
				output("You notice the deputy list on the wall:`n`n`b`c`^Deputy List`c`b`0");
				for ($i=0;$i<db_num_rows($result);$i++){
					$row = db_fetch_assoc($result);
					output("`c`2- %s `2-`n`c",$row['name']);
				}
			}
			addnav("Examine Jail Cells", "runmodule.php?module=djail&op=talk");
			addnav("Apply to Become a Deputy","runmodule.php?module=djail&op=deputy");
			if (is_module_active("sheldon")) addnav("Read the Want Ads","runmodule.php?module=djail&op=wantads");
			villagenav();
		}
	}
	if ($op=="wantads"){
		output("`c`b`^The Jail of %s`b`c`2",$session['user']['location']);
		$sql = "SELECT ".db_prefix("accounts").".name FROM " . db_prefix("module_userprefs") . "," . db_prefix("accounts") . " WHERE acctid = userid AND modulename = 'sheldon' AND setting = 'member' AND value > 0 ORDER BY rand(".e_rand().") LIMIT 1";
		$result = db_query($sql);
		if (db_num_rows($result)==0) {
			output("There are no want ads currently posted in the jail.");
			addnav("Back to Entrance", "runmodule.php?module=$jail");
		}else{
			$row = db_fetch_assoc($result);
			$name=$row['name'];
			output("`cSuspected member of the Sheldon Gang currently wanted for Questioning:`c");
			$poster=e_rand(1,8);
			$gif="wanted".$poster.".gif";
			rawoutput("<br><center><table><tr><td align=center><img src=modules/djail/$gif></td></tr></table></center><br></font>");
			rawoutput("<big><big><big>");
			if (e_rand(1,4)==1) output("`c`b%s`b`c",$name);
			else{
				output("`c`bAny Sheldon Gang Member`b`c");
				$name="";
			}
			rawoutput("<small><small><small>");
			if ($name==$session['user']['name']){
				output("`n`n`2Ummm... It looks like they don't know what you look like yet. Maybe you better get out of here!");
			}else{
				output("`n`n`2You realize that no one is going to pay these prices for their capture, but it's fun to see the list nonetheless.");
				addnav("Look at more Want Ads","runmodule.php?module=djail&op=wantads");
				addnav("Back to Entrance", "runmodule.php?module=$jail");
			}
		}
		villagenav();
	}
	if ($op=="deputy"){
		output("`b`c`^Deputy Application`b`c`n`2");
		$sql1 = "SELECT acctid FROM ".db_prefix("accounts")."";
		$res1 = db_query($sql1);
		$count1=db_num_rows($res1);
		if ($count1<25) $deputy=1;
		elseif ($count1<100) $deputy=2;
		elseif ($count1<200) $deputy=3;
		elseif ($count1<300) $deputy=4;
		else $deputy=5;
		if (get_module_pref("deputy")==0){
			if (get_module_pref("wasdeputy")==1){
				output("The sheriff looks up at you and recognizes you right away.");
				output("`@'Well, hey there former deputy! You know you can't reapply for the job until you've given others a chance to do their civic duties.");
				output("You still have to either kill a `bGreen Dragon`b or you have to wait another `^%s days`@ until you can reapply!",25-get_module_pref("daysdeputy"));
			}else{
				output("You enquire about getting a job as deputy.  The sheriff looks up at you and considers your request.`n`n");
				output("`@'Becoming a deputy isn't easy.  You need to pay `&1 Dragon Egg Point`@ and `^%s Donation Points`@ to get the job. You keep the job for `^25 system days`@ or until you kill the `bGreen Dragon`b, whichever comes first.'`n`n",get_module_setting("deputycost"));
				$sql = "SELECT ".db_prefix("module_userprefs").".value, ".db_prefix("accounts").".name FROM " . db_prefix("module_userprefs") . "," . db_prefix("accounts") . " WHERE acctid = userid AND modulename = 'djail' AND setting = 'deputy' AND value > 0";
				$result = db_query($sql);
				$count = db_num_rows($result);
				if ($count>=$deputy){
					output("`@'Unfortunately, I'm not looking for any more deputies right now.  However, job openings keep popping up.  Check back frequently.'");
				}else{
					$pointsavailable =$session['user']['donation']-$session['user']['donationspent'];
					if (get_module_pref("dragoneggs","dragoneggpoints")>0 && $pointsavailable>get_module_setting("deputycost")){
						output("`@'Well, it looks like I could use someone with your skills.  If you're interested, I have a position available and it looks like you qualify. Are you interested?'");
						addnav("Yes","runmodule.php?module=djail&op=bdeputy");
					}else{
						output("`@'Well, to be honest, I could use some help.  Unfortunately, you can't pay the cost to get the job.'");
					}
				}
			}
		}else{
			output("`@'Well hello there deputy.  Just checking in?  Well, it looks like you still have `^%s days`@ left as my deputy. Keep up the good work!'",25-get_module_pref("daysdeputy"));
		}
		addnav("Back to Entrance", "runmodule.php?module=$jail");
	}
	if ($op=="bdeputy"){
		output("`b`c`^Deputy Hiring`b`c`n`2");
		output("The Sheriff accepts your payment and holds a brief ceremony.");
		output("`n`n`@'You are now an Official Deputy.  You will be able to hold this position for `^25 days`@ or until you kill the `bGreen Dragon`b.'");
		output("`n`n`2He takes your hand and shakes it, hands you a badge, and gives you a nod.`@ 'Welcome aboard!'");
		increment_module_pref("dragoneggs",-1,"dragoneggpoints");
		$session['user']['donationspent']+=get_module_setting("deputycost");
		set_module_pref("deputy",1);
		set_module_pref("daysdeputy",1);
		require_once("lib/titles.php");
		require_once("lib/names.php");
		$newtitle = "Deputy";
		$newname = change_player_title($newtitle);
		$session['user']['title'] = $newtitle;
		$session['user']['name'] = $newname;
		addnews("%s `@became a deputy today.  Respect %s authority!",$session['user']['name'],translate_inline($session['user']['sex']?"her":"his")); 
		addnav("Back to Entrance", "runmodule.php?module=$jail");
	}
	if ($op=="bailafriend"){
		output("`c`b`^The Jail of %s`b`c`2",$session['user']['location']);
		addnav("Back to Jail","runmodule.php?module=djail&op=talk");
		$accounts=db_prefix("accounts");
		$module_userprefs=db_prefix("module_userprefs");
		$sql = "SELECT $accounts.name AS name,$accounts.level AS level,$accounts.login AS login,$accounts.acctid AS acctid, $module_userprefs.userid FROM $module_userprefs INNER JOIN $accounts ON $accounts.acctid = $module_userprefs.userid WHERE $module_userprefs.setting = 'injail' AND $module_userprefs.value > 0 order by $accounts.level DESC";
		$result = db_query($sql) or die(db_error(LINK));
		if (db_num_rows($result)<=0) output("Sorry.  There's nobody in jail right now.");
		else{
			output("`n`n%s`7 pulls out his log book to show you who he has currently in a cell.`n`n`c", $sheriffname);
			$name=translate_inline("Name");
			$level=translate_inline("Level");
			$bail=translate_inline("Bail out");
			$write=translate_inline("Write mail");
			output("<table border='0' cellpadding='3' cellspacing='0'><tr class='trhead'><td>$name</td><td>$level</td><td>&nbsp;</td></tr>",true);
			for ($i = 0 ; $i < db_num_rows($result) ; $i++){
				$row = db_fetch_assoc($result);
				output("<tr class='".($i%2?"trlight":"trdark")."'><td>",true);
				output("".$row['name']."</a></td><td><center>`^".$row['level']."`7</center></td><td>[<a href='runmodule.php?module=djail&op=bailout&player=".rawurlencode($row['acctid'])."'>$bail</a> ]</td></tr>",true);
				addnav("","bio.php?char=".rawurlencode($row['login'])."");
				addnav("", "runmodule.php?module=djail&op=bailout&player=".rawurlencode($row['acctid'])."");
			} 
			output("</table>",true);
			output_notl("`c");
		}
	}
	if ($op=="bailout"){
		output("`c`b`^The Jail of %s`b`c`2",$session['user']['location']);
		$player	= httpget('player');
		$sql= "SELECT name,level,dragonkills FROM ".db_prefix("accounts")." WHERE acctid =".$player;
		$result	= db_query($sql) or die(db_error(LINK));
		$row= db_fetch_assoc($result);
		$playername	= $row['name'];
		$baillvl= get_module_setting('baillvl');
		$baildk	= get_module_setting('baildk');
		$bondtotal = $baillvl*$row['level'];
		if ($row['dragonkills'] > 0) $bondtotal = $bondtotal + ($row['dragonkills'] * $baildk);

		if (httpget('action') == "yes"){
			if ($session['user']['gold'] >= $bondtotal){
				require_once("lib/systemmail.php");
				output("`n`n`7You decide to help ".$row['name']."`7 get out of jail. So you hand over the `^%s gold`7 and %s yells to the back for the guard to bring up ".$row['name']."", $bondtotal, $sheriffname);
				addnav("Back to Jail","runmodule.php?module=djail&op=talk");
				set_module_pref('injail', 0, 'djail',$player);
				$session['user']['gold'] = $session['user']['gold'] - $bondtotal;
				systemmail($player,"`^lucky you!`0","".$session['user']['name']." has bailed you out of jail!",$player);
				addnews("%s has bailed %s out of jail!", $session['user']['name'], $playername);
			}else{
				addnav("Back to Jail","runmodule.php?module=djail&op=talk");
				output("`n`n`7You decide to help %s, but %s tells you that you don't have enough money. %s will have to rot in their cell until later.", $playername, $sheriffname, $playername);
			} 
		}elseif (httpget('action') == "no"){
			output("`7You decide that it's not worth it to get %s out of jail, so you thank %s and leave the office.",$playername, $sheriffname);
			addnav("Back to Jail","runmodule.php?module=djail&op=talk");
		}else{
			output("%s`7 tells you that it will take about `^%s gold`7 to get %s out of jail.`n`nWould you like to bailout %s?`n`n", $sheriffname, $bondtotal, $playername, $playername);
			addnav("Back to Jail","runmodule.php?module=djail&op=talk");
			$yes= translate_inline("Yes");
			$no	= translate_inline("No");
			output("<a href=\"runmodule.php?module=djail&op=bailout&player=$player&action=yes\">`@$yes</a> `0 or <a href=\"runmodule.php?module=djail&op=bailout&player=$player&action=no\">`\$$no</a>", true);
			addnav("", "runmodule.php?module=djail&op=bailout&player=$player&action=yes");
			addnav("", "runmodule.php?module=djail&op=bailout&player=$player&action=no");
			addnav("Yes - Bail 'Em!","runmodule.php?module=djail&op=bailout&player=$player&action=yes");
			addnav("No - Let 'Em Rot!","runmodule.php?module=djail&op=bailout&player=$player&action=no");
		}
	}
	if ($op=="paybond"){
		output("`c`b`^The Jail of %s`b`c`2",$session['user']['location']);
		$bondtotal = $baillvl*$session['user']['level'];
		$clues=ceil($session['user']['level']/3);
		if ($session['user']['dragonkills'] > 0) $bondtotal = $bondtotal + ($session['user']['dragonkills'] * $baildk);
		if ($bondtotal > get_module_setting('maxbail')) $bondtotal = get_module_setting('maxbail');
		output ("%s explains. `@'You can post bail for `^%s dollars`@. If you don't have the money on hand, we can do a direct withdrawal for some or all of it from your bank account.'`n`n", $sheriffname, $bondtotal);
		output("'However, there are other options, too.  A friend can pay to get you out; it would be the same amount.'`n`n");
		output("'If you prefer, you can pay `%%s %s`@.' `n`n'If you don't have either of those and you have `25 turns left`@, you can donate them and do some community service to get out.'`n`n",$clues,translate_inline($clues>1?"gems":"gem"));
		if ($session['user']['gold']+$session['user']['goldinbank']<=0 && $session['user']['gems']<=0 && $session['user']['turns']<=0){
			output("`2He takes a look at you and then throws up his hands. `@'You don't have any money.  You don't have any gems.  And you don't have any turns.  You're useless.  Get out of my jail!'");
			addnav("Cringe Away","runmodule.php?module=djail&op=postbail&op2=5");
		}elseif ($session['user']['gold']+$session['user']['goldinbank']<$bondtotal && $session['user']['gems']<$clues && $session['user']['turns']<5){
			output("'Finally, if you don't have enough money, gems, or turns, I'll take all that you have of each and I'll let you go.'");
			addnav("Pay with Everything","runmodule.php?module=djail&op=postbail&op2=4");
			if (get_module_pref("dragoneggs","dragoneggpoints")>0){
				addnav("Use a Dragon Egg Point","runmodule.php?module=djail&op=postbail&op2=6");
				output("`7He suddenly remembers one other thing, `@'Oh, if you have a dragon egg point you can use that to get out, too.'");
			}
		}else{
			if ($session['user']['gold']+$session['user']['goldinbank'] >= $bondtotal) addnav("Pay Money","runmodule.php?module=djail&op=postbail&op2=1&op3=$bondtotal");
			if ($session['user']['gems']>=$clues)  addnav("Pay Gems","runmodule.php?module=djail&op=postbail&op2=2&op3=$clues");
			if ($session['user']['turns']>=5) addnav("Pay Turns","runmodule.php?module=djail&op=postbail&op2=3");
			if (get_module_pref("dragoneggs","dragoneggpoints")>0){
				addnav("Use a Dragon Egg Point","runmodule.php?module=djail&op=postbail&op2=6");
				output("`7He suddenly remembers one other thing, `@'Oh, if you have a dragon egg point you can use that to get out, too.'");
			}
		}
		addnav("Forget it","runmodule.php?module=djail");
	}
	if ($op=="postbail"){
		output("`c`b`^The Jail of %s`b`c`2",$session['user']['location']);
		if ($op2==1){
			$bondtotal=$op3;
			$bondtotal-=$session['user']['gold'];
			if ($bondtotal<=0){
				$session['user']['gold']-=$op3;
				output("You pay the bail of `^%s dollars`0 with your money on hand.",$op3);
			}else{
				$session['user']['gold']=0;
				$session['user']['goldinbank']-=$bondtotal;
				output("You bring your total payment up to `^%s dollars`0 by using money from the bank.",$op3);
			}
			debuglog("gave $op3 gold to get out of jail.");
		}elseif ($op2==2){
			output("You hand your `%%s gems`0 to the sheriff.",$op3);
			$session['user']['gems']-=$op3;
			debuglog("gave $op3 gems to get out of jail.");
		}elseif ($op2==3){
			output("You spent 5 turns doing community service.");
			$session['user']['turns']-=5;
			debuglog("spent 5 turns to get out of jail.");
		}elseif ($op2==4){
			$session['user']['gold']=0;
			$session['user']['goldinbank']=0;
			$session['user']['gems']=0;
			$session['user']['turns']=0;
			debuglog("gave all gold, goldinbank, gems, and turns to get out of jail.");
			output("You give all the money on hand, in the bank, all your gems left, and all your turns left to get out of jail.`n`n");
		}elseif ($op2==6){
			increment_module_pref("dragoneggs",-1,"dragoneggpoints");
			output("You use your dragon egg point to get out.");
		}
		output("%s `2lets you out of the cell. He isn't happy about it. You're on a steady watch from now on.", $sheriffname); 
		set_module_pref('injail', 0);
		addnav("Leave", "village.php");
	}
	if ($op=="sleep"){
		output("`c`b`^The Jail of %s`b`c`2",$session['user']['location']);
		set_module_pref('playerloc', $session['user']['location']);
		if ($session['user']['loggedin']){
			$session['user']['restorepage'] = "village.php";
			$sql = "UPDATE " . db_prefix("accounts") . " SET loggedin=0, location='".translate_inline("`7The Jail")."', restorepage='{$session['user']['restorepage']}' WHERE acctid = ".$session['user']['acctid'];
			db_query($sql);
			invalidatedatacache("charlisthomepage");
			invalidatedatacache("list.php-warsonline");
		}
		$session = array();
		redirect("index.php");
	}
	if ($op=="soup"){
		output("`c`b`^The Jail of %s`b`c`2",$session['user']['location']);
		if ($session['user']['gems'] == 0){
			output("Since you do not have a gem to give to the guard, he sits the warm, hearty, full bowl of your favorite soup, just out of reach from your cell.");
			addnav("Back to your cell", "runmodule.php?module=djail");
		}else{
			output("The guard takes your gem, and hands you a warm bowl of soup. You feel much better.");
			addnav("Back to your cell", "runmodule.php?module=djail");
			$session['user']['gems'] -- ;
			$session['user']['hitpoints'] += 5;
		}
	}
	if ($op=="talk"){
		output("`c`b`^The Jail of %s`b`c`2",$session['user']['location']);
		modulehook ("sheriff-jail");
		output("The sheriff looks busy studying some new wanted posters he just got in. He looks up for a moment, smiles, and goes back to his work.`n`n");
		addnav("Bail out a friend", "runmodule.php?module=djail&op=bailafriend");
		addnav("Back to Entrance", "runmodule.php?module=djail");
		villagenav();
		require_once("lib/commentary.php");
		addcommentary();
		viewcommentary("djail", "Taunt those in jail",20,"taunts");
	}
	if ($op=="twiddle"){
		output("`c`b`^The Jail of %s`b`c`2",$session['user']['location']);
		output("You twiddle your thumbs for a while.`n`nYou are in your jail cell, there is nothing to do.");
		require_once("lib/commentary.php");
		addcommentary();
		viewcommentary("djail", "Whine about being in jail", 20, "whines"); 
		dinjailnav();
	}
	if ($op=="wakeup"){
		output("`c`b`^The Jail of %s`b`c`2",$session['user']['location']);
		$session['user']['alive'] = 1;
		$session['user']['location'] = get_module_pref('village');
		if(get_module_pref('injail') == 0){
			output("You survived the night in jail with only a few mental scars. %s pulls out his heavy set of keys and unlocks your cell door to let you out.", $sheriffname);
			addnav("Take your things and go", "village.php");
			set_module_pref('daysin', 0);
		}else{
			dinjailnav();
			output("Just as you were getting into a good sleep, the big hairy guy next to you asks if you want to talk about feelings. That new day can't come fast enough.");
		}
	}
	page_footer();
}
?>