<?php

function get_player_hitpoints($player = false)
{
    global $session;

    $user = $session['user'];

    if ($player)
    {
        $repository = \Doctrine::getRepository('LotgdCore:Characters');
        $result = $repository->extractEntity($repository->findOneBy([ 'acct' => $player ]));

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

    $user = $session['user'];

    if ($player)
    {
        $repository = \Doctrine::getRepository('LotgdCore:Characters');
        $result = $repository->extractEntity($repository->findOneBy([ 'acct' => $player ]));

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

    $user = $session['user'];

    if ($player)
    {
        $repository = \Doctrine::getRepository('LotgdCore:Characters');
        $result = $repository->extractEntity($repository->findOneBy(['acct' => $player]));

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

    $user = $session['user'];

    if ($player)
    {
        $repository = \Doctrine::getRepository('LotgdCore:Characters');
        $result = $repository->extractEntity($repository->findOneBy([ 'acct' => $player ]));

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

    $user = $session['user'];

    if ($player)
    {
        $repository = \Doctrine::getRepository('LotgdCore:Characters');
        $result = $repository->extractEntity($repository->findOneBy(['acct' => $player]));

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

    $user = $session['user'];

    if ($player)
    {
        $repository = \Doctrine::getRepository('LotgdCore:Characters');
        $result = $repository->extractEntity($repository->findOneBy(['acct' => $player]));

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

    $user = $session['user'];

    if ($player)
    {
        $repository = \Doctrine::getRepository('LotgdCore:Characters');
        $result = $repository->extractEntity($repository->findOneBy([ 'acct' => $player ]));

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

    $user = $session['user'];

    if ($player)
    {
        $repository = \Doctrine::getRepository('LotgdCore:Characters');
        $result = $repository->extractEntity($repository->findOneBy([ 'acct' => $player ]));

        if (! $result)
        {
            return 0;
        }

        $user = $result;
    }

    $defense = log($user['wisdom']) + $user['constitution'] * 0.08 + log($user['defense']);

    return max($defense, 0);
}
