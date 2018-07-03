<?php

// addnews ready
// translator ready
// mail ready
require_once 'common.php';
require_once 'lib/forest.php';
require_once 'lib/fightnav.php';
require_once 'lib/http.php';
require_once 'lib/taunt.php';
require_once 'lib/events.php';
require_once 'lib/battle/skills.php';

tlschema('forest');

$fight = false;
page_header('The Forest');
$dontdisplayforestmessage = handle_event('forest');

$op = httpget('op');

$battle = false;

if ('run' == $op)
{
    if (0 == e_rand() % 3)
    {
        output('`c`b`&You have successfully fled your opponent!`0`b`c`n');
        $op = '';
        httpset('op', '');
        unsuspend_buffs();

        foreach ($companions as $index => $companion)
        {
            if (isset($companion['expireafterfight']) && $companion['expireafterfight'])
            {
                unset($companions[$index]);
            }
        }

        if (is_string($session['user']['badguy']))
        {
            $enemies = unserialize($session['user']['badguy']);
        }

        if (is_array($enemies))
        {
            $enemies['options']['endbattle'] = 1;

            $session['user']['badguy'] = createstring($enemies);
        }
    }
}

if ('dragon' == $op)
{
    require_once 'lib/partner.php';

    addnav('Enter the cave', 'dragon.php');
    addnav('Run away like a baby', 'inn.php?op=fleedragon');

    $twig = [
        'partner' => get_partner()
    ];

    rawoutput($lotgd_tpl->renderThemeTemplate('pages/forest/dragon.twig', $twig));

    $session['user']['seendragon'] = 1;
}

