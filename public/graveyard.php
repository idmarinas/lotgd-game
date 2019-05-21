<?php

// addnews ready.
// translator ready
// mail ready
require_once 'common.php';
require_once 'lib/buffs.php';
require_once 'lib/events.php';

tlschema('graveyard');

// Don't hook on to this text for your standard modules please, use "graveyard" instead.
// This hook is specifically to allow modules that do other graveyards to create ambience.
$result = modulehook('village-text-domain', ['textDomain' => 'page-graveyard', 'textDomainNavigation' => 'navigation-graveyard']);
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

page_header('title', [], $textDomain);

$op = (string) \LotgdHttp::getQuery('op');

$skipgraveyardtext = handle_event('graveyard');

$params = [
    'textDomain' => $textDomain,
    'graveyardOwnerName' => (string) getsetting('deathoverlord', '`$Ramius`0'),
    'showGraveyardDesc' => ! $skipgraveyardtext
];

if (! $skipgraveyardtext)
{
    if ($session['user']['alive'])
    {
        return redirect('village.php');
    }

    checkday();
}

$battle = false;

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

strip_all_buffs();
$max = $session['user']['level'] * 10 + $session['user']['dragonkills'] * 2 + 50;
$favortoheal = modulehook('favortoheal', ['favor' => round(10 * ($max - $session['user']['soulpoints']) / $max)]);
$favortoheal = (int) $favortoheal['favor'];

$params['favorToHeal'] = $favortoheal;

if ('search' == $op)
{
    require_once 'lib/graveyard/case_battle_search.php';
}
elseif('run' == $op)
{
    if (1 == e_rand(0, 2))
    {
        \LotgdFlashMessages::addSuccessMessage(\LotgdTranslator::t('flash.message.battle.run.success', [
            'graveyardOwnerName' => $params['graveyardOwnerName']
        ], $textDomain));
        $favor = 5 + e_rand(0, $session['user']['level']);
        $favor = min($favor, $session['user']['deathpower']);

        if ($favor > 0)
        {
            \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.battle.run.lost', [
                'favor' => $favor,
                'graveyardOwnerName' => $params['graveyardOwnerName']
            ], $textDomain));
            $session['user']['deathpower'] -= $favor;
        }

        \LotgdNavigation::addNav('nav.return.graveyard', 'graveyard.php');
    }
    else
    {
        \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.battle.run.fail', [], $textDomain));
        $battle = true;
    }
}
elseif ('fight' == $op)
{
    $battle = true;
}
elseif ('enter' == $op)
{
    $params['tpl'] = 'enter';

    \LotgdNavigation::addHeader('category.places');
    \LotgdNavigation::addNav('nav.return.graveyard', 'graveyard.php');
    \LotgdNavigation::addNav('nav.shades', 'shades.php');
    \LotgdNavigation::addHeader('category.souls');
    \LotgdNavigation::addNav('nav.question', 'graveyard.php?op=question', [
        'params' => [
            'graveyardOwnerName', $params['graveyardOwnerName']
        ]
    ]);
    \LotgdNavigation::addNav('nav.restore', 'graveyard.php?op=restore', [
        'params' => [
            'favor', $favortoheal
        ]
    ]);
}
elseif ('restore' == $op)
{
    $params['tpl'] = 'restore';

    if ($session['user']['soulpoints'] < $max)
    {
        if ($session['user']['deathpower'] >= $favortoheal)
        {
            $params['restored'] = true;
            $session['user']['deathpower'] -= $favortoheal;
            $session['user']['soulpoints'] = $max;
        }
        else
        {
            $params['restored'] = false;
        }
    }

    \LotgdNavigation::addHeader('category.places');
    \LotgdNavigation::addNav('nav.return.graveyard', 'graveyard.php');
    \LotgdNavigation::addNav('nav.shades', 'shades.php');

    \LotgdNavigation::addHeader('category.souls');
    \LotgdNavigation::addNav('nav.question', 'graveyard.php?op=question', [
        'params' => [
            'graveyardOwnerName', $params['graveyardOwnerName']
        ]
    ]);
}
elseif ('resurrection' == $op)
{
    $params['tpl'] = 'resurrection';

    \LotgdNavigation::addNav('nav.continue', 'newday.php?resurrection=true');
}
elseif ('question' == $op)
{
    $params['tpl'] = 'question';

    \LotgdNavigation::addHeader('category.places');
    \LotgdNavigation::addNav('nav.return.graveyard', 'graveyard.php');
    \LotgdNavigation::addNav('nav.shades', 'shades.php');

    \LotgdNavigation::addHeader('category.souls');
    \LotgdNavigation::addNav('nav.question', 'graveyard.php?op=question', [
        'params' => [
            'graveyardOwnerName', $params['graveyardOwnerName']
        ]
    ]);
    \LotgdNavigation::addNav('nav.restore', 'graveyard.php?op=restore', [
        'params' => [
            'favor', $favortoheal
        ]
    ]);

    require_once 'lib/graveyard/case_question.php';
}
elseif ('haunt' == $op)
{
    $params['tpl'] = 'haunt';

    \LotgdNavigation::addHeader('category.places');
    \LotgdNavigation::addNav('nav.graveyard', 'graveyard.php');
    \LotgdNavigation::addNav('nav.shades', 'shades.php');
    \LotgdNavigation::addNav('nav.return.mausoleum', 'graveyard.php?op=enter');
}
elseif ('haunt2' == $op)
{
    $params['tpl'] = 'haunt2';

    \LotgdNavigation::addNav('nav.question', 'graveyard.php?op=question', [
        'params' => [
            'graveyardOwnerName', $params['graveyardOwnerName']
        ]
    ]);
    \LotgdNavigation::addNav('nav.restore', 'graveyard.php?op=restore', [
        'params' => [
            'favor', $favortoheal
        ]
    ]);

    \LotgdNavigation::addHeader('category.places');
    \LotgdNavigation::addNav('nav.graveyard', 'graveyard.php');
    \LotgdNavigation::addNav('nav.shades', 'shades.php');
    \LotgdNavigation::addNav('nav.return.mausoleum', 'graveyard.php?op=enter');

    $name = (string) \LotgdHttp::getPost('name');

    $repository = \Doctrine::getRepository('LotgdCore:Characters');
    $params['characters'] = $repository->findLikeName("%{$name}%", 100);
}
elseif ('haunt3' == $op)
{
    require 'lib/systemmail.php';

    $params['tpl'] = 'haunt3';

    \LotgdNavigation::addNav('nav.question', 'graveyard.php?op=question', [
        'params' => [
            'graveyardOwnerName', $params['graveyardOwnerName']
        ]
    ]);
    \LotgdNavigation::addNav('nav.restore', 'graveyard.php?op=restore', [
        'params' => [
            'favor', $favortoheal
        ]
    ]);

    \LotgdNavigation::addHeader('category.places');
    \LotgdNavigation::addNav('nav.graveyard', 'graveyard.php');
    \LotgdNavigation::addNav('nav.shades', 'shades.php');
    \LotgdNavigation::addNav('nav.return.mausoleum', 'graveyard.php?op=enter');

    $characterId = (int) \LotgdHttp::getQuery('charid');

    $repository = \Doctrine::getRepository('LotgdCore:Characters');
    $params['character'] = $repository->find($characterId);

    $params['haunted'] = false;
    if ($params['character'])
    {
        $params['haunted'] = 0;
        if (! $params['character']['haunted'])
        {
            $session['user']['deathpower'] -= 25;

            $roll1 = e_rand(0, $params['character']->getLevel());
            $roll2 = e_rand(0, $session['user']['level']);

            $params['haunted'] = 1;
            $news = 'news.haunted.fail';
            if ($roll2 > $roll1)
            {
                $news = 'news.haunted.success';
                $params['haunted'] = true;

                $params['character']->setHauntedby($session['user']['name']);

                \Doctrine::persist($params['character']);
                \Doctrine::flush();

                $subject = ['mail.haunted.subject', [], $textDomain];
                $message = ['mail.haunted.message', [
                    'playerName' => $session['user']['name']
                ], $textDomain];


                systemmail($params['character']->getAcct()->getAcctid(), $subject, $message);
            }

            addnews($text, [
                'playerName' => $session['user']['name'],
                'hauntedName' => $params['character']->getName()
            ], $textDomain);
        }
    }
}
else
{
    $params['tpl'] = 'default';

    \LotgdNavigation::addNav('nav.return.shades', 'shades.php');

    if ($session['user']['gravefights'])
    {
        addnav('Torment');
        addnav('Look for Something to Torment', 'graveyard.php?op=search');
    }

    \LotgdNavigation::addHeader('category.places');
    \LotgdNavigation::addNav('nav.warriors', 'list.php');
    \LotgdNavigation::addNav('nav.mausoleum', 'graveyard.php?op=enter');
}

