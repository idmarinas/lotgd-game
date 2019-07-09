<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';

tlschema('armor');

checkday();

// Don't hook on to this text for your standard modules please, use "armor" instead.
// This hook is specifically to allow modules that do other armors to create ambience.
$result = modulehook('armor-text-domain', ['textDomain' => 'page-armor', 'textDomainNavigation' => 'navigation-armor']);
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

$tradeinvalue = round(($session['user']['armorvalue'] * .75), 0);

$params = [
    'textDomain' => $textDomain,
    'tradeinvalue' => $tradeinvalue,
];

page_header('title', [], $textDomain);

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

$op = \LotgdHttp::getQuery('op');
$repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Armor::class);

$params['opt'] = 'list';
if ('' == $op)
{
    $armorLevel = $repository->getMaxArmorLevel($session['user']['dragonkills']);

    $result = $repository->findByLevel($armorLevel);

    $params['opt'] = 'list';
    $params['stuff'] = $result;
}
elseif ('buy' == $op)
{
    $id = (int) \LotgdHttp::getQuery('id');

    $params['opt'] = 'buy';
    $params['result'] = $repository->findOneByArmorid($id);

    if ($params['result'])
    {
        $row = $params['result'];
        $params['buyIt'] = false;

        if ($row->getValue() <= ($session['user']['gold'] + $tradeinvalue))
        {
            $params['buyIt'] = true;

            debuglog(sprintf('spent "%s" gold on the "%s" armor', ($row->getValue() - $tradeinvalue), $row->getArmorname()));
            $session['user']['gold'] -= $row->getValue();
            $session['user']['armor'] = $row->getArmorname();
            $session['user']['gold'] += $tradeinvalue;
            $session['user']['defense'] -= $session['user']['armordef'];
            $session['user']['armordef'] = $row->getDefense();
            $session['user']['defense'] += $session['user']['armordef'];
            $session['user']['armorvalue'] = $row->getValue();
        }
    }
}
\LotgdNavigation::villageNav();

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- This is only for params not use for other purpose
$params = modulehook('page-armor-tpl-params', $params);
rawoutput(\LotgdTheme::renderThemeTemplate('page/armor.twig', $params));

page_footer();
