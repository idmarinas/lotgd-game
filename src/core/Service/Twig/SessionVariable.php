<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.1.0
 */

namespace Lotgd\Core\Service\Twig;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class SessionVariable
{
    private $access;

    public function __call($method, $args)
    {
        return $this->access()->getValue($this->getSession(), "[$method]");
    }

    /**
     * Returns the current session.
     */
    public function getSession()
    {
        global $session;

        $sesion = $session ?? [];
        unset($sesion['user']);

        return $sesion;
    }

    private function access(): PropertyAccessor
    {
        if (! $this->access instanceof PropertyAccessor)
        {
            $this->access = PropertyAccess::createPropertyAccessor();
        }

        return $this->access;
    }
}
