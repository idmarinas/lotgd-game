<?php

modulehook('clan-withdraw', ['clanid' => $session['user']['clanid'], 'clanrank' => $session['user']['clanrank'], 'acctid' => $session['user']['acctid']]);

$twig = [
    'registrar' => $registrar,
    'messages' => []
];

if ($session['user']['clanrank'] >= CLAN_LEADER)
{
    //first test to see if we were the leader.
    $select = DB::select('accounts');
    $select->columns(['count' => DB::expression('COUNT(1)')])
        ->where->equalTo('clanid', $session['user']['clanid'])
            ->greaterThanOrEqualTo('clanrank', CLAN_LEADER)
            ->notEqualTo('acctid', $session['user']['acctid'])
    ;
    $row = DB::execute($select)->current();

    if ($row['c'] == 0)
    {
        $select = DB::select('accounts');
        $select->columns(['name', 'acctid', 'clanrank'])
            ->order('clanrank DESC, clanjoindate')
            ->limit(1)
            ->where->equalTo('clanid', $session['user']['clanid'])
                ->greaterThan('clanrank', CLAN_APPLICANT)
                ->notEqualTo('acctid', $session['user']['acctid'])
        ;
        $row = DB::execute($select)->current();

        if ($row)
        {
			//there is no alternate leader, let's promote the
			//highest ranking member (or oldest member in the
			//event of a tie).  This will capture even people
            //who applied for membership.

            $update = DB::update('accounts');
            $update->set(['clanrank' => CLAN_LEADER])
                ->where->equalTo('acctid', $row['acctid'])
            ;
            DB::execute($update);

            $twig['messages'][] = ['`^Promoting %s`^ to leader as they are the highest ranking member (or oldest member in the event of a tie).`n`n', $row['name']];
        }
        else
        {
			//There are no other members, we need to delete the clan.
            modulehook('clan-delete', ['clanid' => $session['user']['clanid']]);

            $delete = DB::delete('clans');
            $delete->where->equalTo('clanid', $session['user']['clanid']);
            DB::execute($delete);

			//just in case we goofed, we don't want to have to worry
			//about people being associated with a deleted clan.
            $update = DB::update('accounts');
            $update->columns(['clanid' => 0, 'clanrank' => CLAN_APPLICANT, 'clanjoindate' => '0000-00-00 00:00:00'])
                ->where->equalTo('clanid', $session['user']['clanid'])
            ;
            DB::execute($update);

            $twig['messages'][] = '`^As you were the last member of this clan, it has been deleted.';

			require_once 'lib/gamelog.php';
			gamelog("Clan {$session['user']['clanid']} has been deleted, last member gone", 'Clan');
		}
    }
    else
    {
		//we don't have to do anything special with this clan as
		//although we were leader, there is another leader already
		//to take our place.
	}
}
else
{
	//we don't have to do anything special with this clan as we were
	//not the leader, and so there should still be other members.
}

$select = DB::select('accounts');
$select->columns(['acctid'])
    ->where->equalTo('clanid', $session['user']['clanid'])
        ->equalTo('clanrank', CLAN_OFFICER)
        ->notEqualTo('acctid', $session['user']['acctid'])
;
$result = DB::execute($select);

$withdraw_subj = array('`$Clan Withdraw: `&%s`0', $session['user']['name']);
$msg = array('`^One of your clan members has resigned their membership.  `&%s`^ has surrendered their position within your clan!', $session['user']['name']);

$delete = DB::delete('mail');
$delete->where->equalTo('msgfrom', 0)
    ->equalTo('seen', 0)
    ->equalTo('subject', addslashes(serialize($withdraw_subj)))
;
DB::execute($delete);

while ($row = $result->next())
{
	systemmail($row['acctid'], $withdraw_subj, $msg);
}

debuglog("{$session['user']['login']} has withdrawn from his/her clan no. {$session['user']['clanid']}");

$session['user']['clanid'] = 0;
$session['user']['clanrank'] = CLAN_APPLICANT;
$session['user']['clanjoindate'] = '0000-00-00 00:00:00';

addnav('Clan Options');
addnav('Return to the Lobby', 'clan.php');

rawoutput($lotgdTpl->renderThemeTemplate('pages/clan/start/withdraw.twig', $twig));
