<?php
// translator ready
// addnews ready
// mail ready
require_once 'lib/bell_rand.php';
require_once 'common.php';
require_once 'lib/http.php';
require_once 'lib/substitute.php';
require_once 'lib/battle/functions.php';
require_once 'lib/battle/buffs.php';
require_once 'lib/battle/skills.php';
require_once 'lib/battle/extended.php';
require_once 'lib/buffs.php';

//just in case we're called from within a function.Yuck is this ugly.
global $badguy, $enemies, $newenemies, $session, $creatureattack, $creatureatkmod, $beta;
global $creaturedefmod, $adjustment, $defmod, $atkmod, $compdefmod, $compatkmod, $buffset, $atk, $def, $options;
global $companions, $companion, $newcompanions, $countround, $defended, $needtostopfighting, $roll, $lotgdBattleContent, $content;

tlschema('battle');

$newcompanions = [];
$lotgdBattleContent = [
    'msg' => [],
    'encounter' => [],
	'battlebars' => [],
	'battlestart' => [],
    'battlerounds' => [],
	'battleend' => []
];
$content = &$lotgdBattleContent;

$attackstack = @unserialize($session['user']['badguy']);
if (isset($attackstack['enemies'])) $enemies = $attackstack['enemies'];
if (isset($attackstack['options'])) $options = $attackstack['options'];

// Make the new battle script compatible with old, single enemy fights.
if (isset($attackstack['creaturename']) && $attackstack['creaturename'] > '')
{
	$safe = $attackstack;
	$enemies = [];
	$enemies[0] = $safe;
	unset($safe);
}
elseif (isset($attackstack[0]['creaturename']) && $attackstack['creaturename'] > '') $enemies=$attackstack;

if (!isset($options) && isset($enemies[0]['type'])) $options['type'] = $enemies[0]['type'];

$options = prepare_fight($options);

$roundcounter = 0;
$adjustment = 1;

$count = 1;
$auto = httpget('auto');
if ($auto == 'full') { $count = -1; }
else if ($auto == 'five') { $count = 5; }
else if ($auto == 'ten') { $count = 10; }

$enemycounter = count($enemies);
$enemies = autosettarget($enemies);

$op = httpget("op");
$skill = httpget("skill");
$l = httpget("l");
$newtarget = httpget('newtarget');
if ($newtarget != '') $op = 'newtarget';
//if (!$targetted) $op = "newtarget";

if ($op == 'fight') apply_skill($skill, $l);
else if ($op == 'newtarget')
{
	foreach ($enemies as $index => $badguy)
    {
		if ($index == (int) $newtarget)
        {
			if (!isset($badguy['cannotbetarget']) || $badguy['cannotbetarget'] === false) $enemies[$index]['istarget'] = 1;
			else
            {
				if (is_array($badguy['cannotbetarget'])) $lotgdBattleContent['msg'][] = substitute($msg);
                else
                {
                    $msg = $badguy['cannotbetarget'];
					if ($badguy['cannotbetarget'] === true) $msg = "{badguy} cannot be selected as target.";

					$lotgdBattleContent['msg'][] = substitute_array("`5{$msg}`0`n");
				}
			}
		}
        else $enemies[$index]['istarget'] = 0;
	}
}

$victory = false;
$defeat = false;

if ($enemycounter > 0)
{
	modulehook('battle', $enemies);
	foreach ($enemies as $index=>$badguy)
    {
		if ($badguy['creaturehealth'] > 0 && $session['user']['hitpoints'] > 0)
        {
            $lotgdBattleContent['encounter'][] = ['`@You have encountered `^%s`@ which lunges at you with `%%s`@!`0`n', $badguy['creaturename'], $badguy['creatureweapon'] ];
		}
	}

	$data = prepare_data_battlebars($enemies);
	$lotgdBattleContent['battlebars']['start'] = [
		'player' => $data['user'],
		'companions' => $data['companions'],
		'enemies' => $data['enemies']
	];
	unset($data);
}

suspend_buffs((($options['type'] == 'pvp')?'allowinpvp':false));
suspend_companions((($options['type'] == 'pvp')?'allowinpvp':false));

// Now that the bufflist is sane, see if we should add in the bodyguard.
$inn = (int) httpget('inn');
if ($options['type'] == 'pvp' && $inn == 1) apply_bodyguard($badguy['bodyguardlevel']);

