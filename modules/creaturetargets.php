<?php

// Creature Targeting System for Want of a Better Name v2008-09-23
// By Dan Hall, ImprobableIsland.com
//
// This mod allows you to add up to six specific areas to a monster, for a player to attempt to hit.
// For example, if a player came across a malfunctioning camera or someone with caterpillars in their vagina, they could choose to hit the lens, the camera battery, the caterpillars, or indeed, the vagina.  Yes, these were my test creatures.  It's 4am, give me a break.
// Each hit area is highly customisable.  When the player attempts to strike an area, the creature's Attack or Defence values will fluctuate in that particular round of combat according to what you specify.
// Each hit area has its own bank of hit points, which can be expressed as a value of the creature's starting hitpoints.  When those hitpoints drop to zero, the creature is permanently buffed or debuffed according to your specifications.  Or, you can take away a number of extra hitpoints as a bonus.  Or both.
// Hitting any area will sap the creature's overall hitpoints by the same number as the hitpoints being taken from that specific area.  Hitting the head for 10 damage will take 10 damage from the head and 10 damage from the overall health.
// Hitting "Fight" or any of the automatic fighting buttons will simply attack as normal, with normal stats (altered stats from destroying hitareas notwithstanding).  It is assumed that unless the player says otherwise, he or she is hitting the creature's body.
//
// INSTALLATION
//
// Move the creaturetargetai.php file into your /ai directory.  If this directory doesn't exist, please create it.
//
// The rest is standard procedure.
//
// Please report any bugs you find.  Have fun!
//
// -CMJ






function creaturetargets_getmoduleinfo(){
	$info = array(
		"name"=>"Creature Targets",
		"author"=>"Dan Hall",
		"version"=>"2008-09-24",
		"category"=>"General",
		"download"=>"",
		"prefs-creatures"=>array(
			"Master toggle,title",
			"usetargets"=>"Are we using multiple targets on this enemy at all?,bool",
			"Target 1,title",
			"target1"=>"Name of target,text",
			"hitpoints1"=>"Hitpoints assigned to target expressed as a percentage of the creature's total hitpoints,int|0",
			"killatk1"=>"Creature's attack modifier when target is out of hitpoints,int|1",
			"killdef1"=>"Creature's defence modifier when target is out of hitpoints,int|1",
			"killhp1"=>"Remove this percentage of the creature's starting hitpoints when target is out of hitpoints,int|0",
			"killmsg1"=>"Message shown when target is out of hitpoints,text",
			"hitatk1"=>"Creature's attack modifier when target is being attacked,int|1",
			"hitdef1"=>"Creature's defence modifier when target is being attacked,int|1",
			"hitmsg1"=>"Message shown when attacking target,text",
			"Target 2,title",
			"target2"=>"Name of target,text",
			"hitpoints2"=>"Hitpoints assigned to target expressed as a percentage of the creature's total hitpoints,int|0",
			"killatk2"=>"Creature's attack modifier when target is out of hitpoints,int|1",
			"killdef2"=>"Creature's defence modifier when target is out of hitpoints,int|1",
			"killhp2"=>"Remove this percentage of the creature's starting hitpoints when target is out of hitpoints,int|0",
			"killmsg2"=>"Message shown when target is out of hitpoints,text",
			"hitatk2"=>"Creature's attack modifier when target is being attacked,int|1",
			"hitdef2"=>"Creature's defence modifier when target is being attacked,int|1",
			"hitmsg2"=>"Message shown when attacking target,text",
			"Target 3,title",
			"target3"=>"Name of target,text",
			"hitpoints3"=>"Hitpoints assigned to target expressed as a percentage of the creature's total hitpoints,int|0",
			"killatk3"=>"Creature's attack modifier when target is out of hitpoints,int|1",
			"killdef3"=>"Creature's defence modifier when target is out of hitpoints,int|1",
			"killhp3"=>"Remove this percentage of the creature's starting hitpoints when target is out of hitpoints,int|0",
			"killmsg3"=>"Message shown when target is out of hitpoints,text",
			"hitatk3"=>"Creature's attack modifier when target is being attacked,int|1",
			"hitdef3"=>"Creature's defence modifier when target is being attacked,int|1",
			"hitmsg3"=>"Message shown when attacking target,text",
			"Target 4,title",
			"target4"=>"Name of target,text",
			"hitpoints4"=>"Hitpoints assigned to target expressed as a percentage of the creature's total hitpoints,int|0",
			"killatk4"=>"Creature's attack modifier when target is out of hitpoints,int|1",
			"killdef4"=>"Creature's defence modifier when target is out of hitpoints,int|1",
			"killhp4"=>"Remove this percentage of the creature's starting hitpoints when target is out of hitpoints,int|0",
			"killmsg4"=>"Message shown when target is out of hitpoints,text",
			"hitatk4"=>"Creature's attack modifier when target is being attacked,int|1",
			"hitdef4"=>"Creature's defence modifier when target is being attacked,int|1",
			"hitmsg4"=>"Message shown when attacking target,text",
			"Target 5,title",
			"target5"=>"Name of target,text",
			"hitpoints5"=>"Hitpoints assigned to target expressed as a percentage of the creature's total hitpoints,int|0",
			"killatk5"=>"Creature's attack modifier when target is out of hitpoints,int|1",
			"killdef5"=>"Creature's defence modifier when target is out of hitpoints,int|1",
			"killhp5"=>"Remove this percentage of the creature's starting hitpoints when target is out of hitpoints,int|0",
			"killmsg5"=>"Message shown when target is out of hitpoints,text",
			"hitatk5"=>"Creature's attack modifier when target is being attacked,int|1",
			"hitdef5"=>"Creature's defence modifier when target is being attacked,int|1",
			"hitmsg5"=>"Message shown when attacking target,text",
			"Target 6,title",
			"target6"=>"Name of target,text",
			"hitpoints6"=>"Hitpoints assigned to target expressed as a percentage of the creature's total hitpoints,int|0",
			"killatk6"=>"Creature's attack modifier when target is out of hitpoints,int|1",
			"killdef6"=>"Creature's defence modifier when target is out of hitpoints,int|1",
			"killhp6"=>"Remove this percentage of the creature's starting hitpoints when target is out of hitpoints,int|0",
			"killmsg6"=>"Message shown when target is out of hitpoints,text",
			"hitatk6"=>"Creature's attack modifier when target is being attacked,int|1",
			"hitdef6"=>"Creature's defence modifier when target is being attacked,int|1",
			"hitmsg6"=>"Message shown when attacking target,text",
		),
	);
	return $info;
}

