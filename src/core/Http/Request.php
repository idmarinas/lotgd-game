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

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class Request extends HttpRequest
{
    /**
     * {@inheritdoc}
     */
    public function getServer($name = null, $default = null)
    {
        if ('REQUEST_URI' == $name || 'SCRIPT_NAME' == $name)
        {
            return $this->sanitizeUri($this->server->get($name, $default));
        }

        return $this->server->get($name, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getCookie($name, $default = null)
    {
        return $this->cookies->get($name, $default);
    }

    /**
     * Send a cookie.
     *
     * @param string $name
     * @param string $value
     * @param string $duration
     * @param string $path
     * @param string $domain
     * @param bool   $secure
     * @param bool   $httponly
     */
    public function setCookie($name, $value, $duration = '+120 days', $path = '', $domain = '', $secure = true, $httponly = true)
    {
        $this->cookies->set($name, $value);
        \LotgdResponse::_i()->headers->setCookie(Cookie::create($name, $value, \strtotime($duration), $path, $domain, $secure, $httponly));
    }

    /**
     * Return all get parameters.
     */
    public function getQueryAll(): array
    {
        return $this->query->all();
    }

    /**
     * Return a parameter.
     *
     * @param mixed      $name
     * @param mixed|null $default
     */
    public function getQuery($name, $default = null)
    {
        return $this->query->get($name, $default);
    }

    /**
     * Set single get parameter.
     *
     * @param mixed $value
     * @param bool  $force
     */
    public function setQuery(string $name, $value)
    {
        $this->query->set($name, $value);
    }

    /**
     * Check if "name" are in post data.
     *
     * @param string $name
     *
     * @return bool
     */
    public function existInPost($name)
    {
        return $this->request->has($name);
    }

    /**
     * Set single post parameter.
     *
     * @param string $name
     * @param mixed  $val
     */
    public function setPost($name, $val)
    {
        $this->request->set($name, $val);
    }

    /**
     * Get single post parameter.
     *
     * @param string $name
     * @param mixed  $default
     */
    public function getPost($name, $default = null)
    {
        return $this->request->get($name, $default);
    }

    /**
     * Get all post data.
     */
    public function getPostAll()
    {
        return $this->request->all();
    }

    /**
     * Check if is a post request.
     *
     * @return bool
     */
    public function isPost()
    {
        return $this->isMethod('post');
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
        return \substr($name, \strrpos($name, '/') + 1);
    }
}
