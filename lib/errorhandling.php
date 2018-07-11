<?php

// addnews ready
// translator ready
// mail ready

function set_magic_quotes(&$vars)
{
    if (is_array($vars))
    {
        reset($vars);

        while (list($key, $val) = each($vars))
        {
            set_magic_quotes($vars[$key]);
        }
    }
    else
    {
        $vars = addslashes($vars);
    }
}

//do some cleanup here to make sure magic_quotes_gpc is ON
if (! get_magic_quotes_gpc())
{
    set_magic_quotes($_GET);
    set_magic_quotes($_POST);
    // set_magic_quotes($_SESSION); //-- This may corrupted any data and emit a error "Allowed memory size of XXX bytes exhausted"
    set_magic_quotes($_COOKIE);
    ini_set('magic_quotes_gpc', 1);
}
