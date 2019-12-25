<?php

// addnews ready
// translator ready
// mail ready
require_once 'lib/constants.php';

// This file encapsulates all the special event handling for most locations

// Returns whether or not the description should be skipped
function handle_event($location, $baseLink = false)
{
    global $session, $html, $playermount, $badguy;

    if (false === $baseLink)
    {
        $PHP_SELF = \LotgdHttp::getServer('PHP_SELF');
        $baseLink = substr($PHP_SELF, strrpos($PHP_SELF, '/') + 1).'?';
    }
    $skipdesc = false;

    $allowinactive = false;
    $eventhandler = (string) \LotgdHttp::getQuery('eventhandler');

    if (($session['user']['superuser'] & SU_DEVELOPER) && '' != $eventhandler)
    {
        $allowinactive = true;
        $array = preg_split('/[:-]/', $eventhandler);

        $session['user']['specialinc'] = '';
        if ('module' == $array[0])
        {
            $session['user']['specialinc'] = 'module:'.$array[1];
        }
    }

    $_POST['i_am_a_hack'] = 'true';

    if (isset($session['user']['specialinc']) && '' != $session['user']['specialinc'])
    {
        $specialinc = $session['user']['specialinc'];
        $session['user']['specialinc'] = '';

        $html['event'] = [
            'title.special',
            [],
            'partial-event'
        ];

        page_header('title.special', [], 'partial-event');

        if (strchr($specialinc, ':'))
        {
            $array = explode(':', $specialinc);
            $modulename = $array[1];
            $starttime = microtime(true);
            module_do_event($location, $modulename, $allowinactive, $baseLink);
            $endtime = microtime(true);

            if (($endtime - $starttime) >= 1.00 && ($session['user']['superuser'] & SU_DEBUG_OUTPUT))
            {
                debug('Slow Event ('.round($endtime - $starttime, 2)."s): $location - {$modulename}`n");
            }
        }

        if (\LotgdNavigation::checkNavs())
        {
            // The page rendered some linkage, so we just want to exit.
            return page_footer();
        }

        $skipdesc = true;
        $session['user']['specialinc'] = '';
        $session['user']['specialmisc'] = '';
        \LotgdHttp::setQuery('op', '');
    }

    return $skipdesc;
}
