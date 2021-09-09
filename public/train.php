<?php

//addnews ready
// mail ready
// translator ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';

// Don't hook on to this text for your standard modules please, use "train" instead.
// This hook is specifically to allow modules that do other trains to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_train', 'textDomainNavigation' => 'navigation_train']);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_TRAIN_PRE);
$result = modulehook('train-text-domain', $args->getArguments());
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

/** @var \Lotgd\Core\Combat\Battle $serviceBattle */
$serviceBattle = \LotgdKernel::get('lotgd_core.combat.battle');

$params = [
    'textDomain' => $textDomain
];

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

$battle = false;

$masterId = (int) \LotgdRequest::getQuery('master');

$repository = \Doctrine::getRepository('LotgdCore:Masters');

if ($masterId !== 0)
{
    $master = $repository->findOneMasterById($masterId);
}
else
{
    $query = $repository->createQueryBuilder('u');

    $master = $query
        ->where('u.creaturelevel = :level')
        ->orderBy('rand()')
    ;
    $query = $repository->createTranslatebleQuery($master);
    $query
        ->setMaxResults(1)
        ->setParameter('level', $session['user']['level'])
    ;

    $master = $query->getArrayResult()[0];
}

$params['masterName'] = $master['creaturename'];

if ($master > 0 && $session['user']['level'] < LotgdSetting::getSetting('maxlevel', 15))
{
    $params['master'] = $master;

    $masterId = $master['creatureid'];

    $level = $session['user']['level'];
    $dks = $session['user']['dragonkills'];
    $exprequired = \LotgdTool::expForNextLevel($level, $dks);

    $op = (string) \LotgdRequest::getQuery('op');

    if ('' == $op)
    {
        \LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

        $params['tpl'] = 'default';

        \LotgdNavigation::addHeader('category.navigation');
        \LotgdNavigation::villageNav();

        \LotgdNavigation::addHeader('category.actions');
        \LotgdNavigation::addNav('nav.question', "train.php?op=question&master=$masterId");
        \LotgdNavigation::addNav('nav.challenge', "train.php?op=challenge&master=$masterId");

        if (($session['user']['superuser'] & SU_DEVELOPER) !== 0)
        {
            \LotgdNavigation::addNav('nav.superuser', "train.php?op=challenge&victory=1&master=$masterId");
        }
    }
    elseif ('challenge' == $op)
    {
        $params['tpl'] = 'challenge';

        if (\LotgdRequest::getQuery('victory'))
        {
            if ($session['user']['experience'] < $exprequired)
            {
                $session['user']['experience'] = $exprequired;
            }
            $session['user']['seenmaster'] = 0;
        }

        if ($session['user']['seenmaster'] && 0 == (int) \LotgdSetting::getSetting('multimaster', 0))
        {
            \LotgdNavigation::addHeader('category.navigation');
            \LotgdNavigation::villageNav();
            \LotgdNavigation::addHeader('category.actions');
        }
        else
        {
            /* OK, let's fix the multimaster thing */
            $session['user']['seenmaster'] = 1;
            \LotgdLog::debug('Challenged master, setting seenmaster to 1');

            if ($session['user']['experience'] >= $exprequired)
            {
                \LotgdKernel::get('lotgd_core.combat.buffer')->restoreBuffFields();

                $master = \LotgdKernel::get('lotgd_core.tool.creature_functions')->buffBadguy($master, 'buffmaster');

                $attackstack['enemies'][0] = $master;
                $attackstack['options']['type'] = 'train';
                $session['user']['badguy'] = $attackstack;

                $battle = true;
            }
            else
            {
                $params['playerWeapon'] = $session['user']['weapon'];
                $params['playerArmor'] = $session['user']['armor'];

                \LotgdNavigation::addHeader('category.navigation');
                \LotgdNavigation::villageNav();
                \LotgdNavigation::addHeader('category.actions');
            }
        }
    }
    elseif ('question' == $op)
    {
        $params['tpl'] = 'question';

        \LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

        \LotgdNavigation::addHeader('category.navigation');
        \LotgdNavigation::villageNav();

        \LotgdNavigation::addHeader('category.actions');
        \LotgdNavigation::addNav('nav.question', "train.php?op=question&master=$masterId");
        \LotgdNavigation::addNav('nav.challenge', "train.php?op=challenge&master=$masterId");

        if (($session['user']['superuser'] & SU_DEVELOPER) !== 0)
        {
            \LotgdNavigation::addNav('nav.superuser', "train.php?op=challenge&victory=1&master=$masterId");
        }

        $params['expRequired'] = $exprequired;
        $params['expNeed'] = $exprequired - $session['user']['experience'];
    }
    elseif ('autochallenge' == $op)
    {
        $params['tpl'] = 'autochallenge';

        \LotgdNavigation::addNav('nav.fight', "train.php?op=challenge&master=$masterId");

        $params['playerHealed'] = false;

        if ($session['user']['hitpoints'] < $session['user']['maxhitpoints'])
        {
            $params['playerHealed'] = true;
            $session['user']['hitpoints'] = $session['user']['maxhitpoints'];
        }

        \LotgdEventDispatcher::dispatch(new GenericEvent(), Events::PAGE_TRAIN_AUTOCHALLENGE);
        modulehook('master-autochallenge');

        if (LotgdSetting::getSetting('displaymasternews', 1))
        {
            \LotgdTool::addNews('news.autochallenge', [
                'playerName' => $session['user']['name'],
                'masterName' => $master['creaturename']
            ], $textDomain);
        }
    }

    if ('fight' == $op)
    {
        $battle = true;
    }
    elseif ('run' == $op)
    {
        \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('flash.message.fight.run', [], $textDomain));

        $op = 'fight';
        $battle = true;
    }

    if ($battle)
    {
        /** @var \Lotgd\Core\Combat\Battle $serviceBattle */
        $serviceBattle = \LotgdKernel::get('lotgd_core.combat.battle');

        //-- Superuser Gain level
        if (\LotgdRequest::getQuery('victory'))
        {
            $session['user']['badguy']['enemies'][0]['creaturehealth'] = 0;
        }

        $badguy = $session['user']['badguy']['enemies'][0];

        //-- Battle zone.
        $serviceBattle->initialize(); //--* Initialize the battle
        $serviceBattle->suspendBuffs('allowintrain');
        $serviceBattle->suspendCompanions('allowintrain');
        $serviceBattle
            //-- Configuration
            ->setBattleZone('train') //-- Battle zone is "forest" by default.
            ->disableDie()
            ->disableCreateNews()
            ->disableProccessBatteResults()
            //-- Battle
            ->battleStart() //--* Start the battle.
            ->battleProcess() //--* Proccess the battle rounds.
            ->battleEnd() //--* End the battle for this petition
        ;

        if ($serviceBattle->isVictory())
        {
            $session['user']['level']++;
            $session['user']['maxhitpoints'] += 10;
            $session['user']['soulpoints'] += 5;
            $session['user']['attack']++;
            $session['user']['defense']++;

            $session['user']['hitpoints'] = $session['user']['maxhitpoints'];

            // Fix the multimaster bug
            if (1 == LotgdSetting::getSetting('multimaster', 1))
            {
                $session['user']['seenmaster'] = 0;
                \LotgdLog::debug('Defeated master, setting seenmaster to 0');
            }

            $serviceBattle->addContextToBattleEnd(['battle.end.victory.end', [], $textDomain]);
            $serviceBattle->addContextToBattleEnd(['battle.end.victory.level', ['level' => $session['user']['level']], $textDomain]);
            $serviceBattle->addContextToBattleEnd(['battle.end.victory.hitpoints', ['hitpoints' => $session['user']['maxhitpoints']], $textDomain]);
            $serviceBattle->addContextToBattleEnd(['battle.end.victory.attack', [], $textDomain]);
            $serviceBattle->addContextToBattleEnd(['battle.end.victory.defense', [], $textDomain]);

            if ($session['user']['level'] < 15)
            {
                $serviceBattle->addContextToBattleEnd(['battle.end.victory.master.new', [], $textDomain]);
            }
            else
            {
                $serviceBattle->addContextToBattleEnd(['battle.end.victory.master.none', [], $textDomain]);
            }

            if ($session['user']['referer'] > 0 && ($session['user']['level'] >= LotgdSetting::getSetting('referminlevel', 4) || $session['user']['dragonkills'] > 0) && $session['user']['refererawarded'] < 1)
            {
                $repository = \Doctrine::getRepository('LotgdCore:User');
                $entity = $repository->find($session['user']['referer']);

                if ($entity)
                {
                    $donation = LotgdSetting::getSetting('refereraward', 25);

                    $subject = [ 'mail.referer.subject', [], $textDomain ];
                    $message = [ 'mail.referer.message' , [
                        'playerName' => $session['user']['name'],
                        'level' => $session['user']['level'],
                        'donationPoints' => $donation
                    ], $textDomain ];

                    $entity->setDonation($entity->getDonation() + $donation);

                    \Doctrine::persist($entity);
                    \Doctrine::flush();

                    systemmail($session['user']['referer'], $subject, $message);
                }

                $session['user']['refererawarded'] = 1;
            }

            LotgdKernel::get('lotgd_core.tool.player_functions')->incrementSpecialty('`^');

            // Level-Up companions
            // We only get one level per pageload. So we just add the per-level-values.
            // No need to multiply and/or substract anything.
            if (LotgdSetting::getSetting('companionslevelup', 1))
            {
                $newcompanions = $companions;

                foreach ($companions as $name => $companion)
                {
                    $companion['attack'] += $companion['attackperlevel'] ?? 0;
                    $companion['defense'] += $companion['defenseperlevel'] ?? 0;
                    $companion['maxhitpoints'] += $companion['maxhitpointsperlevel'] ?? 0;
                    $companion['hitpoints'] = $companion['maxhitpoints'];
                    $newcompanions[$name] = $companion;
                }
                $companions = $newcompanions;
            }

            if (LotgdSetting::getSetting('displaymasternews', 1))
            {
                $days = (1 == $session['user']['age']) ? 'day' : 'days';

                if (LotgdSetting::getSetting('displaymasternews', 1))
                {
                    \LotgdTool::addNews('news.victory', [
                        'sex' => $session['user']['sex'],
                        'playerName' => $session['user']['name'],
                        'masterName' => $badguy['creaturename'],
                        'level' => $session['user']['level'],
                        'age' => $session['user']['age']
                    ], $textDomain);
                }
            }

            $args = new GenericEvent(null, ['badguy' => $badguy, 'messages' => []]);
            \LotgdEventDispatcher::dispatch($args, Events::PAGE_TRAIN_TRANING_VICTORY);
            $result = modulehook('training-victory', $args->getArguments());

            array_walk($result['messages'], function ($elem) use ($serviceBattle)
            {
                $serviceBattle->addContextToBattleEnd($elem);
            });

            \LotgdNavigation::addHeader('category.navigation');
            \LotgdNavigation::villageNav();

            \LotgdNavigation::addHeader('category.actions');
            \LotgdNavigation::addNav('nav.question', 'train.php?op=question');
            \LotgdNavigation::addNav('nav.challenge', 'train.php?op=challenge');

            if (($session['user']['superuser'] & SU_DEVELOPER) !== 0)
            {
                \LotgdNavigation::addNav('nav.superuser', 'train.php?op=challenge&victory=1');
            }
        }
        elseif ($serviceBattle->isDefeat())
        {
            if (LotgdSetting::getSetting('displaymasternews', 1))
            {
                $taunt = \LotgdTool::selectTaunt();

                \LotgdTool::addNews('deathmessage', [
                    'deathmessage' => [
                        'deathmessage' => 'news.defeated',
                        'params' => [
                            'playerName' => $session['user']['name'],
                            'masterName' => $badguy['creaturename']
                        ],
                        'textDomain' => $textDomain
                    ],
                    'taunt' => $taunt
                ], '');
            }

            $session['user']['hitpoints'] = $session['user']['maxhitpoints'];

            $serviceBattle->addContextToBattleEnd(['battle.end.defeat.end', ['masterName' => $badguy['creaturename']], $textDomain]);

            $args = new GenericEvent(null, ['badguy' => $badguy, 'messages' => []]);
            $result = modulehook('training-defeat', $args->getArguments());

            array_walk($result['messages'], function ($elem) use ($serviceBattle)
            {
                $serviceBattle->addContextToBattleEnd($elem);
            });

            \LotgdNavigation::addHeader('category.navigation');
            \LotgdNavigation::villageNav();

            \LotgdNavigation::addHeader('category.actions');
            \LotgdNavigation::addNav('nav.question', "train.php?op=question&master=$masterId");
            \LotgdNavigation::addNav('nav.challenge', "train.php?op=challenge&master=$masterId");

            if (($session['user']['superuser'] & SU_DEVELOPER) !== 0)
            {
                \LotgdNavigation::addNav('nav.superuser', "train.php?op=challenge&victory=1&master=$masterId");
            }
        }
        else
        {
            $serviceBattle->fightNav(false, false, "train.php?master=$masterId");
        }

        //--* Add results to response by default (use ->battleResults(true) if you want result results)
        $serviceBattle->battleResults();

        if ($serviceBattle->battleHasWinner())
        {
            $serviceBattle->unsuspendBuffs('allowintrain');
            $serviceBattle->unSuspendCompanions('allowintrain');
        }
    }
}
else
{
    \LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

    $params['tpl'] = 'maxlevel';

    \LotgdNavigation::addHeader('category.navigation');
    \LotgdNavigation::villageNav();
    \LotgdNavigation::addHeader('category.actions');
}

$params['battle'] = $battle;

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- This is only for params not use for other purpose
$args = new GenericEvent(null, $params);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_TRAIN_POST);
$params = modulehook('page-train-tpl-params', $args->getArguments());
\LotgdResponse::pageAddContent(\LotgdTheme::render('page/train.html.twig', $params));

//-- Finalize page
\LotgdResponse::pageEnd();
