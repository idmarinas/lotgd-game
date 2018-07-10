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
    output('Are you sure you want to withdraw from your clan?');
    addnav('Withdraw?');
    addnav('No', 'clan.php');
    addnav('!?Yes', 'clan.php?op=withdraw');
}
elseif ('withdraw' == $op)
{
    require_once 'lib/clan/clan_withdraw.php';
}
