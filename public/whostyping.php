<?php

global $session, $fiveminuteload;

\define('ALLOW_ANONYMOUS', true);
\define('OVERRIDE_FORCED_NAV', true);
\define('NO_SAVE_USER', true);

require_once 'common.php';

$now    = time();
$minute = round($now / 60) * 60;

$session['typerequests'][$minute] = $session['typerequests'][$minute] ?? 0;
++$session['typerequests'][$minute];

if ($session['typerequests'][$minute] >= 200)
{
    echo "Please don't run multiple Global Banter windows, it puts a tremendous strain on the server.  I've logged you out.  You'll be able to log back in again in a few minutes - please clear your cookies.";
    $session['user']['loggedin'] = false;
    LotgdTool::saveUser();

    exit();
}

if ($fiveminuteload >= 8)
{
    echo 'Server load is too high for auto-update at the moment.  This will hopefully balance out in a few minutes.';

    exit();
}

$section      = LotgdRequest::getquery('section');
$updateplayer = LotgdRequest::getquery('updateplayer');
$name         = addslashes(($session['user']['name'] ?? ''));
$now          = time();

if ( ! isset($session['iterations']))
{
    $session['iterations'] = 0;
}
++$session['iterations'];

$old = $now - 2;

$repository = Doctrine::getRepository('LotgdCore:Whostyping');
//update time
if ($updateplayer)
{
    $entity = $repository->find($name);
    $entity = $repository->hydrateEntity([
        'time'    => $now,
        'name'    => $name,
        'section' => $section,
    ], $entity);

    Doctrine::persist($entity);
    Doctrine::flush();

    //erase old entries once per ten seconds
    $lastdigit = substr($now, -1);

    if ('0' == $lastdigit)
    {
        $repository->deleteOld($old);
    }
}

//retrieve, deleting as appropriate
$result = $repository->findBy(['section' => $section]);

foreach ($result as $row)
{
    if ($row->getTime() > $old)
    {
        echo LotgdFormat::colorize($row->getName().'`0 takes a breath...`n');
    }
}

exit();
