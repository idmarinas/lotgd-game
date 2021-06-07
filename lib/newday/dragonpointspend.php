<?php

//-- Init page
\LotgdResponse::pageStart('title.dragonpoints', [], $textDomain);

\reset($labels);

$params['tpl']          = 'dragonpoints';
$params['points']       = $dkills - $dp;
$params['labels']       = $labels;
$params['canBuy']       = $canbuy;
$params['distribution'] = \array_count_values($session['user']['dragonpoints']);

//-- More than 1 unallocated point
if ($params['points'] > 1)
{
    \LotgdNavigation::addNav('nav.reset', "newday.php?pdk=0{$resline}");

    $params['formUrl'] = "newday.php?pdk=1{$resline}";
}
//-- 1 unallocated point
else
{
    foreach ($labels as $type => $label)
    {
        $head = \explode(',', $label);

        if (\count($head) > 1)
        {
            \LotgdNavigation::addHeader("category.{$type}");

            continue;
        }

        if ($canbuy[$type] ?? false)
        {
            \LotgdNavigation::addNav("nav.{$type}", "newday.php?dk={$type}{$resline}");
        }
    }
}

// $labels = [
//     'general' => 'General Stuff,title',
//         'ff' => 'Forest Fights + 1',
//     'attributes' => 'Attributes,title',
//         'str' => 'Strength +1',
//         'dex' => 'Dexterity +1',
//         'con' => 'Constitution +1',
//         'int' => 'Intelligence +1',
//         'wis' => 'Wisdom +1',
//     'unknown' => 'Unknown Spends (contact an admin to investigate!)',
// ];
