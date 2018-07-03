<?php

// addnews ready
// translator ready
// mail ready
require_once 'common.php';
require_once 'lib/commentary.php';
require_once 'lib/villagenav.php';
require_once 'lib/events.php';
require_once 'lib/http.php';

tlschema('gardens');

page_header('The Gardens');

addcommentary();
$skipgardendesc = handle_event('gardens');
$op = httpget('op');
$com = httpget('comscroll');
$refresh = httpget('refresh');
$commenting = httpget('commenting');
$comment = httppost('insertcommentary');
// Don't give people a chance at a special event if they are just browsing
// the commentary (or talking) or dealing with any of the hooks in the village.
if (! $op && '' == $com && ! $comment && ! $refresh && ! $commenting)
{
    if (0 != module_events('gardens', getsetting('gardenchance', 0)))
    {
        if (checknavs())
        {
            page_footer();
        }
        else
        {
            // Reset the special for good.
            $session['user']['specialinc'] = '';
            $session['user']['specialmisc'] = '';
            $skipgardendesc = true;
            $op = '';
            httpset('op', '');
        }
    }
}

if (! $skipgardendesc)
{
    checkday();

    rawoutput($lotgd_tpl->renderThemeTemplate('pages/gardens.twig', []));
}

villagenav();
modulehook('gardens', []);

commentdisplay('', 'gardens', 'Whisper here', 30, 'whispers');

module_display_events('gardens', 'gardens.php');
page_footer();
