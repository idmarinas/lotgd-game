<?php

// addnews ready
// translator ready
// mail ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';
require_once 'lib/events.php';

// Don't hook on to this text for your standard modules please, use "forest" instead.
// This hook is specifically to allow modules that do other forests to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_forest', 'textDomainNavigation' => 'navigation_forest']);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_FOREST_PRE);
$result               = modulehook('forest-text-domain', $args->getArguments());
$textDomain           = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

/** @var Lotgd\Core\Http\Request */
$request = \LotgdKernel::get(\Lotgd\Core\Http\Request::class);
/** @var \Lotgd\Core\Tool\CreatureFunction */
$creatureFunctions = LotgdKernel::get('lotgd_core.tool.creature_functions');

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

$dontDisplayForestMessage = handle_event('forest');

$params = [
    'textDomain'        => $textDomain,
    'showForestMessage' => ! $dontDisplayForestMessage,
];

$op = (string) $request->query->get('op');

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

$battle = false;

$method = 'index';

if ('dragon' == $op)
{
    require_once 'lib/partner.php';

    $params['partner'] = get_partner();

    \LotgdNavigation::addNav('nav.cave', 'dragon.php');
    \LotgdNavigation::addNav('nav.baby', 'inn.php?op=fleedragon');

    $session['user']['seendragon'] = 1;
}
elseif ('search' == $op)
{
    \LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

    if ($session['user']['turns'] <= 0)
    {
        \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('flash.message.tired', [], $textDomain));

        $op = '';
        $request->query->set('op', '');
    }
    else
    {
        $args = new GenericEvent();
        \LotgdEventDispatcher::dispatch($args, Events::PAGE_FOREST_SEARCH);
        modulehook('forestsearch', $args->getArguments());

        $args = new GenericEvent(null, [
            'soberval' => 0.9,
            'sobermsg' => \LotgdTranslator::t('sober.message', [], $textDomain),
            'schema'   => 'forest',
        ]);
        modulehook('soberup', $args->getArguments());

        if (0 != module_events('forest', LotgdSetting::getSetting('forestchance', 15)))
        {
            if (\LotgdNavigation::checkNavs())
            {
                \LotgdResponse::pageEnd();
            }

            // If we're showing the forest, make sure to reset the special
            // and the specialmisc
            $session['user']['specialinc']  = '';
            $session['user']['specialmisc'] = '';
            $dontDisplayForestMessage       = true;
            $params['showForestMessage']    = ! $dontDisplayForestMessage;

            $op = '';
            $request->query->set('op', '');
        }
        else
        {
            --$session['user']['turns'];
            $battle = true;

            $plev = 0;
            $nlev = 0;

            if (1 == e_rand(0, 2))
            {
                $plev = (1 == e_rand(1, 5) ? 1 : 0);
                $nlev = (1 == e_rand(1, 3) ? 1 : 0);
            }

            $type = (string) $request->query->get('type');

            $extrabuff = 0;

            if ('slum' == $type)
            {
                ++$nlev;
            }
            elseif ('thrill' == $type)
            {
                ++$plev;
                \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('flash.message.thrill', [], $textDomain));
            }
            elseif ('suicide' == $type)
            {
                if ($session['user']['level'] <= 7)
                {
                    ++$plev;
                    $extrabuf = .25;
                }
                elseif ($session['user']['level'] < 14)
                {
                    $plev += 2;
                    $extrabuf = 0;
                }
                else
                {
                    ++$plev;
                    $extrabuff = .4;
                }
                \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.suicide', [], $textDomain));
            }

            $multi          = 1;
            $targetlevel    = ($session['user']['level'] + $plev - $nlev);
            $mintargetlevel = $targetlevel;

            if (LotgdSetting::getSetting('multifightdk', 10) <= $session['user']['dragonkills'])
            {
                if (\mt_rand(1, 100) <= LotgdSetting::getSetting('multichance', 25))
                {
                    $multi = e_rand(LotgdSetting::getSetting('multibasemin', 2), LotgdSetting::getSetting('multibasemax', 3));

                    if ('slum' == $type)
                    {
                        $multi -= e_rand(LotgdSetting::getSetting('multislummin', 0), LotgdSetting::getSetting('multislummax', 1));

                        $mintargetlevel = $targetlevel - 2;

                        if (\mt_rand(0, 1))
                        {
                            $mintargetlevel = $targetlevel - 1;
                        }
                    }
                    elseif ('thrill' == $type)
                    {
                        $multi += e_rand((int) LotgdSetting::getSetting('multithrillmin', 1), (int) LotgdSetting::getSetting('multithrillmax', 2));

                        $mintargetlevel = $targetlevel - 1;

                        if (\mt_rand(0, 1))
                        {
                            ++$targetlevel;
                            $mintargetlevel = $targetlevel - 1;
                        }
                    }
                    elseif ('suicide' == $type)
                    {
                        $multi += e_rand(LotgdSetting::getSetting('multisuimin', 2), LotgdSetting::getSetting('multisuimax', 4));

                        if (\mt_rand(0, 1))
                        {
                            $mintargetlevel = $targetlevel - 1;
                        }
                        else
                        {
                            ++$targetlevel;
                            $mintargetlevel = $targetlevel - 1;
                        }
                    }
                    $multi = \min($multi, $session['user']['level']);
                }
            }
            else
            {
                $multi = 1;
            }

            $multi          = (int) \max(1, $multi);
            $targetlevel    = (int) \max(1, $targetlevel);
            $mintargetlevel = (int) \max(1, \min($mintargetlevel, $targetlevel));

            if ($targetlevel > 17)
            {
                $multi += $targetlevel - 17; //-- More dificult if have more level than 15
                // $targetlevel = 17; //-- Not avoid level range setting
            }
            \LotgdResponse::pageDebug("Creatures: {$multi} Targetlevel: {$targetlevel} Mintargetlevel: {$mintargetlevel}");

            $packofmonsters = (bool) (0 == \mt_rand(0, 5) && LotgdSetting::getSetting('allowpackofmonsters', true)); // true or false
            $packofmonsters = ($multi > 1) ? $packofmonsters : false;

            $result = $creatureFunctions->lotgdSearchCreature($multi, $targetlevel, $mintargetlevel, $packofmonsters, true);

            \LotgdKernel::get('lotgd_core.combat.buffer')->restoreBuffFields();

            if (empty($result))
            {
                // There is nothing in the database to challenge you, let's
                // give you a doppelganger.
                $badguy = $creatureFunctions->lotgdGenerateDoppelganger($session['user']['level']);

                $stack[] = $badguy;
            }
            else
            {
                $count  = (int) \LotgdTranslator::translate('prefix.creature.count', [], $textDomain);
                $key    = \mt_rand(0, $count);
                $prefix = \LotgdTranslator::translate("prefix.creature.0{$key}", [], $textDomain);
                //-- Check if have a valid name
                $prefix = "prefix.creature.0{$key}" == $prefix ? 'Elite' : $prefix;

                if ($packofmonsters)
                {
                    $initialbadguy = $result[0];
                    $prefixs       = ['Elite', 'Dangerous', 'Lethal', 'Savage', 'Deadly', 'Malevolent', 'Malignant'];

                    for ($i = 0; $i < $multi; ++$i)
                    {
                        $initialbadguy['creaturelevel'] = e_rand($mintargetlevel, $targetlevel);
                        $initialbadguy['playerstarthp'] = $session['user']['hitpoints'];
                        $initialbadguy['diddamage']     = 0;
                        $badguy                         = $creatureFunctions->buffBadguy($initialbadguy);

                        if ('thrill' == $type)
                        {
                            // 10% more experience
                            $badguy['creatureexp'] = \round($badguy['creatureexp'] * 1.1, 0);
                            // 10% more gold
                            $badguy['creaturegold'] = \round($badguy['creaturegold'] * 1.1, 0);
                        }

                        if ('suicide' == $type)
                        {
                            // Okay, suicide fights give even more rewards, but
                            // are much harder
                            // 25% more experience
                            $badguy['creatureexp'] = \round($badguy['creatureexp'] * 1.25, 0);
                            // 25% more gold
                            $badguy['creaturegold'] = \round($badguy['creaturegold'] * 1.25, 0);
                            // Now, make it tougher.
                            $mul                       = 1.25 + $extrabuff;
                            $badguy['creatureattack']  = \round($badguy['creatureattack'] * $mul, 0);
                            $badguy['creaturedefense'] = \round($badguy['creaturedefense'] * $mul, 0);
                            $badguy['creaturehealth']  = \round($badguy['creaturehealth'] * $mul, 0);
                            // And mark it as an 'elite' troop.
                            $badguy['creaturename'] = $prefix.' '.$badguy['creaturename'];
                        }
                        $stack[$i] = $badguy;
                    }

                    if ($multi > 1)
                    {
                        \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.group', [
                            'multi'        => $multi,
                            'creatureName' => $badguy['creaturename'],
                        ], $textDomain));
                    }
                }
                else
                {
                    foreach ($result as $key => $badguy)
                    {
                        $badguy['playerstarthp'] = $session['user']['hitpoints'];
                        $badguy['diddamage']     = 0;

                        $badguy = $creatureFunctions->buffBadguy($badguy);
                        // Okay, they are thrillseeking, let's give them a bit extra
                        // exp and gold.
                        if ('thrill' == $type)
                        {
                            // 10% more experience
                            $badguy['creatureexp'] = \round($badguy['creatureexp'] * 1.1, 0);
                            // 10% more gold
                            $badguy['creaturegold'] = \round($badguy['creaturegold'] * 1.1, 0);
                        }
                        elseif ('suicide' == $type)
                        {
                            // Okay, suicide fights give even more rewards, but
                            // are much harder
                            // 25% more experience
                            $badguy['creatureexp'] = \round($badguy['creatureexp'] * 1.25, 0);
                            // 25% more gold
                            $badguy['creaturegold'] = \round($badguy['creaturegold'] * 1.25, 0);
                            // Now, make it tougher.
                            $mul                       = 1.25 + $extrabuff;
                            $badguy['creatureattack']  = \round($badguy['creatureattack'] * $mul, 0);
                            $badguy['creaturedefense'] = \round($badguy['creaturedefense'] * $mul, 0);
                            $badguy['creaturehealth']  = \round($badguy['creaturehealth'] * $mul, 0);
                            // And mark it as an 'elite' troop.
                            $badguy['creaturename'] = $prefix.' '.$badguy['creaturename'];
                        }
                        $stack[] = $badguy;
                    }
                }
            }
            \LotgdKernel::get('lotgd_core.combat.buffer')->calculateBuffFields();
            $args = new GenericEvent(null, [
                'enemies' => $stack,
                'options' => [
                    'type' => 'forest',
                ],
            ]);
            \LotgdEventDispatcher::dispatch($args, Events::PAGE_FOREST_FIGHT_START);
            $attackstack = modulehook('forestfight-start', $args->getArguments());

            $session['user']['badguy'] = $attackstack;
            // If someone for any reason wanted to add a nav where the user cannot choose the number of rounds anymore
            // because they are already set in the nav itself, we need this here.
            // It will not break anything else. I hope.
            if ('' != $request->query->get('auto'))
            {
                $request->query->set('op', 'fight');
                $op = 'fight';
            }
        }
    }
}
elseif ('run' == $op)
{
    if (0 == mt_rand() % 3)
    {
        $battle        = false;
        $params['tpl'] = 'default';

        \LotgdFlashMessages::addSuccessMessage(\LotgdTranslator::t('flash.message.run.success', [], $textDomain));

        $op = '';
        $request->query->set('op', '');
        \LotgdKernel::get('lotgd_core.combat.battle')->unsuspendBuffs();

        foreach ($companions as $index => $companion)
        {
            if (isset($companion['expireafterfight']) && $companion['expireafterfight'])
            {
                unset($companions[$index]);
            }
        }
    }
    else
    {
        $battle = true;
        \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.run.fail', [], $textDomain));
    }
}
elseif ('fight' == $op || 'newtarget' == $op)
{
    $battle = true;
}

if ($battle)
{
    require_once 'battle.php';

    if ($victory)
    {
        $dontDisplayForestMessage    = true;
        $params['tpl']               = 'default';
        $params['showForestMessage'] = ! $dontDisplayForestMessage;

        $op = '';
        $battle = false;
        $request->query->set('op', '');
    }
    else
    {
        \LotgdNavigation::fightNav();
    }
}

if ('' == $op)
{
    $method = 'index';
    $params['showForestMessage'] = ! $dontDisplayForestMessage;
}

$params['battle'] = $battle;

$request->attributes->set('params', $params);

if ( ! $battle)
{
    \LotgdResponse::callController(Lotgd\Core\Controller\ForestController::class, $method);
}

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- Display events
('' == $op) && module_display_events('forest', 'forest.php');

//-- Finalize page
\LotgdResponse::pageEnd();
