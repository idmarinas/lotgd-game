<?php

$apply = (int) httpget('apply');

if (1 == $apply)
{
    $ocn = httppost('clanname');
    $clanname = stripslashes($ocn);
    $clanname = full_sanitize($clanname);

    if (getsetting('clannamesanitize', 0))
    {
        $clanname = preg_replace("'[^[:alpha:] \\'-]'", '', $clanname);
    }
    $clanname = addslashes($clanname);
    httppostset('clanname', $clanname);

    $ocs = httppost('clanshort');
    $clanshort = stripslashes($ocs);
    $clanshort = full_sanitize($ocs);

    if (getsetting('clanshortnamesanitize', 0))
    {
        $clanshort = preg_replace("'[^[:alpha:]]'", '', $clanshort);
    }
    httppostset('clanshort', $clanshort);

    //-- Check if clan name exist
    $select = DB::select('clans');
    $select->columns(['clanid'])
        ->where->equalTo('clanname', $clanname)
    ;
    $clannameexist = DB::execute($select);

    //-- Check if clan short name exist
    $select = DB::select('clans');
    $select->columns(['clanid'])
        ->where->equalTo('clanshort', str_replace(['<', '>'], '', $clanshort))
    ;
    $clanshortexist = DB::execute($select);

    $twig = [
        'registrar' => $registrar,
        'clanform' => clanform()
    ];

    if ($clanname != $ocn || $clanshort != $ocs)
    {
        addnav('Return to the Lobby', 'clan.php');

        rawoutput($lotgdTpl->renderThemeTemplate('pages/clan/applicant/new/apply/errornames.twig', $twig));
    }
    elseif ((strlen($clanname) < 5 || strlen($clanname) > 50) || (strlen($clanshort) < 2 || strlen($clanshort) > getsetting('clanshortnamelength', 5)))
    {
        addnav('Return to the Lobby', 'clan.php');

        $twig['namelength'] = (strlen($clanname) < 5 || strlen($clanname) > 50);
        $twig['shortnamelength'] = (strlen($clanshort) < 2 || strlen($clanshort) > getsetting('clanshortnamelength', 5));

        rawoutput($lotgdTpl->renderThemeTemplate('pages/clan/applicant/new/apply/errornameslength.twig', $twig));
    }
    elseif ($clannameexist->count() || $clanshortexist->count())
    {
        $twig['nameexist'] = (bool) $clannameexist->count();
        $twig['name'] = stripslashes($clanname);
        $twig['shortnameexist'] = (bool) $clanshortexist->count();
        $twig['shortname'] = stripslashes($clanshort);

        rawoutput($lotgdTpl->renderThemeTemplate('pages/clan/applicant/new/apply/errorexist.twig', $twig));
    }
    else
    {
        $twig['gold'] = $gold;
        $twig['gems'] = $gems;

        if ($session['user']['gold'] >= $gold && $session['user']['gems'] >= $gems)
        {
            $args = ['ocn' => $ocn, 'ocs' => $ocs, 'clanname' => $clanname, 'clanshort' => $clanshort];
            $args = modulehook('process-createclan', $args);

            if (isset($args['blocked']) && $args['blocked'])
            {
                addnav('Return to the Lobby', 'clan.php');

                rawoutput($args['blockmsg']);

                clanform();
            }
            else
            {
                $insert = DB::insert('clans');
                $insert->values(['clanname' => $clanname, 'clanshort' => $clanshort]);
                DB::execute($insert);
                $id = DB::insert_id();

                $session['user']['clanid'] = $id;
                $session['user']['clanrank'] = CLAN_LEADER + 1; //+1 because he is the founder
                $session['user']['clanjoindate'] = date('Y-m-d H:i:s');
                $session['user']['gold'] -= $gold;
                $session['user']['gems'] -= $gems;

                addnav('Enter your clan hall', 'clan.php');

                $twig['name'] = stripslashes($clanname);

                rawoutput($lotgdTpl->renderThemeTemplate('pages/clan/applicant/new/apply/created.twig', $twig));

                debuglog("has started a new clan (<$clanshort> $clanname) for $gold gold and $gems gems.");
            }
        }
        else
        {
            addnav('Return to the Lobby', 'clan.php');

            rawoutput($lotgdTpl->renderThemeTemplate('pages/clan/applicant/new/apply/errornomoney.twig', $twig));
        }
    }
}
else
{
    $twig = [
        'registrar' => $registrar,
        'gold' => $gold,
        'gems' => $gems,
        'clanform' => clanform()
    ];

    addnav('Return to the Lobby', 'clan.php');

    rawoutput($lotgdTpl->renderThemeTemplate('pages/clan/applicant/new/default.twig', $twig));
}
