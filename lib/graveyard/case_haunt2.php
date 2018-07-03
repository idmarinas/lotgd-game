<?php

$string = '%';
$name = httppost('name');

for ($x = 0; $x < strlen($name); $x++)
{
    $string .= substr($name, $x, 1).'%';
}

$select = DB::select('accounts');
$select->columns(['login', 'name', 'level'])
    ->order('level, login')
    ->where->like('name', $string)
        ->equalTo('locked', 0)
;
$result = DB::execute($select);

$twig = [
    'deathoverlord' => $deathoverlord,
    'paginator' => $result
];

if ($result->count() <= 0)
{
    $twig['found'] = false;
}
elseif ($result->count() > 100)
{
    $twig['found'] = true;
}
else
{
    $twig['found'] = $result->count();
}

rawoutput($lotgd_tpl->renderThemeTemplate('pages/graveyard/haunt2.twig', $twig));

addnav(['Question `$%s`0 about the worth of your soul', $deathoverlord], 'graveyard.php?op=question');
$max = $session['user']['level'] * 5 + 50;
$favortoheal = round(10 * ($max - $session['user']['soulpoints']) / $max);
addnav(['Restore Your Soul (%s favor)', $favortoheal], 'graveyard.php?op=restore');
addnav('Places');
addnav('S?Land of the Shades', 'shades.php');
addnav('G?The Graveyard', 'graveyard.php');
addnav('M?Return to the Mausoleum', 'graveyard.php?op=enter');
