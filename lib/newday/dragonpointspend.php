<?php

page_header('Dragon Points');

reset($labels);

$params['points'] = $dkills - $dp;
$params['labels'] = $labels;
$params['canBuy'] = $canbuy;
$params['distribution'] = array_count_values($session['user']['dragonpoints']);

//-- More than 1 unallocated point
if ($params['points'] > 1)
{
    \LotgdNavigation::addNav('nav.reset', "newday.php?pdk=0$resline");

    $params['formUrl'] = appendcount("newday.php?pdk=1$resline");
}
//-- 1 unallocated point
else
{
    foreach ($labels as $type => $label)
    {
        $head = explode(',', $label);

        if (count($head) > 1)
        {
            \LotgdNavigation::addHeader($head[0]); //got a headline here
            continue;
        }
        if ($canbuy[$type] ?? false)
        {
            \LotgdNavigation::addNavNotl($label, "newday.php?dk=$type$resline");
        }
    }
}
