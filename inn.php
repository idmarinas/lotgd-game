<?php

// addnews ready
// translator ready
// mail ready
require_once 'common.php';
require_once 'lib/commentary.php';
require_once 'lib/pvpwarning.php';
require_once 'lib/sanitize.php';
require_once 'lib/pvplist.php';
require_once 'lib/http.php';
require_once 'lib/buffs.php';
require_once 'lib/events.php';
require_once 'lib/villagenav.php';
require_once 'lib/partner.php';

tlschema('inn');

addcommentary();
$iname = getsetting('innname', LOCATION_INN);
$vname = getsetting('villagename', LOCATION_FIELDS);
$barkeep = getsetting('barkeep', '`tCedrik`0');

$op = httpget('op');
// Correctly reset the location if they fleeing the dragon
// This needs to be done up here because a special could alter your op.
if ('fleedragon' == $op)
{
    $session['user']['location'] = $vname;
}

page_header(sanitize($iname));
$skipinndesc = handle_event('inn');

if (! $skipinndesc)
{
    checkday();
    rawoutput("<span style='color: #9900FF'>");
    output_notl('`c`b');
    output($iname);
    output_notl('`b`c');
}

$subop = httpget('subop');

$com = httpget('comscroll');
$comment = httppost('insertcommentary');

$partner = get_partner();
addnav('Other');
villagenav();
addnav('I?Return to the Inn', 'inn.php');

switch ($op)
{
    case '': case 'strolldown': case 'fleedragon':
        require 'lib/inn/inn_default.php';
        blocknav('inn.php');
    break;
    case 'converse':
        commentdisplay('You stroll over to a table, place your foot up on the bench and listen in on the conversation:`n', 'inn', 'Add to the conversation?', 20);
    break;
    case 'bartender':
        require 'lib/inn/inn_bartender.php';
    break;
    case 'room':
        require 'lib/inn/inn_room.php';
    break;
}

if (! $skipinndesc)
{
    rawoutput('</span>');
}

page_footer();
