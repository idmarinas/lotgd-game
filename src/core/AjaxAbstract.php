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

namespace Lotgd\Core;

use Jaxon\Response\Response;

/**
 * Use this class to add a basic function to your Jaxon request.
 * Check if user have a session
 */
abstract class AjaxAbstract
{
    /**
     * Return true or empty Jaxon\Response\Response.
     *
     * @return true|Response
     */
    public function checkLoggedIn()
    {
        global $session;

        //-- Do nothing if there is no active session
        if (! ($session['user']['loggedin'] ?? false))
        {
            return new Response();
        }

        return true;
    }

    /**
     * Return true or redirect to home.php page.
     *
     * @return true|Redirect
     */
    public function checkLoggedInRedirect()
    {
        global $session;

        //-- Do nothing if there is no active session
        if (! ($session['user']['loggedin'] ?? false))
        {
            $response = new Response();

            return $response->redirect('home.php');
        }

        return true;
    }
}

