<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/nltoappon.php';
require_once 'lib/commentary.php';
require_once 'lib/systemmail.php';
require_once 'lib/sanitize.php';
require_once 'lib/villagenav.php';

tlschema('clans');

addnav('Village');
villagenav();
addnav('Clan Options');
addnav('C?List Clans', 'clan.php?op=list');
addcommentary();
$gold = getsetting('goldtostartclan', 10000);
$gems = getsetting('gemstostartclan', 15);
$ranks = [CLAN_APPLICANT => '`!Applicant`0', CLAN_MEMBER => '`#Member`0', CLAN_OFFICER => '`^Officer`0', CLAN_ADMINISTRATIVE => '`$Administrative`0', CLAN_LEADER => '`&Leader`0', CLAN_FOUNDER => '`$Founder'];
$args = modulehook('clanranks', ['ranks' => $ranks, 'clanid' => $session['user']['clanid']]);
$ranks = translate_inline($args['ranks']);

$apply_short = '`@Clan App: `&%s`0';
$apply_subj = [$apply_short, $session['user']['name']];

$op = httpget('op');

$detail = httpget('detail');

if ($detail > 0)
{
    require_once 'lib/clan/detail.php';
}
elseif ('list' == $op)
{
    require_once 'lib/clan/list.php';
}
elseif ('waiting' == $op)
{
    require_once 'lib/clan/waiting.php';
}
elseif (CLAN_APPLICANT == $session['user']['clanrank'])
{
    require_once 'lib/clan/applicant.php';
}
else
{
    require_once 'lib/clan/clan_start.php';
}

page_footer();

function clanform()
{
    $data = [
        'clanname' => htmlentities(stripslashes(httppost('clanname')), ENT_COMPAT, getsetting('charset', 'UTF-8')),
        'clanshort' => htmlentities(stripslashes(httppost('clanshort')), ENT_COMPAT, getsetting('charset', 'UTF-8'))
    ];

    output_notl($lotgd_tpl->renderThemeTemplate('pages/clan/new.twig', $data), true);
}
