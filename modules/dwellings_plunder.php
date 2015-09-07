<?php
/*

v1.0 basic performance, no gems

	Bug fix by MarcTheSlayer
	12/09/2010 - v1.1
	+ Missing global $session from plunder_getout() function which stopped 'specialmisc' from being blanked.
*/

function dwellings_plunder_getmoduleinfo(){
	$info = array(
		"name"=>"Dwellings Plunder The Dwelling",
		"author"=>"`2Oliver Brendel",
		"version"=>"1.1",
		"category"=>"Dwellings",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=682",
		"requires"=>array(
			"dwellings"=>"1.0|Dwellings Project Team,http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=162",
		),
		"settings"=>array(
			"Dwellings Plunder Settings,title",
			"`iThis module supports basic alignment`i,note",
			"times"=>"How many times can a person enter an estate `ito steal`i,range,1,5,1|2",
			"experienceloss"=>"Percentage: How many experience is lost when player is killed by a resident,floatrange,1,100,1|10",
			"Plunder is randomized from 1 to the value you enter here (max what the coffers hold of course),note",
			"enablegold"=>"Is the theft of gold allowed?,bool|1",
			"gold"=>"Multiplier of Level to retrieve gold that one can steal,int|150",

			//"enablegems"=>"Is the theft of gems allowed?,bool|0",
			//"gems"=>"Multiplier of Level to retrieve gems that one can steal,int|1", //soon
		),
		"prefs"=> array(
			"Dwellings Plunder Preferences,title",
			"plunderedtoday"=>"How often has this user plundered today?,int",
		),
		"prefs-dwellingtypes"=>array(
			"Dwellings Plunder Settings,title",
			"plunder"=>"Can this dwellings be plundered?,bool|1",

		),
	);
	return $info;
}
function dwellings_plunder_install(){
	module_addhook("dwellings-list-interact");
	module_addhook("newday");
	return true;
}
function dwellings_plunder_uninstall(){
	return true;
}
function dwellings_plunder_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "newday":
			set_module_pref("plunderedtoday",0);
			break;
		case "dwellings-list-interact":
			$typeid=get_module_setting("typeid",$args['type']);
			if ($session['user']['location'] == $args['location']
				&& (get_module_setting("enablegems")||get_module_setting("enablegold"))
				&& get_module_objpref("dwellingtypes", $typeid, "plunder", "dwellings_plunder")
				&& (get_module_pref("plunderedtoday")<get_module_setting("times"))
				&& $args['status'] == 1){
				//a) is the user where the dwelling is, b) is anything allowed to be plundered, c) is no guard there, d) is plundering for that building type allowed, e) if he can plunder this day again, if all yes, enter
				//moved c here:
				if (is_module_active("dwellings_pvp")) {
					if (get_module_objpref("dwellings", $args['dwid'], "bought", "dwellings_pvp"))
						break;
				}
				//don't plunder if you're the owner
				$dwid=$args['dwid'];
				$sql="SELECT ownerid FROM ".db_prefix("dwellings")." WHERE dwid=$dwid AND ownerid={$session['user']['acctid']} LIMIT 1";
				$result=db_query($sql);
				$rows=db_affected_rows($result);
				if ($rows>0) break;
				$plunder = translate_inline("Plunder");
				rawoutput("<a href='runmodule.php?module=dwellings_plunder&op=starttheplunder&dwid=$dwid&typeid=$typeid'>$plunder</a><br>");
				addnav("","runmodule.php?module=dwellings_plunder&op=starttheplunder&dwid=$dwid&typeid=$typeid");
				}
			break;
		}
	return $args;
}

