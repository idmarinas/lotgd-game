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

class UserVariable
{
    private $access;

    public function __call($method, $args)
    {
        return $this->access()->getValue($this->getUser(), "[$method]");
    }

    /**
     * Returns the current user.
     */
    public function getUser(): array
    {
        global $session;

        $user = $session['user'] ?? [];
        unset($user['password']);

        return $user;
    }

    public function __toArray(): array
    {
        return $this->getUser();
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
