<?php

$to = (int) httpget('to');

if ($to)
{
    $session['user']['clanid'] = $to;
	$session['user']['clanrank'] = CLAN_APPLICANT;
    $session['user']['clanjoindate'] = date('Y-m-d H:i:s');

    $select = DB::select('accounts');
    $select->columns(['acctid'])
        ->where->equalTo('clanid', $session['user']['clanid'])
            ->greaterThanOrEqualTo('clanrank', CLAN_OFFICER)
    ;
    $officers = DB::execute($select);

    while ($row = $officers->next())
    {
        $msg = ['`^You have a new clan applicant!  `&%s`^ has completed a membership application for your clan!', $session['user']['name'] ];
		systemmail($row['acctid'], $apply_subj, $msg);
    }

    $delete = DB::delete('mail');
    $delete->where->equalTo('msgfrom', 0)
        ->equalTo('seen', 0)
        ->equalTo('subject', addslashes(serialize($apply_subj)))
    ;
    DB::execute($delete);

    // send reminder mail if clan of choice has a description
    $select = DB::select('clans');
    $select->columns(['clanname', 'clanshort', 'clandesc'])
        ->where->equalTo('clanid', $to);
    $result = DB::execute($select)->current();

    if (nltoappon($result['clandesc']) != '' )
    {
		$subject = 'Clan Application Reminder';
        $mail = '`&Did you remember to read the description of the clan of your choice before applying?  Note that some clans may have requirements that you have to fulfill before you can become a member.  If you are not accepted into the clan of your choice anytime soon, it may be because you have not fulfilled these requirements.  For your convenience, the description of the clan you are applying to is reproduced below.`n`n`c`#%s`@ <`^%s`@>`0`c`n%s';

		systemmail($session['user']['acctid'], [$subject], [$mail, $result['clanname'], $result['clanshort'], addslashes(nltoappon($result['clandesc']))] );
    }

    addnav('Return to the Lobby', 'clan.php');
    addnav('Waiting Area', 'clan.php?op=waiting');

    rawoutput($lotgdTpl->renderThemeTemplate('pages/clan/applicant/apply/success.twig', ['registrar' => $registrar]));
}
else
{
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

    $clans = DB::execute($select);

    if ($clans->count())
    {
		addnav('Return to the Lobby', 'clan.php');
		addnav('Sorting');
		addnav('Order by Membercount', 'clan.php?op=apply&order=0');
        addnav('Order by Clanname', 'clan.php?op=apply&order=1');

        rawoutput($lotgdTpl->renderThemeTemplate('pages/clan/applicant/apply/clans.twig', ['registrar' => $registrar, 'clans' => $clans]));
    }
    else
    {
		addnav('Apply for a New Clan', 'clan.php?op=new');
        addnav('Return to the Lobby', 'clan.php');

        rawoutput($lotgdTpl->renderThemeTemplate('pages/clan/applicant/apply/noclans.twig', ['registrar' => $registrar]));
    }
}


if ($to>0){
}else{

	if (DB::num_rows($result)>0){

	}else{
	}
}
?>
