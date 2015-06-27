<?php
// Itemeffect by Edorian & Christian Rutsch (c) 2006
// currently contains modifications for
// - turns, attack, defense
// - hitpoints / maxhipoints
// - charm, gold, gems
// - experience (with check, if it should be modified or multiplied);
// - specialties (uses for all specialties can be set, skill levels for the chosen one)
// - deathpower, gravefights
// - travel, extraflirt
// - ability to apply or strip buffs

function get_effect($item = false, $noeffecttext = "", $giveoutput = true) {
	global $session;
	tlschema("inventory");
	$out = array();
	if ($item === false) {
		if ($noeffecttext == "") {
			$args = modulehook("item-noeffect", array("msg"=>"`&Nothing happens.`n", "item"=>$item));
			$out[] = sprintf_translate($args['msg']);
		} else {
			$out[] = sprintf_translate($noeffecttext);
		}
	} else {
		eval($item['execvalue']);

		if(isset($hitpoints) && $hitpoints <> 0) {
			if (!isset($override_maxhitpoints)) {
				$override_maxhitpoints = false;
			}
			if($hitpoints > 0) {
				if(($session['user']['hitpoints'] >= $session['user']['maxhitpoints']) && $override_maxhitpoints == false) {
				} else if(($session['user']['hitpoints'] + $hitpoints > $session['user']['maxhitpoints']) && $override_maxhitpoints == false) {
					$session['user']['hitpoints'] = $session['user']['maxhitpoints'];
					$out[] = sprintf_translate("`^Your hitpoints have been `@fully`^ restored.`n");
				} else {
					if ($override_maxhitpoints == false) {
						$hitpoints = min($session['user']['maxhitpoints'] - $session['user']['hitpoints'], $hitpoints);
					}
					if ($hitpoints > 0) {
						$session['user']['hitpoints'] += $hitpoints;
						$out[] = sprintf_translate("`^You have been `@healed`^ for %s points.`n", $hitpoints);
					}
				}
			} else if ($hitpoints < 0) {
				if($session['user']['hitpoints'] + $hitpoints > 0) {
					output("`^You `4loose`^ %s hitpoints.", abs($hitpoints));
					$session['user']['hitpoints'] += $hitpoints;
				} else if (!$killable) {
					$session['user']['hitpoints'] = 1;
					$out[] = sprintf_translate("`^You were `\$almost`^ killed.`n");
				} else {
					$experience = -$killable/100;
					$session['user']['hitpoints'] = 0;
					$session['user']['alive'] = 0;
					$out[] = sprintf_translate("`\$You die.`n");
				}
			}
		}

		if (isset($turns) && $turns <> 0) {
			$session['user']['turns'] += $turns;
			debuglog("'s turns were altered by $turns by item {$item['itemid']}.");
			if ($turns > 0) {
				if($turns==1){
					$out[] = sprintf_translate("`^You `@gain`^ one turn.`n");
				}else{
					$out[] = sprintf_translate("`^You `@gain`^ %s turns.`n", $turns);
				}
			} else {
				if ($session['user']['turns'] <= 0) {
					$out[] = sprintf_translate("`^You `\$lose`^ all your turns.`n");
					$session['user']['turns'] = 0;
				} else {
					if($turns==-1){
						$out[] = sprintf_translate("`^You `\$lose`^ one turn.`n");
					}else{
						$out[] = sprintf_translate("`^You `\$lose`^ %s turns.`n", abs($turns));
					}
				}
			}
		}

		if (isset($attack) && $attack <> 0) {
			$session['user']['attack'] += $attack;
			debuglog("'s attack was altered by $attack by item {$item['itemid']}.");
			if ($attack > 0) {
				$out[] = sprintf_translate("`^Your attack is `@increased`^ by %s.`n", $attack);
			} else {
				if ($session['user']['attack'] <= 1) {
					$out[] = sprintf_translate("`^You `\$lose`^ all your attack except the strength of your bare fists.`n");
					$session['user']['attack'] = 1;
				} else {
					$out[] = sprintf_translate("`^Your attack is `\$decreased`^ by %s.`n", abs($attack));
				}
			}
		}

		if (isset($defense) && $defense <> 0) {
			$session['user']['defense'] += $defense;
			debuglog("'s defense was altered by $defense by item {$item['itemid']}.");
			if ($defense > 0) {
				$out[] = sprintf_translate("`^Your defense is `@increased`^ by %s.`n", $defense);
			} else {
				if ($session['user']['defense'] <= 1) {
					$out[] = sprintf_translate("`^You `\$lose`^ all your defense except the durability of your everpresent T-Shirt.`n");
					$session['user']['defense'] = 1;
				} else {
					$out[] = sprintf_translate("`^Your defense is `\$decreased`^ by %s.`n", abs($defense));
				}
			}
		}

		if (isset($charm) && $charm <> 0) {
			$session['user']['charm'] += $turns;
			if ($charm > 0) {
				$out[] = sprintf_translate("`^Your charm is `@increased`^ by %s.`n", $charm);
			} else {
				if ($session['user']['charm'] <= 0) {
					$out[] = sprintf_translate("`^You `\$lose`^ all your charm.`n");
					$session['user']['charm'] = 0;
				} else {
					$out[] = sprintf_translate("`^Your charm is `\$decreased`^ by %s.`n", abs($charm));
				}
			}
		}


		if (isset($maxhitpoints) && $maxhitpoints <> 0) {
			if ($maxhitpoints > 0) {
				$session['user']['maxhitpoints'] += $maxhitpoints;
				$out[] = sprintf_translate("`^Your maximum hitpoints are permanently `@increased`^ by %s.`n", $maxhitpoints);
			} else {
				reset($session['user']['dragonpoints']);
				$hp=0;
				while(list($key,$val)=each($session['user']['dragonpoints'])){
					if ($val=="hp") $dkhp++;
				}
				$minhp = $session['user']['level'] * 10 + $hp*5;
				if (($session['user']['maxhitpoints'] + $maxhitpoints) < $minhp) $maxhitpoints = $session['user']['maxhitpoints'] - $minhp;
				if ($maxhitpoints < 0) {
					$out[] = sprintf_translate("`^Your maximum hitpoints are permanently `\$decreased`^ by %s.`n", abs($maxhitpoints));
					$session['user']['maxhitpoints'] += $maxhitpoints;
				}
			}
		}

		if (isset($uses)) {
			$modules = modulehook("specialtymodules");
			$names = modulehook("specialtynames");
			if (is_array($uses)){
				while(list($key,$val) = each ($uses)){
					if ($val > 0) {
						if($val==1){
							$out[] = sprintf_translate("`^You `@gain`^ one point in %s.`n",$val, $names[$key]);
						} else {
							$out[] = sprintf_translate("`^You `@gain`^ %s points in %s.`n",$val, $names[$key]);
						}
					} else {
						$val = min(abs($val), get_module_pref("uses", $modules[$key]));
						if ($val==1){
							$out[] = sprintf_translate("`^You `\$lose`^ one point in %s.`n",$val, $names[$key]);
						}else{
							$out[] = sprintf_translate("`^You `\$lose`^ %s points in %s.`n",$val, $names[$key]);
						}
						$val *= -1;
					}
					increment_module_pref("uses", $val, $modules[$key]);
				}
			} else if ($uses == 'looseall') {
				while(list($key, $val) = each($modules))
					set_module_pref("uses", 0, $val);
				$out[] = "`^You `\$lose all`^ uses in `\$all`^ specialties.`n";
			} else {
				if (is_numeric($uses) && $uses > 0 && $session['user']['specialty'] != "") {
					increment_module_pref("uses", $uses, $modules[$session['user']['specialty']]);
					$out[] = sprintf_translate("`^You `@gain`^ %s points in %s.`n",$uses, $names[$session['user']['specialty']]);
				} else if (is_numeric($uses) && $session['user']['specialty'] != "") {
					$out[] = sprintf_translate("`^You `\$lose`^ %s points in %s.`n",$uses, $names[$key]);
				}
			}
		}

		if (isset($increment_specialty) && $increment_specialty > 0) {
			require_once("lib/increment_specialty.php");
			while($increment_specialty) {
				increment_specialty("`^");
				$increment_specialty--;
			}
			$giveoutput = false;
		}

		if (isset($gems) && $gems != 0) {
			$session['user']['gems'] += $gems;
			debuglog("'s gems were altered by $gems by item {$item['itemid']}.");
			if ($gems > 0) {
				if($gems==1){
					$out[] = sprintf_translate("`^You `@gain`^ one gem.`n");
				}else{
					$out[] = sprintf_translate("`^You `@gain`^ %s gems.`n", $gems);
				}
			} else {
				$gems = min(abs($gems), $session['user']['gems']);
				if($gems==1){
					$out[] = sprintf_translate("`^You `\$lose`^ one gem.`n", $gems);
				}else{
					$out[] = sprintf_translate("`^You `\$lose`^ %s gems.`n", $gems);
				}
			}
		}

		if (isset($gold) && $gold != 0) {
			$session['user']['gold'] += $gold;
			debuglog("'s gold were altered by $gold by item {$item['itemid']}.");
			if ($gold > 0) {
				$out[] = sprintf_translate("`^You `@gain`^ %s gold.`n", $gold);
			} else {
				$gold = min(abs($gold), $session['user']['gold']);
				$out[] = sprintf_translate("`^You `\$lose`^ %s gold.`n", $gold);
			}
		}

		if (isset($experience) && $experience != 0)	{
			if(is_float($experience)) {
				$bonus = round($session['user']['experience'] * $experience, 0);
			} else {
				$bonus = $experience;
			}
			$session['user']['experience'] += $experience;
			debuglog("'s experience was altered by $bonus by item {$item['itemid']}.");
			if ($bonus > 0) {
				$out[] = sprintf_translate("`^You `@gain`^ %s experience.`n", $bonus);
			} else {
				$bonus = min(abs($bonus), $session['user']['experience']);
				$out[] = sprintf_translate("`^You `\$lose`^ %s experience.`n", $bonus);
			}
		}

		if (isset($deathpower) && $deathpower != 0) {
			$session['user']['deathpower'] += $deathpower;
			if ($deathpower > 0) {
				$out[] = sprintf_translate("`^You `@gain`^ %s favor with `\$Ramius`0.`n", $deathpower);
			} else {
				$deathpower = min(abs($deathpower), $session['user']['deathpower']);
				$out[] = sprintf_translate("`^You `\$lose`^ %s favor with `\$Ramius`0.`n", $deathpower);
			}
		}

		if (isset($gravefights) && $gravefights != 0) {
			$session['user']['gravefights'] += $gravefights;
			if ($gravefights > 0) {
				$out[] = sprintf_translate("`^You `@gain`^ %s gravefights.`n", $gravefights);
			} else {
				$deathpower = min(abs($gravefights), $session['user']['gravefights']);
				$out[] = sprintf_translate("`^You `\$lose`^ %s gravefights.`n", $gravefights);
			}
		}

		if (isset($extraflirt) && is_module_active("lovers") && $extraflirt == true && get_module_pref("seenlover", "lovers")) {
			set_module_pref("seenlover", false, "lovers");
			$him = sprintf_translate("him");
			$her = sprintf_translate("her");
			require_once("lib/partner.php");
			$out[] = sprintf_translate("`^You miss %s`^ and want to see %s again.`n", get_partner(), $session['user']['sex']?$him:$her);
		}

		if (isset($extratravel) && is_module_active("cities") && $extratravel != 0) {
			increment_module_pref("traveltoday", -$extratravel, "cities");
			if ($extratravel > 0) {
				$out[] = sprintf_translate("`^You feel `@refreshed`^.");
				$out[] = sprintf_translate("`^You may travel %s times `@more`^ today.`n", $extratravel);
			} else {
				$out[] = sprintf_translate("`^You feel `\$tired`^.");
				$out[] = sprintf_translate("`^You may travel %s times `\$less`^ today.`n", $extratravel);
			}
		}

		if (isset($buff) && is_array($buff)) {
			require_once("lib/buffs.php");
			apply_buff("item-{$item['itemid']}", $buff);
			$out[] = sprintf_translate("`^Something feels strange within your body.`n");
		}

		if (isset($extrabuff) && is_array($extrabuff)) {
			require_once("lib/buffs.php");
			while(list($key, $val) = each($extrabuff)) {
				if(has_buff($key)) {
					while (list($vkey, $vval) = each($val)) {
						$session['bufflist'][$key][$vkey] += $vval;
						$things = true;
					}
				}
			}
			if ($things) $out[] = sprintf_translate("`^You feel something strange happening.`n");
		}

		if (isset($strip)) {
			require_once("lib/buffs.php");
			if (has_buff($strip)) {
				strip_buff($strip);
				$out[] = sprintf_translate("`^You have a weird feeling.`n");
			}
		}

	}
	foreach($out as $index=>$text) {
		if (is_array($text)) {
			foreach($text as $sec=>$text2) {
				$newout[] = $text2;
			}
		} else {
			$newout[] = $text;
		}
	}
	$args = modulehook("itemeffect", array("out"=>$newout, "item"=>$item));
	$out = $args['out'];
	if (count($out) == 0) {
		if ($noeffecttext == "") {
			$args = modulehook("item-noeffect", array("msg"=>"`&Nothing happens.`n", "item"=>$item));
			$out[] = sprintf_translate($args['msg']);
		} else if ($giveoutput) {
			$out[] = sprintf_translate($noeffecttext);
		}
	}

	$effect_text = join($out, "");
	tlschema();
	return $effect_text;
}
?>
