<?php

// addnews ready.
// translator ready
// mail ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';
require_once 'lib/events.php';

// Don't hook on to this text for your standard modules please, use "graveyard" instead.
// This hook is specifically to allow modules that do other graveyards to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_graveyard', 'textDomainNavigation' => 'navigation_graveyard']);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_GRAVEYARD_PRE);
$result               = modulehook('graveyard-text-domain', $args->getArguments());
$textDomain           = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

$op = (string) \LotgdRequest::getQuery('op');

$skipgraveyardtext = handle_event('graveyard');

$params = [
    'textDomain'                    => $textDomain,
    'translation_domain'            => $textDomain,
    'translation_domain_navigation' => $textDomain,
    'graveyardOwnerName'            => (string) LotgdSetting::getSetting('deathoverlord', '`$Ramius`0'),
    'showGraveyardDesc'             => ! $skipgraveyardtext,
];

if ( ! $skipgraveyardtext)
{
    if ($session['user']['alive'])
    {
        redirect('village.php');
    }

    \LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();
}

$battle = false;

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

\LotgdKernel::get('lotgd_core.combat.buffer')->stripAllBuffs();
$max  = $session['user']['level'] * 10 + $session['user']['dragonkills'] * 2 + 50;
$args = new GenericEvent(null, ['favor' => round(10 * ($max - $session['user']['soulpoints']) / $max)]);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_GRAVEYARD_HEAL);
$favortoheal = modulehook('favortoheal', $args->getArguments());
$favortoheal = (int) $favortoheal['favor'];

$params['favorToHeal'] = $favortoheal;

\LotgdNavigation::addHeader('category.navigation');

