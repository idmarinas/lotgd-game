<?php

// require_once 'lib/clan/func.php';

$setrank = (int) httppost('setrank');

if (0 === $setrank)
{
    $setrank = (int) httpget('setrank');
}
$whoacctid = (int) httpget('whoacctid');
$remove = (int) httpget('remove');

if ($setrank >= 0 && $setrank <= $session['user']['clanrank'])
{
    $select = DB::select('accounts');
    $select->columns(['name', 'login', 'clanrank'])
        ->limit(1)
        ->where->equalTo('acctid', $whoacctid)
    ;
    $row = DB::execute($select)->current();

    if ($setrank > 0)
    {
        $args = [
            'setrank' => $setrank,
            'login' => $row['login'],
            'name' => $row['name'],
            'acctid' => $whoacctid,
            'clanid' => $session['user']['clanid'],
            'oldrank' => $row['clanrank']
        ];
        $args = modulehook('clan-setrank', $args);

        if (! isset($args['handled']) || ! $args['handled'])
        {
            $sql = 'UPDATE '.DB::prefix('accounts')." SET clanrank=GREATEST(0,least({$session['user']['clanrank']},$setrank)) WHERE acctid=$whoacctid";

            if ($whoacctid == $session['user']['acctid'])
            {
                $session['user']['clanrank'] = max(0, min($session['user']['clanrank'], $setrank));
            }

            DB::query($sql);
            debuglog("Player {$session['user']['name']} changed rank of {$row['name']} from {$row['clanrank']} to {$setrank}.", $whoacctid);
        }
    }
}

if ($remove)
{
    require_once 'lib/safeescape.php';

    $select = DB::select('accounts');
    $select->columns(['name', 'login', 'clanrank'])
        ->limit(1)
        ->where->equalTo('acctid', $remove)
    ;
    $row = DB::execute($select)->current();

    $args = [
        'setrank' => 0,
        'login' => $row['login'],
        'name' => $row['name'],
        'acctid' => $remove,
        'clanid' => $session['user']['clanid'],
        'oldrank' => $row['clanrank']
    ];
    $args = modulehook('clan-setrank', $args);

    $update = DB::update('accounts');
    $update->set(['clanrank' => CLAN_APPLICANT, 'clanid' => 0, 'clanjoindate' => '0000-00-00 00:00:00'])
        ->where->equalTo('acctid', $remove)
            ->lessThanOrEqualTo('clanrank', $session['user']['clanrank'])
    ;
    DB::execute($update);
    debuglog("Player {$session['user']['name']} removed player {$row['login']} from {$claninfo['clanname']}.", $remove);

    //delete unread application emails from this user.
    //breaks if the applicant has had their name changed via
    //dragon kill, superuser edit, or lodge color change
    $subj = safeescape(serialize([$apply_short, $row['name']]));
    $sql = 'DELETE FROM '.DB::prefix('mail')." WHERE msgfrom=0 AND seen=0 AND subject='$subj'";
    DB::query($sql);
}

addnav('Clan Hall', 'clan.php');
addnav('Clan Options');

$select = DB::select('accounts');
$select->columns(['name', 'login', 'acctid', 'clanrank', 'laston', 'clanjoindate', 'dragonkills', 'level'])
    ->order('clanrank DESC, dragonkills DESC, level DESC, clanjoindate')
    ->where->equalTo('clanid', $claninfo['clanid'])
;
$members = DB::execute($select);

$twig = [
    'members' => $members,
    'ranks' => $ranks,
    'CLAN_OFFICER' => CLAN_OFFICER,
    'CLAN_FOUNDER' => CLAN_FOUNDER,
    'CLAN_ADMINISTRATIVE' => CLAN_ADMINISTRATIVE,
    'ret' => urlencode($_SERVER['REQUEST_URI']),
    'validranks' => array_intersect_key($ranks, range(0, $session['user']['clanrank']))
];

rawoutput($lotgdTpl->renderThemeTemplate('pages/clan/start/membership.twig', $twig));
