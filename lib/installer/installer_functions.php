<?php

$tipid = 0;
function tip()
{
    global $tipid;

    $tip = translate_inline('Tip');
    output_notl("<div style='cursor: pointer; cursor: hand; display: inline;' onMouseOver=\"tip$tipid.style.visibility='visible'; tip$tipid.style.display='inline';\" onMouseOut=\"tip$tipid.style.visibility='hidden'; tip$tipid.style.display='none';\">`i[ `b{$tip}´b ]´i", true);
    rawoutput("<div class='debug' id='tip$tipid' style='position: absolute; width: 200px; max-width: 200px; float: right;'>");
    $args = func_get_args();
    call_user_func_array('output', $args);
    rawoutput('</div></div>');
    rawoutput("<script language='JavaScript'>var tip$tipid = document.getElementById('tip$tipid'); tip$tipid.style.visibility='hidden'; tip$tipid.style.display='none';</script>");
    $tipid++;
}

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
