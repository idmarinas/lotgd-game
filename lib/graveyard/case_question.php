<?php

$hauntcost = (int) getsetting('hauntcost', 25);
$resurrectioncost = (int) getsetting('resurrectioncost', 100);

$default_actions = [
    [
        'textDomain' => $textDomain, //-- For translator text
        'textDomainNavigation' => $textDomainNavigation, //-- For translation navigation
        'link' => 'graveyard.php?op=resurrection', //-- Full link for action
        'linkText' => 'nav.resurrection', //-- Translator key for link
        'favor' => $resurrectioncost, //-- Cost
        'text' => '', //-- Translator key for output in body
        'highest' => 'section.question.highest', //-- Translator key, text that represent highest possible buy
        'params' => [
            'graveyardOwnerName' => $params['graveyardOwnerName']
        ]
    ]
];

//build navigation
$actions = modulehook('deathoverlord_actions', $default_actions);

$favorCostList = [];

foreach ($actions as $key => $value)
{
    if ($value['favor'] > $session['user']['deathpower'])
    {
        if (! isset($value['hidden']) || ! $value['hidden'])
        {
            unset($actions[$key]);
            continue; //-- Strip hidden
        }

        $actions[$key]['link'] = ''; //-- Deactivate not buyable
    }

    $favorCostList[$key] = $value['favor']; //cost of favor
}

asort($favorCostList);
end($favorCostList);

$high = key($favorCostList);
$params['highest'] = [
    $actions[$high]['highest'], //-- Translator key
    $actions[$high]['params'], //-- Translator params
    $actions[$high]['textDomain'] //-- Translator domain
];

\LotgdNavigation::addHeader('category.question.favor', [
    'params' => [
        'graveyardOwnerName' => $params['graveyardOwnerName']
    ]
]);

$params['texts'] = [];
foreach($actions as $key => $value)
{
    \LotgdNavigation::addNav('nav.question.favor', $value['link'], [
        'params' => [
            'favor' => $value['favor'],
            'text' => \LotgdTranslator::t($value['linkText'], $value['params'], $value['textDomainNavigation'])
        ]
    ]);

    if ($value['text'] ?? '')
    {
        $params['texts'] = [
            $value['text'],
            $value['params'],
            $value['textDomain']
        ];
    }
}

\LotgdNavigation::addHeader('category.other');

modulehook('ramiusfavors');
