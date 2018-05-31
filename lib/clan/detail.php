<?php

if ($session['user']['superuser'] & SU_EDIT_COMMENTS)
{
	$clanname = httppost('clanname');
	$clanshort = httppost('clanshort');
	if ($clanname) $clanname = full_sanitize($clanname);
    if ($clanshort) $clanshort = full_sanitize($clanshort);

    if ($clanname > '' && $clanshort > '')
    {
		$sql = "UPDATE " . DB::prefix("clans") . " SET clanname='$clanname',clanshort='$clanshort' WHERE clanid='$detail'";
		output("Updating clan names`n");
		DB::query($sql);
		invalidatedatacache("clandata-$detail");
	}
    if (httppost('block') > '')
    {
		$blockdesc = translate_inline("Description blocked for inappropriate usage.");
		$sql = "UPDATE " . DB::prefix("clans") . " SET descauthor=4294967295, clandesc='$blockdesc' where clanid='$detail'";
		output("Blocking public description`n");
		DB::query($sql);
		invalidatedatacache("clandata-$detail");
    }
    elseif (httppost('unblock') > '')
    {
		$sql = "UPDATE " . DB::prefix("clans") . " SET descauthor=0, clandesc='' where clanid='$detail'";
		output("UNblocking public description`n");
		DB::query($sql);
		invalidatedatacache("clandata-$detail");
	}
}

//-- Info of Clan
$select = DB::select('clans');
$select->where->equalTo('clanid', $detail);
$clan = DB::execute($select)->current();

//-- List of members
$select = DB::select('accounts');
$select->columns(['acctid', 'name', 'login', 'clanrank', 'clanjoindate', 'dragonkills'])
    ->order('clanrank DESC')
    ->where->equalTo('clanid', $detail);
$members = DB::execute($select);

page_header('Clan Membership for %s &lt;%s&gt;', full_sanitize($clan['clanname']), full_sanitize($clan['clanshort']));

addnav("Clan Options");

//little hack with the hook...can't think of any other way
$args = modulehook('clanranks', ['ranks' => $defaultRanks, 'clanid' => $detail]);

$data = [
    'clan' => $clan,
    'members' => $members,
    'ranks' => $args['ranks'],
    'ret' => urlencode($_SERVER['REQUEST_URI']),
    'moderator' => ($session['user']['superuser'] & SU_AUDIT_MODERATION),
    'moderatorform' => htmlspecialchars($lotgdTpl->renderThemeTemplate('pages/clan/form/moderator.twig', ['clan' => $clan]))
];

rawoutput($lotgdTpl->renderThemeTemplate('pages/clan/detail.twig', $data));
