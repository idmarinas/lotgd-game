<?php

/**
 * Generate a base creature stats
 * Can use for generated your own creatures in your modules.
 *
 * @param false|int $level Level of creature
 *
 * @return array
 */
function lotgd_generate_creature_levels($level = false)
{
    $maxlvl = getsetting('maxlevel', 15) + 5;
    $stats = datacache("lotgd-generate-creature-levels-$maxlvl", 83000, true);

    if (empty($stats))
    {
        $creatureexp = 14;
        $creaturegold = 36;
        $creaturedefense = 0;

        for ($i = 1; $i <= $maxlvl; $i++)
        {
            //apply algorithmic creature generation.
            $creaturehealth = ($i * 10) + ($i - 1) - round(sqrt($i - 1));
            $creatureattack = 1 + ($i - 1) * 2;
            $creaturedefense += ($i % 2 ? 1 : 2);

            if ($i > 1)
            {
                $creatureexp += round(10 + 1.5 * log($i));
                $creaturegold += (31 * ($i < 4 ? 2 : 1));
                //give lower levels more gold
            }
            $stats[$i] = [
                'creaturelevel' => $i,
                'creaturehealth' => $creaturehealth,
                'creatureattack' => $creatureattack,
                'creaturedefense' => $creaturedefense,
                'creatureexp' => $creatureexp,
                'creaturegold' => $creaturegold,
            ];
        }

        updatedatacache("lotgd-generate-creature-levels-$maxlvl", $stats, true);
    }

    if (false === $level)
    {
        return $stats;
    }
    elseif (isset($stats[$level]))
    {
        return $stats[$level];
    }
    else
    {
        $stats;
    }
}

/**
 * Transform creature to adapt to player.
 *
 * @param array $badguy Data of creature
 * @param bool  $debug  Show or not debug of creature
 *
 * @return array
 */
function lotgd_transform_creature(array $badguy, $debug = true)
{
    global $session;

    // This will save us a lot of trouble when going through
    static $dk = false;	// this function more than once...

    if (false === $dk)
    {
        $dk = $session['user']['dragonkills'];
    }

    $badguy = array_merge(lotgd_generate_creature_levels($badguy['creaturelevel']), $badguy);

    $badguy['playerdragonkills'] = $dk;
    $badguy['creaturespeed'] = $badguy['creaturespeed'] ?? 2.5;
    $badguy['creatureexp'] = $badguy['creatureexp'] ?? 0;
    $badguy['physicalresistance'] = $badguy['physicalresistance'] ?? 0;

    //-- Apply multipliers
    $badguy['creaturegold'] = round($badguy['creaturegold'] * ($badguy['creaturegoldbonus'] ?? 1));
    $badguy['creatureattack'] = $badguy['creatureattack'] * ($badguy['creatureattackbonus'] ?? 1);
    $badguy['creaturedefense'] = $badguy['creaturedefense'] * ($badguy['creaturedefensebonus'] ?? 1);
    $badguy['creaturehealth'] = round($badguy['creaturehealth'] * ($badguy['creaturehealthbonus'] ?? 1));

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

    //-- Set max health for creature
    $badguy['creaturemaxhealth'] = $badguy['creaturehealth'];
    $badguy['creaturestartinghealth'] = $badguy['creaturehealth'];

    //-- Check if script exist
    if (isset($badguy['creatureaiscript']))
    {
        $aiscriptfile = "{$badguy['creatureaiscript']}.php";

        if (file_exists($aiscriptfile))
        {
            $badguy['creatureaiscript'] = "include '{$aiscriptfile}';";
        }
        else
        {
            $badguy['creatureaiscript'] = '';
        }
    }

    //-- Not show debug
    if (! $debug)
    {
        return $badguy;
    }

    lotgd_show_debug_creature($badguy);

    return $badguy;
}

/**
 * Search for creature in data base.
 *
 * @param int       $multi
 * @param int       $targetlevel
 * @param int       $mintargetlevel
 * @param bool      $packofmonsters For diferent or same creatures
 * @param bool|null $forest         TRUE for creature of forest, FALSE for graveyard and NULL for none
 *
 * @return array
 */
