<?php

/**
 * Called to block the display of a nav
 * if $partial is true, it will block any nav that begins with the given $link.
 * if $partial is false, it will block only navs that have exactly the given $link.
 *
 * @param string $link    The URL to block
 * @param bool   $partial
 */
function blocknav(string $link, bool $partial = false)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use LotgdNavigation::blockLink() or LotgdNavigation::blockPartialLink() instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    if ($partial)
    {
        return \LotgdNavigation::blockPartialLink($link);
    }

    return \LotgdNavigation::blockLink($link);
}

/**
 * Unlocks a nav from the blocked navs Array
 * if $partial is true, it will block any nav that begins with the given $link.
 * if $partial is false, it will block only navs that have exactly the given $link.
 *
 * @param string $link    The nav to unblock
 * @param bool   $partial If the passed nav is partial or not
 */
function unblocknav($link, $partial = false)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use LotgdNavigation::unBlockLink() or LotgdNavigation::unBlockPartialLink() instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    if ($partial)
    {
        return \LotgdNavigation::unBlockPartialLink($link);
    }

    return \LotgdNavigation::unBlockLink($link);
}

function appendcount($link)
{
    global $session;

    return appendlink($link, 'c='.$session['counter'].'-'.date('His'));
}

function appendlink($link, $new)
{
    if (false !== strpos($link, '?'))
    {
        return $link.'&'.$new;
    }

    return $link.'?'.$new;
}

$navtldomain = 'app';
$navsection = '';
$navbysection = [];
$navschema = [];
$navnocollapse = [];
$block_new_navs = false;

/**
 * Allow header/footer code to block/unblock additional navs.
 *
 * @param bool $block should new navs be blocked
 */
function set_block_new_navs($block)
{
    global $block_new_navs;

    $block_new_navs = $block;
}

/**
 * Generate and/or store a nav banner for the player.
 *
 * @param string   $text     the display string for the nav banner
 * @param collapse $collapse (default true) can the nav section collapse
 */
function addnavheader($text, $collapse = true, $translate = true)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use LotgdNavigation::addHeader() instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdNavigation::addHeader($text, ['translate' => $translate]);
}

/**
 * Generate and/or store the allowed navs or nav banners for the player.
 * If $link is missing - then a banner will be displayed in the nav list
 * If $text is missing - the nav will be stored in the allowed navs for the player but not displayed
 * <B>ALL</B> internal site links that are displayed <B>MUST</B> also call addnav or badnav will occur.
 *
 * @param string $text    (optional) The display string for the nav or nav banner
 * @param string $link    (optional) The URL of the link
 * @param bool   $priv    Indicates if the name contains HTML
 * @param bool   $pop     Indicates if the URL should generate a popup
 * @param string $popsize If a popup - the size of the popup window
 *
 * @see badnav, apponencode
 */
function addnav_notl($text, $link = false, $priv = false, $pop = false, $popsize = '500x300')
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use LotgdNavigation::addNavNotl() instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdNavigation::addNavNotl($text, $link);
}

function addnav($text, $link = false, $priv = false, $pop = false, $popsize = '500x300', $namespace = 'navigation-app')
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use LotgdNavigation::addNav() instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    \LotgdNavigation::addNav($text, $link);
}

/**
 * Determine if a nav/URL is blocked.
 *
 * @param string $link The nav to check
 *
 * @return bool
 */
function is_blocked(string $link): bool
{
    return \LotgdNavigation::isBlocked($link);
}

/**
 * Determine how many navs are available.
 *
 * @param string $section The nav section to check
 *
 * @return int
 */
function count_viable_navs($section)
{
    global $navbysection;

    $count = 0;
    $val = $navbysection[$section];

    if (count($val) > 0)
    {
        foreach ($val as $nav)
        {
            if (is_array($nav) && count($nav) > 0)
            {
                $link = $nav[1]; // [0] is the text, [1] is the link
                if (! is_blocked($link))
                {
                    $count++;
                }
            }
        }
    }

    return $count;
}

/**
 * Determins if there are any navs for the player.
 *
 * @return bool
 */
function checknavs()
{
    global $navbysection, $session;

    // If we already have navs entered (because someone stuck raw links in)
    // just return true;
    if (is_array($session['user']['allowednavs']) && count($session['user']['allowednavs']) > 0)
    {
        return true;
    }

    // If we have any links which are going to be stuck in, return true
    foreach ($navbysection as $key => $val)
    {
        if (count_viable_navs($key) > 0)
        {
            foreach ($val as $v)
            {
                if (is_array($v) && count($v) > 0)
                {
                    return true;
                }
            }
        }
    }

    // We have no navs.
    return false;
}

/**
 * Builds navs for display.
 *
 * @return string Output formatted navs
 */
function buildnavs()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use {{ navigation_menu() }} in template.',
        __METHOD__
    ), E_USER_DEPRECATED);
}

$accesskeys = [];
$quickkeys = [];

/**
 * Private functions (Undocumented).
 *
 * @param string $text
 * @param string $link
 * @param bool   $priv
 * @param bool   $pop
 * @param string   $popsize
 *
 * @return mixed
 */
