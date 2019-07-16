<?php

function get_player_hitpoints($player = false)
{
    global $session;

    $user = &$session['user'];

    if (! $player)
    {
        $repository = \Doctrine::getRepository('LotgdCore:Characters');
        $result = $repository->extractEntity($repository->findBy(['acct' => $player]));

        if (! $result)
        {
            return 0;
        }

        $user = $result;
    }

    $conbonus = $user['constitution'] * .5;
    $wisbonus = $user['wisdom'] * .2;
    $strbonus = $user['strength'] * .3;
    $levelbonus = ($user['level'] - 1) * 10;

    $hitpoints = round($conbonus + $wisbonus + $strbonus + $levelbonus + $user['permahitpoints'], 0);

    //-- The minimum hitpoints the character can have is 10, regardless of the penalty of the 'permahitpoints'
    return max($hitpoints, 10);
}

function explained_get_player_hitpoints($player = false, $colored = false)
{
    global $session;

    $user = &$session['user'];

    if (! $player)
    {
        $repository = \Doctrine::getRepository('LotgdCore:Characters');
        $result = $repository->extractEntity($repository->findBy(['acct' => $player]));

        if (! $result)
        {
            return 0;
        }

        $user = $result;
    }

    $conbonus = $user['constitution'] * .5;
    $wisbonus = $user['wisdom'] * .2;
    $strbonus = $user['strength'] * .3;
    $levelbonus = ($user['level'] - 1) * 10;

    if ($colored)
    {
        return sprintf_translate('%s %s`0 CON %s %s`0 WIS %s %s`0 STR %s %s`0 Train %s %s`0 MISC',
            ($conbonus >= 0 ? '`8+' : '`$-'), abs($conbonus),
            ($wisbonus >= 0 ? '`8+' : '`$-'), abs($wisbonus),
            ($strbonus >= 0 ? '`8+' : '`$-'), abs($strbonus),
            ($levelbonus >= 0 ? '`8+' : '`$-'), abs($levelbonus),
            ($user['permahitpoints'] >= 0 ? '`8+' : '`$-'), abs($user['permahitpoints'])
        );
    }

    return sprintf_translate('%s CON %s WIS %s STR %s Train %s MISC', $conbonus, $wisbonus, $strbonus, $levelbonus, $user['permahitpoints']);
}

function get_player_attack($player = false)
{
    global $session;

    $user = &$session['user'];

    if (false !== $player)
    {
        $repository = \Doctrine::getRepository('LotgdCore:Characters');
        $result = $repository->extractEntity($repository->findBy(['acct' => $player]));

        if (! $result)
        {
            return 0;
        }

        $user = $result;
    }

    $strbonus = (1 / 3) * $user['strength'];
    $speedbonus = (1 / 3) * get_player_speed($player);
    $wisdombonus = (1 / 6) * $user['wisdom'];
    $intbonus = (1 / 6) * $user['intelligence'];
    $miscbonus = $user['attack'] - 9;

    $attack = $strbonus + $speedbonus + $wisdombonus + $intbonus + $miscbonus;

    return max($attack, 0);
}

function explained_row_get_player_attack($player = false)
{
    global $session;

    $user = &$session['user'];

    if (false !== $player)
    {
        $repository = \Doctrine::getRepository('LotgdCore:Characters');
        $result = $repository->extractEntity($repository->findBy(['acct' => $player]));

        if (! $result)
        {
            return 0;
        }

        $user = $result;
    }

    $strbonus = round((1 / 3) * $user['strength'], 2);
    $speedbonus = round((1 / 3) * get_player_speed($player), 2);
    $wisdombonus = round((1 / 6) * $user['wisdom'], 2);
    $intbonus = round((1 / 6) * $user['intelligence'], 2);
    $miscbonus = round($user['attack'] - 9, 2);
    // $atk = $strbonus+$speedbonus+$wisdombonus+$intbonus+$miscbonus;
    $weapondmg = (int) $user['weapondmg'];
    $levelbonus = (int) $user['level'] - 1;
    $miscbonus -= $weapondmg + $levelbonus;

    return [
        'strbonus' => $strbonus,
        'speedbonus' => $speedbonus,
        'wisdombonus' => $wisdombonus,
        'intbonus' => $intbonus,
        'weapondmg' => $weapondmg,
        'levelbonus' => $levelbonus,
        'miscbonus' => $miscbonus
    ];
}

