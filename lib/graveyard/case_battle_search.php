<?php
if ($session['user']['gravefights']<=0){
	output("`\$`bYour soul can bear no more torment in this afterlife.`b`0");
	$op="";
	httpset('op', "");
}else{
    require_once 'lib/battle/extended.php';
	require_once 'lib/creaturefunctions.php';

	suspend_companions("allowinshades", true);
	if (module_events("graveyard", getsetting("gravechance", 0)) != 0) {
		if (!checknavs()) {
			// If we're going back to the graveyard, make sure to reset
			// the special and the specialmisc
			$session['user']['specialinc'] = "";
			$session['user']['specialmisc'] = "";
			$skipgraveyardtext=true;
			$op = "";
			httpset("op", "");
		} else {
			page_footer();
		}
	} else {
		$session['user']['gravefights']--;
		$level = (int) $session['user']['level'];
        $battle = true;
 		$sql = "SELECT * FROM " . DB::prefix("creatures") . " WHERE graveyard = '1' and creaturelevel = '$level' ORDER BY rand(".e_rand().") LIMIT 1";
		$result = DB::query($sql);

        $badguy = lotgd_transform_creature($result->current());
		$badguy['creaturehealth'] += 50;
		$badguy['creaturemaxhealth'] = $badguy['creaturehealth'];

		// Make graveyard creatures easier.
		$badguy['creatureattack'] *= .7;
		$badguy['creaturedefense'] *= .7;

		// Add enemy
		$attackstack['enemies'][0] = $badguy;
		$attackstack['options']['type'] = 'graveyard';

		//no multifights currently, so this hook passes the badguy to modify
		$attackstack = modulehook('graveyardfight-start', $attackstack);

		$session['user']['badguy'] = createstring($attackstack);
	}
}
?>
