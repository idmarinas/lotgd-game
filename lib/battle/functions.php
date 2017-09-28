<?php

/**
 * Battle: attack of player
 *
 * @return boolean
 */
function battle_player_attacks()
{
	global $badguy, $enemies, $newenemies, $session, $creatureattack, $creatureatkmod, $beta;
	global $creaturedefmod, $adjustment, $defmod,$atkmod,$compatkmod, $compdefmod, $buffset, $atk, $def, $options;
	global $companions, $companion, $newcompanions, $roll, $countround, $needtostopfighting, $lotgdBattleContent;

	$break = false;
	$creaturedmg = $roll['creaturedmg'];
	if ($options['type'] != 'pvp') $creaturedmg = report_power_move($atk, $creaturedmg);

	if ($creaturedmg == 0)
    {
		$lotgdBattleContent['battlerounds'][$countround]['allied'][] = ['`4You try to hit `^%s`4 but `$MISS!`n', $badguy['creaturename']];
		process_dmgshield($buffset['dmgshield'], 0);
		process_lifetaps($buffset['lifetap'], 0);
	}
    else if ($creaturedmg < 0)
    {
		$lotgdBattleContent['battlerounds'][$countround]['allied'][] = ['`4You try to hit `^%s`4 but are `$RIPOSTED `4for `$%s`4 points of damage!`n', $badguy['creaturename'], (0-$creaturedmg)];
		$badguy['diddamage'] = 1;
		$session['user']['hitpoints'] += $creaturedmg;
		if ($session['user']['hitpoints'] <= 0)
        {
			$badguy['killedplayer'] = true;
			$count = 1;
			$break = true;
			$needtostopfighting = true;
		}
		process_dmgshield($buffset['dmgshield'],-$creaturedmg);
		process_lifetaps($buffset['lifetap'],$creaturedmg);
	}
    else
    {
		$lotgdBattleContent['battlerounds'][$countround]['allied'][] = ["`4You hit `^%s`4 for `^%s`4 points of damage!`n", $badguy['creaturename'], $creaturedmg];
		$badguy['creaturehealth']-=$creaturedmg;
		process_dmgshield($buffset['dmgshield'],-$creaturedmg);
		process_lifetaps($buffset['lifetap'],$creaturedmg);
	}

	if ($badguy['creaturehealth'] <= 0)
    {
		$badguy['dead'] = true;
		$badguy['istarget'] = false;
		$count = 1;
		$break = true;
	}

	return $break;
}

/**
 *  Battle: attack of badguy
 *
 * @return boolean
 */
function battle_badguy_attacks()
{
	global $badguy, $enemies, $newenemies, $session, $creatureattack, $creatureatkmod, $beta;
	global $creaturedefmod, $adjustment, $defmod, $atkmod, $compatkmod, $compdefmod, $buffset, $atk, $def, $options;
	global $companions, $companion, $newcompanions, $roll, $countround, $index, $defended, $needtostopfighting, $lotgdBattleContent;

	$break = false;
	$selfdmg = $roll['selfdmg'];
	if ($badguy['creaturehealth'] <= 0 && $session['user']['hitpoints'] <=0 )
    {
		$creaturedmg = 0;
		$selfdmg = 0;
		if ($badguy['creaturehealth'] <= 0)
        {
			$badguy['dead'] = true;
			$badguy['istarget'] = false;
			$count = 1;
			$needtostopfighting = true;
			$break = true;
		}
		$newenemies[$index] = $badguy;
		$newcompanions = $companions;
		$break = true;
	}
    else
    {
		if ($badguy['creaturehealth']>0 && $session['user']['hitpoints']>0 && $badguy['istarget'])
        {
			if (is_array($companions))
            {
                foreach ($companions as $name => $companion)
                {
                    if ($companion['hitpoints'] > 0)
                    {
                        $buffer = report_companion_move($companion, "defend");
                        if ($buffer !== false)
                        {
                            $newcompanions[$name] = $buffer;
                            unset($buffer);
                        }
                        else unset($companion, $newcompanions[$name]);
                    }
                    else $newcompanions[$name] = $companion;
                }
			}
		}
        else $newcompanions = $companions;

		$companions = $newcompanions;
		if ($defended == false)
        {
			if ($selfdmg == 0)
            {
				$lotgdBattleContent['battlerounds'][$countround]['enemy'][] = ["`^%s`4 tries to hit you but `^MISSES!`n", $badguy['creaturename']];
				process_dmgshield($buffset['dmgshield'], 0);
				process_lifetaps($buffset['lifetap'], 0);
			}
            else if ($selfdmg < 0)
            {
				$lotgdBattleContent['battlerounds'][$countround]['enemy'][] = ["`^%s`4 tries to hit you but you `^RIPOSTE`4 for `^%s`4 points of damage!`n", $badguy['creaturename'], (0-$selfdmg)];
				$badguy['creaturehealth']+=$selfdmg;
				process_lifetaps($buffset['lifetap'], -$selfdmg);
				process_dmgshield($buffset['dmgshield'], $selfdmg);
			}
            else
            {
				$lotgdBattleContent['battlerounds'][$countround]['enemy'][] = ['`^%s`4 hits you for `$%s`4 points of damage!`n', $badguy['creaturename'], $selfdmg];
				$session['user']['hitpoints']-=$selfdmg;
				if ($session['user']['hitpoints'] <= 0)
                {
					$badguy['killedplayer'] = true;
					$count = 1;
				}
				process_dmgshield($buffset['dmgshield'], $selfdmg);
				process_lifetaps($buffset['lifetap'], -$selfdmg);
				$badguy['diddamage'] = 1;
			}
		}
		if ($badguy['creaturehealth'] <= 0)
        {
			$badguy['dead'] = true;
			$badguy['istarget'] = false;
			$count = 1;
			$break = true;
		}
	}

	return $break;
}

