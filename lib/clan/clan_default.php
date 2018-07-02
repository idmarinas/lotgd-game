<?php

$select = DB::select('accounts');
$select->columns([
    'motdauthname' => 'name',
    'descauthname' => DB::expression("(SELECT `name` FROM `accounts` WHERE `acctid` = '{$claninfo['descauthor']}')")
])
    ->where->equalTo('acctid', $claninfo['motdauthor'])
;
$result = DB::execute($select)->current();

$twig = [
    'registrar' => $registrar,
    'claninfo' => $claninfo,
    'ranks' => $ranks,
    // 'CLAN_LEADER' => CLAN_LEADER,
    'motdauthname' => $result['motdauthname'],
    'descauthname' => $result['descauthname']
];

//-- Count members
$select = DB::select('accounts');
$select->columns(['clanrank', 'count' => DB::expression('COUNT(1)')])
    ->order('clanrank DESC')
    ->group('clanrank')
    ->where->equalTo('clanid', $claninfo['clanid'])
;
$twig['members'] = DB::execute($select);

//-- Check for leaders
$select = DB::select('accounts');
$select->columns(['count' => DB::expression('COUNT(1)')])
    ->order('clanrank DESC')
    ->group('clanrank')
    ->where->equalTo('clanid', $claninfo['clanid'])
        ->greaterThanOrEqualTo('clanrank', CLAN_LEADER)
;
$leaders = DB::execute($select)->current();

$twig['newleader'] = false;
//-- There's no leader here, probably because the leader's account expired.
if (0 == $leaders['count'])
{
    $select = DB::select('accounts');
    $select->columns(['name', 'acctid', 'clanrank'])
        ->order('clanrank DESC, clanjoindate')
        ->where->equalTo('clanid', $session['user']['clanid'])
            ->greaterThan('clanrank', CLAN_APPLICANT)
    ;
    $result = DB::execute($select);

    if ($result->count())
    {
        $row = $result->current();
        $twig['newleader'] = $row['name'];

        $update = DB::update('accounts');
        $update->set(['clanrank' => CLAN_LEADER])
            ->where->equalTo('acctid', $row['acctid'])
        ;
        DB::execute($update);

        if ($row['acctid'] == $session['user']['acctid'])
        {
            //if it's the current user, we'll need to update their
            //session in order for the db write to take effect.
            $session['user']['clanrank'] = CLAN_LEADER;
        }
    }
}

if ($session['user']['clanrank'] > CLAN_MEMBER)
{
    addnav('Update MoTD / Clan Desc', 'clan.php?op=motd');
}

addnav('M?View Membership', 'clan.php?op=membership');
addnav('Online Members', 'list.php?op=clan');
addnav("Your Clan's Waiting Area", 'clan.php?op=waiting');
addnav('`$Withdraw From Your Clan`0', 'clan.php?op=withdrawconfirm');

rawoutput($lotgdTpl->renderThemeTemplate('pages/clan/start/default.twig', $twig));

modulehook('clanhall');

commentdisplay('', "clan-{$claninfo['clanid']}", 'Speak', 25, ($claninfo['customsay'] ?: 'says'));