function dwellings_plunder_run(){
	global $session,$plunder;
	$op=httpget('op');
	$dwid=httpget('dwid');
	if ($session['user']['specialmisc']=='') { //easier way to prevent unnecessary queries
		if ($dwid==0) {
			output_notl("Critical module error, please report this to your admin.");
			page_footer();
			return;
		}
		$plunder=plunder_getmemyarray($dwid);
		$session['user']['specialmisc']=rawurlencode(serialize($plunder));
	} else {
		$plunder=unserialize(rawurldecode($session['user']['specialmisc']));
	}
	page_header("Plunder the dwelling!");
	switch ($op){
		case "starttheplunder":
			//first secure setup, specialmisc might be used for another thingy before+not cleaned up
			$plunder=plunder_getmemyarray($dwid);
			$session['user']['specialmisc']=rawurlencode(serialize($plunder));
			//done
			if ($plunder['name']=='Noname') {
				output("`5You sneak near the building of your desire and start to observe.`n");
			} else {
				output("`5You sneak near the building of your desire, named '%s'`5 and start to observe.`n",$plunder['name']);
			}
			output("No guard visible? -check-`n");
			output("Lights off? -check-`n");
			output("Guts? err...-check-`n");
			output("`nNow is the last chance to step back...what do you do?");
			addnav("Actions");
			addnav("Go Forward","runmodule.php?module=dwellings_plunder&op=commencetheplunder&dwid={$plunder['id']}&typeid={$plunder['typeid']}");
			addnav("Step Back","runmodule.php?module=dwellings_plunder&op=aborttheplunder&dwid={$plunder['id']}&typeid={$plunder['typeid']}");
			break;
		case "aborttheplunder":
			output("`5You take your tail between your legs and run... seems your guts left you. After little time you are out of sight of this dwelling.");
			$session['user']['specialmisc']='';
			addnav("Leave");
			addnav("Hamlet Registry","runmodule.php?module=dwellings&op=list&ref=hamlet");
			break;
		case "commencetheplunder":
			$mode=httpget('mode');
			switch($mode) {
				case "backdoor": case "frontdoor": case "window":
					$res=e_rand(1,6);
					if ($session['user']['race']=='Dwarf' || $session['user']['race']=='Elf')	$res+=1;
					if ($res>6) $res=6;
					// use this hook and set chance to -1 if you have defined an own trap here, then below the getout function will be called.
					// use $args['chance']='fight' to enter a fight here. setup your enemy acctid in the fight field as an array.
					$array=modulehook("dwellings_plunder_traps",array_merge($plunder,array("chance"=>$res, "fight"=>array())));
					$res=$array['chance'];
					switch($res) {
						case -1: 
							//here the mighty module which coped with the array wishes a safe exit as everything has been done
							plunder_getout();
							break;
						case 1:
							output("`5Oh no! Seems you attracted a watchdog!`n");
							output("You try to play 'sit' with him, but apparently he is not eager to do so... yet wants to take a bit of your enormous ...`n");
							output("`nAfter you have run for a couple of minutes... just to make sure you left the dwelling and the dog behind you, you stop to take a little rest.`n");
							if ($session['user']['turns']>0) {
								output("You have `\$lost`5 one forest fight worth of time!");
								$session['user']['turns']--;
							}
							plunder_getout();
							break;
						case 2:
							output("`5Good idea... but unfortunately you run into a trap...");
							if (e_rand(0,1))
								output (" an arrow hits you from behind...`n");
							else
								output (" you fall down a pit with spikes...`n");
							$damage=e_rand(1,$session['user']['maxhitpoints']);
							if ($damage>=$session['user']['hitpoints']) {
								output("Too much... too much... you see the world `)fading before your eyes`5...");
								output("You are dead... your soul drifts to the Land of the Shades...");
								$session['user']['alive']=0;
								$session['user']['hitpoints']=0;
								addnews("%s`^ tried to plunder the dwelling '%s`^' but ran into a trap and got killed!",$session['user']['name'],$plunder['name']);
								$session['user']['specialmisc']='';
								addnav("Return");
								addnav("Return to the Shades","shades.php");
							} else {
								$session['user']['hitpoints']-=$damage;
								output("You suffer %s points of damage!",$damage);
								addnav("Continue to the dwelling","runmodule.php?module=dwellings_plunder&op=commencetheplunder&mode=inside");
							}
							break;
						case 3: case 4: case "fight":
							plunder_preparefight($array['fight']);
							break;
						case 5: case 6:
							output("Nothing happens as you sneak into the building...");
							addnav("Continue to the dwelling","runmodule.php?module=dwellings_plunder&op=commencetheplunder&mode=inside");
							break;
						}
					break;
				case "inside":
					$mode=httpget('mode');
					$coffers=plunder_getcoffers($plunder['id']);
					switch($mode) {
						case "k":

						default:
							output("`5Finally... you are there... before you are the coffers...");
							output("You see the trunks before you...`n`n");
							if ($coffers['gold']<1) {
								output("You start to open them all... but you are a bit shocked... there is not a `\$SINGLE`5 piece of gold in there...");
								output("`nAll the hard work for nothing...");
							} else {
								$gold=e_rand(1,min($session['user']['level']*get_module_setting("gold"),$coffers['gold']));
								output("You try to dig into your pockets as much as you can but you hear the noise of footsteps coming along...`n");
								output("You snatch `^%s gold pieces`5 and flee from the dwelling.",$gold);
								$session['user']['gold']+=$gold;
								$sql="UPDATE ".db_prefix("dwellings")." SET gold=gold-$gold WHERE dwid=".$plunder['id'].";";
								$result=db_query($sql);
								$message = sprintf_translate("::stole `^%s gold pieces`&.", $gold);
								require_once("lib/commentary.php");
								injectrawcomment("coffers-{$plunder['id']}", $session['user']['acctid'], $message,$session['user']['name']);
								require_once("lib/systemmail.php");
								systemmail($plunder['ownerid'],array("`\$Plunder!"),array("`^The dwelling '%s`^' you own was plundered by the foul %s`^ and %s gold pieces were stolen!",$plunder['name'],$session['user']['name'],$gold));
							}
							plunder_getout();
					}
					break;
				default:
					if (is_module_active("alignment")) {
						require_once("modules/alignment/func.php");
						align("-2"); //bad boy
					}
					output("`5Okay, you're there... which way do you choose?");
					output_notl("`n`n");
					output("`\$Front Door.`n");
					output("`^Window.`n");
					output("`!Check for backdoor.`n");
					addnav("`\$Front Door.","runmodule.php?module=dwellings_plunder&op=commencetheplunder&mode=frontdoor");
					addnav("`^Window.","runmodule.php?module=dwellings_plunder&op=commencetheplunder&mode=window");
					addnav("`!Backdoor.","runmodule.php?module=dwellings_plunder&op=commencetheplunder&mode=backdoor");
					increment_module_pref("plunderedtoday",1);
					break;
			}
			break;
		case "fight":
			if (httpget('mode')=='setup') {
				$badguy = array(
				"creaturename"=>$plunder['residentname'],
				"creaturelevel"=>$plunder['level'],
				"creatureweapon"=>$plunder['weapon'],
				"creatureattack"=>$plunder['attack'],
				"creaturedefense"=>$plunder['defense'],
				"creaturehealth"=>$plunder['maxhitpoints'],
				"diddamage"=>0,);
				$session['user']['badguy'] = createstring($badguy);
				require_once("lib/battle-skills.php");
				suspend_buffs('allowintrain',"`!Time ceases to exist... You suddenly feel vulnerable... the gods ripped you of any external support you might have!!!");
			}
			include("battle.php");
			if ($victory){ //no exp at all for such a foul act
				addnews("%s`^ has somehow survived a failed plunder at the dwelling '%s`^'",$session['user']['name'],$plunder['name']);
				unsuspend_buffs('allowintrain',"You feel that time and the energies are now flowing normally again.");
				require_once("lib/systemmail.php");
				systemmail($plunder['acctid'],array("Failed Plunder"),array("`^The dwelling where you slept was attacked and you fought the intruder %s`^... you were not able to defeat him.",$session['user']['name']));
				debuglog("survived plunder in dwelling number {$plunder['id']}.");
				$badguy=array();
				$session['user']['badguy']="";
				if (e_rand(0,1)) {
					output("`n`n`5%s`5 is critically wounded and drops down consciousless... yet it seems nobody wake up... excellent... you can continue on your plundertour...",$plunder['residentname']);
					addnav("Continue to the dwelling","runmodule.php?module=dwellings_plunder&op=commencetheplunder&mode=inside");
				} else {
					output("`5%s`5 is critically wounded...but before you can deliver the final blow you hear the other residents awake and the local authorities coming... so you have to leave... `\$NOW`5...",$plunder['residentname']);			
					$session['user']['specialinc'] = "";
					$session['user']['specialmisc'] = "";					
					plunder_getout();
				}
			}elseif ($defeat){ //but a loss of course if you die
				$exploss = $session['user']['experience']*get_module_setting("experienceloss")/100;
				output("`5%s`5 strikes you down... and your vision blurs...you have been defeated while commiting such a foul act.`n",$plunder['residentname']);
				if ($exploss>0) output(" `5You lose `^%s percent`5  of your experience and all of your gold.",get_module_setting("experienceloss"));
				$session['user']['experience']-=$exploss;
				$session['user']['gold']=0;
				debuglog("lost $exploss experience and all gold while plundering.");
				addnews("%s`^ has been killed by %s`^ while trying to plunder the dwelling '%s`^'",$session['user']['name'],$plunder['residentname'],$plunder['name']);
				require_once("lib/systemmail.php");
				systemmail($plunder['acctid'],array("Failed Plunder"),array("`^The dwelling where you slept was attacked but you killed the intruder %s`^ and saved the coffers.",$session['user']['name']));
				addnav("Return");
				addnav("Return to the Shades","shades.php");
				$session['user']['specialinc'] = "";
				$session['user']['specialmisc'] = "";
				$badguy=array();
				$session['user']['badguy']="";
				unsuspend_buffs('allowintrain',"");
			}else{
				require_once("lib/fightnav.php");
				fightnav(true,false,"runmodule.php?module=dwellings_plunder");
				if ($session['user']['superuser'] & SU_DEVELOPER) addnav("Escape to Village","village.php");
				}
			break;
	}
	page_footer();
}

