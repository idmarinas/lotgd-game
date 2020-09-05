<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.4.0
 */

namespace Lotgd\Core\Http;

use Laminas\Http\Header\Cookie;
use Laminas\Http\PhpEnvironment\Request as HttpRequest;

class Request extends HttpRequest
{
    /**
     * {@inheritdoc}
     */
    public function getServer($name = null, $default = null)
    {
        if ('REQUEST_URI' == $name || 'SCRIPT_NAME' == $name)
        {
            return $this->sanitizeUri(parent::getServer($name, $default));
        }

        return parent::getServer($name, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getCookie()
    {
        $cookie = parent::getCookie();

        return $cookie ?: new Cookie();
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestUri(): string
    {
        return $this->sanitizeUri(parent::getRequestUri());
    }

    /**
     * Sanitize uri for usage.
     *
     * @param string $name
     */
    protected function sanitizeUri($name): string
    {
        return substr($name, strrpos($name, '/') + 1);
    }
}
