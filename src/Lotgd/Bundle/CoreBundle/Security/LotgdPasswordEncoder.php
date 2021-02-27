<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\CoreBundle\Security;

use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class LotgdPasswordEncoder extends BasePasswordEncoder
{
    /**
     * {@inheritdoc}
     */
    public function encodePassword(string $raw, ?string $salt)
    {
        if ($this->isPasswordTooLong($raw))
        {
            throw new BadCredentialsException('Invalid password.');
        }

        return \md5(\md5($raw));
    }

    /**
     * {@inheritdoc}
     */
    public function isPasswordValid(string $encoded, string $raw, ?string $salt)
    {
        if ($this->isPasswordTooLong($raw))
        {
            throw new BadCredentialsException('Invalid password.');
        }

        return ! $this->isPasswordTooLong($raw) && $this->comparePasswords($encoded, $this->encodePassword($raw, $salt));
    }
}
