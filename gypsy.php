<?php

// addnews ready
// translator ready
// mail ready
require_once 'common.php';
require_once 'lib/commentary.php';
require_once 'lib/http.php';
require_once 'lib/villagenav.php';

tlschema('gypsy');

addcommentary();

$cost = $session['user']['level'] * 20;
$op = httpget('op');

$twig = ['cost' => $cost];

if ('pay' == $op)
{
    if ($session['user']['gold'] >= $cost)
    { // Gunnar Kreitz
        $session['user']['gold'] -= $cost;
        debuglog("spent $cost gold to speak to the dead");
        redirect('gypsy.php?op=talk');
    }
    else
    {
        $twig['paid'] = false;
        page_header("Gypsy Seer's tent");
        villagenav();

        rawoutput($lotgd_tpl->renderThemeTemplate('pages/gypsy/nomoney.twig', $twig));
    }
}
elseif ('talk' == $op)
{
    page_header('In a deep trance, you talk with the shades');
    commentdisplay('`5While in a deep trance, you are able to talk with the dead:`n', 'shade', 'Project', 25, translate_inline('projects'));
    addnav('Snap out of your trance', 'gypsy.php');
}
else
{
    checkday();
    page_header("Gypsy Seer's tent");

    rawoutput($lotgd_tpl->renderThemeTemplate('pages/gypsy.twig', $twig));

    addnav('Seance');
    addnav(['Pay to talk to the dead (%s gold)', $cost], 'gypsy.php?op=pay');

    if ($session['user']['superuser'] & SU_EDIT_COMMENTS)
    {
        addnav('Superuser Entry', 'gypsy.php?op=talk');
    }

    addnav('Other');
    addnav('Forget it', 'village.php');
    modulehook('gypsy');
}

page_footer();
