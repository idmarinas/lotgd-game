<?php
	$op = httpget("op");
	$city = urldecode(httpget("city"));
	$continue = httpget("continue");
	$danger = httpget("d");
	$su = httpget("su");
	if ($op != "faq") {
		require_once("lib/forcednavigation.php");
		do_forced_nav(false, false);
	}

	// I really don't like this being out here, but it has to be since
	// events can define their own op=.... and we might need to handle them
	// otherwise things break.
	require_once("lib/events.php");
	if ($session['user']['specialinc'] != "" || httpget("eventhandler")){
		$in_event = handle_event("travel",
			"runmodule.php?module=cities&city=".urlencode($city)."&d=$danger&continue=1&",
			"Travel");
		if ($in_event) {
			addnav("Continue","runmodule.php?module=cities&op=travel&city=".urlencode($city)."&d=$danger&continue=1");
			module_display_events("travel",
				"runmodule.php?module=cities&city=".urlencode($city)."&d=$danger&continue=1");
			page_footer();
		}
	}

	if ($op=="travel"){
		$args = modulehook("count-travels", array('available'=>0,'used'=>0));
		$free = max(0, $args['available'] - $args['used']);
		if ($city==""){
			require_once("lib/villagenav.php");
			page_header("Travel");
			modulehook("collapse{", array("name"=>"traveldesc"));
			output("`%Travelling the world can be a dangerous occupation.");
			output("Although other villages might offer things not found in your current one, getting from village to village is no easy task, and might subject you to various dangerous creatures or brigands.");
			output("Be sure you're willing to take on the adventure before you set out, as not everyone arrives at their destination intact.");
			output("Also, pay attention to the signs, some roads are safer than others.`n");
			modulehook("}collapse");
			addnav("Forget about it");
			villagenav();
			modulehook("pre-travel");
			if (!($session['user']['superuser']&SU_EDIT_USERS) && ($session['user']['turns']<=0) && $free == 0) {
				// this line rewritten so as not to clash with the hitch module.
				output("`nYou don't feel as if you could face the prospect of walking to another city today, it's far too exhausting.`n");
			}else{
				addnav("Travel");
				modulehook("travel");
			}
			module_display_events("travel",
				"runmodule.php?module=cities&city=".urlencode($city)."&d=$danger&continue=1");
			page_footer();
		}else{
			if ($continue!="1" && $su!="1" && !get_module_pref("paidcost")){
				set_module_pref("paidcost", 1);
				$httpcost=httpget('cost');
				$cost=modulehook("travel-cost",array("from"=>$session['user']['location'],"to"=>$city,"cost"=>0));
				$cost=max(1,$cost['cost'],$httpcost);
				$reallyfree=$free-$cost;
				if ($reallyfree > 0) {
					// Only increment travel used if they are still within
					// their allowance.
					increment_module_pref("traveltoday",$cost);
					//do nothing, they're within their travel allowance.
				}elseif ($session['user']['turns']+$free>0){
					$over=abs($reallyfree);
					increment_module_pref("traveltoday",$free);
					$session['user']['turns']-=$over;
				}else{
					output("Hey, looks like you managed to travel with out having any forest fights.  How'd you swing that?");
					debuglog("Travelled with out having any forest fights, how'd they swing that?");
				}
			}
			// Let's give the lower DK people a slightly better chance.
			$dlevel = cities_dangerscale($danger);
			if (e_rand(0,100)< $dlevel && $su!='1'){
				//they've been waylaid.

				if (module_events("travel", get_module_setting("travelspecialchance"),"runmodule.php?module=cities&city=".urlencode($city)."&d=$danger&continue=1&") != 0) {
					page_header("Something Special!");
					if (checknavs()) {
						page_footer();
					} else {
						// Reset the special for good.
						$session['user']['specialinc'] = "";
						$session['user']['specialmisc'] = "";
						$skipvillagedesc=true;
						$op = "";
						httpset("op", "");
						addnav("Continue","runmodule.php?module=cities&op=travel&city=".urlencode($city)."&d=$danger&continue=1");
						module_display_events("travel",
							"runmodule.php?module=cities&city=".urlencode($city)."&d=$danger&continue=1");
						page_footer();
					}
				}

				$args = array("soberval"=>0.9,
						"sobermsg"=>"`&Facing your bloodthirsty opponent, the adrenaline rush helps to sober you up slightly.", "schema"=>"module-cities");
				modulehook("soberup", $args);
				require_once("lib/forestoutcomes.php");
				$sql = "SELECT * FROM " . db_prefix("creatures") . " WHERE creaturelevel = '{$session['user']['level']}' AND forest = 1 ORDER BY rand(".e_rand().") LIMIT 1";
				$result = db_query($sql);
				restore_buff_fields();
				if (db_num_rows($result) == 0) {
					// There is nothing in the database to challenge you,
					// let's give you a doppleganger.
					$badguy = array();
					$badguy['creaturename']=
						"An evil doppleganger of ".$session['user']['name'];
					$badguy['creatureweapon']=$session['user']['weapon'];
					$badguy['creaturelevel']=$session['user']['level'];
					$badguy['creaturegold']=0;
					$badguy['creatureexp'] =
						round($session['user']['experience']/10, 0);
					$badguy['creaturehealth']=$session['user']['maxhitpoints'];
					$badguy['creatureattack']=$session['user']['attack'];
					$badguy['creaturedefense']=$session['user']['defense'];
				} else {
					$badguy = db_fetch_assoc($result);
					$aiscriptfile=$badguy['creatureaiscript'].".php";
					if (file_exists($aiscriptfile)) {
						//file there, get content and put it into the ai script field.
						$badguy['creatureaiscript']="require_once('".$aiscriptfile."');";
					}
					else
					{
						$badguy['creatureaiscript'] = '';
					}
					$badguy = buffbadguy($badguy);
				}
				calculate_buff_fields();
				$badguy['playerstarthp']=$session['user']['hitpoints'];
				$badguy['diddamage']=0;
				$badguy['type'] = 'travel';
				$session['user']['badguy']=createstring($badguy);
				$battle = true;
			}else{
				set_module_pref("paidcost", 0);
				//they arrive with no further scathing.
				$session['user']['location']=$city;
				redirect("village.php");
			}
		}
	}elseif ($op=="fight" || $op=="run"){
		if ($op == "run" && e_rand(1, 5) < 3) {
			// They managed to get away.
			page_header("Escape");
			output("You set off running through the forest at a breakneck pace heading back the way you came.`n`n");
			$coward = get_module_setting("coward");
			if ($coward) {
				modulehook("cities-usetravel",
				array(
					"foresttext"=>array("In your terror, you lose your way and become lost, losing time for a forest fight.`n`n", $session['user']['location']),
					"traveltext"=>array("In your terror, you lose your way and become lost, losing precious travel time.`n`n", $session['user']['location']),
					)
				);
			}
			output("After running for what seems like hours, you finally arrive back at %s.", $session['user']['location']);

			addnav(array("Enter %s",$session['user']['location']), "village.php");
			page_footer();
		}
		$battle=true;
	} elseif ($op == "faq") {
		cities_faq();
	} elseif ($op == "") {
		page_header("Travel");
		output("A divine light ends the fight and you return to the road.");
		addnav("Continue your journey","runmodule.php?module=cities&op=travel&city=".urlencode($city)."&continue=1&d=$danger");
		module_display_events("travel",
			"runmodule.php?module=cities&city=".urlencode($city)."&d=$danger&continue=1");
		page_footer();
	}

	if ($battle){
		page_header("You've been waylaid!");
		require_once("battle.php");
		if ($victory){
			require_once("lib/forestoutcomes.php");
			forestvictory($newenemies,"This fight would have yielded an extra turn except it was during travel.");
			addnav("Continue your journey","runmodule.php?module=cities&op=travel&city=".urlencode($city)."&continue=1&d=$danger");
			module_display_events("travel",
				"runmodule.php?module=cities&city=".urlencode($city)."&d=$danger&continue=1");
		}elseif ($defeat){
			require_once("lib/forestoutcomes.php");
			forestdefeat($newenemies,array("travelling to %s",$city));
		}else{
			require_once("lib/fightnav.php");
			fightnav(true,true,"runmodule.php?module=cities&city=".urlencode($city)."&d=$danger");
		}
		page_footer();
	}

?>