/**
 * Function to show result of victory battle
 *
 * @param array $enemies
 * @param boolean $denyflawless
 * @param boolean $forest
 *
 * @return void
 */
function battlevictory($enemies, $denyflawless = false, $forest = true)
{
	global $session, $options, $lotgdBattleContent, $expbonus, $exp, $enemies, $deathoverlord;

	$diddamage = false;
	$creaturelevel = 0;
	$gold = 0;
	$exp = 0;
	$expbonus = 0;
	$count = count($enemies);
	$totalbackup = 0;

	foreach ($enemies as $index => $badguy)
    {
		if (getsetting('dropmingold', 0)) { $badguy['creaturegold'] = e_rand(round($badguy['creaturegold']/4), round(3*$badguy['creaturegold']/4)); }
        else { $badguy['creaturegold'] = e_rand(0, $badguy['creaturegold']); }

		$gold += $badguy['creaturegold'];

		if(isset($badguy['creaturelose'])) $lotgdBattleContent['battleend'][] = substitute_array($badguy['creaturelose'].'`n');

		if ($forest === true) $lotgdBattleContent['battleend'][] = ['`b`$You have slain %s!`0`b`n', $badguy['creaturename']];
        elseif ($forest === false) $lotgdBattleContent['battleend'][] = ['`b`$You have tormented %s!`0`b`n', $badguy['creaturename']];

		// If any creature did damage, we have no flawless fight. Easy as that.
		if (isset($badguy['diddamage']) && $badguy['diddamage'] == 1) { $diddamage = true; }
		$creaturelevel = max($creaturelevel, $badguy['creaturelevel']);
		if (!$denyflawless && isset($badguy['denyflawless']) && $badguy['denyflawless'] > '') { $denyflawless = $badguy['denyflawless']; }

		$expbonus += round(($badguy['creatureexp'] * (1 + .25 * ($badguy['creaturelevel']-$session['user']['level']))) - $badguy['creatureexp'],0);
	}

	$multibonus = $count>1?1:0;
	$expbonus += $session['user']['dragonkills'] * $session['user']['level'] * $multibonus;
	$totalexp = 0;
	foreach ($options['experience'] as $index => $experience) { $totalexp += $experience; }

	// We now have the total experience which should have been gained during the fight.
	// Now we will calculate the average exp per enemy.
	$exp = round($totalexp / $count);
	$gold = e_rand(round($gold/$count), round($gold/$count)*round(($count+1)*pow(1.2, $count-1), 0));
	$expbonus = round($expbonus/$count, 0);

	if ($gold && $forest === true)//-- Only in forest
	{
		$lotgdBattleContent['battleend'][] = ['`#You receive `^%s`# gold!`n', $gold];
        $session['user']['gold'] += $gold;
		debuglog('received gold for slaying a monster.', false, false, 'forestwin', $badguy['creaturegold']);
	}
	// No gem hunters allowed!
	$args = modulehook('alter-gemchance', ['chance' => getsetting('forestgemchance', 25)]);
	$gemchances = $args['chance'];
	if ($session['user']['level'] < getsetting('maxlevel', 15) && e_rand(1,$gemchances) == 1 && $forest === true)//-- Only find in forest
    {
		$lotgdBattleContent['battleend'][] = ["`&You find A GEM!`n`#"];
		$session['user']['gems']++;
		debuglog("found gem when slaying a monster.",false,false,"forestwingem",1);
	}

    //-- Process exp/favor
    if ($forest === true) battlegainexperienceforest();
    elseif ($forest === false) battlegainexperiencegraveyard();

	// Increase the level for each enemy by one half, so flawless fights can be achieved for
	// fighting multiple low-level critters
	if (!$creaturelevel) { $creaturelevel = $badguy['creaturelevel']; }
    else { $creaturelevel += (0.5 * ($count - 1)); }

    //-- Perfect battle
	if (!$diddamage)
    {
		$lotgdBattleContent['battleend'][] = "`c`b`&~~ Flawless Fight! ~~`0`b`c";
		if ($denyflawless) { $lotgdBattleContent['battleend'][] = "`c`\${$denyflawless}`0`c"; }
		elseif ($session['user']['level'] <= $creaturelevel)
		{
			if (is_module_active('staminasystem') && $forest)//-- Only When active stamina system and is forest
			{
				require_once 'modules/staminasystem/lib/lib.php';

				$lotgdBattleContent['battleend'][] = '`c`b`$You receive some stamina!`0`b`c`n';
				addstamina(25000);
			}
			else if (! $forest)//-- Only when is a Graveyard
            {
                $lotgdBattleContent['battleend'][] = '`c`b`$You receive an extra torment!`0`b`c`n';
				$session['user']['gravefights']++;
            }
            else//-- Other
			{
				$lotgdBattleContent['battleend'][] = '`c`b`$You receive an extra turn!`0`b`c`n';
				$session['user']['turns']++;
			}
		}
		else
		{
			if (is_module_active('staminasystem') && $forest) $lotgdBattleContent['battleend'][] = '`c`$A more difficult fight would have yielded some stamina.`0`c`n';
            elseif (! $forest) $lotgdBattleContent['battleend'][] = '`c`$A more difficult fight would have yielded an extra torment.`0`c`n';
			else $lotgdBattleContent['battleend'][] = '`c`$A more difficult fight would have yielded an extra turn.`0`c`n';
		}
	}

	if ($session['user']['hitpoints'] <= 0)
	{
		$lotgdBattleContent['battleend'][] = 'With your dying breath you spy a small stand of mushrooms off to the side.';
		$lotgdBattleContent['battleend'][] = 'You recognize them as some of the ones that the healer had drying in the hut and taking a chance, cram a handful into your mouth.';
		$lotgdBattleContent['battleend'][] = 'Even raw they have some restorative properties.`n';
		$session['user']['hitpoints'] = 1;
	}
}

