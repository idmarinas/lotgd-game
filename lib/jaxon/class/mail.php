<?php

namespace Lotgd\Ajax;

class Mail
{
    public function status()
    {
        global $session;

        $response = new \Jaxon\Response\Response();

        //-- Do nothing if there is no active session
        if (! $session['user']['loggedin'])
        {
            return $response;
        }

        $response->html('maillink', maillink());

        return $response;
    }
}
