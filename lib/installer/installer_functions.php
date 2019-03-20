<?php

//This function is borrowed from the php manual.
function return_bytes($val)
{
    $val = trim($val);
    $last = $val[strlen($val) - 1];
    $val = (int) str_replace($last, '', $val);

    switch (strtolower($last))
    {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}
