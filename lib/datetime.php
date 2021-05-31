<?php

// addnews ready
// translator ready
// mail ready

/**
 * Check if is a new day.
 */
function checkday()
{
    global $session, $revertsession;

    if ($session['user']['loggedin'] ?? false)
    {
        \LotgdResponse::pageAddContent('<!--CheckNewDay()-->');

        if (is_new_day())
        {
            $post = $_POST;
            unset($post['i_am_a_hack']);

            if (\count($post) > 0)
            {
                $session['user']['lasthit'] = new \DateTime('0000-00-00 00:00:00');

                return;
            }
            else
            {
                $session                        = $revertsession;
                $session['user']['restorepage'] = \LotgdRequest::getServer('REQUEST_URI');
                $session['user']['allowednavs'] = [];
                \LotgdNavigation::addNavAllow('newday.php');

                redirect('newday.php');
            }
        }
    }
}

/**
 * @param int $now
 *
 * @return bool
 */
function is_new_day($now = 0)
{
    global $session;

    if (new \DateTime('0000-00-00 00:00:00') == $session['user']['lasthit'])
    {
        return true;
    }

    $t1 = gametime();
    $t2 = convertgametime($session['user']['lasthit']->getTimestamp());
    $d1 = \gmdate('Y-m-d', $t1);
    $d2 = \gmdate('Y-m-d', $t2);

    return (bool) ($d1 != $d2);
}

function getgametime()
{
    return \gmdate(getsetting('gametime', 'g:i a'), gametime());
}

/**
 * Undocumented function.
 *
 * @return int
 */
function gametime()
{
    return convertgametime(\time());
}

function convertgametime(int $intime, $debug = false)
{
    //adjust the requested time by the game offset
    $intime -= (int) getsetting('gameoffsetseconds', 0);

    // we know that strtotime gives us an identical timestamp for
    // everywhere in the world at the same time, if it is provided with
    // the GMT offset:
    $epoch          = \strtotime(getsetting('game_epoch', \gmdate('Y-m-d 00:00:00 O', \strtotime('-30 days'))));
    $now            = \strtotime(\gmdate('Y-m-d H:i:s O', $intime));
    $logd_timestamp = \round(($now - $epoch) * getsetting('daysperday', 4), 0);

    if ($debug)
    {
        \LotgdResponse::pageDebug('Game Timestamp: '.$logd_timestamp.', which makes it '.\gmdate('Y-m-d H:i:s', $logd_timestamp));
    }

    return (int) $logd_timestamp;
}

function gametimedetails()
{
    $ret                       = [];
    $ret['now']                = \date('Y-m-d 00:00:00');
    $ret['gametime']           = gametime();
    $ret['daysperday']         = getsetting('daysperday', 4);
    $ret['secsperday']         = 86400 / $ret['daysperday'];
    $ret['today']              = \strtotime(\gmdate('Y-m-d 00:00:00 O', $ret['gametime']));
    $ret['tomorrow']           = \strtotime(\gmdate('Y-m-d H:i:s O', $ret['gametime']).' + 1 day');
    $ret['tomorrow']           = \strtotime(\gmdate('Y-m-d 00:00:00 O', $ret['tomorrow']));
    $ret['secssofartoday']     = $ret['gametime'] - $ret['today'];
    $ret['secstotomorrow']     = $ret['tomorrow'] - $ret['gametime'];
    $ret['realsecssofartoday'] = $ret['secssofartoday']             / $ret['daysperday'];
    $ret['realsecstotomorrow'] = $ret['secstotomorrow']             / $ret['daysperday'];
    $ret['dayduration']        = ($ret['tomorrow'] - $ret['today']) / $ret['daysperday'];

    return $ret;
}

function secondstonextgameday($details = false)
{
    if (false === $details)
    {
        $details = gametimedetails();
    }

    return \strtotime("{$details['now']} + {$details['realsecstotomorrow']} seconds");
}
