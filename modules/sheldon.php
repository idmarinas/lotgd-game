<?php
function sheldon_getmoduleinfo(){
	$info = array(
		"name"=>"The Sheldon Gang",
		"author"=>"DaveS",
		"version"=>"1.0",
		"category"=>"Dragon Expansion",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1362",
		"settings"=>array(
			"Sheldon Gang,title",
			"sheldonloc"=>"Where is the Sheldon Gang located?,location|".getsetting("villagename", LOCATION_FIELDS),
			"newestmember"=>"Name of the last player to join the gang:,text|",
			"chance"=>"Chance to encounter sheriff in the forest:,range,0,100,5|30",
		),
		"prefs"=>array(
			"Sheldon Gang,title",
			"member"=>"What number member was this player to join?,int|0",
			"raid"=>"Has the player one on a raid today?,bool|0",
			"sheriff"=>"Seen the sheriff today?,bool|0",
		),
		"requires"=>array(
			"dragoneggs"=>"1.0|Dragon Eggs Expansion by DaveS",
		),
	);
	return $info;
}
function sheldon_chance() {
	global $session;
	if (get_module_pref("sheriff","sheldon")==1 || $session['user']['dragonkills']<get_module_setting("mindk","dragoneggs")) $ret=0;
	else $ret= get_module_setting('chance','sheldon');
	return $ret;
}
function sheldon_install(){
	module_addhook("changesetting");
	module_addhook("newday-runonce");
	module_addhook("newday");
	module_addhook("village");
	module_addhook("bioinfo");
	module_addhook("dragonkill");
	module_addeventhook("forest","require_once(\"modules/sheldon.php\"); 
	return sheldon_chance();");
	return true;
}
function sheldon_uninstall(){
	return true;
}
function sheldon_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "changesetting":
			if ($args['setting'] == "villagename") {
				if ($args['old'] == get_module_setting("sheldonloc")) set_module_setting("sheldonloc", $args['new']);
			}
		break;
		case "newday-runonce":
			$sql = "update ".db_prefix("module_userprefs")." set value=0 where value<>0 and setting='raid' and modulename='sheldon'";
			db_query($sql);
		break;
		case "newday":
			if (get_module_pref("member")==-1) set_module_pref("member",0);
			set_module_pref("sheriff",0);
		break;
		case "village":
			if ($session['user']['location'] == get_module_setting("sheldonloc") && get_module_pref("member")<>0){
				tlschema($args['schemas']['tavernnav']);
				addnav($args['tavernnav']);
				tlschema();
				addnav("Sheldon Gang Hideout","runmodule.php?module=sheldon");
			}
		break;
		case "bioinfo":
			if (get_module_pref('member','sheldon',$args['acctid'])>0){
				output("`n`&%s `# has a scar on %s shoulder in the shape of an `)Assassin's Dagger`#.`n", $args['name'],translate_inline(($args['sex']?"her":"his")));
			}
		break;
		case "dragonkill":
			set_module_pref("member",0);
		break;
	}
	return $args;
}
function sheldon_runevent($type){
	global $session;
	$session['user']['specialinc']="module:sheldon";
	$op = httpget('op');
	$op2 = httpget('op2');
	set_module_pref("sheriff",1);
	if (is_module_active("jail")) $jail="jail";
	elseif(is_module_active("djail")) $jail="djail";
	else $jail="none";
	if ($op==""){
		if (get_module_pref("member")>0) $case=e_rand(1,5);
		else $case=6;
		switch($case){
		//switch(6){
			case 1: case 2:
				if (($jail=="jail" && e_rand(1,8)==1) || ($jail=="djail" && e_rand(1,3)==1)){
					output("You see the sheriff and the sheriff recognizes you.");
					output("`@'You're a `QSheldon Gang Member`@! Hey! You're under arrest!'`0`n`n");
					output("Instead of taking this lightly, you deside to take off in a sprint.`n`n");
					if ($session['user']['turns']>2){
						output("You can probably get away, but you realize you'll have to run for `@3 turns`0 to be successful.  Otherwise, you can stand and fight or go peacefully.");
						addnav("Run for 3 Turns","forest.php?op=accept&op2=gold");
						addnav("Surrender","forest.php?op=decline");
					}elseif ($session['user']['gold']>=1000){
						output("You're caught pretty quickly though.  Bummer.");
						output("However, to your benefit, it seems the sheriff is a little corrupt.");
						output("`n`n`@'If you hand over `^1000 gold`@ I'll let you go.'");
						output("`n`n`0Will you take the offer?");
						addnav("Accept","forest.php?op=accept&op2=gold");
						addnav("Decline","forest.php?op=decline");
					}else{
						output("You don't even get a chance to get your feet moving when you feel a hand around your neck.");
						output("`@'You ready to go peacefully?'`0 you hear.");
						addnav("Go Peacefully","forest.php?op=decline");
					}
					addnav("Fight the Sheriff","forest.php?op=attack");
				}else{
					output("The sheriff sees you and recognizes you from the wanted posters for being a member in the Sheldon Gang.");
					$rand=e_rand(1,4);
					if ($rand==1){
						output("You spend a turn convincing the sheriff that it's not you... it's a case of mistaken identity!");
						$session['user']['turns']--;
						debuglog("spent a turn convincing the sheriff it was someone else!");
					}else{
						if ($session['user']['gold']>=1000){
							$session['user']['gold']-=1000;
							output("You pay the `^1000 gold`0 warrant out for your arrest and leave.");
							debuglog("paid 1000 gold to the sheriff.");
						}elseif ($session['user']['turns']>=3){
							$session['user']['turns']-=3;
							output("You spend `@3 turns`0 trying to convince the sheriff that it was someone else.");
							debuglog("spent 3 turns talking to the sheriff.");
						}elseif ($session['user']['gems']>=3){
							$session['user']['gems']-=3;
							output("You give the sheriff `%3 gems`0 to bribe him so you can get away.");
							debuglog("spent 3 gems bribing the sheriff.");
						}else{
							output("The sheriff, not wanting to bother with paperwork, beats you to a pulp.");
							output("You're left with `\$1 hitpoint`0 and `^no gold`0.");
							$session['user']['hitpoints']=1;
							$session['user']['gold']=0;
							debuglog("lost all gold and was left with 1 hitpoint after encountering the sheriff.");
						}
					}
				}
			break;
			case 3: case 4:
				output("You gather together some of the `QSheldon Gang Members`0 and go on a rampage through the forest.");
				output("`n`nSoon enough, the flowers are weeping, the grass is stomped down, and several mail boxes have been destroyed.");
				output("`n`nYou pick up some of the mail to see if there's anything good.`n`n");
				switch(e_rand(1,4)){
					case 1:
						output("Oooh... here's a nice letter from Grandma Jenkins.  Hmmm... nothing else.");
						output("`n`nTime to get back to the forest.");
					break;
					case 2:
						output("Awww, it's a present to little Billy! Look! It's `^50 gold pieces`0!");
						$session['user']['gold']+=50;
						debuglog("found 50 gold in the forest with the Sheldon Gang.");
					break;
					case 3:
						output("Hey! Someone was mailing a `%gem`0! Cool!");
						$session['user']['gem']++;
						debuglog("found a gem in the forest with the Sheldon Gang.");
					break;
					case 4:
						output("A witch comes running out of one of the houses. `2'A CURSE ON YE!!!!'`0 she yells.");
						output("`n`nYou are `iCursed`i!");
						apply_buff('blesscurse',
							array("name"=>translate_inline("Cursed"),
								"rounds"=>15,
								"survivenewday"=>1,
								"wearoff"=>translate_inline("The burst of energy passes."),
								"atkmod"=>0.8,
								"defmod"=>0.9,
								"roundmsg"=>translate_inline("Dark Energy flows through you!"),
							)
						);
						debuglog("received a curse by running with the Sheldon Gang.");
					break;
				}
			break;
			case 5:
				output("You head out with some of your fellow `QSheldon Gang Members`0 and look for trouble.`n`n");
				$meid=$session['user']['acctid'];
				$sql = "SELECT acctid,name,gold FROM ".db_prefix("accounts")." WHERE acctid<>'$meid' ORDER BY rand(".e_rand().") LIMIT 1";
				$res = db_query($sql);
				$row = db_fetch_assoc($res);
				$name = $row['name'];
				$id = $row['acctid'];
				$gold= $row['gold'];
				$takegold=round($gold*.1);
				if ($takegold>500) $takegold=500;
				db_query($sql);
				if ($name==$session['user']['name'] ||e_rand(1,5)<5 || $takegold==0){
					output("Lucky for everyone else, you don't find any.");
				}else{
					$sql = "UPDATE " . db_prefix("accounts") . " SET gold=gold-$takegold WHERE acctid='$id'";
					db_query($sql);
					require_once("lib/systemmail.php");
					$subj = sprintf("`QThe Sheldon Gang has struck!");
					$body = sprintf("`QThe Sheldon Gang has struck!`n`nThey stole `^%s gold`Q from you. There's a rumor going around that %s`Q is one of the gang members.",$takegold,$session['user']['name']);
					systemmail($id,$subj,$body);
					output("Before you know it, you find someone to pick on... It's %s`0!",$name);
					output("`n`nSoon enough, you've stolen `^%s gold`0 from %s.  Oh, how cruel you are!",$takegold,$name);
					$session['user']['gold']+=$takegold;
				}
				$session['user']['specialinc']="";
				require_once("lib/forest.php");
				forest(true);
			break;
			case 6:
				if (e_rand(1,10)==1 && get_module_pref("member")==0 && ($session['user']['gems']>=4 || get_module_pref("dragoneggs","dragoneggpoints")>=1)){
					output("You see a stranger in the forest and he comes over to you and looks you up and down.");
					output("`Q'I'm recruiting people into my gang. Are you interested?'`0 he asks.`n`n");
					output("It looks like you've met `b`QRazor Sheldon`b`0, the leader of the Sheldon Gang.");
					output("`n`n`Q'Unfortunately, joining my gang isn't like getting invited to a ball.  You have to show me that you've got what it takes,'`0 he says.");
					output("`QRazor`0 tells you that you're going to have to cough up `&1 Dragon Egg Point`0 or `%4 gems`0 to join.`n`n");
					addnav("Join the Gang");
					if ($session['user']['gems']>=4) addnav("Give 4 gems","forest.php?op=join&op2=gems");
					if (get_module_pref("dragoneggs","dragoneggpoints")>=1) addnav("Give 1 Dragon Egg Point","forest.php?op=join&op2=egg");
					addnav("Leave");
					addnav("Return to the Forest","forest.php?op=return");
				}else{
					output("You see some pretty shady people wandering through the forest.  Hmmm... looks like the `QSheldon Gang`0 is out looking for trouble.  You decide to move along.");
					$session['user']['specialinc']="";
					require_once("lib/forest.php");
					forest(true);
				}
			break;
		}
	}elseif ($op=="return"){
		$session['user']['specialinc']="";
		require_once("lib/forest.php");
		forest(true);
	}elseif ($op=="join"){
		if ($op2=="gems"){
			$session['user']['gems']-=4;
			$item="`%4 gems";
			debuglog("gave up 4 gems to become a member of the Sheldon Gang while wandering through the forest.");
		}else{
			increment_module_pref("dragoneggs",-1,"dragoneggpoints");
			$item="`&1 Dragon Egg Point";
			debuglog("gave up 1 dragon egg point to become a member of the Sheldon Gang while wandering through the forest.");
		}
		set_module_pref("member",-1,"sheldon");
		output("You hand over the %s`0 and tell `QRazor Sheldon`0 that you're ready to join.",$item);
		output("`n`nHe smiles, takes your stuff, and gives you a ratty piece of paper.  Before you get a chance to look at it, he tells you last thing.");
		output("`n`n`Q'You better show up before the day is over.  This offer does not last more than a day.'`0");
		output("`n`nThe slip of paper explains that you'll have to find the `QSheldon Gang Hideout`0 in %s... which will now be visible to you.",get_module_setting("sheldonloc"));
		$session['user']['specialinc']="";
		require_once("lib/forest.php");
		forest(true);
	}elseif ($op=="accept"){
		if ($op2=="gold"){
			$session['user']['gold']-=1000;
			output("You pay the `^1000 gold`0 'warrant' out for your arrest and leave.");
			debuglog("paid 1000 gold to the sheriff.");
		}else{
			$session['user']['turns']-=3;
			output("You decide to `@spend 3 turns`0 arguing with the sheriff.  Finally, he gives in and accepts that he made a mistake.");
			debuglog("spent 3 turns talking to the sheriff.");
		}
		$session['user']['specialinc']="";
		require_once("lib/forest.php");
		forest(true);
	}elseif ($op=="decline"){
		output("`@'Off you go to jail! Hopefully this will teach you a lesson,'`0 says the Sheriff.");
		addnews("`@The sheriff arrested %s`@ in the forest for suspicion of being a member of the `QSheldon Gang`@!",$session['user']['name']);
		set_module_pref('injail',1,$jail);
		addnav("To Jail", "runmodule.php?module=$jail");
		$session['user']['specialinc']="";
		if ($session['user']['superuser'] &~ SU_DOESNT_GIVE_GROTTO){
			addnav("Superuser");
			addnav("Newday", "newday.php");
		}
	}
	if ($op=="attack") {
		$rand=e_rand(120,140)/100;
		$title=translate_inline("Sheriff ");
		$name=$title.get_module_setting("sheriffname",$jail);
		$badguy = array(
			"creaturename"=>$name,
			"creaturelevel"=>16,
			"creatureweapon"=>"Sheriff's Sword",
			"creatureattack"=>$session['user']['attack']+3,
			"creaturedefense"=>round($session['user']['defense']*1.3),
			"creaturehealth"=>round($session['user']['maxhitpoints']*$rand),
			"type"=>"sheldon"
		);
		$session['user']['badguy']=createstring($badguy);
		$op="fight";
	}
	if ($op=="fight"){
		$battle=true;
	}
	if ($battle){       
		include("battle.php");  
		if ($victory){
			$session['user']['specialinc'] = "";
			$expbonus=$session['user']['dragonkills']*4;
			$expgain =($session['user']['level']*44+$expbonus);
			$session['user']['experience']+=$expgain;
			output("You leave as quickly as you can. You shoot the sheriff an evil look before you leave.`n");
			output("`@`bYou gain `#%s experience`@.`b",$expgain);
			addnews("%s`@ shot the Sheriff.  However, %s is not a suspect for having shot the deputy.",$session['user']['name'],translate_inline($session['user']['sex']?"she":"he"));
		}elseif($defeat){
			$session['user']['specialinc'] = "";
			require_once("lib/taunt.php");
			$taunt = select_taunt_array();
			$exploss = round($session['user']['experience']*.1);
			output("`n`n`\$Do not pass `@go`\$. Do not collect `^200 gold`\$.  Do not go to `)jail`\$.  You're `bdead`b.`n");
			output(" You lose `^%s `&experience`\$.`n",$exploss);
			output("`n`c`bYou may begin fighting again tomorrow.`c`b");
			addnav("Daily news","news.php");
			$session['user']['experience']-=$exploss;
			$session['user']['alive'] = false;
			$session['user']['hitpoints'] = 0;
			$session['user']['gold']=0;
			addnews("%s `@fought the law and the law won.`n%s",$session['user']['name'],$taunt);
		}else{
			fightnav(true,true);
		}
	}
}
function sheldon_run(){
	include("modules/sheldon/sheldon.php");
}
?>