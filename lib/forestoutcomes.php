<?php
// addnews ready
// translator ready
// mail ready
require_once 'lib/taunt.php';
require_once 'lib/deathmessage.php';
require_once 'lib/e_rand.php';
require_once 'lib/pageparts.php';
require_once 'lib/output.php';
require_once 'lib/nav.php';
require_once 'lib/playerfunctions.php';
require_once 'lib/creaturefunctions.php';

function forestvictory($enemies,$denyflawless=false)
{
	global $session, $options;

	tlschema('battle');

	$diddamage = false;
	$creaturelevel = 0;
	$gold = 0;
	$exp = 0;
	$expbonus = 0;
	$count = 0;
	$totalbackup = 0;
	foreach ($enemies as $badguy) {
		if (getsetting("dropmingold",0)){
			$badguy['creaturegold']= e_rand(round($badguy['creaturegold']/4), round(3*$badguy['creaturegold']/4));
		}else{
			$badguy['creaturegold']=e_rand(0,$badguy['creaturegold']);
		}
		$gold += $badguy['creaturegold'];
		if (isset($badguy['creaturelose'])) {
			$msg = translate_inline($badguy['creaturelose'],"battle");
			output_notl("`b`&%s`0`b`n",$msg);
		}
		output("`b`\$You have slain %s!`0`b`n",$badguy['creaturename']);
		$count++;
		// If any creature did damage, we have no flawless fight. Easy as that.
		if ($badguy['diddamage'] == 1) {
			$diddamage = true;
		}
		$creaturelevel = max($creaturelevel, $badguy['creaturelevel']);
		if (!$denyflawless && isset($badguy['denyflawless']) && $badguy['denyflawless']>"") {
			$denyflawless = $badguy['denyflawless'];
		}
		$expbonus += round(($badguy['creatureexp'] * (1 + .25 * ($badguy['creaturelevel']-$session['user']['level']))) - $badguy['creatureexp'],0);
	}
	$multibonus = $count>1?1:0;
	$expbonus += $session['user']['dragonkills'] * $session['user']['level'] * $multibonus;
	$totalexp = 0;
	foreach ($options['experience'] as $experience) {
		$totalexp += $experience;
	}
	// We now have the total experience which should have been gained during the fight.
	// Now we will calculate the average exp per enemy.
	$exp = round($totalexp / $count);
	$gold = e_rand(round($gold/$count),round($gold),0);
	$expbonus = round ($expbonus/$count,0);

	if ($gold) {
		output("`#You receive `^%s`# gold!`n",$gold);
		debuglog("received gold for slaying a monster.",false,false,"forestwin",$badguy['creaturegold']);
	}
	// No gem hunters allowed!
	$args = modulehook("alter-gemchance", array("chance"=>getsetting("forestgemchance", 25)));
	$gemchances = $args['chance'];
	if ($session['user']['level'] < getsetting('maxlevel',15) && e_rand(1,$gemchances) == 1) {
		output("`&You find A GEM!`n`#");
		$session['user']['gems']++;
		debuglog("found gem when slaying a monster.",false,false,"forestwingem",1);
	}
	if (getsetting("instantexp",false) == true) {
		$expgained = 0;
		foreach ($options['experiencegained'] as $experience) {
			$expgained += $experience;
		}

		$diff = $expgained - $exp;
		$expbonus += $diff;
		if (floor($exp + $expbonus) < 0) {
			$expbonus = -$exp+1;
		}
		if ($expbonus>0){
			$expbonus = round($expbonus * pow(1+(getsetting("addexp", 5)/100), $count-1),0);
			output("`#***Because of the difficult nature of this fight, you are awarded an additional `^%s`# experience! `n",$expbonus);
		} elseif ($expbonus<0){
			output("`#***Because of the simplistic nature of this fight, you are penalized `^%s`# experience! `n",abs($expbonus));
		}
		if (count($enemies) > 1) {
			output("During this fight you received `^%s`# total experience!`n`0",$exp+$expbonus);
		}
		$session['user']['experience']+=$expbonus;
	} else {
		if (floor($exp + $expbonus) < 0) {
			$expbonus = -$exp+1;
		}
		if ($expbonus>0){
			$expbonus = round($expbonus * pow(1+(getsetting("addexp", 5)/100), $count-1),0);
			output("`#***Because of the difficult nature of this fight, you are awarded an additional `^%s`# experience! `n(%s + %s = %s) ",$expbonus,$exp,abs($expbonus),$exp+$expbonus);
		} elseif ($expbonus<0){
			output("`#***Because of the simplistic nature of this fight, you are penalized `^%s`# experience! `n(%s - %s = %s) ",abs($expbonus),$exp,abs($expbonus),$exp+$expbonus);
		}
		output("You receive `^%s`# total experience!`n`0",$exp+$expbonus);
		$session['user']['experience']+=($exp+$expbonus);
	}
	$session['user']['gold']+=$gold;
	// Increase the level for each enemy by one half, so flawless fights can be achieved for
	// fighting multiple low-level critters
	if (!$creaturelevel)
		$creaturelevel = $badguy['creaturelevel'];
	else
		$creaturelevel+=(0.5*($count-1));

	if (!$diddamage) {
		output("`c`b`&~~ Flawless Fight! ~~`0`b`c");
		if ($denyflawless){
			output("`c`\$%s`0`c", translate_inline($denyflawless));
		}elseif ($session['user']['level']<=$creaturelevel){
			if (is_module_active('staminasystem'))
			{
				require_once 'modules/staminasystem/lib/lib.php';

				output("`c`b`\$You receive some stamina!`0`b`c`n");
				addstamina(25000);
			}
			else
			{
				output("`c`b`\$You receive an extra turn!`0`b`c`n");
				$session['user']['turns']++;
			}
		}else{
			if (is_module_active('staminasystem')) output("`c`\$A more difficult fight would have yielded some stamina.`0`c`n");
			else output("`c`\$A more difficult fight would have yielded an extra turn.`0`c`n");
		}
	}
	if ($session['user']['hitpoints'] <= 0) {
		output("With your dying breath you spy a small stand of mushrooms off to the side.");
		output("You recognize them as some of the ones that the healer had drying in the hut and taking a chance, cram a handful into your mouth.");
		output("Even raw they have some restorative properties.`n");
		$session['user']['hitpoints'] = 1;
	}

	tlschema();
}

