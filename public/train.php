<?php

//addnews ready
// mail ready
// translator ready
require_once 'common.php';
require_once 'lib/systemmail.php';
require_once 'lib/increment_specialty.php';
require_once 'lib/fightnav.php';
require_once 'lib/taunt.php';
require_once 'lib/substitute.php';
require_once 'lib/experience.php';
require_once 'lib/forestoutcomes.php';

tlschema('train');

// Don't hook on to this text for your standard modules please, use "train" instead.
// This hook is specifically to allow modules that do other trains to create ambience.
$result = modulehook('train-text-domain', ['textDomain' => 'page-train', 'textDomainNavigation' => 'navigation-train']);
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

page_header('title', [], $textDomain);

$params = [
    'textDomain' => $textDomain
];

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

$battle = false;
$victory = false;
$defeat = false;

$mid = (int) \LotgdHttp::getQuery('master');

$repository = \Doctrine::getRepository('LotgdCore:Masters');

if ($mid)
{
    $master = $repository->extractEntity($repository->find($mid));
}
else
{
    $query = $repository->createQueryBuilder('u');

    $master = $query
        ->where('u.creaturelevel = :level')
        ->orderBy('rand()')
        ->setParameter('level', $session['user']['level'])
        ->setMaxResults(1)
        ->getQuery()
        ->getSingleResult()
    ;
    $master = $repository->extractEntity($master);
}

$params['masterName'] = $master['creaturename'];