function plunder_getout() {
	global $session;
	addnav("Leave");
	addnav("Hamlet Registry","runmodule.php?module=dwellings&op=list&ref=hamlet");
	$session['user']['specialmisc']='';
	return;
}
function plunder_getmemyarray($id){
	$sql = "SELECT name,ownerid FROM ".db_prefix("dwellings")." WHERE dwid=$id";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	if ($row['name']=='') $row['name']="Noname";
	return array("name"=>$row['name'],"id"=>$id,"typeid"=>httpget('typeid'),"ownerid"=>$row['ownerid']);
}

function plunder_getcoffers($id) {
	$sql = "SELECT gold,gems FROM ".db_prefix("dwellings")." WHERE dwid=$id";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	return array("id"=>$id,"gold"=>$row['gold'],"gems"=>$row['gems']);
}

function plunder_preparefight($fight=array()) {
	global $session,$plunder;
	if ($fight==array()) {
		$ac=db_prefix("accounts");
		$mu=db_prefix("module_userprefs");
		$sql = "SELECT $ac.name AS residentname,
			$ac.acctid AS acctid,
			$ac.level AS level,
			$ac.defense AS defense,
			$ac.attack AS attack,
			$ac.maxhitpoints AS maxhitpoints,
			$ac.weapon AS weapon,
			$mu.userid FROM $mu
			INNER JOIN $ac ON $ac.acctid = $mu.userid
			WHERE $mu.setting = 'dwelling_saver'
			and $mu.value = {$plunder['id']}
			and $ac.loggedin = 0
			and $ac.alive != 0
			ORDER BY rand(".e_rand().") LIMIT 1";
		$result=db_query($sql);
		if (db_num_rows($result)<1) {
			output("You made enough noise for 3 burglars, but you are fortunate... no one is sleeping here right now...");
			addnav("Continue","runmodule.php?module=dwellings_plunder&op=commencetheplunder&mode=inside");
		} else {
			$row=db_fetch_assoc($result);
			output("`5As you sneak into the dwelling, you alert one of the residents! %s`5 stands before you with drawn %s`5!",$row['residentname'],$row['weapon']);
			addnav("Fight!","runmodule.php?module=dwellings_plunder&op=fight&mode=setup");
			$session['user']['specialmisc']=rawurlencode(serialize(array_merge($row,$plunder)));
		}
	} else {
		$ac=db_prefix("accounts");
		$mu=db_prefix("module_userprefs");
		$sql = "SELECT $ac.name AS residentname,
			$ac.acctid AS acctid,
			$ac.level AS level,
			$ac.sex AS sex,
			$ac.defense AS defense,
			$ac.attack AS attack,
			$ac.maxhitpoints AS maxhitpoints,
			$ac.weapon AS weapon
			FROM $ac WHERE acctid={$fight['acctid']} LIMIT 1";
		$result=db_query($sql);
		$row=db_fetch_assoc($result);
		$sex=translate_inline(($row['sex']?"he":"she"));
		output("`5As you sneak into the dwelling, you have some very uneasy feeling crouching up... `n`n");
		output("And well, you were right... just as you enter the coffers, you see %s`5 before you, battleready with drawn %s`5... seems like %s`5 stood guard the entire night!`n`n",$row['residentname'],$row['weapon'],$row['residentname']);
		output("Knowing you can't back off and flee now you get ready for rumble...");
		addnav("Fight!","runmodule.php?module=dwellings_plunder&op=fight&mode=setup");
		$session['user']['specialmisc']=rawurlencode(serialize(array_merge($row,$plunder)));
	}
}
?>