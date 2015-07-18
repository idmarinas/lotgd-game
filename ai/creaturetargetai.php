<?php
global $session, $badguy;
//debug("Executing AI Script");
//The script is running at the end of the combat round
//Put the creature's attack and defense back to how they were
if ($badguy['revert']==1){
//	debug("Reverting stats");
	$badguy['creatureattack'] = $badguy['oldcreatureattack'];
	$badguy['creaturedefense'] = $badguy['oldcreaturedefense'];
	$badguy['revert'] = 0;
}
//Check to see how much damage the player has done
$damagedealt = $badguy['oldhitpoints'] - $badguy['creaturehealth'];
//See which target was being targeted, and apply damage
for ($i=1;$i<=6;$i++) {
	if ($badguy['target'.$i.'']['currenttarget'] == 1){
//		debug("Applying damage");
		$badguy['target'.$i.'']['hitpoints'] -= $damagedealt;
		// If the hitpoints are zero, show the message and adjust the stats according to the objectpref.
		if ($badguy['target'.$i.'']['hitpoints'] <= 0){
//			debug("Removing target and altering stats");
			//Output the target kill message
			$msg = $badguy['target'.$i.'']['killmsg'];
			output("%s`n",stripslashes($msg));
			//Adjust stats
			$badguy['creatureattack'] = ($badguy['creatureattack']*$badguy['target'.$i.'']['killatk']);
			$badguy['creaturedefense'] = ($badguy['creaturedefense']*$badguy['target'.$i.'']['killdef']);
			//Remove Hitpoints
			$removehitpoints = round(($badguy['creaturestartinghealth'] / 100) * $badguy['target'.$i.'']['killhp']);
			$badguy['creaturehealth'] -= $removehitpoints;
			if ($removehitpoints >= 1){
				output("`4Your enemy loses %s hitpoints!`0`n",$removehitpoints);
			}
		}
	}
}
//Reset all the targets so they're not being targeted anymore
for ($i=1;$i<=6;$i++) {
	$badguy['target'.$i.'']['currenttarget']=0;
}
//Output messages about the creature's target hitpoints
output("`n`0");
for ($i=1;$i<=6;$i++) {
	if ($badguy['target'.$i.'']['hitpoints'] >= 1){
		output("%s's %s: `b%s`b hitpoints remaining`n",$badguy['creaturename'],$badguy['target'.$i.'']['name'],$badguy['target'.$i.'']['hitpoints']);
	}
}
output("`n");
//debug
//debug("Debug from AI Script");
//debug($badguy);
return $args;
?>