function lotgd_search_creature($multi, $targetlevel, $mintargetlevel, $packofmonsters = false, $forest = true): array
{
    $limit = ($multi > 1 ? (false === $packofmonsters ? $multi : 1) : 1);

    $repository = \Doctrine::getRepository('LotgdCore:Creatures');
    $query = $repository->createQueryBuilder('u');

    $query->setMaxResults($limit)
        ->orderBy('rand()')
    ;

    if (true === $forest)
    {
        $query->where('u.forest = 1');
    }
    elseif (false === $forest)
    {
        $query->where('u.graveyard = 1');
    }

    //-- Select creatures of diferent categories
    if (getsetting('multicategory', 0) && $limit > 1)
    {
        $query->groupBy('u.creaturecategory');
    }

    $creatures = $query->getQuery()->getArrayResult();

    if (count($creatures))
    {
        foreach($creatures as $key => $creature)
        {
            $creatures[$key]['creaturelevel'] = mt_rand($mintargetlevel, $targetlevel);
        }
    }

    //-- You can add more creatures. This is good, when not find nothing in data base
    $creatures = modulehook('creature-search', [
        'creatures' => $creatures,
        'multi' => $multi,
        'targetlevel' => $targetlevel,
        'mintargetlevel' => $mintargetlevel,
        'packofmonsters' => $packofmonsters,
        'forest' => $forest
    ]);

    return $creatures['creatures'];
}

function get_creature_stats($dk = 0)
{
    global $session;

    if (0 == $dk)
    {
        $dk = $session['user']['dragonkills'];
    }

    //-- They are placed in order of importance
    $con = e_rand($dk / 6, $dk / 2);
    $dk -= $con;
    $str = e_rand(0, $dk);
    $dk -= $str;
    $dex = e_rand(0, $dk);
    $dk -= $dex;
    $int = e_rand(0, $dk);
    $wis = ($dk - $int);

    return ['str' => $str, 'dex' => $dex, 'con' => $con, 'int' => $int, 'wis' => $wis];
}

function get_creature_hitpoints($attrs)
{
    $conbonus = $attrs['con'] * .5;
    $wisbonus = $attrs['wis'] * .2;
    $strbonus = $attrs['str'] * .3;

    $hitpoints = round($conbonus + $wisbonus + $strbonus, 0);

    return max($hitpoints, 0);
}

function get_creature_attack($attrs)
{
    $strbonus = (1 / 3) * $attrs['str'];
    $speedbonus = (1 / 3) * get_creature_speed($attrs);
    $wisdombonus = (1 / 6) * $attrs['wis'];
    $intbonus = (1 / 6) * $attrs['int'];

    $attack = $strbonus + $wisdombonus + $intbonus;

    return max($attack, 0);
}

function get_creature_defense($attrs)
{
    $wisdombonus = (1 / 4) * $attrs['wis'];
    $constbonus = (3 / 8) * $attrs['con'];
    $speedbonus = (3 / 8) * get_player_speed($attrs);

    $defense = $wisdombonus + $constbonus;

    return max($defense, 0);
}

function get_creature_speed($attrs)
{
    $speed = (1 / 2) * $attrs['dex'] + (1 / 4) * $attrs['int'] + (5 / 2);

    return max($speed, 0);
}

function lotgd_show_debug_creature(array $badguy)
{
    debug("DEBUG: Basic information: Atk: {$badguy['creatureattack']}, Def: {$badguy['creaturedefense']}, HP: {$badguy['creaturehealth']}");
    debug("DEBUG: {$badguy['playerdragonkills']} modification points total for attributes.");
    debug("DEBUG: +{$badguy['creaturestrbonus']} allocated to strength.");
    debug("DEBUG: +{$badguy['creaturedexbonus']} allocated to dexterity.");
    debug("DEBUG: +{$badguy['creatureconbonus']} allocated to constitution.");
    debug("DEBUG: +{$badguy['creatureintbonus']} allocated to intelligence.");
    debug("DEBUG: +{$badguy['creaturewisbonus']} allocated to wisdom.");
    debug("DEBUG: +{$badguy['creatureattackattrs']} modification of attack.");
    debug("DEBUG: +{$badguy['creaturedefenseattrs']} modification of defense.");
    debug("DEBUG: +{$badguy['creaturespeedattrs']} modification of speed.");
    debug("DEBUG: +{$badguy['creaturehealthattrs']} modification of hitpoints.");
}
