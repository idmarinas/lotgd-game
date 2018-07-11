<?php

// addnews ready
// translator ready
// mail ready
require_once 'lib/constants.php';
require_once 'lib/http.php';

// This file encapsulates all the special event handling for most locations

// Returns whether or not the description should be skipped
function handle_event($location, $baseLink = false, $needHeader = false)
{
    global $session, $playermount, $badguy;

    if (false === $baseLink)
    {
        global $PHP_SELF;
        $baseLink = substr($PHP_SELF, strrpos($PHP_SELF, '/') + 1).'?';
    }
    // else
    // {
    // 	debug("Base link was specified as $baseLink");
    // 	debug(debug_backtrace());
    // }
    $skipdesc = false;

    tlschema('events');
    $allowinactive = false;
    $eventhandler = httpget('eventhandler');

    if (($session['user']['superuser'] & SU_DEVELOPER) && '' != $eventhandler)
    {
        $allowinactive = true;
        $array = preg_split('/[:-]/', $eventhandler);

        if ('module' == $array[0])
        {
            $session['user']['specialinc'] = 'module:'.$array[1];
        }
        else
        {
            $session['user']['specialinc'] = '';
        }
    }

    $_POST['i_am_a_hack'] = 'true';

    if (isset($session['user']['specialinc']) && '' != $session['user']['specialinc'])
    {
        $specialinc = $session['user']['specialinc'];
        $session['user']['specialinc'] = '';

        if (false !== $needHeader)
        {
            page_header($needHeader);
        }

        output('`^`c`bSomething Special!`c`b`0');

        if (strchr($specialinc, ':'))
        {
            $array = explode(':', $specialinc);
            $modulename = $array[1];
            $starttime = microtime(true);
            module_do_event($location, $modulename, $allowinactive, $baseLink);
            $endtime = microtime(true);

            if (($endtime - $starttime >= 1.00 && ($session['user']['superuser'] & SU_DEBUG_OUTPUT)))
            {
                debug('Slow Event ('.round($endtime - $starttime, 2)."s): $location - {$modulename}`n");
            }
        }

        if (checknavs())
        {
            // The page rendered some linkage, so we just want to exit.
            page_footer();
        }
        else
        {
            $skipdesc = true;
            $session['user']['specialinc'] = '';
            $session['user']['specialmisc'] = '';
            httpset('op', '');
        }
    }
    tlschema();

    return $skipdesc;
}
