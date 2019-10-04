<?php

// translator ready
// addnews ready
// mail ready

function get_player_title($old = false)
{
    global $session;
    $title = '';

    if (false === $old)
    {
        $title = $session['user']['title'];

        if ($session['user']['ctitle'])
        {
            $title = $session['user']['ctitle'];
        }
    }
    else
    {
        $title = $old['title'];

        if ($old['ctitle'])
        {
            $title = $old['ctitle'];
        }
    }

    return $title;
}

function get_player_basename($old = false)
{
    global $session;
    $name = '';
    $title = get_player_title($old);

    if (false === $old)
    {
        $name = $session['user']['name'];
    }
    else
    {
        $name = $old['name'];
    }

    if ($title)
    {
        $x = strpos($name, $title);

        if (false !== $x)
        {
            $name = trim(substr($name, $x + strlen($title)));
        }
    }

    return $name;
}

function change_player_name($newname, $old = false)
{
    if ('' == $newname)
    {
        $newname = get_player_basename($old);
    }

    $title = get_player_title($old);

    if ($title)
    {
        $x = strpos($newname, $title);

        if (0 === $x)
        {
            $newname = trim(substr($newname, $x + strlen($title)));
        }
        $newname = $title.' '.$newname;
    }

    return $newname;
}

function change_player_ctitle($nctitle, $old = false)
{
    global $session;

    if ('' == $nctitle)
    {
        if (false == $old)
        {
            $nctitle = $session['user']['title'];
        }
        else
        {
            $nctitle = $old['title'];
        }
    }
    $newname = get_player_basename($old);

    if ($nctitle)
    {
        $newname = $nctitle.' '.$newname;
    }

    return $newname;
}

function change_player_title($ntitle, $old = false)
{
    global $session;

    if (false === $old)
    {
        $ctitle = $session['user']['ctitle'];
    }
    else
    {
        $ctitle = $old['ctitle'];
    }

    $newname = get_player_basename($old);

    if ('' == $ctitle)
    {
        if ('' != $ntitle)
        {
            $newname = $ntitle.' '.$newname;
        }
    }
    else
    {
        $newname = $ctitle.' '.$newname;
    }

    return $newname;
}
