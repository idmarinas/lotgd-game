<?php

$act = httpget('act');

$twig = [
    'barkeep' => $barkeep
];

if ('' == $act)
{
    rawoutput($lotgd_tpl->renderThemeTemplate('pages/inn/bartender/default.twig', $twig));

    addnav_notl(sanitize($barkeep));
    addnav('Bribe', 'inn.php?op=bartender&act=bribe');
    addnav('Drinks');
    modulehook('ale', []);
}
elseif ('bribe' == $act)
{
    $g1 = $session['user']['level'] * 10;
    $g2 = $session['user']['level'] * 50;
    $g3 = $session['user']['level'] * 100;
    $type = httpget('type');

    if ('' == $type)
    {
        rawoutput($lotgd_tpl->renderThemeTemplate('pages/inn/bartender/bribe/default.twig', $twig));

        addnav('1 gem', 'inn.php?op=bartender&act=bribe&type=gem&amt=1');
        addnav('2 gems', 'inn.php?op=bartender&act=bribe&type=gem&amt=2');
        addnav('3 gems', 'inn.php?op=bartender&act=bribe&type=gem&amt=3');
        addnav(['%s gold', $g1], "inn.php?op=bartender&act=bribe&type=gold&amt=$g1");
        addnav(['%s gold', $g2], "inn.php?op=bartender&act=bribe&type=gold&amt=$g2");
        addnav(['%s gold', $g3], "inn.php?op=bartender&act=bribe&type=gold&amt=$g3");
    }
    else
    {
        $amt = httpget('amt');

        if ('gem' == $type)
        {
            if ($session['user']['gems'] < $amt)
            {
                $try = false;
                output("You don't have %s gems!", $amt);
            }
            else
            {
                $chance = $amt * 30;
                $session['user']['gems'] -= $amt;
                debuglog("spent $amt gems on bribing $barkeep");
                $try = true;
            }
        }
        else
        {
            if ($session['user']['gold'] < $amt)
            {
                output("You don't have %s gold!", $amt);
                $try = false;
            }
            else
            {
                $try = true;
                $sfactor = 50 / 90;
                $fact = $amt / $session['user']['level'];
                $chance = ($fact - 10) * $sfactor + 25;
                $session['user']['gold'] -= $amt;
                debuglog("spent $amt gold bribing $barkeep");
            }
        }

        if ($try)
        {
            if (e_rand(0, 100) < $chance)
            {
                rawoutput($lotgd_tpl->renderThemeTemplate('pages/inn/bartender/bribe/try/success.twig', $twig));

                addnav('What do you want?');
                modulehook('bartenderbribe', []);

                if (getsetting('pvp', 1))
                {
                    addnav("Who's upstairs?", 'inn.php?op=bartender&act=listupstairs');
                }
                addnav('Tell me about colors', 'inn.php?op=bartender&act=colors');

                if (getsetting('allowspecialswitch', true))
                {
                    addnav('Switch specialty', 'inn.php?op=bartender&act=specialty');
                }
            }
            else
            {
                $twig['gem'] = $type;
                $twig['amt'] = $amt;

                rawoutput($lotgd_tpl->renderThemeTemplate('pages/inn/bartender/bribe/try/fail.twig', $twig));

                addnav(['B?Talk to %s`0 again', $barkeep], 'inn.php?op=bartender');
            }
        }
        else
        {
            rawoutput($lotgd_tpl->renderThemeTemplate('pages/inn/bartender/bribe/error.twig', $twig));

            addnav(['B?Talk to %s`0 the Barkeep', $barkeep], 'inn.php?op=bartender');
        }
    }
}
elseif ('listupstairs' == $act)
{
    addnav('Refresh the list', 'inn.php?op=bartender&act=listupstairs');

    rawoutput($lotgd_tpl->renderThemeTemplate('pages/inn/bartender/listupstairs.twig', $twig));

    pvplist($iname, 'pvp.php', '?act=attack&inn=1');
}
elseif ('colors' == $act)
{
    $testtext = httppost('testtext');
    $twig['colors'] = $output->get_colors();
    $twig['REQUEST_URI'] = $REQUEST_URI;
    $twig['preventtesttext'] = prevent_colors(htmlentities($testtext, ENT_COMPAT, getsetting('charset', 'UTF-8')));
    $twig['testtext'] = $testtext;

    rawoutput($lotgd_tpl->renderThemeTemplate('pages/inn/bartender/colors.twig', $twig));

    addnav('', $REQUEST_URI);
}
elseif ('specialty' == $act)
{
    $specialty = httpget('specialty');

    if ('' == $specialty)
    {
        rawoutput($lotgd_tpl->renderThemeTemplate('pages/inn/bartender/specialty/default.twig', $twig));

        $specialities = modulehook('specialtynames');

        foreach ($specialities as $key => $name)
        {
            addnav($name, cmd_sanitize($REQUEST_URI)."&specialty=$key");
        }
    }
    else
    {
        rawoutput($lotgd_tpl->renderThemeTemplate('pages/inn/bartender/specialty/changed.twig', $twig));

        $session['user']['specialty'] = $specialty;
    }

    rawoutput($lotgd_tpl->renderThemeTemplate('pages/inn/room.twig', $twig));
}