if ($master > 0 && $session['user']['level'] < getsetting('maxlevel', 15))
{
    $params['master'] = $master;

    $mid = $master['creatureid'];

    $level = $session['user']['level'];
    $dks = $session['user']['dragonkills'];
    $exprequired = exp_for_next_level($level, $dks);

    $op = (string) \LotgdHttp::getQuery('op');

    if ('' == $op)
    {
        checkday();

        $params['tpl'] = 'default';

        \LotgdNavigation::addHeader('category.navigation');
        \LotgdNavigation::villageNav();

        \LotgdNavigation::addHeader('category.actions');
        \LotgdNavigation::addNav('nav.question', "train.php?op=question&master=$mid");
        \LotgdNavigation::addNav('nav.challenge', "train.php?op=challenge&master=$mid");

        if ($session['user']['superuser'] & SU_DEVELOPER)
        {
            \LotgdNavigation::addNav('nav.superuser', "train.php?op=challenge&victory=1&master=$mid");
        }
    }
    elseif ('challenge' == $op)
    {
        $params['tpl'] = 'challenge';

        if (\LotgdHttp::getQuery('victory'))
        {
            $victory = true;
            $defeat = false;

            if ($session['user']['experience'] < $exprequired)
            {
                $session['user']['experience'] = $exprequired;
            }
            $session['user']['seenmaster'] = 0;
        }

        if ($session['user']['seenmaster'])
        {
            \LotgdNavigation::addHeader('category.navigation');
            \LotgdNavigation::villageNav();
            \LotgdNavigation::addHeader('category.actions');
        }
        else
        {
            /* OK, let's fix the multimaster thing */
            $session['user']['seenmaster'] = 1;
            debuglog('Challenged master, setting seenmaster to 1');

            if ($session['user']['experience'] >= $exprequired)
            {
                restore_buff_fields();

                $master = buffbadguy($master, 'buffmaster');

                $attackstack['enemies'][0] = $master;
                $attackstack['options']['type'] = 'train';
                $session['user']['badguy'] = $attackstack;

                $battle = true;

                if ($victory)
                {
                    $badguy = $session['user']['badguy'];
                    $badguy = $badguy['enemies'][0];
                }
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

        checkday();

        \LotgdNavigation::addHeader('category.navigation');
        \LotgdNavigation::villageNav();

        \LotgdNavigation::addHeader('category.actions');
        \LotgdNavigation::addNav('nav.question', "train.php?op=question&master=$mid");
        \LotgdNavigation::addNav('nav.challenge', "train.php?op=challenge&master=$mid");

        if ($session['user']['superuser'] & SU_DEVELOPER)
        {
            \LotgdNavigation::addNav('nav.superuser', "train.php?op=challenge&victory=1&master=$mid");
        }

        $params['expRequired'] = $exprequired;
        $params['expNeed'] = $exprequired - $session['user']['experience'];
    }
    elseif ('autochallenge' == $op)
    {
        $params['tpl'] = 'autochallenge';

        \LotgdNavigation::addNav('nav.fight', "train.php?op=challenge&master=$mid");

        $params['playerHealed'] = false;

        if ($session['user']['hitpoints'] < $session['user']['maxhitpoints'])
        {
            $params['playerHealed'] = true;
            $session['user']['hitpoints'] = $session['user']['maxhitpoints'];
        }

        modulehook('master-autochallenge');

        if (getsetting('displaymasternews', 1))
        {
            addnews('news.autochallenge', [
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
        //-- Any data for personalize results
        $battleDefeatWhere = false; //-- Use for create a news, set to false for not create news
        $battleInForest = 'train'; //-- Indicating if is a Forest (true) or Graveyard (false)
        $battleDefeatLostGold = false; //-- Indicating if lost gold when lost in battle
        $battleDefeatLostExp = false; //-- Indicating if lost exp when lost in battle
        $battleDefeatCanDie = false; //-- Indicating if die when lost in battle
        $battleShowResult = false; //-- Show result of battle. If no need any extra modification of result no need change this

        require_once 'battle.php';

        suspend_buffs('allowintrain');
        // suspend_buffs('allowintrain', '`&Your pride prevents you from using extra abilities during the fight!`0`n');
        suspend_companions('allowintrain');

        //-- Superuser Gain level
        if (\LotgdHttp::getQuery('victory'))
        {
            $victory = true;
            $defeat = false;
        }

        if ($victory)
        {
            $session['user']['level']++;
            $session['user']['maxhitpoints'] += 10;
            $session['user']['soulpoints'] += 5;
            $session['user']['attack']++;
            $session['user']['defense']++;

            $session['user']['hitpoints'] = $session['user']['maxhitpoints'];

            // Fix the multimaster bug
            if (1 == getsetting('multimaster', 1))
            {
                $session['user']['seenmaster'] = 0;
                debuglog('Defeated master, setting seenmaster to 0');
            }
            $lotgdBattleContent['battleend'][] = ['battle.end.victory.end', [], $textDomain];
            $lotgdBattleContent['battleend'][] = ['battle.end.victory.level', ['level' => $session['user']['level']], $textDomain];
            $lotgdBattleContent['battleend'][] = ['battle.end.victory.hitpoints', ['hitpoints' => $session['user']['maxhitpoints']], $textDomain];
            $lotgdBattleContent['battleend'][] = ['battle.end.victory.attack', [], $textDomain];
            $lotgdBattleContent['battleend'][] = ['battle.end.victory.defense', [], $textDomain];

            if ($session['user']['level'] < 15)
            {
                $lotgdBattleContent['battleend'][] = ['battle.end.victory.master.new', [], $textDomain];
            }
            else
            {
                $lotgdBattleContent['battleend'][] = ['battle.end.victory.master.none', [], $textDomain];
            }

            if ($session['user']['referer'] > 0 && ($session['user']['level'] >= getsetting('referminlevel', 4) || $session['user']['dragonkills'] > 0) && $session['user']['refererawarded'] < 1)
            {
                $repository = \Doctrine::getRepository('LotgdCore:Accounts');
                $entity = $repository->find($session['user']['referer']);

                if ($entity)
                {
                    $donation = getsetting('refereraward', 25);

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

            increment_specialty('`^');

            // Level-Up companions
            // We only get one level per pageload. So we just add the per-level-values.
            // No need to multiply and/or substract anything.
            if (getsetting('companionslevelup', 1))
            {
                $newcompanions = $companions;

                foreach ($companions as $name => $companion)
                {
                    $companion['attack'] = $companion['attack'] + ($companion['attackperlevel'] ?? 0);
                    $companion['defense'] = $companion['defense'] + ($companion['defenseperlevel'] ?? 0);
                    $companion['maxhitpoints'] = $companion['maxhitpoints'] + ($companion['maxhitpointsperlevel'] ?? 0);
                    $companion['hitpoints'] = $companion['maxhitpoints'];
                    $newcompanions[$name] = $companion;
                }
                $companions = $newcompanions;
            }

            invalidatedatacache('list.php-warsonline');

            if (getsetting('displaymasternews', 1))
            {
                $days = (1 == $session['user']['age']) ? 'day' : 'days';

                if (getsetting('displaymasternews', 1))
                {
                    addnews('news.victory', [
                        'sex' => $session['user']['sex'],
                        'playerName' => $session['user']['name'],
                        'masterName' => $badguy['creaturename'],
                        'level' => $session['user']['level'],
                        'age' => $session['user']['age']
                    ], $textDomain);
                }
            }

            $result = modulehook('training-victory', ['badguy' => $badguy, 'messages' => []]);

            $lotgdBattleContent['battleend'] = array_merge($lotgdBattleContent['battleend'], $result['messages']);

            \LotgdNavigation::addHeader('category.navigation');
            \LotgdNavigation::villageNav();

            \LotgdNavigation::addHeader('category.actions');
            \LotgdNavigation::addNav('nav.question', 'train.php?op=question');
            \LotgdNavigation::addNav('nav.challenge', 'train.php?op=challenge');

            if ($session['user']['superuser'] & SU_DEVELOPER)
            {
                \LotgdNavigation::addNav('nav.superuser', 'train.php?op=challenge&victory=1');
            }
        }
        elseif ($defeat)
        {
            if (getsetting('displaymasternews', 1))
            {
                $taunt = select_taunt();

                addnews('deathmessage', [
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

            $lotgdBattleContent['battleend'][] = ['battle.end.defeat.end', ['masterName' => $badguy['creaturename']], $textDomain];

            $result = modulehook('training-defeat', ['badguy' => $badguy, 'messages' => []]);

            $lotgdBattleContent['battleend'] = array_merge($lotgdBattleContent['battleend'], $result['messages']);

            \LotgdNavigation::addHeader('category.navigation');
            \LotgdNavigation::villageNav();

            \LotgdNavigation::addHeader('category.actions');
            \LotgdNavigation::addNav('nav.question', "train.php?op=question&master=$mid");
            \LotgdNavigation::addNav('nav.challenge', "train.php?op=challenge&master=$mid");

            if ($session['user']['superuser'] & SU_DEVELOPER)
            {
                \LotgdNavigation::addNav('nav.superuser', "train.php?op=challenge&victory=1&master=$mid");
            }
        }
        else
        {
            fightnav(false, false, "train.php?master=$mid");
        }

        battleshowresults($lotgdBattleContent);

        if ($victory || $defeat)
        {
            unsuspend_buffs('allowintrain');
            // unsuspend_buffs('allowintrain', '`&You now feel free to make use of your buffs again!`0`n');
            unsuspend_companions('allowintrain');
        }
    }
}
else
{
    checkday();

    $params['tpl'] = 'maxlevel';

    \LotgdNavigation::addHeader('category.navigation');
    \LotgdNavigation::villageNav();
    \LotgdNavigation::addHeader('category.actions');
}

$params['battle'] = $battle;

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- This is only for params not use for other purpose
$params = modulehook('page-train-tpl-params', $params);
rawoutput(\LotgdTheme::renderThemeTemplate('page/train.twig', $params));

page_footer();
