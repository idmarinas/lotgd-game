<?php
// translator ready
// addnews ready
// mail ready

if (isset($_POST['template']))
{
	$skin = $_POST['template'];
	if ($skin > '')
    {
		setcookie('template', $skin, strtotime('+45 days'));
		$_COOKIE['template'] = $skin;
	}
}

define('ALLOW_ANONYMOUS', true);

require_once 'common.php';
require_once 'lib/http.php';

if (! isset($session['loggedin'])) { $session['loggedin'] = false; }
if ($session['loggedin']) { redirect('badnav.php'); }

tlschema('home');

$op = httpget('op');

page_header();

clearnav();
addnav("New to LoGD?");
addnav("Create a character","create.php");
addnav("Game Functions");
addnav("Forgotten Password","create.php?op=forgot");
addnav("List Warriors","list.php");
addnav("Daily News", "news.php");
addnav("Other Info");
addnav("About LoGD","about.php");
addnav("Game Setup Info", "about.php?op=setup");
addnav("LoGD Net","logdnet.php?op=list");

$data = [];
if (getsetting('homenewestplayer', 1))
{
	$name = '';
    $newplayer = (int) getsetting('newestplayer', 0);
    $data['homenewestplayer'] = '';
    if ($newplayer)
    {
        $select = DB::select('accounts');
        $select->columns(['name'])
            ->where->equalTo('acctid', $newplayer);

        $result = DB::execute($select)->current();

        $data['homenewestplayer'] = $result['name'];
    }
}

if (abs(getsetting('OnlineCountLast', 0) - strtotime('now')) > 60)
{
    $select = DB::select('accounts');
    $select->columns(['onlinecount' => DB::expression('COUNT(1)')])
        ->where->equalTo('locked', 0)
            ->equalTo('loggedin', 1)
            ->greaterThan('laston', date('Y-m-d H:i:s', strtotime('-' . getsetting('LOGINTIMEOUT', 900) . ' seconds')));
    $result = DB::execute($select)->current();

	savesetting('OnlineCount', $result['onlinecount']);
	savesetting('OnlineCountLast', strtotime('now'));
}

$data['onlinecount'] = getsetting('OnlineCount', 0);

if (! isset($session['message'])) $session['message'] = '';

if ($op == 'timeout') { $session['message'] .= ' Your session has timed out, you must log in again.`n'; }

if (!isset($_COOKIE['lgi']))
{
	$session['message'] .= 'It appears that you may be blocking cookies from this site.  At least session cookies must be enabled in order to use this site.`n';
	$session['message'] .= "`b`#If you are not sure what cookies are, please <a href='http://en.wikipedia.org/wiki/WWW_browser_cookie'>read this article</a> about them, and how to enable them.`b`n";
}

$data['message'] = $session['message'];

$session['message'] = '';
$data['logd_version'] = $logd_version;

if (getsetting('homeskinselect', 1))
{
	require_once 'lib/showform.php';

	$prefs['template'] = (isset($_COOKIE['template']) ? $_COOKIE['template'] : '');
	if ($prefs['template'] == '') $prefs['template'] = getsetting('defaultskin', 'jade.htm');
	$form = ['template' => 'Choose a different display skin:,theme'];

    $data['skinselect'] = lotgd_showform($form, $prefs, true, false, false);
}

rawoutput($lotgd_tpl->renderThemeTemplate('pages/home.twig', $data));

page_footer();