function private_addnav($text, $link = false, $priv = false, $pop = false, $popsize = '500x300')
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use LotgdNavigation::addNav() instead.',
        __METHOD__
    ), E_USER_DEPRECATED);
}

/**
 * Determine how many navs are available.
 *
 * @return int The number of legal navs
 */
function navcount()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, not have usage.',
        __METHOD__
    ), E_USER_DEPRECATED);
}

/**
 * Reset and wipe the navs.
 */
function clearnav()
{
    $session['user']['allowednavs'] = [];
}

/**
 * Reset the output and wipe the navs.
 */
function clearoutput()
{
    global $output, $header, $nav, $session;

    clearnav();
    $output = \LotgdLocator::build(Lotgd\Core\Output\Collector::class);
    $header = '';
    $nav = '';
}

/**
 * Adds an access key for a text
 * You need to add the attribute "accesskey" to an HTML element
 *
 * @param string|array $text
 * @param string $link
 * @param boolean $pop
 * @param string $popsize
 * @param string $extra
 *
 * @return string A string that contain a key
 */
function add_accesskey($text, $link, $pop, $popsize , $extra)
{
    global $accesskeys, $quickkeys;

    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, this not have remplace.',
        __METHOD__
    ), E_USER_DEPRECATED);

    $key = '';
    $ignoreuntil = '';
    if ('?' == $text[1])
    {
        // check to see if a key was specified up front.
        $hchar = strtolower($text[0]);

        if (' ' == $hchar || array_key_exists($hchar, $accesskeys) && 1 == $accesskeys[$hchar])
        {
            $text = substr($text, 2);
            $text = holidayize($text, 'nav');

            if (' ' == $hchar)
            {
                $key = ' ';
            }
        }
        else
        {
            $key = $text[0];
            $text = substr($text, 2);
            $text = holidayize($text, 'nav');
            $found = false;
            $text_len = strlen($text);

            for ($i = 0; $i < $text_len; $i++)
            {
                $char = $text[$i];

                if ($ignoreuntil == $char)
                {
                    $ignoreuntil = '';
                }
                elseif ('' != $ignoreuntil)
                {
                    if ('<' == $char)
                    {
                        $ignoreuntil = '>';
                    }
                    elseif ('&' == $char)
                    {
                        $ignoreuntil = ';';
                    }
                    elseif ('`' == $char)
                    {
                        $ignoreuntil = $text[$i + 1];
                    }
                }
                else if ($char == $key)
                {
                    $found = true;
                    break;
                }
            }

            if (false == $found)
            {
                //the hotkey for this link wasn't actually in the
                //text, prepend it in parens.
                $text = '('.strtoupper($key).') '.$text;
                if (false !== strpos($text, '__'))
                {
                    $text = str_replace('__', '('.$key.') ', $text);
                }
                $i = strpos($text, $key);
            }
        }
    }
    else
    {
        $text = holidayize($text, 'nav');
    }

    //we have no previously defined key.  Look for a new one.
    $strlen = strlen($text);
    for ($i = 0; $i < $strlen; $i++)
    {
        $char = substr($text, $i, 1);

        if ($ignoreuntil == $char)
        {
            $ignoreuntil = '';
        }
        else
        {
            if ((isset($accesskeys[strtolower($char)]) && 1 == $accesskeys[strtolower($char)]) || (false === strpos('abcdefghijklmnopqrstuvwxyz0123456789', strtolower($char))) || '' != $ignoreuntil)
            {
                if ('<' == $char)
                {
                    $ignoreuntil = '>';
                }
                else if ('&' == $char)
                {
                    $ignoreuntil = ';';
                }
                else if ('`' == $char)
                {
                    $ignoreuntil = substr($text, $i + 1, 1);
                }
            }
            else
            {
                break;
            }
        }
    }

    if (! isset($i))
    {
        $i = 0;
    }

    $key = '';
    $keyrep = '';
    if ($i < strlen($text) && ' ' != $key)
    {
        $key = substr($text, $i, 1);
        $accesskeys[strtolower($key)] = 1;
        $keyrep = " accesskey=\"$key\" ";
    }

    if ('' != $key || ' ' != $key)
    {
        $pattern1 = '/^'.preg_quote($key, '/').'/';
        $pattern2 = '/([^`])'.preg_quote($key, '/').'/';
        $rep1 = "`H{$key}´H";
        $rep2 = "\$1`H{$key}´H";
        $text = preg_replace($pattern1, $rep1, $text, 1);

        if (false === strpos($text, '`H'))
        {
            $text = preg_replace($pattern2, $rep2, $text, 1);
        }

        $quickkeys[$key] = "window.location='$link$extra'";
        if ($pop)
        {
            $quickkeys[$key] = 'onclick="Lotgd.embed(this)"';
            if ('' == $popsize)
            {
                $quickkeys[$key] = "window.open('$link')";
            }
        }
    }

    return [
        'key' => $key,
        'keyrep' => $keyrep,
        'text' => $text
    ];
}
