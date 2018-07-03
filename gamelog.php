<?php

// translator ready
// addnews ready
// mail ready

// Written by Christian Rutsch and rewritten by IDMarinas

require_once 'common.php';
require_once 'lib/http.php';
require_once 'lib/superusernav.php';

check_su_access(SU_EDIT_CONFIG);

tlschema('gamelog');

page_header('Game Log');

addnav('Navigation');
superusernav();

$step = 500; // hardcoded stepping
$category = httpget('cat');
$page = max((int) httpget('page'), 1); //Page
$sortorder = (int) httpget('sortorder');
$sort_order = (0 == $sortorder ? 'DESC' : 'ASC'); // 0 = DESC 1= ASC
$sortby = (string) httpget('sortby');

addnav('Operations');
addnav('Refresh', "gamelog.php?page=$page&category=$category&sortorder=$sortorder&sortby=$sortby");

if ($category)
{
    addnav('View all', 'gamelog.php');
}

$select = DB::select(['log' => 'gamelog']);
$select->join(['acct' => 'accounts'], 'acct.acctid = log.who', ['name']);

if ($category)
{
    $select->where->equalTo('log.category', $category);
}

if ($sortby)
{
    $select->order("$sortby $sort_order");
}

$paginator = DB::paginator($select, $page, 500);

$twig = [
    'paginator' => $paginator,
    'category' => $category
];

rawoutput($lotgd_tpl->renderThemeTemplate('pages/gamelog.twig', $twig));

DB::pagination($paginator, "gamelog.php?category=$category&sortorder=$sortorder&sortby=$sortby");

addnav('Sorting');
addnav('Sort by date ascending', "gamelog.php?page=$page&category=$category&sortorder=1&sortby=date");
addnav('Sort by date descending', "gamelog.php?page=$page&category=$category&sortorder=0&sortby=date");
addnav('Sort by category ascending', "gamelog.php?page=$page&category=$category&sortorder=1&sortby=category");
addnav('Sort by category descending', "gamelog.php?page=$page&category=$category&sortorder=0&sortby=category");

page_footer();
