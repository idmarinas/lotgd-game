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

namespace Lotgd\Core\Fixed;

use Lotgd\Core\Http as CoreHttp;

class Http
{
    /**
     * Instance of Http.
     *
     * @var Lotgd\Core\Http
     */
    protected static $instance;

    /**
     * Cookies added in request.
     *
     * @var Zend\Http\Header\Cookie
     */
    protected static $cookies;

    /**
     * Return single get parameter.
     *
     * @param string      $name
     * @param string|null $default
     *
     * @return mixed
     */
    public static function getQuery(string $name, ?string $default = null)
    {
        return self::$instance->getQuery($name, $default);
    }

    /**
     * Return all get parameters.
     *
     * @return array
     */
    public static function getAllQuery(): array
    {
        return self::$instance->getQuery()->toArray();
    }

    /**
     * Set single get parameter.
     *
     * @param string $name
     * @param mixed  $value
     * @param bool   $force
     */
    public static function setQuery(string $name, $value, ?bool $force = null)
    {
        $get = self::$instance->getQuery();

        if ($get->offsetExists($name) || $force)
        {
            $get->set($name, $value);
        }
    }

    /**
     * Return single post parameter.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function getPost($name, $default = null)
    {
        return self::$instance->getPost($name, $default);
    }

    /**
     * Check if "name" are in post data.
     *
     * @param string $name
     *
     * @return bool
     */
    public static function existInPost($name)
    {
        return self::$instance->getPost()->offsetExists($name);
    }

    /**
     * Set single post parameter.
     *
     * @param string $var
     * @param mixed  $val
     * @param bool   $sub
     */
    public static function setPost($var, $val, ?bool $sub = null)
    {
        $post = self::$instance->getPost()->toArray();

        if ((null === $sub || false === $sub) && isset($post[$var]))
        {
            $post[$var] = $val;
        }
        elseif (isset($post[$var]) && isset($post[$var][$sub]))
        {
            $post[$var][$sub] = $val;
        }

        self::$instance->setPost($post);
    }

    /**
     * Get all post data.
     *
     * @return array
     */
    public static function getPostAll(): array
    {
        return self::$instance->getPost()->toArray();
    }

    /**
     * Return the parameter container responsible for server parameters or a single parameter value.
     *
     * @param string|null $name
     * @param string|null $default
     *
     * @return string
     */
    public static function getServer($name = null, $default = null)
    {
        return self::$instance->getServer($name, $default);
    }

    /**
     * Get a value of a cookie.
     *
     * @param mixed $name
     *
     * @return mixed|null
     */
    public static function getCookie($name)
    {
        $cookie = self::$instance->getCookie();

        if ($cookie->offsetExists($name))
        {
            return $cookie->offsetGet($name);
        }

        return null;
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
    public static function setCookie($name, $value, $duration = '+120 days', $path = '', $domain = '', $secure = true, $httponly = true)
    {
        setcookie($name, $value, strtotime($duration), $path, $domain, $secure, $httponly);

        if (! self::$cookies instanceof Cookie)
        {
            self::$cookies = self::$instance->getCookie();
        }

        self::$cookies->offsetSet($name, $value);

        self::$instance->getHeaders()->addHeader(self::$cookies);
    }

    /**
     * Set instance of Navigation.
     *
     * @param CoreHttp $container
     */
    public static function instance(CoreHttp $instance)
    {
        self::$instance = $instance;
    }
}

class_alias('Lotgd\Core\Fixed\Http', 'LotgdHttp', false);
