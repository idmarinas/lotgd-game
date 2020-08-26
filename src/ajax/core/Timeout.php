<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Ajax\Core;

use Lotgd\Core\AjaxAbstract;

class Timeout extends AjaxAbstract
{
    public function status()
    {
        global $session;

        $check = $this->checkLoggedIn();

        if (true !== $check)
        {
            return $check;
        }

        $response = new \Jaxon\Response\Response();

        $timeout = $session['user']['laston']->getTimestamp() - \strtotime(\date('Y-m-d H:i:s', \strtotime('-'.getsetting('LOGINTIMEOUT', 900).' seconds')));

        if ($timeout <= 1)
        {
            $text    = \LotgdTranslator::t('session.timeout', [], 'app-default');
            $warning = '<b>'.$text.'</b>';
        }
        elseif ($timeout < 120)
        {
            $text    = \LotgdTranslator::t('session.time_out_in', ['n' => $timeout], 'app-default');
            $warning = \sprintf($text, $timeout);
        }
        else
        {
            return $response;
        }

        $response->dialog->warning($warning);

        return $response;
    }
}
