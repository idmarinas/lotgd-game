<?php

// addnews ready
// translator ready
// mail ready
require_once 'common.php';
require_once 'lib/fightnav.php';
require_once 'lib/taunt.php';
require_once 'lib/events.php';
require_once 'lib/battle/skills.php';

// Don't hook on to this text for your standard modules please, use "forest" instead.
// This hook is specifically to allow modules that do other forests to create ambience.
$result = modulehook('forest-text-domain', ['textDomain' => 'page-forest', 'textDomainNavigation' => 'navigation-forest']);
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

page_header('title', [], $textDomain);

$dontDisplayForestMessage = handle_event('forest');

$params = [
    'textDomain' => $textDomain,
    'showForestMessage' => ! $dontDisplayForestMessage
];

$op = (string) \LotgdHttp::getQuery('op');

$battle = false;

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
    checkday();

    require_once 'lib/forestoutcomes.php';

    if ($session['user']['turns'] <= 0)
    {
        \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('flash.message.tired', [], $textDomain));

        $op = '';
        \LotgdHttp::setQuery('op', '');
    }
    else
    {
        modulehook('forestsearch', []);
        $args = [
            'soberval' => 0.9,
            'sobermsg' => \LotgdTranslator::t('sober.message', [], $textDomain),
            'schema' => 'forest'
        ];
        modulehook('soberup', $args);

        if (0 != module_events('forest', getsetting('forestchance', 15)))
        {
            if (\LotgdNavigation::checkNavs())
            {
                page_footer();
            }

            // If we're showing the forest, make sure to reset the special
            // and the specialmisc
            $session['user']['specialinc'] = '';
            $session['user']['specialmisc'] = '';
            $dontDisplayForestMessage = true;
            $params['showForestMessage'] = ! $dontDisplayForestMessage;

            $op = '';
            \LotgdHttp::setQuery('op', '');
        }
        else
        {
            $session['user']['turns']--;
            $battle = true;

            $plev = 0;
            $nlev = 0;

            if (1 == e_rand(0, 2))
            {
                $plev = (1 == e_rand(1, 5) ? 1 : 0);
                $nlev = (1 == e_rand(1, 3) ? 1 : 0);
            }

            $type = (string) \LotgdHttp::getQuery('type');

            $extrabuff = 0;

            if ('slum' == $type)
            {
                $nlev++;
            }
            elseif ('thrill' == $type)
            {
                $plev++;
                \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('flash.message.thrill', [], $textDomain));
            }
            elseif ('suicide' == $type)
            {
                if ($session['user']['level'] <= 7)
                {
                    $plev++;
                    $extrabuf = .25;
                }
                elseif ($session['user']['level'] < 14)
                {
                    $plev += 2;
                    $extrabuf = 0;
                }
                else
                {
                    $plev++;
                    $extrabuff = .4;
                }
                \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.suicide', [], $textDomain));
            }

            $multi = 1;
            $targetlevel = ($session['user']['level'] + $plev - $nlev);
            $mintargetlevel = $targetlevel;

            if (getsetting('multifightdk', 10) <= $session['user']['dragonkills'])
            {
                if (mt_rand(1, 100) <= getsetting('multichance', 25))
                {
                    $multi = e_rand(getsetting('multibasemin', 2), getsetting('multibasemax', 3));

                    if ('slum' == $type)
                    {
                        $multi -= e_rand(getsetting('multislummin', 0), getsetting('multislummax', 1));

                        $mintargetlevel = $targetlevel - 2;

                        if (mt_rand(0, 1))
                        {
                            $mintargetlevel = $targetlevel - 1;
                        }
                    }
                    elseif ('thrill' == $type)
                    {
                        $multi += e_rand((int) getsetting('multithrillmin', 1), (int) getsetting('multithrillmax', 2));

                        $mintargetlevel = $targetlevel - 1;

                        if (mt_rand(0, 1))
                        {
                            $targetlevel++;
                            $mintargetlevel = $targetlevel - 1;
                        }
                    }
                    elseif ('suicide' == $type)
                    {
                        $multi += e_rand(getsetting('multisuimin', 2), getsetting('multisuimax', 4));

                        if (mt_rand(0, 1))
                        {
                            $mintargetlevel = $targetlevel - 1;
                        }
                        else
                        {
                            $targetlevel++;
                            $mintargetlevel = $targetlevel - 1;
                        }
                    }
                    $multi = min($multi, $session['user']['level']);
                }
            }
            else
            {
                $multi = 1;
            }

            $multi = (int) max(1, $multi);
            $targetlevel = (int) max(1, $targetlevel);
            $mintargetlevel = (int) max(1, min($mintargetlevel, $targetlevel));

            if ($targetlevel > 17)
            {
                $multi += $targetlevel - 17; //-- More dificult if have more level than 15
                // $targetlevel = 17; //-- Not avoid level range setting
            }
            debug("Creatures: $multi Targetlevel: $targetlevel Mintargetlevel: $mintargetlevel");

            $packofmonsters = (bool) (0 == mt_rand(0, 5) && getsetting('allowpackofmonsters', true)); // true or false
            $packofmonsters = ($multi > 1) ? $packofmonsters : false;

            $result = lotgd_search_creature($multi, $targetlevel, $mintargetlevel, $packofmonsters, true);

            restore_buff_fields();

            if (0 == count($result))
            {
                // There is nothing in the database to challenge you, let's
                // give you a doppelganger.
                $badguy = lotgd_generate_doppelganger($session['user']['level']);

                $stack[] = $badguy;
            }
            else
            {
                if ($packofmonsters)
                {
                    $initialbadguy = $result[0];
                    $prefixs = ['Elite', 'Dangerous', 'Lethal', 'Savage', 'Deadly', 'Malevolent', 'Malignant'];

                    for ($i = 0; $i < $multi; $i++)
                    {
                        $initialbadguy['creaturelevel'] = e_rand($mintargetlevel, $targetlevel);
                        $initialbadguy['playerstarthp'] = $session['user']['hitpoints'];
                        $initialbadguy['diddamage'] = 0;
                        $badguy = buffbadguy($initialbadguy);

                        if ('thrill' == $type)
                        {
                            // 10% more experience
                            $badguy['creatureexp'] = round($badguy['creatureexp'] * 1.1, 0);
                            // 10% more gold
                            $badguy['creaturegold'] = round($badguy['creaturegold'] * 1.1, 0);
                        }

                        if ('suicide' == $type)
                        {
                            // Okay, suicide fights give even more rewards, but
                            // are much harder
                            // 25% more experience
                            $badguy['creatureexp'] = round($badguy['creatureexp'] * 1.25, 0);
                            // 25% more gold
                            $badguy['creaturegold'] = round($badguy['creaturegold'] * 1.25, 0);
                            // Now, make it tougher.
                            $mul = 1.25 + $extrabuff;
                            $badguy['creatureattack'] = round($badguy['creatureattack'] * $mul, 0);
                            $badguy['creaturedefense'] = round($badguy['creaturedefense'] * $mul, 0);
                            $badguy['creaturehealth'] = round($badguy['creaturehealth'] * $mul, 0);
                            // And mark it as an 'elite' troop.
                            $prefixs = translate_inline($prefixs);
                            $key = array_rand($prefixs);
                            $prefix = $prefixs[$key];
                            $badguy['creaturename'] = $prefix.' '.$badguy['creaturename'];
                        }
                        $stack[$i] = $badguy;
                    }

                    if ($multi > 1)
                    {
                        \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.group', [
                            'multi' => $multi,
                            'creatureName' => $badguy['creaturename']
                        ], $textDomain));
                    }
                }
                else
                {
                    foreach ($result as $key => $badguy)
                    {
                        $badguy['playerstarthp'] = $session['user']['hitpoints'];
                        $badguy['diddamage'] = 0;

                        $badguy = buffbadguy($badguy);
                        // Okay, they are thrillseeking, let's give them a bit extra
                        // exp and gold.
                        if ('thrill' == $type)
                        {
                            // 10% more experience
                            $badguy['creatureexp'] = round($badguy['creatureexp'] * 1.1, 0);
                            // 10% more gold
                            $badguy['creaturegold'] = round($badguy['creaturegold'] * 1.1, 0);
                        }
                        elseif ('suicide' == $type)
                        {
                            // Okay, suicide fights give even more rewards, but
                            // are much harder
                            // 25% more experience
                            $badguy['creatureexp'] = round($badguy['creatureexp'] * 1.25, 0);
                            // 25% more gold
                            $badguy['creaturegold'] = round($badguy['creaturegold'] * 1.25, 0);
                            // Now, make it tougher.
                            $mul = 1.25 + $extrabuff;
                            $badguy['creatureattack'] = round($badguy['creatureattack'] * $mul, 0);
                            $badguy['creaturedefense'] = round($badguy['creaturedefense'] * $mul, 0);
                            $badguy['creaturehealth'] = round($badguy['creaturehealth'] * $mul, 0);
                            // And mark it as an 'elite' troop.
                            $prefixs = ['Elite', 'Dangerous', 'Lethal', 'Savage', 'Deadly', 'Malevolent', 'Malignant'];
                            $prefixs = translate_inline($prefixs);
                            $key = array_rand($prefixs);
                            $prefix = $prefixs[$key];
                            $badguy['creaturename'] = $prefix.' '.$badguy['creaturename'];
                        }
                        $stack[] = $badguy;
                    }
                }
            }
            calculate_buff_fields();
            $attackstack = [
                'enemies' => $stack,
                'options' => [
                    'type' => 'forest'
                ]
            ];
            $attackstack = modulehook('forestfight-start', $attackstack);

            $session['user']['badguy'] = $attackstack;
            // If someone for any reason wanted to add a nav where the user cannot choose the number of rounds anymore
            // because they are already set in the nav itself, we need this here.
            // It will not break anything else. I hope.
            if ('' != \LotgdHttp::getQuery('auto'))
            {
                \LotgdHttp::setQuery('op', 'fight');
                $op = 'fight';
            }
        }
    }
}
elseif ('run' == $op)
{
    if (0 == e_rand() % 3)
    {
        $battle = false;
        $params['tpl'] = 'default';

        \LotgdFlashMessages::addSuccessMessage(\LotgdTranslator::t('flash.message.run.success', [], $textDomain));

        $op = '';
        \LotgdHttp::setQuery('op', '');
        unsuspend_buffs();

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
        $dontDisplayForestMessage = true;
        $params['tpl'] = 'default';
        $params['showForestMessage'] = ! $dontDisplayForestMessage;

        $op = '';
        \LotgdHttp::setQuery('op', '');
    }
    else
    {
        fightnav();
    }
}

