<?php
page_header('Clan Listing');

$registrar = getsetting('clanregistrar', '`%Karissa');
$order = (int) httpget('order');

//-- Frist delete clans with 0 members
DB::query("DELETE FROM `clans` WHERE (SELECT COUNT(`acctid`) FROM `accounts` WHERE `clans`.`clanid` = `accounts`.`clanid` AND `accounts`.`clanrank` > " . CLAN_APPLICANT . ") = 0");

//-- Select clans and total members
$select = DB::select('clans');
$select->columns(['clanid', 'clanshort', 'clanname'])
    ->join('accounts', DB::expression('`accounts`.`clanid` = `clans`.`clanid` AND `accounts`.`clanrank` > ' . CLAN_APPLICANT), ['count' => DB::expression('COUNT(`accounts`.`acctid`)')])
    ->group('clans.clanid')
;

//-- Order of results
if ($order == 1) { $select->order('clans.clanname DESC'); }
elseif ($order == 2) { $select->order('clans.clanshort DESC'); }
else { $select->order('count DESC'); }

$result = DB::execute($select);

addnav('Clan Options');
if ($result->count())
{
    addnav('Return to the Lobby', 'clan.php');
    addnav('Sorting');
    addnav('Order by Membercount', 'clan.php?op=list&order=0');
    addnav('Order by Clanname', 'clan.php?op=list&order=1');
    addnav('Order by Shortname', 'clan.php?op=list&order=2');

    $twig = [
        'clans' => $result,
        'registrar' => $registrar
    ];

    rawoutput($lotgdTpl->renderThemeTemplate('pages/clan/list/clans.twig', $twig));
}
else
{
    addnav('Apply for a New Clan', 'clan.php?op=new');
    addnav('Return to the Lobby', 'clan.php');

    $twig = [
        'registrar' => $registrar
    ];

    rawoutput($lotgdTpl->renderThemeTemplate('pages/clan/list/none.twig', $twig));
}
page_footer();
