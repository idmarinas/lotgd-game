<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 *
 * @since 5.5.0
 */

namespace Lotgd\Core\Tool;

use Lotgd\Core\Http\Request;
use Lotgd\Core\Http\Response;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Navigation\Navigation;

class DateTime
{
    private $response;
    private $navigation;
    private $request;
    private $settings;

    public function __construct(Response $response, Navigation $navigation, Request $request, Settings $settings)
    {
        $this->response   = $response;
        $this->navigation = $navigation;
        $this->request    = $request;
        $this->settings   = $settings;
    }

    /**
     * Check if is a new day.
     */
    public function checkDay(): void
    {
        global $session, $revertsession;

        if ($session['user']['loggedin'] ?? false)
        {
            $this->response->pageAddContent('<!--CheckNewDay()-->');

            if ($this->isNewDay())
            {
                $post = $_POST;
                unset($post['i_am_a_hack']);

                if ( ! empty($post))
                {
                    $session['user']['lasthit'] = new \DateTime('0000-00-00 00:00:00');
                }
                else
                {
                    $session                        = $revertsession;
                    $session['user']['restorepage'] = $this->request->getServer('REQUEST_URI');
                    $session['user']['allowednavs'] = [];
                    $this->navigation->addNavAllow('newday.php');

                    redirect('newday.php');
                }
            }
        }
    }

    /**
     * Check if is a new day.
     */
    public function isNewDay(): bool
    {
        global $session;

        if (new \DateTime('0000-00-00 00:00:00') == $session['user']['lasthit'])
        {
            return true;
        }

        $t1 = $this->gameTime();
        $t2 = $this->convertGameTime($session['user']['lasthit']->getTimestamp());
        $d1 = gmdate('Y-m-d', $t1);
        $d2 = gmdate('Y-m-d', $t2);

        return $d1 !== $d2;
    }

    public function getGameTime()
    {
        return gmdate($this->settings->getSetting('gametime', 'g:i a'), $this->gameTime());
    }

    public function gameTime(): int
    {
        return $this->convertGameTime(time());
    }

    public function convertGameTime(int $intime, bool $debug = false)
    {
        //adjust the requested time by the game offset
        $intime -= (int) $this->settings->getSetting('gameoffsetseconds', 0);

        // we know that strtotime gives us an identical timestamp for
        // everywhere in the world at the same time, if it is provided with
        // the GMT offset:
        $epoch          = strtotime($this->settings->getSetting('game_epoch', gmdate('Y-m-d 00:00:00 O', strtotime('-30 days'))));
        $now            = strtotime(gmdate('Y-m-d H:i:s O', $intime));
        $logd_timestamp = round(($now - $epoch) * $this->settings->getSetting('daysperday', 4), 0);

        if ($debug)
        {
            $this->response->pageDebug('Game Timestamp: '.$logd_timestamp.', which makes it '.gmdate('Y-m-d H:i:s', $logd_timestamp));
        }

        return (int) $logd_timestamp;
    }

    public function gameTimeDetails()
    {
        $ret                       = [];
        $ret['now']                = date('Y-m-d 00:00:00');
        $ret['gametime']           = $this->gameTime();
        $ret['daysperday']         = $this->settings->getSetting('daysperday', 4);
        $ret['secsperday']         = 86400 / $ret['daysperday'];
        $ret['today']              = strtotime(gmdate('Y-m-d 00:00:00 O', $ret['gametime']));
        $ret['tomorrow']           = strtotime(gmdate('Y-m-d H:i:s O', $ret['gametime']).' + 1 day');
        $ret['tomorrow']           = strtotime(gmdate('Y-m-d 00:00:00 O', $ret['tomorrow']));
        $ret['secssofartoday']     = $ret['gametime'] - $ret['today'];
        $ret['secstotomorrow']     = $ret['tomorrow'] - $ret['gametime'];
        $ret['realsecssofartoday'] = $ret['secssofartoday']             / $ret['daysperday'];
        $ret['realsecstotomorrow'] = $ret['secstotomorrow']             / $ret['daysperday'];
        $ret['dayduration']        = ($ret['tomorrow'] - $ret['today']) / $ret['daysperday'];

        return $ret;
    }

    public function secondsToNextGameDay(?array $details = null)
    {
        if ( ! $details || empty($details))
        {
            $details = $this->gameTimeDetails();
        }

        return strtotime("{$details['now']} + {$details['realsecstotomorrow']} seconds");
    }
}