if ('' == $op)
{
    $params['tpl'] = 'default';
    $params['showForestMessage'] = ! $dontDisplayForestMessage;

    //-- Change text domain for navigation
    \LotgdNavigation::setTextDomain($textDomainNavigation);

    \LotgdNavigation::addHeader('category.navigation');
    \LotgdNavigation::villageNav();

    \LotgdNavigation::addHeader('category.heal');
    \LotgdNavigation::addNav('nav.healer', 'healer.php');

    \LotgdNavigation::addHeader('category.fight');
    \LotgdNavigation::addNav('nav.search', 'forest.php?op=search');

    ($session['user']['level'] > 1) && \LotgdNavigation::addNav('nav.slum', 'forest.php?op=search&type=slum');

    \LotgdNavigation::addNav('nav.thrill', 'forest.php?op=search&type=thrill');

    (getsetting('suicide', 0) && getsetting('suicidedk', 10) <= $session['user']['dragonkills']) && \LotgdNavigation::addNav('nav.suicide', 'forest.php?op=search&type=suicide');

    \LotgdNavigation::addHeader('other');

    modulehook('forest-header');

    if ($session['user']['level'] >= getsetting('maxlevel', 15) && 0 == $session['user']['seendragon'])
    {
        // Only put the green dragon link if we are a location which
        // should have a forest.   Don't even ask how we got into a forest()
        // call if we shouldn't have one.   There is at least one way via
        // a superuser link, but it shouldn't happen otherwise.. We just
        // want to make sure however.
        $isforest = 0;
        $vloc = modulehook('validforestloc', []);

        foreach ($vloc as $i => $l)
        {
            if ($session['user']['location'] == $i)
            {
                $isforest = 1;
                break;
            }
        }

        if ($isforest || 0 == count($vloc))
        {
            \LotgdNavigation::addNav('nav.dragon', 'forest.php?op=dragon');
        }
    }

    modulehook('forest', []);

    //-- Restore text domain for navigation
    \LotgdNavigation::setTextDomain();
}

$params['battle'] = $battle;

//-- This is only for params not use for other purpose
$params = modulehook('page-forest-tpl-params', $params);
rawoutput(\LotgdTheme::renderThemeTemplate('page/forest.twig', $params));

//-- Display events
('' == $op) && module_display_events('forest', 'forest.php');

page_footer();
