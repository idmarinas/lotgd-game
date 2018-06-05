<?php

page_header('Update Clan Description / MoTD');

addnav('Clan Options');
if ($session['user']['clanrank'] >= CLAN_OFFICER)
{
	$clanmotd = sanitize_mb(mb_substr(httppost('clanmotd'), 0, 4096));
    if (httppostisset('clanmotd') && stripslashes($clanmotd) != $claninfo['clanmotd'])
    {
		$sql = "UPDATE " . DB::prefix("clans") . " SET clanmotd='$clanmotd',motdauthor={$session['user']['acctid']} WHERE clanid={$claninfo['clanid']}";
		DB::query($sql);
		invalidatedatacache("clandata-{$claninfo['clanid']}");
		$claninfo['clanmotd'] = stripslashes($clanmotd);
		output("Updating MoTD`n");
		$claninfo['motdauthor'] = $session['user']['acctid'];
    }

	$clandesc = httppost('clandesc');
    if (httppostisset('clandesc') && stripslashes($clandesc) != $claninfo['clandesc'] && $claninfo['descauthor'] != 4294967295)
    {
		$sql = "UPDATE " . DB::prefix("clans") . " SET clandesc='".addslashes(substr(stripslashes($clandesc),0,4096))."',descauthor={$session['user']['acctid']} WHERE clanid={$claninfo['clanid']}";
		DB::query($sql);
		invalidatedatacache("clandata-{$claninfo['clanid']}");
		output("Updating description`n");
		$claninfo['clandesc'] = stripslashes($clandesc);
		$claninfo['descauthor'] = $session['user']['acctid'];
    }

	$customsay = httppost('customsay');
    if (httppostisset('customsay') && $customsay!=$claninfo['customsay'] && $session['user']['clanrank'] >= CLAN_LEADER)
    {
		$sql = "UPDATE " . DB::prefix("clans") . " SET customsay='$customsay' WHERE clanid={$claninfo['clanid']}";
		DB::query($sql);
		invalidatedatacache("clandata-{$claninfo['clanid']}");
		output("Updating custom say line`n");
		$claninfo['customsay'] = stripslashes($customsay);
    }

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
        'motdauthname' => $result['motdauthname'],
        'descauthname' => $result['descauthname'],
        'descriptionblocked' => ($claninfo['descauthor'] == INT_MAX),
        'rankleader' => ($session['user']['clanrank'] >= CLAN_LEADER)
    ];

    rawoutput($lotgdTpl->renderThemeTemplate('pages/clan/start/motd/edit.twig', $twig));
}
else
{
    rawoutput($lotgdTpl->renderThemeTemplate('pages/clan/start/motd/prohibited.twig', ['registrar' => $registrar]));
}

addnav('Return to your clan hall', 'clan.php');
