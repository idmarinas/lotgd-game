<?php
// translator ready
// addnews ready
// mail ready
function substitute($string, $extra=false, $extrarep=false) {
	global $badguy, $session;
	
	//that is only provoking stuff here...
	/*$search = array("%s",
		"%o",
		"%p",
		"%x",
		"%X",
		"%a",
		"%W",
		"%w",
		"{badguy}",
		"{goodguy}",
		"{weapon}",
		"{armor}",
		"{creatureweapon}",
		);
	// I had a player using `%orog in his name, and in the marriage buff it become `%%orog... %o got filtered... great idea... now making failsafe...
	*/

	$search = array(
		"{himher}",
		"{heshe}",
		"{hisher}",
		"{goodguyweapon}",
		"{badguyweapon}",
		"{goodguyarmor}",
		"{badguyname}",
		"{goodguyname}",
		"{badguy}",
		"{goodguy}",
		"{weapon}",
		"{armor}",
		"{creatureweapon}",
	);
	$replace = array(translate_inline($session['user']['sex']?"her":"him","buffs"),
		($session['user']['sex']?"she":"he"),
		($session['user']['sex']?"her":"his"),
		$session['user']['weapon'],
		$badguy['creatureweapon'],
		$session['user']['armor'],
		$badguy['creaturename'],
		"`^".$session['user']['name']."`^",
		$badguy['creaturename'],
		"`^".$session['user']['name']."`^",
		$session['user']['weapon'],
		$session['user']['armor'],
		$badguy['creatureweapon'],
		);

	if ($extra !== false && $extrarep !== false) {
		$search = array_merge($search, $extra);
		$replace = array_merge($replace, $extrarep);
	}

	$string = str_replace($search, $replace, $string);
	return $string;
}

function substitute_array($string, $extra=false, $extrarep=false){
	global $badguy, $session;
	// separate substitutions for gender items (makes 2 translations per
	// substition that uses these)
	$search = array(
		"{himher}",
		"{heshe}",
		"{hisher}",
		);

	$replace = array(
		($session['user']['sex']?"her":"him"),
		($session['user']['sex']?"she":"he"),
		($session['user']['sex']?"her":"his"),
		);
	$string = str_replace($search, $replace, $string);

	$search = array(
		"{goodguyweapon}",
		"{badguyweapon}",
		"{goodguyarmor}",
		"{badguyname}",
		"{goodguyname}",
		"{badguy}",
		"{goodguy}",
		"{weapon}",
		"{armor}",
		"{creatureweapon}",
	);

	$replace = array(
		$session['user']['weapon'],
		$badguy['creatureweapon'],
		$session['user']['armor'],
		$badguy['creaturename'],
		"`^".$session['user']['name']."`^",
		$badguy['creaturename'],
		"`^".$session['user']['name']."`^",
		$session['user']['weapon'],
		$session['user']['armor'],
		$badguy['creatureweapon'],
		);

	if ($extra !== false && $extrarep !== false) {
		$search = array_merge($search, $extra);
		$replace = array_merge($replace, $extrarep);
	}
	$replacement_array=array($string);

	// Do this the right way.
	// Iterate the string and find the replacements in order
	$length=strlen($replacement_array[0]);
	for ($x=0; $x<$length; $x++){
		foreach ($search as $skey=>$sval) {
			// Get the replacement for this value.
			$rval = $replace[$skey];
			if (substr($replacement_array[0],$x,strlen($sval))==$sval){
				array_push($replacement_array,$rval);
				$replacement_array[0] =
					substr($replacement_array[0],0,$x) . "%s" .
					substr($replacement_array[0],$x+strlen($sval));
				// Making a replacement changes the length, so we need to
				// restart at the beginning of the string.
				$x = -1;
				break;
			}
		}
	}
	return $replacement_array;
}
?>
