<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Nav;

class Blocked
{
    protected $blockednavs = [
        'blockpartial' => [],
        'blockfull' => [],
        'unblockpartial' => [],
        'unblockfull' => []
    ];

    /**
     * Block given full nav.
     *
     * @param string $link
     *
     * @return $this
     */
    public function blockFullNav(string $link)
    {
        $this->blockednavs['blockfull'][$link] = true;

        //eliminate any unblocked navs that match this description.
        if (isset($this->blockednavs['unblockfull'][$link]))
        {
            unset($this->blockednavs['unblockfull'][$link]);
        }

        return $this;
    }

    /**
     * Block given partial nav.
     *
     * @param string $link
     */
    public function blockPartialNav(string $link)
    {
        $this->blockednavs['blockpartial'][$link] = true;

        //eliminate any unblocked navs that match this description.
        if (isset($this->blockednavs['unblockpartial'][$link]))
        {
            unset($this->blockednavs['unblockpartial'][$link]);
        }

        foreach ($this->blockednavs['unblockpartial'] as $val)
        {
            if (substr($link, 0, strlen($val)) == $val || substr($val, 0, strlen($link)) == $link)
            {
                unset($this->blockednavs['unblockpartial'][$val]);
            }
        }

        return $this;
    }

    /**
     * Unblock given full nav.
     *
     * @param string $link
     *
     * @return $this
     */
    public function unBlockFullNav(string $link)
    {
        $this->blockednavs['unblockfull'][$link] = true;

        //eliminate any unblocked navs that match this description.
        if (isset($this->blockednavs['blockfull'][$link]))
        {
            unset($this->blockednavs['blockfull'][$link]);
        }

        return $this;
    }

    /**
     * Unblock given partial nav.
     *
     * @param string $link
     */
    public function unBlockPartialNav(string $link)
    {
        $this->blockednavs['unblockpartial'][$link] = true;

        //eliminate any unblocked navs that match this description.
        if (isset($this->blockednavs['blockpartial'][$link]))
        {
            unset($this->blockednavs['blockpartial'][$link]);
        }

        foreach ($this->blockednavs['blockpartial'] as $val)
        {
            if (substr($link, 0, strlen($val)) == $val || substr($val, 0, strlen($link)) == $link)
            {
                unset($this->blockednavs['blockpartial'][$val]);
            }
        }

        return $this;
    }

    /**
     * Check if the link is blocked
     *
     * @param string $link
     *
     * @return bool
     */
    public function isBlocked(string $link): bool
    {
        if (isset($this->blockednavs['blockfull'][$link]))
        {
            return true;
        }

        foreach ($this->blockednavs['blockpartial'] as $l => $dummy)
        {
            $shouldblock = false;

            if (substr($link, 0, strlen($l)) == $l)
            {
                if (isset($this->blockednavs['unblockfull'][$link]) && $this->blockednavs['unblockfull'][$link])
                {
                    return false;
                }

                foreach ($this->blockednavs['unblockpartial'] as $l2 => $dummy)
                {
                    if (substr($link, 0, strlen($l2)) == $l2)
                    {
                        return false;
                    }
                }

                return true;
            }
        }

        return false;
    }
}

