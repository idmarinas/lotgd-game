<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Navigation\Pattern;

trait Links
{
    /**
     * List of links.
     *
     * @var array
     */
    protected $links = [];

    /**
     * List of partial links.
     *
     * @var array
     */
    protected $partialLinks = [];

    /**
     * Add a link to navigation menu.
     *
     * @param string $link
     *
     * @return $this
     */
    public function addLink(string $link)
    {
        if (! isset($this->links[$link]))
        {
            $this->links[$link] = ['blocked' => false];
        }

        return $this;
    }

    /**
     * Block a link.
     *
     * @param string $link
     *
     * @return $this
     */
    public function blockLink(string $link)
    {
        if (isset($this->links[$link]))
        {
            $this->links[$link] = array_merge($this->links[$link], ['blocked' => true]);

            return $this;
        }

        $this->links[$link] = ['blocked' => true];

        return $this;
    }

    /**
     * Unblock a link.
     *
     * @param string $link
     *
     * @return $this
     */
    public function unBlockLink(string $link)
    {
        if (isset($this->links[$link]))
        {
            $this->links[$link] = array_merge($this->links[$link], ['blocked' => false]);

            return $this;
        }

        $this->links[$link] = ['blocked' => false];

        return $this;
    }

    /**
     * Block a partial link.
     *
     * @param string $link
     *
     * @return $this
     */
    public function blockPartialLink(string $link)
    {
        if (isset($this->partialLinks[$link]))
        {
            $this->partialLinks[$link] = array_merge($this->partialLinks[$link], ['blocked' => true]);

            return $this;
        }

        $this->partialLinks[$link] = ['blocked' => true];

        return $this;
    }

    /**
     * Unblock a partial link.
     *
     * @param string $link
     *
     * @return $this
     */
    public function unBlockPartialLink(string $link)
    {
        if (isset($this->partialLinks[$link]))
        {
            $this->partialLinks[$link] = array_merge($this->partialLinks[$link], ['blocked' => false]);

            return $this;
        }

        $this->partialLinks[$link] = ['blocked' => false];

        return $this;
    }

    /**
     * Check if link is blocked.
     *
     * @param string $link
     *
     * @return bool
     */
    public function isBlocked(string $link): bool
    {
        if ($this->links[$link]['blocked'] ?? false)
        {
            return true;
        }

        //-- Check if are blocked by partial link
        foreach($this->partialLinks as $partial => $options)
        {
            if (substr($link, 0, strlen($partial)) == $partial && ($options['blocked'] ?? false))
            {
                return true;
            }
        }

        return false;
    }
}
