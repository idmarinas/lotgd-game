<?php

// addnews ready
// translator ready
// mail ready

function reltime($date, $short = true)
{
    $x = abs(time() - $date);
    $d = (int) ($x / 86400);
    $x = $x % 86400;
    $h = (int) ($x / 3600);
    $x = $x % 3600;
    $m = (int) ($x / 60);
    $x = $x % 60;
    $s = (int) ($x);

    if ($short)
    {
        $array = ['d' => 'd', 'h' => 'h', 'm' => 'm', 's' => 's'];
        $array = translate_inline($array, 'datetime');

        if ($d > 0)
        {
            $o = $d.$array['d'].' '.($h > 0 ? $h.$array['h'] : '');
        }
        elseif ($h > 0)
        {
            $o = $h.$array['h'].' '.($m > 0 ? $m.$array['m'] : '');
        }
        elseif ($m > 0)
        {
            $o = $m.$array['m'].' '.($s > 0 ? $s.$array['s'] : '');
        }
        else
        {
            $o = $s.$array['s'];
        }
    }
    else
    {
        $array = ['day' => 'day', 'days' => 'days', 'hour' => 'hour', 'hours' => 'hours', 'minute' => 'minute', 'minutes' => 'minutes', 'second' => 'second', 'seconds' => 'second'];
        $array = translate_inline($array, 'datetime'); //translate it... tl-ready now

        if ($d > 0)
        {
            $o = "$d ".($d > 1 ? $array['days'] : $array['day']).($h > 0 ? ", $h ".($h > 1 ? $array['hours'] : $array['hour']) : '');
        }
        elseif ($h > 0)
        {
            $o = "$h ".($h > 1 ? $array['hours'] : $array['hour']).($m > 0 ? ", $m ".($m > 1 ? $array['minutes'] : $array['minute']) : '');
        }
        elseif ($m > 0)
        {
            $o = "$m ".($m > 1 ? $array['minutes'] : $array['minute']).($s > 0 ? ", $s ".($s > 1 ? $array['seconds'] : $array['second']) : '');
        }
        else
        {
            $o = "$s ".($s > 0 ? $array['seconds'] : $array['second']);
        }
    }

    return $o;
}

/**
 * Check if is a new day.
 */
function checkday()
{
    global $session, $revertsession;

    if ($session['user']['loggedin'] ?? false)
    {
        rawoutput('<!--CheckNewDay()-->');

        if (is_new_day())
        {
            $post = $_POST;
            unset($post['i_am_a_hack']);

            if (count($post) > 0)
            {
                $session['user']['lasthit'] = new \DateTime('0000-00-00 00:00:00');

                return;
            }
            else
            {
                $request = \LotgdLocator::get(\Lotgd\Core\Http::class);

                $session = $revertsession;
                $session['user']['restorepage'] = $request->getServer('REQUEST_URI');
                $session['user']['allowednavs'] = [];
                addnav('', 'newday.php');

                return redirect('newday.php');
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
    $d1 = gmdate('Y-m-d', $t1);
    $d2 = gmdate('Y-m-d', $t2);

    if ($d1 != $d2)
    {
        return true;
    }

    return false;
}

function getgametime()
{
    return gmdate(getsetting('gametime', 'g:i a'), gametime());
}

/**
 * Undocumented function.
 *
 * @return int
 */
function gametime()
{
    $time = convertgametime(time());

    return $time;
}

function convertgametime(int $intime, $debug = false)
{
    //adjust the requested time by the game offset
    $intime -= (int) getsetting('gameoffsetseconds', 0);

    // we know that strtotime gives us an identical timestamp for
    // everywhere in the world at the same time, if it is provided with
    // the GMT offset:
    $epoch = strtotime(getsetting('game_epoch', gmdate('Y-m-d 00:00:00 O', strtotime('-30 days'))));
    $now = strtotime(gmdate('Y-m-d H:i:s O', $intime));
    $logd_timestamp = round(($now - $epoch) * getsetting('daysperday', 4), 0);

    if ($debug)
    {
        echo 'Game Timestamp: '.$logd_timestamp.', which makes it '.gmdate('Y-m-d H:i:s', $logd_timestamp).'<br>';
    }

    return (int) $logd_timestamp;
}

function gametimedetails()
{
    $ret = [];
    $ret['now'] = date('Y-m-d 00:00:00');
    $ret['gametime'] = gametime();
    $ret['daysperday'] = getsetting('daysperday', 4);
    $ret['secsperday'] = 86400 / $ret['daysperday'];
    $ret['today'] = strtotime(gmdate('Y-m-d 00:00:00 O', $ret['gametime']));
    $ret['tomorrow'] = strtotime(gmdate('Y-m-d H:i:s O', $ret['gametime']).' + 1 day');
    $ret['tomorrow'] = strtotime(gmdate('Y-m-d 00:00:00 O', $ret['tomorrow']));
    $ret['secssofartoday'] = $ret['gametime'] - $ret['today'];
    $ret['secstotomorrow'] = $ret['tomorrow'] - $ret['gametime'];
    $ret['realsecssofartoday'] = $ret['secssofartoday'] / $ret['daysperday'];
    $ret['realsecstotomorrow'] = $ret['secstotomorrow'] / $ret['daysperday'];
    $ret['dayduration'] = ($ret['tomorrow'] - $ret['today']) / $ret['daysperday'];

    return $ret;
}

function secondstonextgameday($details = false)
{
    if (false === $details)
    {
        $details = gametimedetails();
    }

    return strtotime("{$details['now']} + {$details['realsecstotomorrow']} seconds");
}
