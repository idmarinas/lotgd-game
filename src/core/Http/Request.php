<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.4.0
 */

namespace Lotgd\Core\Http;

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
