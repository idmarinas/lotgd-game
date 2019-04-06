<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';

tlschema('armor');

checkday();
$tradeinvalue = round(($session['user']['armorvalue'] * .75), 0);
$basetext = [
    'title' => 'title',
    'description' => 'description',
    'tradein' => 'tradein',
    'nosuchweapon' => 'nosuchweapon',
    'tryagain' => 'tryagain',
    'notenoughgold' => 'notenoughgold',
    'payarmor' => 'payarmor',
    'table' => [
        'header' => [
            'name' => 'table.header.name',
            'defense' => 'table.header.defense',
            'cost' => 'table.header.cost',
        ],
        'notFound' => 'table.notFound'
    ]
];

$schemas = [
    'title' => 'page-armor',
    'description' => 'page-armor',
    'tradein' => 'page-armor',
    'nosuchweapon' => 'page-armor',
    'tryagain' => 'page-armor',
    'notenoughgold' => 'page-armor',
    'payarmor' => 'page-armor',
    //-- For list of armors (table)
    'table' => 'page-armor',
];

$basetext['schemas'] = $schemas;

// This hook is specifically to allow modules that do other armor to create ambience.
$texts = modulehook('armortext', $basetext);
$schemas = $texts['schemas'];
unset($texts['schemas']);

$params = [
    'texts' => $texts,
    'schemas' => $schemas
];

page_header($texts['title'], [], $schemas['title']);

$op = \LotgdHttp::getQuery('op');
$repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Armor::class);

$params['opt'] = 'list';
if ('' == $op)
{
    $armorLevel = $repository->getMaxArmorLevel($session['user']['dragonkills']);

    $result = $repository->findByLevel($armorLevel);

    $params['opt'] = 'list';
    $params['stuff'] = $result;
    $params['tradeinvalue'] = $tradeinvalue;
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

            debuglog(sprintf('spent "%s" gold on the "%s" armor'), ($row->getValue() - $tradeinvalue), $row->getArmorname());
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

rawoutput(LotgdTheme::renderThemeTemplate('page/armor.twig', $params));

page_footer();
