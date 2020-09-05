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

namespace Lotgd\Core\Fixed;

use Lotgd\Core\Http\Request as CoreRequest;

class Request
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
     * @var Laminas\Http\Header\Cookie
     */
    protected static $cookies;

    /**
     * Add support for magic static method calls.
     *
     * @param string $name
     * @param array  $arguments
     * @param mixed  $method
     *
     * @return mixed the returned value from the resolved method
     */
    public static function __callStatic($method, $arguments)
    {
        if (\method_exists(self::$instance, $method))
        {
            return self::$instance->{$method}(...$arguments);
        }

        $methods = implode(', ', get_class_methods(self::$instance));

        throw new \BadMethodCallException("Undefined method '{$method}'. The method name must be one of '{$methods}'");
    }

    /**
     * Return all get parameters.
     */
    public static function getQueryAll(): array
    {
        return self::$instance->getQuery()->toArray();
    }

    /**
     * Set single get parameter.
     *
     * @param mixed $value
     * @param bool  $force
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
        elseif (isset($post[$var], $post[$var][$sub]))
        {
            $post[$var][$sub] = $val;
        }

        self::$instance->setPost(new \Laminas\Stdlib\Parameters($post));
    }

    /**
     * Get all post data.
     */
    public static function getPostAll(): array
    {
        return self::$instance->getPost()->toArray();
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

        if ( ! self::$cookies instanceof Cookie)
        {
            self::$cookies = self::$instance->getCookie();
        }

        self::$cookies->offsetSet($name, $value);

        self::$instance->getHeaders()->addHeader(self::$cookies);
    }

    /**
     * Set instance of Navigation.
     *
     * @param CoreRequest $container
     */
    public static function instance(CoreRequest $instance)
    {
        self::$instance = $instance;
    }
}

class_alias('Lotgd\Core\Fixed\Http', 'LotgdRequest', false);
