<?php

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

        $timeout = $session['user']['laston']->getTimestamp() - strtotime(date('Y-m-d H:i:s', strtotime('-'.getsetting('LOGINTIMEOUT', 900).' seconds')));

        if ($timeout <= 1)
        {
            $text = translate_inline('Your session has timed out!');
            $warning = '<b>'.$text.'</b>';
        }
        elseif ($timeout < 120)
        {
            $text = translate_inline('TIMEOUT in %s seconds!');
            $warning = sprintf($text, $timeout);
        }
        else
        {
            return $response;
        }

        $response->dialog->warning($warning);

        return $response;
    }
}
