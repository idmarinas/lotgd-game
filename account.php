<?php
// translator ready
// addnews ready
// mail ready
require_once 'common.php';
// require_once 'lib/commentary.php';
require_once 'lib/datetime.php';
require_once 'lib/villagenav.php';

tlschema('account');

page_header('Account Information');
//-- This page no have comments
// addcommentary();
checkday();

addnav('Navigation');
villagenav();
addnav('Actions');
addnav('Refresh', 'account.php');

$user = $session['user'];

//pre-fill
$stats = [];

$stats[] = ['title' => 'Account created on:', 'value' => ($user['regdate'] == '0000-00-00 00:00:00' ? '---' : $user['regdate'])];
$stats[] = ['title' => 'Last Comment posted:', 'value' => $user['recentcomments']];
$stats[] = ['title' => 'Last PvP happened:', 'value' => $user['pvpflag']];
$stats[] = ['title' => 'Dragonkills:', 'value' => $user['dragonkills']];
$stats[] = ['title' => 'Total Pages generated for you:', 'value' => $user['gentimecount']];
$stats[] = ['title' => 'How long did these pages take to generate:', 'value' => reltime($user['gentime'])];
$stats[] = ['title' => 'You are Account Number:', 'value' => ($user['acctid']-1)];
//Add the count summary for DKs
$dksummary = '';
if ($user['dragonkills'] > 0) { $dragonpointssummary = array_count_values($user['dragonpoints']); }
else { $dragonpointssummary = []; }

foreach ($dragonpointssummary as $key => $value)
{
	$dksummary .= "$key --> $value`n";
}
$stats[] = ['title' => 'Dragon Point Spending:', 'value' => $dksummary];

$stats = modulehook('accountstats', $stats);

rawoutput($lotgd_tpl->renderThemeTemplate('pages/account.twig', ['stats' => $stats]));

tlschema();

page_footer();
