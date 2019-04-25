<?php

// translator ready
// addnews ready
// mail ready

function sanitize($in)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use new "LotgdSanitize::fullSanitize($string) instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdSanitize::fullSanitize($in);
}

function newline_sanitize($in)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use new "LotgdSanitize::newLineSanitize($string) instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdSanitize::newLineSanitize($in);
}

function color_sanitize($in)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use new "LotgdSanitize::fullSanitize($string) instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdSanitize::fullSanitize($in);
}

function comment_sanitize($in)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, has no replacement, new commentary system, sanitize comments by default.',
        __METHOD__
    ), E_USER_DEPRECATED);

    global $output;
    // to keep the regexp from boinging this, we need to make sure
    // that we're not replacing in with the ` mark.
    //no italic, nor centered, nor newlines allowed here
    $out = preg_replace('/[`](?=[^0'.$output->get_colormap_escaped().'])/', chr(1).chr(1), $in);
    $out = str_replace(chr(1), '`', $out);

    return $out;
}

function logdnet_sanitize($in)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use new "LotgdSanitize::logdnetSanitize($string) instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdSanitize::logdnetSanitize($in);
}

function full_sanitize($in)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use new "LotgdSanitize::fullSanitize($string) instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdSanitize::fullSanitize($in);
}

function cmd_sanitize($in)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use new "LotgdSanitize::cmdSanitize($string) instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdSanitize::cmdSanitize($in);
}

function comscroll_sanitize($in)
{
    trigger_error(sprintf(
        'The use of %s is obsolete since version 4.0.0; and remove it in version 4.1.0, has no replacement.',
        __METHOD__
    ), E_USER_DEPRECATED);

    $out = preg_replace("'&c(omscroll)?=([[:digit:]]|-)*'", '', $in);
    $out = preg_replace("'\\?c(omscroll)?=([[:digit:]]|-)*'", '?', $out);
    $out = preg_replace("'&(refresh|comment)=1'", '', $out);
    $out = preg_replace("'\\?(refresh|comment)=1'", '?', $out);

    return $out;
}

function prevent_colors($in)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use new "LotgdSanitize::preventLotgdCodes($string) instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdSanitize::preventLotgdCodes($in);
}

function translator_uri($in)
{
    trigger_error(sprintf(
        'The use of %s is obsolete since version 4.0.0; and remove it in version 4.1.0, has no replacement.',
        __METHOD__
    ), E_USER_DEPRECATED);

    $uri = comscroll_sanitize($in);
    $uri = cmd_sanitize($uri);

    if ('?' == substr($uri, -1))
    {
        $uri = substr($uri, 0, -1);
    }

    return $uri;
}

function translator_page($in)
{
    trigger_error(sprintf(
        'The use of %s is obsolete since version 4.0.0; and remove it in version 4.1.0, has no replacement.',
        __METHOD__
    ), E_USER_DEPRECATED);

    $page = $in;

    if (false !== strpos($page, '?'))
    {
        $page = substr($page, 0, strpos($page, '?'));
    }
    //if ($page=="runmodule.php" && 0){
    //	//we should handle this in runmodule.php now that we have tlschema.
    //	$matches = array();
    //	preg_match("/[&?](module=[^&]*)/i",$in,$matches);
    //	if (isset($matches[1])) $page.="?".$matches[1];
    //}
    return $page;
}

function modulename_sanitize($in)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use new "LotgdSanitize::moduleNameSanitize($string) instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdSanitize::moduleNameSanitize($in);
}

// the following function borrowed from mike-php at emerge2 dot com's post
// to php.net documentation.
//Original post is available here: http://us3.php.net/stripslashes
function stripslashes_array($given)
{
    trigger_error(sprintf(
        'The use of %s is obsolete since version 4.0.0; and remove it in version 4.1.0, has no replacement.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return is_array($given) ?
       array_map('stripslashes_array', $given) : stripslashes($given);
}

// Handle spaces in character names
function sanitize_name($spaceallowed, $inname)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use new "LotgdSanitize::nameSanitize($spaceallowed, $inname) instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdSanitize::nameSanitize($spaceallowed, $inname);
}

// Handle spaces and color in character names
function sanitize_colorname($spaceallowed, $inname, $admin = false)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use new "LotgdSanitize::colorNameSanitize($spaceallowed, $inname, $admin) instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdSanitize::colorNameSanitize($spaceallowed, $inname, $admin);
}

// Strip out <script>...</script> blocks and other HTML tags to try and
// detect if we have any actual output.  Used by the collapse code to try
// and make sure we don't add spurious collapse boxes.
// Also used by the rename code to remove HTML that some admins try to
// insert.. Bah
function sanitize_html($str)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use new "LotgdSanitize::htmlSanitize($string) instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdSanitize::htmlSanitize($str);
}

function sanitize_mb($str)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use new "LotgdSanitize::mbSanitize($string) instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdSanitize::mbSanitize($str);
}
