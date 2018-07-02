<?php

page_header('Clan Halls');

addnav('Clan Options');

if ('apply' == $op)
{
    require_once 'lib/clan/applicant_apply.php';
}
elseif ('new' == $op)
{
    require_once 'lib/clan/applicant_new.php';
}
else
{
    rawoutput($lotgdTpl->renderThemeTemplate('pages/clan/applicant.twig', ['registrar' => $registrar]));
    modulehook('clan-enter');

    if ('withdraw' == $op)
    {
        $session['user']['clanid'] = 0;
        $session['user']['clanrank'] = CLAN_APPLICANT;
        $session['user']['clanjoindate'] = '0000-00-00 00:00:00';

        rawoutput($lotgdTpl->renderThemeTemplate('pages/clan/applicant/withdraw.twig', ['registrar' => $registrar, 'claninfo' => $claninfo]));

        $claninfo = [];
        $delete = DB::delete('mail');
        $delete->where->equalTo('msgfrom', 0)
            ->equalTo('seen', 0)
            ->equalTo('subject', addslashes(serialize($apply_subj)))
        ;
        DB::execute($select);

        addnav('Apply for Membership to a Clan', 'clan.php?op=apply');
        addnav('Apply for a New Clan', 'clan.php?op=new');
    }
    else
    {
        if (isset($claninfo['clanid']) && $claninfo['clanid'] > 0)
        {
            rawoutput($lotgdTpl->renderThemeTemplate('pages/clan/applicant/waiting.twig', ['registrar' => $registrar, 'claninfo' => $claninfo]));

            addnav('Waiting Area', 'clan.php?op=waiting');
            addnav('Withdraw Application', 'clan.php?op=withdraw');
        }
        else
        {
            rawoutput($lotgdTpl->renderThemeTemplate('pages/clan/applicant/noclan.twig', ['registrar' => $registrar]));

            addnav('Apply for Membership to a Clan', 'clan.php?op=apply');
            addnav('Apply for a New Clan', 'clan.php?op=new');
        }
    }
}