if ('search' == $op)
{
    require_once 'lib/graveyard/case_battle_search.php';
}
elseif ('run' == $op)
{
    if (1 == e_rand(0, 2))
    {
        \LotgdFlashMessages::addSuccessMessage(\LotgdTranslator::t('flash.message.battle.run.success', [
            'graveyardOwnerName' => $params['graveyardOwnerName'],
        ], $textDomain));
        $favor = 5 + e_rand(0, $session['user']['level']);
        $favor = min($favor, $session['user']['deathpower']);

        if ($favor > 0)
        {
            \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.battle.run.lost', [
                'favor'              => $favor,
                'graveyardOwnerName' => $params['graveyardOwnerName'],
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

if ($battle)
{
    $params['tpl']               = 'default';
    $params['showGraveyardDesc'] = false;

    /** @var \Lotgd\Core\Combat\Battle $serviceBattle */
    $serviceBattle = \LotgdKernel::get('lotgd_core.combat.battle');

    $serviceBattle
        ->initialize()
        //-- Configuration
        ->setBattleZone('graveyard')
        ->enableGhost() //-- Player is dead so configure as Ghost
        ->disableGainGold()
        ->disableGainExp()
        ->disableDie()
        ->disableLostExp()
        ->disableLostGold()
        ->enableGainFavor()

        ->battleStart()
        ->battleProcess()
        ->battleEnd()
        ->battleResults()
    ;

    if ($serviceBattle->battleHasWinner())
    {
        $battle = false;
        $op     = '';
        \LotgdRequest::setQuery('op', '');
        $skipgraveyardtext           = true;
        $params['showGraveyardDesc'] = ! $skipgraveyardtext;
    }
    else
    {
        $serviceBattle->fightNav(false, true, 'graveyard.php');
    }

    //-- Revert changes not need more battle end
    $serviceBattle->disableGhost(); //-- Remember call this if you enableGhost
}

if ('enter' == $op)
{
    $params['tpl'] = 'enter';

    \LotgdNavigation::addHeader('category.places');
    \LotgdNavigation::addNav('nav.return.graveyard', 'graveyard.php');
    \LotgdNavigation::addNav('nav.shades', 'shades.php');
    \LotgdNavigation::addHeader('category.souls');
    \LotgdNavigation::addNav('nav.question', 'graveyard.php?op=question', [
        'params' => [
            'graveyardOwnerName' => $params['graveyardOwnerName'],
        ],
    ]);
    \LotgdNavigation::addNav('nav.restore', 'graveyard.php?op=restore', [
        'params' => [
            'favor' => $favortoheal,
        ],
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
            'graveyardOwnerName' => $params['graveyardOwnerName'],
        ],
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
            'graveyardOwnerName' => $params['graveyardOwnerName'],
        ],
    ]);
    \LotgdNavigation::addNav('nav.restore', 'graveyard.php?op=restore', [
        'params' => [
            'favor' => $favortoheal,
        ],
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
            'graveyardOwnerName' => $params['graveyardOwnerName'],
        ],
    ]);
    \LotgdNavigation::addNav('nav.restore', 'graveyard.php?op=restore', [
        'params' => [
            'favor' => $favortoheal,
        ],
    ]);

    \LotgdNavigation::addHeader('category.places');
    \LotgdNavigation::addNav('nav.graveyard', 'graveyard.php');
    \LotgdNavigation::addNav('nav.shades', 'shades.php');
    \LotgdNavigation::addNav('nav.return.mausoleum', 'graveyard.php?op=enter');

    $name = (string) \LotgdRequest::getPost('name');

    $repository           = \Doctrine::getRepository('LotgdCore:Avatar');
    $params['characters'] = $repository->findLikeName("%{$name}%", 100);
}
elseif ('haunt3' == $op)
{
    $params['tpl'] = 'haunt3';

    \LotgdNavigation::addNav('nav.question', 'graveyard.php?op=question', [
        'params' => [
            'graveyardOwnerName' => $params['graveyardOwnerName'],
        ],
    ]);
    \LotgdNavigation::addNav('nav.restore', 'graveyard.php?op=restore', [
        'params' => [
            'favor' => $favortoheal,
        ],
    ]);

    \LotgdNavigation::addHeader('category.places');
    \LotgdNavigation::addNav('nav.graveyard', 'graveyard.php');
    \LotgdNavigation::addNav('nav.shades', 'shades.php');
    \LotgdNavigation::addNav('nav.return.mausoleum', 'graveyard.php?op=enter');

    $characterId = (int) \LotgdRequest::getQuery('charid');

    $repository          = \Doctrine::getRepository('LotgdCore:Avatar');
    $params['character'] = $repository->find($characterId);

    $params['haunted'] = false;
    if ($params['character'])
    {
        $params['haunted'] = 0;
        if ( ! $params['character']['haunted'])
        {
            $session['user']['deathpower'] -= 25;

            $roll1 = e_rand(0, $params['character']->getLevel());
            $roll2 = e_rand(0, $session['user']['level']);

            $params['haunted'] = 1;
            $news              = 'news.haunted.fail';
            if ($roll2 > $roll1)
            {
                $news              = 'news.haunted.success';
                $params['haunted'] = true;

                $params['character']->setHauntedby($session['user']['name']);

                \Doctrine::persist($params['character']);
                \Doctrine::flush();

                $subject = ['mail.haunted.subject', [], $textDomain];
                $message = ['mail.haunted.message', [
                    'playerName' => $session['user']['name'],
                ], $textDomain];

                systemmail($params['character']->getAcct()->getAcctid(), $subject, $message);
            }

            \LotgdTool::addNews($text, [
                'playerName'  => $session['user']['name'],
                'hauntedName' => $params['character']->getName(),
            ], $textDomain);
        }
    }
}
elseif ( ! $battle)
{
    $params['tpl'] = 'default';

    \LotgdNavigation::graveyardNav($textDomainNavigation);
}

\LotgdEventDispatcher::dispatch(new GenericEvent(), Events::PAGE_GRAVEYARD);
modulehook('deathoverlord');

//-- This is only for params not use for other purpose
$args = new GenericEvent(null, $params);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_GRAVEYARD_POST);
$params = modulehook('page-graveyard-tpl-params', $args->getArguments());
\LotgdResponse::pageAddContent(\LotgdTheme::render('page/graveyard.html.twig', $params));

if ('default' == $params['tpl'])
{
    module_display_events('graveyard', 'graveyard.php');
}

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- Finalize page
\LotgdResponse::pageEnd();
