<?php

page_header('Clan Hall for %s', full_sanitize($claninfo['clanname']));

addnav('Clan Options');

if ('' == $op)
{
    require_once 'lib/clan/clan_default.php';
}
elseif ('motd' == $op)
{
    require_once 'lib/clan/clan_motd.php';
}
elseif ('membership' == $op)
{
    require_once 'lib/clan/clan_membership.php';
}
elseif ('withdrawconfirm' == $op)
{
    addnav('Withdraw?');
    addnav('No', 'clan.php');
    addnav('!?Yes', 'clan.php?op=withdraw');

    rawoutput($lotgdTpl->renderThemeTemplate('pages/clan/start/withdraw/confirm.twig', ['registrar' => $registrar]));
}
elseif ('withdraw' == $op)
{
    require_once 'lib/clan/clan_withdraw.php';
}