/**
 * Process win experiencie in battle win in forest
 *
 * @return void
 */
function battlegainexperienceforest()
{
    global $lotgdBattleContent, $options, $enemies, $session, $expbonus, $exp;

	$count = count($enemies);

    if (getsetting('instantexp', false) == true)
    {
		$expgained = 0;
		foreach ($options['experiencegained'] as $index => $experience) { $expgained += $experience; }

		$diff = $expgained - $exp;
		$expbonus += $diff;
		if (floor($exp + $expbonus) < 0) { $expbonus = -$exp+1; }

		if ($expbonus > 0)
		{
			$expbonus = round($expbonus * pow(1+(getsetting('addexp', 5)/100), $count-1),0);
			$lotgdBattleContent['battleend'][] = ["`#***Because of the difficult nature of this fight, you are awarded an additional `^%s`# experience! `n",$expbonus];
		}
        elseif ($expbonus < 0)
        {
			$lotgdBattleContent['battleend'][] = ["`#***Because of the simplistic nature of this fight, you are penalized `^%s`# experience! `n",abs($expbonus)];
		}
		if (count($enemies) > 1)
        {
			$lotgdBattleContent['battleend'][] = ["During this fight you received `^%s`# total experience!`n`0",$exp+$expbonus];
		}
		$session['user']['experience'] += $expbonus;
	}
    else
    {
		if (floor($exp + $expbonus) < 0) { $expbonus = -$exp+1; }
		if ($expbonus > 0)
        {
			$expbonus = round($expbonus * pow(1+(getsetting('addexp', 5)/100), $count-1),0);
			$lotgdBattleContent['battleend'][] = ["`#***Because of the difficult nature of this fight, you are awarded an additional `^%s`# experience! `n(%s + %s = %s) ",$expbonus,$exp,abs($expbonus), $exp+$expbonus];
		}
        elseif ($expbonus < 0)
        {
			$lotgdBattleContent['battleend'][] = ["`#***Because of the simplistic nature of this fight, you are penalized `^%s`# experience! `n(%s - %s = %s) ",abs($expbonus),$exp,abs($expbonus), $exp+$expbonus];
		}

        $totalExp = ($exp+$expbonus);
        //-- Only show if win Exp
        if ($totalExp)
        {
            $lotgdBattleContent['battleend'][] = ['You receive `^%s`# total experience!`n`0', $totalExp];
            $session['user']['experience'] += $totalExp;
        }
	}
}