function forestdefeat($enemies,$where="in the forest")
{
	global $session;

	tlschema('battle');

	$percent=getsetting('forestexploss',10);
	addnav("Daily news","news.php");
	$names = array();
	$killer = false;
	foreach ($enemies as $badguy) {
		$names[] = $badguy['creaturename'];
		if (isset($badguy['killedplayer']) && $badguy['killedplayer'] == true) $killer = $badguy['creaturename'];
		if (isset($badguy['creaturewin']) && $badguy['creaturewin'] > "") {
			$msg = translate_inline($badguy['creaturewin'],"battle");
			output_notl("`b`&%s`0`b`n",$msg);
		}
	}
	if (count($names) > 1) $lastname = array_pop($names);
	$enemystring = join(", ", $names);
	$and = translate_inline("and");
	if (isset($lastname) && $lastname > "") $enemystring = "$enemystring $and $lastname";
	$taunt = select_taunt_array();
	//leave it for now, it's tricky
	if (is_array($where)) {
		$where=sprintf_translate($where);
	} else {
		$where=translate_inline($where);
	}
	$deathmessage=select_deathmessage_array(true,array("{where}"),array($where));
	if ($deathmessage['taunt']==1) {
		addnews("%s`n%s",$deathmessage['deathmessage'],$taunt);
	} else {
		addnews("%s",$deathmessage['deathmessage']);
	}
	$session['user']['alive']=false;
	debuglog("lost gold when they were slain $where",false,false,"forestlose",-$session['user']['gold']);
	$session['user']['gold']=0;
	$session['user']['hitpoints']=0;
	$session['user']['experience']=round($session['user']['experience']*(1-($percent/100)),0);
	output("`4All gold on hand has been lost!`n");
	output("`4%s %% of experience has been lost!`b`n",$percent);
	output("You may begin fighting again tomorrow.");

	tlschema();

	page_footer();
}

