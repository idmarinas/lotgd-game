<?php

if (! $skipgraveyardtext)
{
    $twig = ['deathoverlord' => $deathoverlord];

    rawoutput($lotgd_tpl->renderThemeTemplate('pages/graveyard.twig', $twig));
}

addnav('S?Return to the Shades', 'shades.php');

if ($session['user']['gravefights'])
{
    addnav('Torment');
    addnav('Look for Something to Torment', 'graveyard.php?op=search');
}
addnav('Places');
addnav('W?List Warriors', 'list.php');
addnav('M?Enter the Mausoleum', 'graveyard.php?op=enter');
module_display_events('graveyard', 'graveyard.php');
