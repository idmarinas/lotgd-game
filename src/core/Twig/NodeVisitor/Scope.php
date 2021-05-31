<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Twig\NodeVisitor;

class Scope
{
    private $parent;
    private $data = [];
    private $left = false;

    public function __construct(?self $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Opens a new child scope.
     *
     * @return self
     */
    public function enter()
    {
        return new self($this);
    }

    /**
     * Closes current scope and returns parent one.
     *
     * @return self|null
     */
    public function leave()
    {
        $this->left = true;

        return $this->parent;
    }

    /**
     * Stores data into current scope.
     *
     * @scope string $key
     * @scope mixed  $value
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @throws \LogicException
     *
     * @return $this
     */
    public function set($key, $value)
    {
        if ($this->left)
        {
            throw new \LogicException('Left scope.');
        }

        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Tests if a data is visible from current scope.
     *
     * @scope string $key
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function has($key)
    {
        if (\array_key_exists($key, $this->data))
        {
            return true;
        }

        if (null === $this->parent)
        {
            return false;
        }

        return $this->parent->has($key);
    }

    /**
     * Returns data visible from current scope.
     *
     * @scope string $key
     * @scope mixed  $default
     *
     * @param mixed      $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (\array_key_exists($key, $this->data))
        {
            return $this->data[$key];
        }

        if (null === $this->parent)
        {
            return $default;
        }

        return $this->parent->get($key, $default);
    }
}