/**
 * Buff creature for optimiza to character stats
 *
 * @var array $badguy Information of creature
 * @var string $hook Hook to activate when buff badguy
 */
function buffbadguy($badguy, $hook = 'buffbadguy')
{
	global $session;

    // This will save us a lot of trouble when going through
	static $dk = false;	// this function more than once...

	if ($dk === false) $dk = get_player_dragonkillmod();

    $expflux = round($badguy['creatureexp']/10,0);
	$expflux = e_rand(-$expflux,$expflux);
	$badguy['creatureexp']+=$expflux;


	if (! isset($badguy['creaturespeed'])) $badguy['creaturespeed'] = 2.5;

    $creatureattr = get_creature_stats($dk);

    //-- Bonus to atributes
    $badguy['creaturestrbonus'] = $creatureattr['str'];
    $badguy['creaturedexbonus'] = $creatureattr['dex'];
    $badguy['creatureconbonus'] = $creatureattr['con'];
    $badguy['creatureintbonus'] = $creatureattr['int'];
    $badguy['creaturewisbonus'] = $creatureattr['wis'];

    //-- Total atributes of creature
    $badguy['creaturestr'] = $creatureattr['str'] + 10;
    $badguy['creaturedex'] = $creatureattr['dex'] + 10;
    $badguy['creaturecon'] = $creatureattr['con'] + 10;
    $badguy['creatureint'] = $creatureattr['int'] + 10;
    $badguy['creaturewis'] = $creatureattr['wis'] + 10;

    //-- Attack, defense, health from attributes
    $badguy['creatureattackattrs'] = get_creature_attack($creatureattr);
	$badguy['creaturedefenseattrs'] = get_creature_defense($creatureattr);
	$badguy['creaturehealthattrs'] = get_creature_hitpoints($creatureattr);
	$badguy['creaturespeedattrs'] = get_creature_speed($creatureattr);

	//-- Sum bonus
	$badguy['creatureattack'] += $badguy['creatureattackattrs'];
	$badguy['creaturedefense'] += $badguy['creaturedefenseattrs'];
	$badguy['creaturehealth'] += $badguy['creaturehealthattrs'];
	$badguy['creaturespeed'] += $badguy['creaturespeedattrs'];

	if (getsetting('disablebonuses', 1))
    {
		//adapting flux as for people with many DKs they will just bathe in gold....
		$base = 30 - min(20,round(sqrt($session['user']['dragonkills'])/2));
		$base /=1000;
		$bonus = 1 + $base*($badguy['creatureattackattrs']+$badguy['creaturedefenseattrs']) + .001*$badguy['creaturehealthattrs'];
		$badguy['creaturegold'] = round($badguy['creaturegold']*$bonus, 0);
		$badguy['creatureexp'] = round($badguy['creatureexp']*$bonus, 0);
	}

	$badguy = modulehook("creatureencounter",$badguy);
	debug("DEBUG: Basic information: Atk: {$badguy['creatureattack']}, Def: {$badguy['creaturedefense']}, HP: {$badguy['creaturehealth']}");
	debug("DEBUG: $dk modification points total for attributes.");
	debug("DEBUG: +{$badguy['creaturestrbonus']} allocated to strength.");
	debug("DEBUG: +{$badguy['creaturedexbonus']} allocated to dexterity.");
	debug("DEBUG: +{$badguy['creatureconbonus']} allocated to constitution.");
	debug("DEBUG: +{$badguy['creatureintbonus']} allocated to intelligence.");
	debug("DEBUG: +{$badguy['creaturewisbonus']} allocated to wisdom.");
	debug("DEBUG: +{$badguy['creatureattackattrs']} modification of attack.");
	debug("DEBUG: +{$badguy['creaturedefenseattrs']} modification of defense.");
	debug("DEBUG: +{$badguy['creaturespeedattrs']} modification of speed.");
	debug("DEBUG: +{$badguy['creaturehealthattrs']} modification of hitpoints.");

	return modulehook($hook, $badguy);
}
?>
