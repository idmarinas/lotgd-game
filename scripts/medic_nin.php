<?php

//script file for use with the creatureaiscript
//mind that this is PURE PHP+lotgd, you can do anything nasty in here!

global $badguy,$session;
if(!isset($badguy['maxhealth'])) {
	$badguy['maxhealth'] = $badguy['creaturehealth'];
	//can heal up to the amount of DKs the user has
	$badguy['healpoints'] = $session['user']['dragonkills'];
} 
//activates when the creature has less than 60% of his initial HP AND has healpoints left

if($badguy['creaturehealth'] < $badguy['maxhealth']*0.60 && $badguy['healpoints']>0) {
  //heal randomly up to 33% of his maxhp
  $heal = min($badguy['healpoints'],e_rand(1,$badguy['maxhealth']/3));
  $badguy['healpoints']-=$heal;
  $badguy['creaturehealth'] += $heal;
  output("`!%s`# heals for `$%s hitpoints`#.`n", $heal);
}

?>
