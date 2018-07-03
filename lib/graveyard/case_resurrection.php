<?php

$twig = [
    'deathoverlord' => $deathoverlord
];

rawoutput($lotgd_tpl->renderThemeTemplate('pages/graveyard/resurrection.twig', $twig));

addnav('Continue', 'newday.php?resurrection=true');
