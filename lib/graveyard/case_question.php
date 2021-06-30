<?php

use Lotgd\Core\Event\Graveyard;

$hauntcost        = (int) LotgdSetting::getSetting('hauntcost', 25);
$resurrectioncost = (int) LotgdSetting::getSetting('resurrectioncost', 100);

$default_actions = new Graveyard([
    [
        'textDomain'           => $textDomain, //-- For translator text
        'textDomainNavigation' => $textDomainNavigation, //-- For translation navigation
        'link'                 => 'graveyard.php?op=resurrection', //-- Full link for action
        'linkText'             => 'nav.resurrection', //-- Translator key for link
        'favor'                => $resurrectioncost, //-- Cost
        'text'                 => '', //-- Translator key for output in body
        'highest'              => 'section.question.highest', //-- Translator key, text that represent highest possible buy
        'params'               => [
            'graveyardOwnerName' => $params['graveyardOwnerName'],
        ],
    ],
]);

//build navigation
\LotgdEventDispatcher::dispatch($default_actions, Graveyard::DEATH_OVERLORD_ACTIONS);
$actions = modulehook('deathoverlord_actions', $default_actions->getData());

$favorCostList = [];

foreach ($actions as $key => $value)
{
    if ($value['favor'] > $session['user']['deathpower'])
    {
        if ( ! isset($value['hidden']) || ! $value['hidden'])
        {
            unset($actions[$key]);

            continue; //-- Strip hidden
        }

        $actions[$key]['link'] = ''; //-- Deactivate not buyable
    }

    $favorCostList[$key] = $value['favor']; //cost of favor
}

\asort($favorCostList);
\end($favorCostList);

$high              = \key($favorCostList);
$params['highest'] = [
    $actions[$high]['highest'], //-- Translator key
    $actions[$high]['params'], //-- Translator params
    $actions[$high]['textDomain'], //-- Translator domain
];

\LotgdNavigation::addHeader('category.question.favor', [
    'params' => [
        'graveyardOwnerName' => $params['graveyardOwnerName'],
    ],
]);

$params['texts'] = [];

foreach ($actions as $key => $value)
{
    \LotgdNavigation::addNav('nav.question.favor', $value['link'], [
        'params' => [
            'favor' => $value['favor'],
            'text'  => \LotgdTranslator::t($value['linkText'], $value['params'], $value['textDomainNavigation']),
        ],
    ]);

    if ($value['text'] ?? '')
    {
        $params['texts'] = [
            $value['text'],
            $value['params'],
            $value['textDomain'],
        ];
    }
}

\LotgdNavigation::addHeader('category.other');

\LotgdEventDispatcher::dispatch(new Graveyard(), Graveyard::DEATH_OVERLORD_FAVORS);
modulehook('ramiusfavors');
