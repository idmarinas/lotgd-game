<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core;

use Zend\Http\PhpEnvironment\Request;

class Http extends Request
{
    /**
     * @inheritDoc
     */
    public function getServer($name = null, $default = null)
    {
        if ('REQUEST_URI' == $name)
        {
            return $this->sanitizeUri(parent::getServer($name, $default));
        }
        elseif ('SCRIPT_NAME' == $name)
        {
            return $this->sanitizeUri(parent::getServer($name, $default));
        }

        return parent::getServer($name, $default);
    }

    /**
     * @inheritDoc
     */
    public function getRequestUri(): string
    {
        return $this->sanitizeUri(parent::getRequestUri());
    }

    /**
     * Sanitize uri for usage
     *
     * @return string
     */
    protected function sanitizeUri($name): string
    {
        return substr($name, strrpos($name, '/') + 1);
    }
}