/**
 * Process win experiencie in battle win in graveyard
 *
 * @return void
 */
function battlegainexperiencegraveyard()
{
    global $lotgdBattleContent, $options, $session, $expbonus, $exp, $deathoverlord;

    if (floor($exp + $expbonus) < 0) { $expbonus = -$exp+1; }

	if ($expbonus > 0)
    {
		$expbonus = round($expbonus * pow(1+(getsetting('addexp', 5)/100), $count-1),0);
		$lotgdBattleContent['battleend'][] = ["`#***Because of the difficult nature of this fight, you are awarded an additional `^%s`# favor! `n(%s + %s = %s) ",$expbonus,$exp,abs($expbonus), $exp+$expbonus];
	}
    elseif ($expbonus < 0)
    {
		$lotgdBattleContent['battleend'][] = ["`#***Because of the simplistic nature of this fight, you are penalized `^%s`# favor! `n(%s - %s = %s) ",abs($expbonus),$exp,abs($expbonus), $exp+$expbonus];
	}

    $totalExp = ($exp+$expbonus);
    //-- Only show if win Exp/favor
    if ($totalExp)
    {
        $lotgdBattleContent['battleend'][] = ['`#You receive `^%s`# favor with `$%s`#!`n`0', $totalExp, $deathoverlord];
        $session['user']['deathpower'] += $totalExp;
    }
}

/**
 * Function to show result of defeated battle
 *
 * @param array $enemies
 * @param string|false $where
 * @param boolean $forest
 * @param boolean $candie Can die in battle?
 * @param boolean $lostexp Lost exp when die in battle?
 * @param boolean $lostgold Lost gold when die in battle?
 * @return void
 */
function battledefeat($enemies, $where = 'in the forest', $forest = true, $candie = true, $lostexp = true, $lostgold = true)
{
    global $session, $lotgdBattleContent;

    require_once 'lib/deathmessage.php';
    require_once 'lib/taunt.php';

	$percent = getsetting('forestexploss', 10);
	$killer = false;
	foreach ($enemies as $index => $badguy)
    {
		if (isset($badguy['killedplayer']) && $badguy['killedplayer'] == true) { $killer = $badguy; }
		if (isset($badguy['creaturewin']) && $badguy['creaturewin'] > '') { $lotgdBattleContent['battleend'][] = substitute_array("`b`&{$badguy['creaturewin']}`0`b`n"); }
	}

    if ($killer)
    {
        $lotgdBattleContent['battleend'][] = ["`&`bYou have been defeated by `%%s`&!`b`n", $killer['creaturename']];
    }

	if (is_string($where))
    {
        $where = translate_inline($where);

        $deathmessage = select_deathmessage($forest, ['{where}'], [$where]);
        if ($deathmessage['taunt'] == 1)
        {
            $taunt = '`n' . select_taunt();
        }
        else { $taunt = ''; }

        addnews('%s `b`i%s`i`b', $deathmessage['deathmessage'], $taunt);
    }

    if ($lostgold)
    {
        debuglog("lost gold when they were slain $where", false, false, 'forestlose', -$session['user']['gold']);
        $session['user']['gold'] = 0;

        $lotgdBattleContent['battleend'][] = '`4All gold on hand has been lost!`n';
    }

    if ($lostexp)
    {
        $session['user']['experience'] = round($session['user']['experience']*(1-($percent/100)),0);

        $lotgdBattleContent['battleend'][] = ['`4%s %% of experience has been lost!`b`n', $percent];
    }

    if ($candie)
    {
        tlschema('nav');
	    addnav('Daily news', 'news.php');
        tlschema();

        $session['user']['alive'] = false;
	    $session['user']['hitpoints'] = 0;
        $lotgdBattleContent['battleend'][] = 'You may begin fighting again tomorrow.';
    }
    elseif ($forest === false)
    {
		tlschema('nav');
		addnav("G?Return to the Graveyard","graveyard.php");
		tlschema();

		$session['user']['gravefights'] = 0;
        $lotgdBattleContent['battleend'][] = 'You may not torment any more souls today.';
    }
}

/**
 * Show result of battle
 *
 * @param array $lotgdBattleContent
 * @return void
 */
function battleshowresults(array $lotgdBattleContent)
{
    global $lotgd_tpl;

    tlschema('battle');
    output_notl($lotgd_tpl->renderThemeTemplate('battle/battle.twig', $lotgdBattleContent), true);
	tlschema();
}