if ($battle)
{
    //-- Any data for personalize results
    $battleDefeatWhere = 'graveyard';
    $battleInForest = false;
    $battleDefeatLostGold = false;
    $battleDefeatLostExp = false;
    $battleDefeatCanDie = false;

    //make some adjustments to the user to put them on mostly even ground
    //with the undead guy.
    $originalhitpoints = $session['user']['hitpoints'];
    $session['user']['hitpoints'] = $session['user']['soulpoints'];
    $originalattack = $session['user']['attack'];
    $originaldefense = $session['user']['defense'];

    require_once 'battle.php';

    //reverse those adjustments, battle calculations are over.
    $session['user']['attack'] = $originalattack;
    $session['user']['defense'] = $originaldefense;
    $session['user']['soulpoints'] = $session['user']['hitpoints'];
    $session['user']['hitpoints'] = $originalhitpoints;

    if ($victory)
    {
        $op = '';
        \LotgdHttp::setQuery('op', '');
        $skipgraveyardtext = true;
        $params['showGraveyardDesc'] = ! $skipgraveyardtext;
    }
    else
    {
        require_once 'lib/fightnav.php';

        fightnav(false, true, 'graveyard.php');
    }
}

modulehook('deathoverlord', []);

//-- This is only for params not use for other purpose
$params = modulehook('page-graveyard-tpl-params', $params);
rawoutput(\LotgdTheme::renderThemeTemplate('page/graveyard.twig', $params));

if ('default' == $params['tpl'])
{
    module_display_events('graveyard', 'graveyard.php');
}

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

page_footer();