function explained_get_player_attack($player = false, $colored = false)
{
    $result = explained_row_get_player_attack($player);

    $strbonus = $result['strbonus'];
    $speedbonus = $result['speedbonus'];
    $wisdombonus = $result['wisdombonus'];
    $intbonus = $result['intbonus'];
    $weapondmg = $result['weapondmg'];
    $levelbonus = $result['levelbonus'];
    $miscbonus = $result['miscbonus'];

    if ($colored)
    {
        return sprintf_translate('%s %s`0 STR %s %s`0 SPD %s %s`0 WIS %s %s`0 INT %s %s`0 Weapon %s %s`0 Train %s %s`0 MISC ',
            ($strbonus >= 0 ? '`8+' : '`$-'), abs($strbonus),
            ($speedbonus >= 0 ? '`8+' : '`$-'), abs($speedbonus),
            ($wisdombonus >= 0 ? '`8+' : '`$-'), abs($wisdombonus),
            ($intbonus >= 0 ? '`8+' : '`$-'), abs($intbonus),
            ($weapondmg >= 0 ? '`8+' : '`$-'), abs($weapondmg),
            ($levelbonus >= 0 ? '`8+' : '`$-'), abs($levelbonus),
            ($miscbonus >= 0 ? '`8+' : '`$-'), abs($miscbonus)
        );
    }

    return sprintf_translate('%s STR + %s SPD + %s WIS+ %s INT + %s Weapon + %s Train + %s MISC ', $strbonus, $speedbonus, $wisdombonus, $intbonus, $weapondmg, $levelbonus, $miscbonus);
}

function get_player_defense($player = false)
{
    global $session;

    $user = &$session['user'];

    if (false !== $player)
    {
        $repository = \Doctrine::getRepository('LotgdCore:Characters');
        $result = $repository->extractEntity($repository->findBy(['acct' => $player]));

        if (! $result)
        {
            return 0;
        }

        $user = $result;
    }

    $wisdombonus = (1 / 4) * $user['wisdom'];
    $constbonus = (3 / 8) * $user['constitution'];
    $speedbonus = (3 / 8) * get_player_speed($player);
    $miscbonus = $user['defense'] - 9;
    $defense = $wisdombonus + $speedbonus + $constbonus + $miscbonus;

    return max($defense, 0);
}

function explained_row_get_player_defense($player = false)
{
    global $session;

    $user = &$session['user'];

    if (false !== $player)
    {
        $repository = \Doctrine::getRepository('LotgdCore:Characters');
        $result = $repository->extractEntity($repository->findBy(['acct' => $player]));

        if (! $result)
        {
            return 0;
        }

        $user = $result;
    }

    $wisdombonus = round((1 / 4) * $user['wisdom'], 2);
    $constbonus = round((3 / 8) * $user['constitution'], 2);
    $speedbonus = round((3 / 8) * get_player_speed($player), 2);
    $miscbonus = round($user['defense'] - 9, 2);
    // $defense = $wisdombonus+$speedbonus+$constbonus+$miscbonus;
    $armordef = (int) $user['armordef'];
    $levelbonus = (int) $user['level'] - 1;
    $miscbonus -= $armordef + $levelbonus;

    return [
        'wisdombonus' => $wisdombonus,
        'constbonus' => $constbonus,
        'speedbonus' => $speedbonus,
        'armordef' => $armordef,
        'levelbonus' => $levelbonus,
        'miscbonus' => $miscbonus
    ];
}

function explained_get_player_defense($player = false, $colored = false)
{
    $result = explained_row_get_player_defense($player);

    $wisdombonus = $result['wisdombonus'];
    $constbonus = $result['constbonus'];
    $speedbonus = $result['speedbonus'];
    $armordef = $result['armordef'];
    $levelbonus = $result['levelbonus'];
    $miscbonus = $result['miscbonus'];

    if ($colored)
    {
        return sprintf_translate('%s %s`0 WIS %s %s`0 CON %s %s`0 SPD %s %s`0 Armor %s %s`0 Train %s %s`0 MISC',
            ($wisdombonus >= 0 ? '`8+' : '`$-'), abs($wisdombonus),
            ($constbonus >= 0 ? '`8+' : '`$-'), abs($constbonus),
            ($speedbonus >= 0 ? '`8+' : '`$-'), abs($speedbonus),
            ($armordef >= 0 ? '`8+' : '`$-'), abs($armordef),
            ($levelbonus >= 0 ? '`8+' : '`$-'), abs($levelbonus),
            ($miscbonus >= 0 ? '`8+' : '`$-'), abs($miscbonus)
        );
    }

    return sprintf_translate('%s WIS + %s CON + %s SPD + %s Armor + %s Train + %s MISC ', $wisdombonus, $constbonus, $speedbonus, $armordef, $levelbonus, $miscbonus);
}

