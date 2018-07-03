<?php

output('`)`b`cThe Mausoleum`c`b');

$twig = [
    'deathoverlord' => $deathoverlord,
    'needrestore' => ($session['user']['soulpoints'] < $max),
    'favortoheal' => $favortoheal,
    'restoredsoul' => false
];

if ($session['user']['soulpoints'] < $max)
{
    if ($session['user']['deathpower'] >= $favortoheal)
    {
        $twig['restoredsoul'] = true;
        $session['user']['deathpower'] -= $favortoheal;
        $session['user']['soulpoints'] = $max;
    }
}

rawoutput($lotgd_tpl->renderThemeTemplate('pages/graveyard/restore.twig', $twig));

addnav('Places');
addnav('S?Land of the Shades', 'shades.php');
addnav('G?Return to the Graveyard', 'graveyard.php');
addnav('Souls');
addnav(['Question `$%s`0 about the worth of your soul', $deathoverlord], 'graveyard.php?op=question');
