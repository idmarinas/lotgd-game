<?php

$twig = [
    'deathoverlord' => $deathoverlord
];

rawoutput($lotgd_tpl->renderThemeTemplate('pages/graveyard/haunt.twig', $twig));

addnav('Continue', 'newday.php?resurrection=true');

addnav('Places');
addnav('S?Land of the Shades', 'shades.php');
addnav('G?The Graveyard', 'graveyard.php');
addnav('M?Return to the Mausoleum', 'graveyard.php?op=enter');
