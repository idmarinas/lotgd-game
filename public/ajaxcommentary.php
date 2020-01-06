<?php

global $session, $fiveminuteload;

define('ALLOW_ANONYMOUS', true);
define('OVERRIDE_FORCED_NAV', true);

require_once 'common.php';

if (! $session['user']['loggedin'])
{
    return;
}

$now = time();
$minute = round($now / 60) * 60;

if (! isset($session['chatrequests'][$minute]))
{
    $session['chatrequests'][$minute] = 0;
}
$session['chatrequests'][$minute]++;

if ($session['chatrequests'][$minute] >= 50)
{
    echo "Please don't run multiple Global Banter windows, it puts a tremendous strain on the server.  I've logged you out.  You'll be able to log back in again in a few minutes - please clear your cookies.";
    $session['user']['loggedin'] = false;
    saveuser();

    exit();
}

$expiresin = $session['user']['laston']->getTimestamp() + 600;

$section = $_REQUEST['section'];

if ($now > $expiresin || (isset($session['user']['chatloc']) && 'global_banter' != $session['user']['chatloc'] && 'global_banter' != $section && $session['user']['chatloc'] != $section && $session['user']['chatloc'].'_aux' != $section))
{
    echo 'Chat disabled due to inactivity';
}
else
{
    $message = $_REQUEST['message'];
    $limit = $_REQUEST['limit'];
    $talkline = $_REQUEST['talkline'];
    $returnlink = urlencode($_REQUEST['returnlink']);
    $showmodlink = ($_REQUEST['showmodlink'] ?? '');

    $commentary = preparecommentaryblock($section, $message, $limit, $talkline, false, false, false, false, $showmodlink, $returnlink);

    echo appoencode($commentary, true);
}
saveuser();

exit();
