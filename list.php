<?php

// addnews ready
// translator ready
// mail ready
define('ALLOW_ANONYMOUS', true);
require_once 'common.php';
require_once 'lib/http.php';
require_once 'lib/villagenav.php';

tlschema('list');

page_header('List Warriors');

if ($session['user']['loggedin'])
{
    checkday();

    if ($session['user']['alive'])
    {
        villagenav();
    }
    else
    {
        addnav('Return to the Graveyard', 'graveyard.php');
    }
    addnav('Currently Online', 'list.php');

    if ($session['user']['clanid'] > 0)
    {
        addnav('Online Clan Members', 'list.php?op=clan');

        if ($session['user']['alive'])
        {
            addnav('Clan Hall', 'clan.php');
        }
    }
}
else
{
    addnav('Login page');
    addnav('Login Screen', 'index.php');
    addnav('Currently Online', 'list.php');
}

$playersperpage = (int) getsetting('maxlistsize', 100);

$op = (string) httpget('op');
$page = (int) httpget('page');

// Order the list by level, dragonkills, name so that the ordering is total!
// Without this, some users would show up on multiple pages and some users
// wouldn't show up
if (0 == $page && '' == $op)
{
    $select = DB::select('accounts');
    $select->columns(['acctid', 'name', 'login', 'alive', 'hitpoints', 'location', 'race', 'sex', 'level', 'laston', 'loggedin', 'lastip', 'uniqueid'])
        ->order('level DESC, dragonkills DESC, login ASC')
        ->where->equalTo('locked', 0)
            ->equalTo('loggedin', 1)
            ->greaterThan('laston', date('Y-m-d H:i:s', strtotime('-'.getsetting('LOGINTIMEOUT', 900).' seconds')))
    ;
    $paginator = DB::paginator($select, $page, $playersperpage);

    $title = ['Warriors Currently Online  (%s warriors)', $paginator->getTotalItemCount()];
}
elseif ('clan' == $op)
{
    $title = 'Clan Members Online';

    $select = DB::select('accounts');
    $select->columns(['acctid', 'name', 'login', 'alive', 'hitpoints', 'location', 'race', 'sex', 'level', 'laston', 'loggedin', 'lastip', 'uniqueid'])
        ->order('level DESC, dragonkills DESC, login ASC')
        ->where->equalTo('locked', 0)
            ->equalTo('loggedin', 1)
            ->equalTo('clanid', $session['user']['clanid'])
            ->greaterThan('laston', date('Y-m-d H:i:s', strtotime('-'.getsetting('LOGINTIMEOUT', 900).' seconds')))
    ;
    $paginator = DB::paginator($select, $page, $playersperpage);
}
else
{
    $select = DB::select('accounts');
    $select->columns(['acctid', 'name', 'login', 'alive', 'hitpoints', 'location', 'race', 'sex', 'level', 'laston', 'loggedin', 'lastip', 'uniqueid'])
        ->order('level DESC, dragonkills DESC, login ASC')
        ->where->equalTo('locked', 0)
    ;

    if ('search' == $op)
    {
        $search = addslashes((string) httppost('name'));
        $select->where->like('name', "%$search%");

        $title = 'Warriors of the realm';
    }

    $paginator = DB::paginator($select, $page, $playersperpage);

    if ('search' != $op)
    {
        $minpage = (($page - 1) * $paginator->getItemCountPerPage()) + 1;
        $maxpage = $paginator->getItemCountPerPage() * $page;
        $maxpage = ($paginator->getTotalItemCount() >= $maxpage? $maxpage : $paginator->getTotalItemCount());
        $title = ['Warriors of the realm (Page %s: %s-%s of %s)', $paginator->getCurrentPageNumber(), $minpage, $maxpage, $paginator->getTotalItemCount()];
    }
}

$totalplayers = $paginator->getTotalItemCount();

//-- Add pages links
DB::pagination($paginator, 'list.php', true);

$twig = [
    'title' => $title,
    'RACE_UNKNOWN' => RACE_UNKNOWN,
    'paginator' => $paginator
];

rawoutput($lotgd_tpl->renderThemeTemplate('pages/list.twig', $twig));

page_footer();