$surprised = false;
if ($op != 'run' && $op != 'fight' && $op != 'newtarget')
{
	if (count($enemies) > 1)
    {
		$surprised = true;
		$lotgdBattleContent['battlerounds'][$countround]['enemy'][] = '`b`^YOUR ENEMIES`$ surprise you and get the first round of attack!`0`b`n`n';
	}
    else
    {
		// Let's try this instead.Biggest change is that it adds possibility of
		// being surprised to all fights.
		if (!array_key_exists('didsurprise', $options) || !$options['didsurprise'])
        {
			// By default, surprise is 50/50
			$surprised = e_rand(0, 1) ? true : false;
			// Now, adjust for slum/thrill
			$type = httpget('type');
			if ($type == 'slum' || $type == 'thrill')
            {
				$num = e_rand(0, 2);
				$surprised = true;
				if ($type == 'slum' && $num != 2) $surprised = false;
				if (($type == 'thrill' || $type == 'suicide') && $num == 2) $surprised = false;
			}

			if (!$surprised) $lotgdBattleContent['battlestart'][] = '`b`$Your skill allows you to get the first attack!`0`b`n`n';
			else
            {
				if ($options['type'] == 'pvp') $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = ["`b`^%s`\$'s skill allows them to get the first round of attack!`0`b`n`n", $badguy['creaturename']];
                else $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = ['`b`^%s`$ surprises you and gets the first round of attack!`0`b`n`n', $badguy['creaturename']];

				$op = 'run';
			}
			$options['didsurprise'] = 1;
		}
	}
}
$needtostopfighting = false;
if ($op != 'newtarget')
{
	$countround = 0;
	// Run through as many rounds as needed.
	do
    {
		//we need to restore and calculate here to reflect changes that happen throughout the course of multiple rounds.
		modulehook('startofround-prebuffs'); //-- For Stamina System
		restore_buff_fields();
		calculate_buff_fields();
		prepare_companions();
		$newenemies = [];
		// Run the beginning of round buffs (this also calculates all modifiers)
		foreach ($enemies as $index => $badguy)
        {
			if ($badguy['dead'] == false && $badguy['creaturehealth'] > 0)
            {
                if (! isset($badguy['alwaysattacks']) || $badguy['alwaysattacks'] != true) $roundcounter++;

				if (($roundcounter > $options['maxattacks']) && $badguy['istarget'] == false) $newcompanions = $companions;
				else
                {
					$buffset = activate_buffs('roundstart');
					if ($badguy['creaturehealth'] <= 0 || $session['user']['hitpoints'] <= 0)
                    {
						$creaturedmg = 0;
						$selfdmg = 0;
						if ($badguy['creaturehealth'] <= 0)
                        {
							$badguy['dead'] = true;
							$badguy['istarget'] = false;
							$count = 1;
							$needtostopfighting = true;
						}
						if ($session['user']['hitpoints'] <= 0)
                        {
							$count = 1;
							$needtostopfighting = true;
						}
						$newenemies[$index] = $badguy;
						$newcompanions = $companions;
						// No break here. It would break the foreach statement.
					}
                    else
                    {
						$creaturedefmod = $buffset['badguydefmod'];
						$creatureatkmod = $buffset['badguyatkmod'];
						$atkmod = $buffset['atkmod'];
						$defmod = $buffset['defmod'];
						$compatkmod = $buffset['compatkmod'];
						$compdefmod = $buffset['compdefmod'];
						if ($badguy['creaturehealth'] > 0 && $session['user']['hitpoints'] > 0 && $badguy['istarget'])
						{
							if (is_array($companions))
							{
								$newcompanions = [];
								foreach ($companions as $name => $companion)
								{
									if ($companion['hitpoints'] > 0)
									{
										$buffer = report_companion_move($companion, 'heal');
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

						if ($op == 'fight' || $op == 'run' || $surprised)
                        {
							// Grab an initial roll.
							$roll = rolldamage();
							if ($op == 'fight' && !$surprised)
                            {
								$ggchancetodouble = $session['user']['dragonkills'];
								$bgchancetodouble = $session['user']['dragonkills'];

								if ($badguy['creaturehealth'] > 0 && $session['user']['hitpoints'] > 0)
								{
									$buffset = activate_buffs('offense');

									if ($badguy['creaturehealth'] > 0 && $session['user']['hitpoints'] > 0 && $badguy['istarget'] && is_array($companions))
                                    {
										if (is_array($companions))
										{
											$newcompanions = [];
											foreach ($companions as $name=>$companion)
											{
												if ($companion['hitpoints'] > 0)
												{
													$buffer = report_companion_move($companion, 'magic');
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
									if ($badguy['creaturehealth'] <= 0 || $session['user']['hitpoints'] <= 0)
                                    {
										$creaturedmg = 0;
										$selfdmg = 0;
										if ($badguy['creaturehealth'] <= 0)
                                        {
											$badguy['dead'] = true;
											$badguy['istarget'] = false;
											$count = 1;
											$needtostopfighting=true;
										}
										$newenemies[$index] = $badguy;
										$newcompanions = $companions;
										// No break here. It would break the foreach statement.
									}
                                    else if ($badguy['istarget'] == true)
                                    {
										do
                                        {
											if ($badguy['creaturehealth']<=0 || $session['user']['hitpoints']<=0)
                                            {
												$creaturedmg = 0;
												$selfdmg = 0;
												$newenemies[$index] = $badguy;
												$newcompanions = $companions;
												$needtostopfighting = true;
											}
                                            else $needtostopfighting = battle_player_attacks();

											$r = mt_rand(0,100);
											if ($r < $ggchancetodouble && $badguy['creaturehealth']>0 && $session['user']['hitpoints']>0 && !$needtostopfighting){
												$additionalattack = true;
												$ggchancetodouble -= ($r+5);
												$roll = rolldamage();
											}
                                            else $additionalattack = false;
										}
                                        while($additionalattack && !$needtostopfighting);

										if ($needtostopfighting) $newcompanions = $companions;
									}
								}
							}
                            else if ($op == 'run' && !$surprised)
							{
								$lotgdBattleContent['battlerounds'][$countround]['allied'][] = ["`4You are too busy trying to run away like a cowardly dog to try to fight `^%s`4.`n", $badguy['creaturename']];
							}

							//Need to insert this here because of auto-fighting!
							if ($op != 'newtarget')	$op = 'fight';

							// We need to check both user health and creature health. Otherwise
							// the user can win a battle by a RIPOSTE after he has gone <= 0 HP.
							//-- Gunnar Kreitz
							if ($badguy['creaturehealth'] > 0 && $session['user']['hitpoints'] > 0 && $roundcounter <= $options['maxattacks'])
							{
								$buffset = activate_buffs('defense');
								do
								{
									$defended = false;
									$needtostopfighting = battle_badguy_attacks();
									$r = mt_rand(0,100);
									if (!isset($bgchancetodouble)) $bgchancetodouble = 0;
									if ($r < $bgchancetodouble && $badguy['creaturehealth']>0 && $session['user']['hitpoints']>0 && !$needtostopfighting)
									{
										$additionalattack = true;
										$bgchancetodouble -= ($r+5);
										$roll = rolldamage();
									}
									else $additionalattack = false;
								}
								while ($additionalattack && !$defended);
							}

							$companions = $newcompanions;
							if ($badguy['creaturehealth'] > 0 && $session['user']['hitpoints']>0 && $badguy['istarget'])
							{
								if (is_array($companions))
								{
									foreach ($companions as $name=>$companion)
									{
										if ($companion['hitpoints'] > 0)
										{
											$buffer = report_companion_move($companion, 'fight');
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
							else { $newcompanions = $companions; }
						}
						else { $newcompanions = $companions; }

						if($badguy['dead'] == false && isset($badguy['creatureaiscript']) && $badguy['creatureaiscript'] > '')
						{
							global $unsetme;

							execute_ai_script($badguy['creatureaiscript']);
						}
					}
				}
			}
			else $newcompanions = $companions;
			// Copy the companions back so in the next round (multiple rounds) they can be used again.
			// We will also delete the now old set of companions. Just in case.
			$companions = $newcompanions;
			unset($newcompanions);

			// If any A.I. script wants the current enemy to be deleted completely, we will obey.
			// For multiple rounds/multiple A.I. scripts we will although unset this order.
			if (isset($unsetme) && $unsetme === true)
			{
				$unsetme = false;
				unset($unsetme);
			}
			else $newenemies[$index] = $badguy;

			if ($surprised || $op == 'run' || $op == 'fight' || $op == 'newtarget') $badguy = modulehook('endofround', $badguy); //-- For Stamina System
		}
		expire_buffs();
		$creaturedmg = 0;
		$selfdmg = 0;

		if (count($newenemies) > 0)
		{
			$verynewenemies = [];
			$alive = 0;
			$fleeable = 0;
			$leaderisdead = false;
			foreach ($newenemies as $index => $badguy)
			{
				if ($badguy['dead'] == true || $badguy['creaturehealth'] <= 0)
				{
					if (isset($badguy['essentialleader']) && $badguy['essentialleader'] == true)
					{
						$defeat = false;
						$victory = true;
						$needtostopfighting = true;
						$leaderisdead = true;
					}
					$badguy['istarget'] = false;
					// We'll either add the experience right away or store it in a seperate array.
					// If through any script enemies are added during the fight, the amount of
					// experience would stay the same
					// We'll also check if the user is actually alive. If we didn't, we would hand out
					// experience for graveyard fights.
					if (getsetting('instantexp', false) == true && $session['user']['alive'] && $options['type'] != 'pvp' && $options['type'] != 'train')
					{
						if (!isset($badguy['expgained']) || $badguy['expgained'] == false)
						{
							if (!isset($badguy['creatureexp'])) $badguy['creatureexp'] = 0;
							$session['user']['experience'] += round($badguy['creatureexp']/count($newenemies));
							$lotgdBattleContent['battlerounds'][$countround]['allied'][] = ['`#You receive `^%s`# experience!`n`0', round($badguy['creatureexp']/count($newenemies))];
							$options['experience'][$index] = $badguy['creatureexp'];
							$options['experiencegained'][$index] = round($badguy['creatureexp']/count($newenemies));
							$badguy['expgained']=true;
						}
					}
					else
					{
						$options['experience'][$index] = $badguy['creatureexp'];
					}
				}
				else
				{
					$alive++;
					if (isset($badguy['fleesifalone']) && $badguy['fleesifalone'] == true) $fleeable++;

					if ($session['user']['hitpoints'] <= 0)
					{
						$defeat = true;
						$victory = false;

						break;
					}
					else if(!$leaderisdead)
					{
						$defeat = false;
						$victory = false;
					}
				}

				$verynewenemies[$index] = $badguy;
			}
			$enemiesflown = false;
			if ($alive == $fleeable && $session['user']['hitpoints'] > 0)
			{
				$defeat = false;
				$victory = true;
				$enemiesflown = true;
				$needtostopfighting = true;
			}
		}
		if ($alive == 0)
		{
			$defeat=false;
			$victory=true;
			$needtostopfighting=true;
		}
		if ($count != -1) { $count--; }
		if ($needtostopfighting) { $count = 0; }
		if ($enemiesflown)
		{
			foreach ($newenemies as $index => $badguy)
			{
				if (isset($badguy['fleesifalone']) && $badguy['fleesifalone'] == true)
				{
					if (is_array($badguy['fleesifalone'])) { $msg = substitute($badguy['fleesifalone']); }
					else
					{
						if ($badguy['fleesifalone'] === true) { $msg = "{badguy} flees in panic."; }
						else { $msg = $badguy['fleesifalone']; }

						$msg = substitute_array("`5{$msg}`0`n");
					}

					$lotgdBattleContent['battlerounds'][$countround]['enemy'][] = $msg;
				}
				else { $newenemies[$index] = $badguy; }
			}
		}
		else if ($leaderisdead)
		{
			if (is_array($badguy['essentialleader']))
			{
				$msg = substitute($badguy['essentialleader']);
				$lotgdBattleContent['battlerounds'][$countround]['enemy'][] = $msg;
			}
			else
			{
				if ($badguy['essentialleader'] === true)
				{
					$msg = "All other other enemies flee in panic as `^{badguy}`5 falls to the ground.";
				}
				else
				{
					$msg = $badguy['essentialleader'];
				}
				$msg = substitute_array("`5{$msg}`0`n");
				$lotgdBattleContent['battlerounds'][$countround]['enemy'][] = $msg;
			}
		}
		if (is_array($newenemies)) { $enemies = $newenemies; }

		$roundcounter = 0;
		$countround ++;
	}
    while ($count > 0 || $count == -1);

	$newenemies = $enemies;
}
else { $newenemies = $enemies; }

$newenemies = autosettarget($newenemies);

$badguy = modulehook('endofpage', $badguy);

if ($session['user']['hitpoints'] > 0 && count($newenemies) > 0 && ($op == 'fight' || $op == 'run'))
{
	$data = prepare_data_battlebars($newenemies);
	$lotgdBattleContent['battlebars']['end'] = [
		'player' => $data['user'],
		'companions' => $data['companions'],
		'enemies' => $data['enemies']
	];
	unset($data);
}
else
{
	$lotgdBattleContent['battlebars']['end'] = [
		'player' => [],
		'companions' => [],
		'enemies' => []
	];
}

if ($session['user']['hitpoints'] <= 0)
{
	$session['user']['hitpoints'] = 0;
	$victory = false;
	$defeat = true;
}

//-- Any data for personalize results
if (! isset($battleDefeatWhere)) $battleDefeatWhere = 'in the forest';//-- Use for create a news, set to false for not create news
if (! isset($battleInForest)) $battleInForest = true;//-- Indicating if is a Forest (true) or Graveyard (false)
if (! isset($battleDefeatLostGold)) $battleDefeatLostGold = true;//-- Indicating if lost gold when lost in battle
if (! isset($battleDefeatLostExp)) $battleDefeatLostExp = true;//-- Indicating if lost exp when lost in battle
if (! isset($battleDefeatCanDie)) $battleDefeatCanDie = true;//-- Indicating if die when lost in battle
if (! isset($battleDenyFlawless)) $battleDenyFlawless = false;//-- Deny flawlees for perfect battle
if (! isset($battleShowResult)) $battleShowResult = true;//-- Show result of battle. If no need any extra modification of result no need change this

if ($victory || $defeat)
{
	// expire any buffs which cannot persist across fights and
	// unsuspend any suspended buffs
	expire_buffs_afterbattle();
	//unsuspend any suspended buffs
	unsuspend_buffs((($options['type']=='pvp') ? 'allowinpvp' : false));
	if ($session['user']['alive']) unsuspend_companions((($options['type'] == 'pvp') ? 'allowinpvp' : false));

	foreach($companions as $index => $companion)
    {
		if(isset($companion['expireafterfight']) && $companion['expireafterfight'])
        {
			$lotgdBattleContent['battleend'][] = $companion['dyingtext'];
			unset($companions[$index]);
		}
	}

	if (is_array($newenemies))
	{
		foreach ($newenemies as $index => $badguy)
		{
			//-- Not use this hooks, better use battle-victory-end and battle-defeat-end
			//-- This other hooks have an array with all enemies and can add a extra information

			//-- This hooks triger for each badguy and maybe saturate server
			//-- Legacy support
			if ($victory) $badguy = modulehook('battle-victory', $badguy);
			if ($defeat) $badguy = modulehook('battle-defeat', $badguy);
		}
	}
	if ($victory)
	{
		$result = modulehook('battle-victory-end', ['enemies' => $newenemies, 'options' => $options, 'messages' => []]);

		$lotgdBattleContent['battleend'] = array_merge($lotgdBattleContent['battleend'], $result['messages']);

		battlevictory($newenemies, (isset($options['denyflawless'])?$options['denyflawless']:$battleDenyFlawless), $battleInForest);
	}
	else if ($defeat)
	{
		$result = modulehook('battle-defeat-end', ['enemies' => $newenemies, 'options' => $options, 'messages' => []]);

		$lotgdBattleContent['battleend'] = array_merge($lotgdBattleContent['battleend'], $result['messages']);

		battledefeat($newenemies, $battleDefeatWhere, $battleInForest, $battleDefeatCanDie, $battleDefeatLostExp, $battleDefeatLostGold);
	}
}

$attackstack = ['enemies' => $newenemies, 'options' => $options];
$session['user']['badguy'] = createstring($attackstack);
$session['user']['companions'] = createstring($companions);

if ($battleShowResult) battleshowresults($lotgdBattleContent);

tlschema();

//-- If battle end in defeat, break page after show content
if ($defeat && $battleShowResult) page_footer();
