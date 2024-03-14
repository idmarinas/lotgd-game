<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core;

use function Jaxon\jaxon;

/**
 * Use this class to add a basic function to your Jaxon request.
 * Check if user have a session.
 */
abstract class AjaxAbstract
{
    /**
     * Return true or empty Jaxon\Response\Response.
     *
     * @return \Jaxon\Response\ResponseInterface|true
     */
    protected function checkLoggedIn()
    {
        global $session;

        //-- Do nothing if there is no active session
        if ( ! ($session['user']['loggedin'] ?? false))
        {
            return jaxon()->getResponse();
        }

        return true;
    }

    /**
     * Return true or redirect to home.php page.
     *
     * @return \Jaxon\Response\ResponseInterface|true
     */
    protected function checkLoggedInRedirect()
    {
        global $session;

        //-- Do nothing if there is no active session
        if ( ! ($session['user']['loggedin'] ?? false))
        {
            $response = jaxon()->getResponse();

            return $response->redirect('home.php');
        }

        return true;
    }
}
