<?php

$twig = ['deathoverlord' => $deathoverlord];

rawoutput($lotgd_tpl->renderThemeTemplate('pages/graveyard/enter.twig', $twig));

addnav('G?Return to the Graveyard', 'graveyard.php');
addnav('Places');
addnav('S?Land of the Shades', 'shades.php');
addnav('Souls');
addnav(['Question `$%s`0 about the worth of your soul', $deathoverlord], 'graveyard.php?op=question');
addnav(['Restore Your Soul (%s favor)', $favortoheal], 'graveyard.php?op=restore');
modulehook('mausoleum');