function creaturetargets_install(){
	module_addhook("buffbadguy");
	module_addhook("fightnav-specialties");
	module_addhook("apply-specialties");
	module_addhook("battle");
	require_once "modules/staminasystem/lib/lib.php";
	install_action("Fighting - Targeted",array(
		"maxcost"=>1000,
		"mincost"=>500,
		"firstlvlexp"=>2000,
		"expincrement"=>1.12,
		"costreduction"=>5,
		"class"=>"Combat"
	));
	return true;
}

function creaturetargets_uninstall(){
	require_once "modules/staminasystem/lib/lib.php";
	uninstall_action("Fighting - Targeted");
	return true;
}

function creaturetargets_dohook($hookname,$args){
	global $session, $enemies;
	switch($hookname){
	case "buffbadguy":
		if (get_module_objpref("creatures",  $args['creatureid'], "usetargets")==1){
			for ($i=1;$i<=6;$i++) {
				$args['target'.$i]['name'] = get_module_objpref("creatures",  $args['creatureid'], "target".$i);
$args['target'.$i.'']['hitpoints'] = round((($args['creaturehealth']/100)*get_module_objpref("creatures",  $args['creatureid'], "hitpoints".$i."")));
				$args['target'.$i]['killatk'] = get_module_objpref("creatures",  $args['creatureid'], "killatk".$i);
				$args['target'.$i]['killdef'] = get_module_objpref("creatures",  $args['creatureid'], "killdef".$i);
				$args['target'.$i]['killhp'] = get_module_objpref("creatures",  $args['creatureid'], "killhp".$i);
				$args['target'.$i]['killmsg'] = get_module_objpref("creatures",  $args['creatureid'], "killmsg".$i);
				$args['target'.$i]['hitatk'] = get_module_objpref("creatures",  $args['creatureid'], "hitatk".$i);
				$args['target'.$i]['hitdef'] = get_module_objpref("creatures",  $args['creatureid'], "hitdef".$i);
				$args['target'.$i]['hitmsg'] = get_module_objpref("creatures",  $args['creatureid'], "hitmsg".$i);
				$args['target'.$i]['currenttarget'] = 0;
			}
		}
		//Check to see if there's an AI Script involved with this creature, and if not, tell battle.php (via our modifications) to execute our AI Script at the start of the round, not the end - as long as we're actually using multiple hit targets with this creature, of course
		if ($args['target1']['hitpoints']!=0){
			if ($args['creatureaiscript']==''){
				$args['creatureaiscript'] = "require_once(\"ai/creaturetargetai.php\");";
			}
		}
		//Give the creature a set of starting hitpoints, because it's SO DAMNED USEFUL
		$args['creaturestartinghealth'] = $args['creaturehealth'];
		debug($args['creaturestartinghealth']);
		//Set the phase for the AI Script and move on
		$args['phase'] = 1;
		// debug("Debugging args after creatureencounter");
		// debug ($args);
		return $args;
		break;
	case "battle":
		foreach ($enemies as $index=>$badguy) {
			$badguy['oldhitpoints'] = $badguy['creaturehealth'];
			$enemies[$index] = $badguy;
		}
//		debug("Debugging enemies at battle");
//		debug($enemies);
		break;
	case "apply-specialties":
		foreach ($enemies as $index=>$badguy) {
			$skill = httpget('skill');
			$target = httpget('target');
			if ($badguy['istarget']==1){
				if ($skill=="target"){
					require_once "modules/staminasystem/lib/lib.php";
					$return = process_action("Fighting - Targeted");
					if ($return['lvlinfo']['levelledup']==true){
						output("`n`c`b`0You gained a level in Targeted Fighting!  You are now level %s!  This action will cost fewer Stamina points now.`b`c`n",$return['lvlinfo']['newlvl']);
					}
//					debug("Altering creature stats");
					//First grab and save the current attack and defence stats, so we can revert back in the next round
					$badguy['oldcreatureattack'] = $badguy['creatureattack'];
					$badguy['oldcreaturedefense'] = $badguy['creaturedefense'];
					//Now change the values for hitting the relevant target
					$badguy['creatureattack'] = ($badguy['creatureattack']*$badguy['target'.$target.'']['hitatk']);
					$badguy['creaturedefense'] = ($badguy['creaturedefense']*$badguy['target'.$target.'']['hitdef']);
					// Set this target as the target being... targeted, I guess.
					$badguy['target'.$target.'']['currenttarget']=1;
					//Tell the AI Script to put the stats back the way they were
					$badguy['revert']=1;
					//Output the hit message.  We're using a buff so that it goes in the right place in the text.  The atkmod is only there to force the buff to output the start message.
					apply_buff("creaturetargets",array(
						"rounds"=>1,
						"startmsg"=>stripslashes($badguy['target'.$target.'']['hitmsg']),
						"atkmod"=>1,
						"schema"=>"module-creaturetargets",
						)
					);
				}
			}
			$enemies[$index] = $badguy;
		}
//		debug("Debugging enemies after apply-specialties");
//		debug($enemies);
		break;
	case "fightnav-specialties":
		$script = $args['script'];
		require_once "modules/staminasystem/lib/lib.php";
		$cost = stamina_getdisplaycost("Fighting - Targeted");
		foreach ($enemies as $index=>$badguy) {
			for ($i=1;$i<=6;$i++) {
				if ($badguy['istarget']==1 && $badguy['target'.$i.'']['hitpoints']>0){
					addnav(array("Targeting (`Q+%s%%`0)",$cost));
					addnav(array("Aim for the %s", $badguy['target'.$i.'']['name']),
						$script."op=fight&skill=target&target=".$i."", true);
				}
			}
		}
		break;
	}
	return $args;
}
?>