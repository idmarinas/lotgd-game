<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Tool\Tool;

trait Name
{
    public function getPlayerTitle($old = null): string
    {
        global $session;

        if ( ! $old)
        {
            return (string) ($session['user']['ctitle'] ? $session['user']['ctitle'] : $session['user']['title']);
        }

        return (string) ($old['ctitle'] ? $old['ctitle'] : $old['title']);
    }

    public function getPlayerBasename($old = null): string
    {
        global $session;

        return (string) ($old) !== '' && (string) ($old) !== '0' ? $old['playername'] : $session['user']['playername'];
    }

    public function getPlayerCBasename($old = null): string
    {
        global $session;

        $name  = $session['user']['name'];
        $title = $this->getPlayerTitle($old);

        if ( ! empty($old))
        {
            $name = $old['name'];
        }

        if ($title)
        {
            $x = strpos($name, (string) $title);

            if (false !== $x)
            {
                $name = trim(substr($name, $x + \strlen($title)));
            }
        }

        return $name;
    }

    public function changePlayerName($newname, $old = null): string
    {
        if (empty($newname))
        {
            $newname = $this->getPlayerBasename($old);
        }

        $title = $this->getPlayerTitle($old);

        if ($title)
        {
            $x = strpos($newname, (string) $title);

            if (0 === $x)
            {
                $newname = trim(substr($newname, $x + \strlen($title)));
            }

            $newname = $title.' '.$newname;
        }

        return (string) $newname;
    }

    public function changePlayerCtitle($nctitle, $old = false): string
    {
        global $session;

        if (empty($nctitle))
        {
            $nctitle = $session['user']['title'];

            if ( ! empty($old))
            {
                $nctitle = $old['title'];
            }
        }

        $newname = $this->getPlayerBasename($old);

        if ($nctitle)
        {
            $newname = $nctitle.' '.$newname;
        }

        return $newname;
    }

    public function changePlayerTitle($ntitle, $old = null): string
    {
        global $session;

        $ctitle = $session['user']['ctitle'];

        if ( ! empty($old))
        {
            $ctitle = $old['ctitle'];
        }

        $newname = $this->getPlayerBasename($old);

        $title = $ctitle;

        if (empty($ctitle) && ! empty($ntitle))
        {
            $title = $ntitle;
        }

        return $title.' '.$newname;
    }
}