function get_player_speed($player = false)
{
    global $session;

    $user = &$session['user'];

    if (false !== $player)
    {
        $repository = \Doctrine::getRepository('LotgdCore:Characters');
        $result = $repository->extractEntity($repository->findBy([ 'acct' => $player ]));

        if (! $result)
        {
            return 0;
        }

        $user = $result;
    }

    // $speed = round((1/2)*$user['dexterity']+(1/4)*$user['intelligence']+(5/2),1);
    //## Modificación no se redondea
    $speed = (1 / 2) * $user['dexterity'] + (1 / 4) * $user['intelligence'] + (5 / 2);

    return max($speed, 0);
}

function get_player_physical_resistance($player = false)
{
    global $session;

    $user = &$session['user'];

    if (false !== $player)
    {
        $repository = \Doctrine::getRepository('LotgdCore:Characters');
        $result = $repository->extractEntity($repository->findBy([ 'acct' => $player ]));

        if (! $result)
        {
            return 0;
        }

        $user = $result;
    }

    $defense = log($user['wisdom']) + $user['constitution'] * 0.08 + log($user['defense']);

    return max($defense, 0);
}

function is_player_online($player = false)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0.',
        __METHOD__
    ), E_USER_DEPRECATED);

    //don't call this with like 100 people on a screen, it's pretty high load, 1 query each call
    //do mass_is_player_online($array_of_ids) instead
    static $checked_users = []; //remember for later, I am sucker for doing unnecessary stuff, and adding and checkin an array is better than one sql query more than necessary ;)
    if (false === $player)
    {
        global $session;
        $user = &$session['user'];
    }
    elseif (isset($checked_users[$player]))
    {
        $user = &$checked_users[$player];
    }
    else
    {
        //fetch the data from the DB
        $sql = 'SELECT laston,loggedin FROM '.DB::prefix('accounts').' WHERE acctid='.((int) $player).';';
        $result = DB::query($sql);
        $row = DB::fetch_assoc($result);

        if (! $row)
        {
            return false;
        }
        $checked_users[$player] = $row;
        $user = $row;
    }

    if (isset($user['laston']) && isset($user['loggedin']))
    {
        if (strtotime('-'.getsetting('LOGINTIMEOUT', 900).' seconds') > strtotime($user['laston']) && strtotime($user['laston']) > 0)
        {
            return false;
        }

        if (! $user['loggedin'])
        {
            return false;
        }

        return true;
    }

    return false;
}

function mass_is_player_online($players = false)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0.',
        __METHOD__
    ), E_USER_DEPRECATED);

    //don't call this with like 100 people on a screen, it's pretty high load, 1 query each call
    //do mass_is_player_online($array_of_ids) instead
    $users = [];

    if (false === $players || $players == [] || ! is_array($players))
    {
        return []; //nothing to do
    }
    else
    {
        //fetch the data from the DB
        $sql = 'SELECT acctid,laston,loggedin FROM '.DB::prefix('accounts').' WHERE acctid IN ('.addslashes(implode(',', $players)).')';
        $result = DB::query($sql);

        while ($user = DB::fetch_assoc($result))
        {
            $users[$user['acctid']] = 1;

            if (isset($user['laston']) && isset($user['loggedin']))
            {
                if (strtotime('-'.getsetting('LOGINTIMEOUT', 900).' seconds') > strtotime($user['laston']) && $user['laston'] > '')
                {
                    $users[$user['acctid']] = 0;
                }

                if (! $user['loggedin'])
                {
                    $users[$user['acctid']] = 0;
                }
            }
            else
            {
                $users[$user['acctid']] = 0;
            }
        }
    }

    return $users;
}

/*
 * Lo único que hace es devolver los DK que tiene el jugador
 *
 * Las criaturas se calculará su ataque, defensa y salud de una forma similar a los jugadores
 */
function get_player_dragonkillmod()
{
    global $session;

    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0. Only return dragonkills of user',
        __METHOD__
    ), E_USER_DEPRECATED);

    return $session['user']['dragonkills'];
}

function get_player_info($player = false)
{
    global $session;

    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0.',
        __METHOD__
    ), E_USER_DEPRECATED);

    if (false !== $player)
    {
        $select = DB::select('accounts');
        $select->where->equalTo('acctid', (int) $player);
        $user = DB::execute($select)->current();

        if (! $user)
        {
            return [];
        }

        unset($user['password']);

        $user['dragonpoints'] = unserialize($user['dragonpoints']);
        $user['prefs'] = unserialize($user['prefs']);
        $user['bufflist'] = unserialize($user['bufflist']);

        if (! is_array($user['bufflist']))
        {
            $user['bufflist'] = [];
        }

        if (! is_array($user['dragonpoints']))
        {
            $user['dragonpoints'] = [];
        }
    }
    else
    {
        $user = &$session['user'];
    }

    return $user;
}
