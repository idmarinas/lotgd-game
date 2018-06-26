<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/dhms.php';
require_once 'lib/http.php';
require_once 'lib/superusernav.php';

tlschema('debug');

check_su_access(SU_EDIT_CONFIG);

$sort = (string) httpget('sort');
$debug = (string) httpget('debug');
$ascdesc_raw = (int) httpget('direction');

superusernav();
addnav('Debug Options');
addnav('', $_SERVER['REQUEST_URI']);
addnav('Get Pageruntimes', 'debug.php?debug=pageruntime&sort='.urlencode($sort));
addnav('Get Modulehooktimes', 'debug.php?debug=hooksort&sort='.urlencode($sort));

page_header('Debug Analysis');

$select = DB::select('debug');

if ('' == $debug)
{
    $debug = 'pageruntime';
}

switch ($debug)
{
    case 'hooksort':
        $select->columns(['category', 'subcategory', 'sum' => 'value', 'medium' => DB::expression('value/COUNT(1)'), 'counter' => DB::expression('COUNT(1')])
            ->limit(30)
            ->group('type, category, subcategory')
            ->where->equalTo('type', 'hooktime')
        ;
        break;

    case 'pageruntime':
    default:
        $select->columns(['category', 'subcategory', 'sum' => 'value', 'medium' => DB::expression('value/COUNT(1)'), 'counter' => DB::expression('COUNT(1')])
            ->limit(30)
            ->group('type, category, subcategory')
            ->where->equalTo('type', 'pagegentime')
        ;
}

$order = 'sum';
if ('' != $sort)
{
    $order = $sort;
}

$ascdesc = 'DESC';
if ($ascdesc_raw)
{
    $ascdesc = 'ASC';
}

$select->order("$order $ascdesc");
$result = DB::execute($select);

addnav('Sorting');
addnav('By Total', 'debug.php?debug='.$debug.'&sort=sum&direction='.$ascdesc_raw);
addnav('By Average', 'debug.php?debug='.$debug.'&sort=medium&direction='.$ascdesc_raw);
addnav('Switch ASC/DESC', 'debug.php?debug='.$debug.'&sort='.urlencode($sort).'&direction='.(! $ascdesc_raw));

$twig = [
    'content' => $result,
];

rawoutput($lotgd_tpl->renderThemeTemplate('pages/debug.twig', $twig));

page_footer();