if ('search' == $op)
{
    checkday();

    if ($session['user']['turns'] <= 0)
    {
        output('`$`bYou are too tired to search the forest any longer today.  Perhaps tomorrow you will have more energy.`b`0');
        $op = '';
        httpset('op', '');
    }
    else
    {
        modulehook('forestsearch', []);
        $args = [
            'soberval' => 0.9,
            'sobermsg' => '`&Faced with the prospect of death, you sober up a little.`n',
            'schema' => 'forest'
        ];
        modulehook('soberup', $args);

        if (0 != module_events('forest', getsetting('forestchance', 15)))
        {
            if (! checknavs())
            {
                // If we're showing the forest, make sure to reset the special
                // and the specialmisc
                $session['user']['specialinc'] = '';
                $session['user']['specialmisc'] = '';
                $dontdisplayforestmessage = true;
                $op = '';
                httpset('op', '');
            }
            else
            {
                page_footer();
            }
        }
        else
        {
            $session['user']['turns']--;
            $battle = true;

            if (1 == e_rand(0, 2))
            {
                $plev = (1 == e_rand(1, 5) ? 1 : 0);
                $nlev = (1 == e_rand(1, 3) ? 1 : 0);
            }
            else
            {
                $plev = 0;
                $nlev = 0;
            }

            $type = httpget('type');

            if ('slum' == $type)
            {
                $nlev++;
                output("`\$You head for the section of forest you know to contain foes that you're a bit more comfortable with.`0`n");
            }

            if ('thrill' == $type)
            {
                $plev++;
                output('`$You head for the section of forest which contains creatures of your nightmares, hoping to find one of them injured.`0`n');
            }
            $extrabuff = 0;

            if ('suicide' == $type)
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
                output('`$You head for the section of forest which contains creatures of your nightmares, looking for the biggest and baddest ones there.`0`n');
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

                        if (mt_rand(0, 1))
                        {
                            $mintargetlevel = $targetlevel - 1;
                        }
                        else
                        {
                            $mintargetlevel = $targetlevel - 2;
                        }
                    }
                    elseif ('thrill' == $type)
                    {
                        $multi += e_rand(getsetting('multithrillmin', 1), getsetting('multithrillmax', 2));

                        if (mt_rand(0, 1))
                        {
                            $targetlevel++;
                            $mintargetlevel = $targetlevel - 1;
                        }
                        else
                        {
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

            $multi = max(1, $multi);

            if ($targetlevel < 1)
            {
                $targetlevel = 1;
            }

            if ($mintargetlevel < 1)
            {
                $mintargetlevel = 1;
            }

            if ($mintargetlevel > $targetlevel)
            {
                $mintargetlevel = $targetlevel;
            }

            if ($targetlevel > 17)
            {
                $multi += $targetlevel - 17;
                $targetlevel = 17;
            }
            debug("Creatures: $multi Targetlevel: $targetlevel Mintargetlevel: $mintargetlevel");

            if ($multi > 1)
            {
                $packofmonsters = (bool) (0 == mt_rand(0, 5) && getsetting('allowpackofmonsters', true)); // true or false
                if (false === $packofmonsters)
                {
                    $multicat = (getsetting('multicategory', 0) ? 'GROUP BY creaturecategory' : '');
                    $sql = 'SELECT * FROM '.DB::prefix('creatures')." WHERE creaturelevel <= $targetlevel AND creaturelevel >= $mintargetlevel AND forest=1 ORDER BY rand(".e_rand().") LIMIT $multi";
                }
                else
                {
                    $sql = 'SELECT * FROM '.DB::prefix('creatures')." WHERE creaturelevel <= $targetlevel AND creaturelevel >= $mintargetlevel AND forest=1 ORDER BY rand(".e_rand().') LIMIT 1';
                }
            }
            else
            {
                $sql = 'SELECT * FROM '.DB::prefix('creatures')." WHERE creaturelevel <= $targetlevel AND creaturelevel >= $mintargetlevel AND forest=1 ORDER BY rand(".e_rand().') LIMIT 1';
                $packofmonsters = 0;
            }
            $result = DB::query($sql);
            restore_buff_fields();

            if (0 == DB::num_rows($result))
            {
                // There is nothing in the database to challenge you, let's
                // give you a doppleganger.
                $badguy = [];
                $badguy['creaturename'] = "An evil doppleganger of {$session['user']['name']}";
                $badguy['creatureweapon'] = $session['user']['weapon'];
                $badguy['creaturelevel'] = $session['user']['level'];
                $badguy['creaturegold'] = 0;
                $badguy['creatureexp'] = round($session['user']['experience'] / 10, 0);
                $badguy['creaturehealth'] = $session['user']['maxhitpoints'];
                $badguy['creatureattack'] = $session['user']['attack'];
                $badguy['creaturedefense'] = $session['user']['defense'];
                $stack[] = $badguy;
            }
            else
            {
                require_once 'lib/forestoutcomes.php';

                if (true == $packofmonsters)
                {
                    $initialbadguy = DB::fetch_assoc($result);
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
                        output('`2You encounter a group of `^%i`2 %s`2.`n`n', $multi, $badguy['creaturename']);
                    }
                }
                else
                {
                    while ($badguy = DB::fetch_assoc($result))
                    {
                        $badguy['playerstarthp'] = $session['user']['hitpoints'];
                        $badguy['diddamage'] = 0;
                        //decode and test the AI script file in place if any
                        $aiscriptfile = $badguy['creatureaiscript'].'.php';
                        //file there, get content and put it into the ai script field.
                        if (file_exists($aiscriptfile))
                        {
                            $badguy['creatureaiscript'] = "require_once '$aiscriptfile';";
                        }
                        else
                        {
                            $badguy['creatureaiscript'] = '';
                        }

                        //AI setup
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

            $session['user']['badguy'] = createstring($attackstack);
            // If someone for any reason wanted to add a nav where the user cannot choose the number of rounds anymore
            // because they are already set in the nav itself, we need this here.
            // It will not break anything else. I hope.
            if ('' != httpget('auto'))
            {
                httpset('op', 'fight');
                $op = 'fight';
            }
        }
    }
}

if ('fight' == $op || 'run' == $op || 'newtarget' == $op)
{
    $battle = true;
}

if ($battle)
{
    require_once 'battle.php';

    if ($victory)
    {
        $op = '';
        httpset('op', '');
        $dontdisplayforestmessage = true;
    }
    else
    {
        fightnav();
    }
}

if ('' == $op)
{
    // Need to pass the variable here so that we show the forest message
    // sometimes, but not others.
    forest($dontdisplayforestmessage);
}

page_footer();
