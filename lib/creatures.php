<?php
//this is a setup where all the creatures are generated.
$creaturetats = [];
$creatureexp=14;
$creaturegold=36;
$maxlevel = getsetting('maxlevel',15);
for ($i=1;$i<=($maxlevel+4);$i++) {
	//apply algorithmic creature generation.
	$level=$i;
	$creaturehealth=($i*10)+($i-1)-round(sqrt($i-1));
	$creatureattack=1+($i-1)*2;
	$creaturedefense+=($i%2?1:2);
	if ($i>1) {
		$creatureexp+=round(10+1.5*log($i));
		$creaturegold+=31*($i<4?2:1);
		//give lower levels more gold
	}
	$creaturestats[$i] = [
		'creaturelevel' => $i,
		'creaturehealth' => $creaturehealth,
		'creatureattack' => $creatureattack,
		'creaturedefense' => $creaturedefense,
		'creatureexp' => $creatureexp,
		'creaturegold' => $creaturegold,
	];
}
function creature_stats($level){
	$creaturestats[$level];
}
