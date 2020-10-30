<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';

checkday();

// Don't hook on to this text for your standard modules please, use "weapon" instead.
// This hook is specifically to allow modules that do other weapons to create ambience.
$result = modulehook('weapon-text-domain', ['textDomain' => 'page-weapon', 'textDomainNavigation' => 'navigation-weapon']);
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

$tradeinvalue = round(($session['user']['weaponvalue'] * .75), 0);

$params = [
    'textDomain' => $textDomain,
    'tradeinvalue' => $tradeinvalue,
];

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

$op = (string) \LotgdRequest::getQuery('op');
$repository = \Doctrine::getRepository('LotgdCore:Weapons');

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

if ('' == $op)
{
    $params['opt'] = 'default';
    $weaponLevel = $repository->getMaxWeaponLevel($session['user']['dragonkills']);

    $result = $repository->findByLevel($weaponLevel);

    $params['weapons'] = $result;
}
elseif ('buy' == $op)
{
    $id = (int) \LotgdRequest::getQuery('id');

    $params['opt'] = 'buy';
    $params['result'] = $repository->findOneWeaponById($id);

    if ($params['result'])
    {
        $row = $params['result'];
        $params['buyIt'] = false;

        if ($row['value'] <= ($session['user']['gold'] + $tradeinvalue))
        {
            $params['buyIt'] = true;

            debuglog(sprintf('spent "%s" gold on the "%s" weapon', ($row['value'] - $tradeinvalue), $row['weaponname']));
            $session['user']['gold'] -= $row['value'];
            $session['user']['weapon'] = $row['weaponname'];
            $session['user']['gold'] += $tradeinvalue;
            $session['user']['attack'] -= $session['user']['weapondmg'];
            $session['user']['weapondmg'] = $row['damage'];
            $session['user']['attack'] += $session['user']['weapondmg'];
            $session['user']['weaponvalue'] = $row['value'];
        }
    }
}

\LotgdNavigation::villageNav();

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- This is only for params not use for other purpose
$params = modulehook('page-weapon-tpl-params', $params);
\LotgdResponse::pageAddContent(\LotgdTheme::renderTheme('pages/weapon.html.twig', $params));

//-- Finalize page
\LotgdResponse::pageEnd();
