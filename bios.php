<?php
// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/systemmail.php';
require_once 'lib/http.php';
require_once 'lib/superusernav.php';

tlschema('bio');
check_su_access(SU_EDIT_COMMENTS);

page_header('User Bios');

$op = httpget('op');
$userid = httpget('userid');

$twig = [];

if ($op == 'block')
{
    $update = DB::update('accounts');
    $update->set([
            'bio' => translate_inline('`iBlocked for inappropriate usage`i'),
            'biotime' => '9999-12-31 23:59:59'
        ])
        ->where->equalTo('acctid', $userid);

	$subj = ['Your bio has been blocked'];
	$msg = ['The system administrators have decided that your bio entry is inappropriate, so it has been blocked.`n`nIf you wish to appeal this decision, you may do so with the petition link.'];
    systemmail($userid, $subj, $msg);

	DB::execute($update);
}

if ($op == 'unblock')
{
    $update = DB::update('accounts');
    $update->set([
            'bio' => '',
            'biotime' => '0000-00-00 00:00:00'
        ])
        ->where->equalTo('acctid', $userid);

	$subj = ['Your bio has been unblocked'];
	$msg = ['The system administrators have decided to unblock your bio.  You can once again enter a bio entry.'];
    systemmail($userid,$subj,$msg);

	DB::execute($update);
}

superusernav();

addnav('Moderation');
if ($session['user']['superuser'] & SU_EDIT_COMMENTS) { addnav('Return to Comment Moderation', 'moderate.php'); }
addnav('Refresh', 'bios.php');

$select = DB::select('accounts');
$select->columns(['name', 'acctid', 'bio', 'biotime'])
    ->limit(100)
    ->order('biotime DESC')
    ->where
        ->lessThan('biotime', '9999-12-31')
        ->notEqualTo('bio', '')
;
$twig['playersbios'] = DB::execute($select);

// $sql = "SELECT name,acctid,bio,biotime FROM " . DB::prefix("accounts") . " WHERE biotime>'9000-01-01' AND bio>'' ORDER BY biotime DESC LIMIT 100";
$select = DB::select('accounts');
$select->columns(['name', 'acctid', 'bio', 'biotime'])
    ->limit(100)
    ->order('biotime DESC')
    ->where
        ->greaterThan('biotime', '9000-01-01')
        ->notEqualTo('bio', '')
;
$twig['biosblocked'] = DB::execute($select);

// $result = DB::query($sql);
// output("`n`n`b`&Blocked Bios:`0`b`n");
// $unblock = translate_inline("Unblock");
// $number=DB::num_rows($result);

// for ($i=0;$i<$number;$i++)
// {
// 	$row = DB::fetch_assoc($result);

// 	output_notl("`![<a href='bios.php?op=unblock&userid={$row['acctid']}'>$unblock</a>]", true);
// 	addnav("", "bios.php?op=unblock&userid={$row['acctid']}");
// 	output_notl("`&%s`0: `^%s`0`n", $row['name'], soap($row['bio']));
// }
// DB::free_result($result);

rawoutput($lotgdTpl->renderThemeTemplate('pages/bios.twig', $twig));

tlschema();

page_footer();
