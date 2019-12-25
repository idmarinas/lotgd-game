<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/villagenav.php';

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

page_header('title', [], $textDomain);

$op = (string) \LotgdHttp::getQuery('op');
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
    $id = (int) \LotgdHttp::getQuery('id');

    $params['opt'] = 'buy';
    $params['result'] = $repository->findOneByWeaponid($id);

    if ($params['result'])
    {
        $row = $params['result'];
        $params['buyIt'] = false;

        if ($row->getValue() <= ($session['user']['gold'] + $tradeinvalue))
        {
            $params['buyIt'] = true;

            debuglog(sprintf('spent "%s" gold on the "%s" weapon', ($row->getValue() - $tradeinvalue), $row->getWeaponname()));
            $session['user']['gold'] -= $row->getValue();
            $session['user']['weapon'] = $row->getWeaponname();
            $session['user']['gold'] += $tradeinvalue;
            $session['user']['attack'] -= $session['user']['weapondmg'];
            $session['user']['weapondmg'] = $row->getDamage();
            $session['user']['attack'] += $session['user']['weapondmg'];
            $session['user']['weaponvalue'] = $row->getValue();
        }
    }
}

\LotgdNavigation::villageNav();

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- This is only for params not use for other purpose
$params = modulehook('page-weapon-tpl-params', $params);
rawoutput(\LotgdTheme::renderThemeTemplate('page/weapon.twig', $params));

page_footer();
