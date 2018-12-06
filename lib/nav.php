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
    //prevents a script from being able to generate navs on the given $link.

    $block = \LotgdLocator::get(\Lotgd\Core\Nav\Blocked::class);

    if ($partial)
    {
        return $block->blockPartialNav($link);
    }

    return $block->blockFullNav($link);
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
    //prevents a link that was otherwise blocked with blocknav() from
    //actually being blocked.

    $block = \LotgdLocator::get(\Lotgd\Core\Nav\Blocked::class);

    if ($partial)
    {
        return $block->unBlockPartialNav($link);
    }

    return $block->unBlockFullNav($link);
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
    global $navsection,$navbysection,$translation_namespace, $navschema, $navnocollapse, $block_new_navs, $notranslate;

    if ($block_new_navs)
    {
        return;
    }

    if (is_array($text))
    {
        $text = '!array!'.serialize($text);
    }

    $navsection = $text;

    if (! array_key_exists($text, $navschema))
    {
        $navschema[$text] = $translation_namespace;
    }

    //So we can place sections with out adding navs to them.
    if (! isset($navbysection[$navsection]))
    {
        $navbysection[$navsection] = [];
    }

    if (false === $collapse)
    {
        $navnocollapse[$text] = true;
    }

    if (false === $translate)
    {
        if (! isset($notranslate))
        {
            $notranslate = [];
        }

        array_push($notranslate, [$text, '']);
    }
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
    global $navsection, $navbysection, $navschema, $notranslate, $block_new_navs;

    if ($block_new_navs)
    {
        return;
    }

    if (false === $link && '' != $text)
    {
        // Don't do anything if text is ""
        addnavheader($text, true, false);
    }
    else
    {
        $args = func_get_args();

        if ('' == $text)
        {
            //if there's no text to display, may as well just stick this on
            //the nav stack now.
            call_user_func_array('private_addnav', $args);
        }
        else
        {
            if (! isset($navbysection[$navsection]))
            {
                $navbysection[$navsection] = [];
            }

            if (! isset($notranslate))
            {
                $notranslate = [];
            }
            array_push($navbysection[$navsection], $args);
            array_push($notranslate, $args);
        }
    }
}

function addnav($text, $link = false, $priv = false, $pop = false, $popsize = '500x300')
{
    global $navsection, $navbysection, $translation_namespace, $navschema, $block_new_navs;

    if ($block_new_navs)
    {
        return;
    }

    if (false === $link && '' != $text)
    {
        // Don't do anything if text is ""
        addnavheader($text);
    }
    else
    {
        $args = func_get_args();

        if ('' == $text)
        {
            //if there's no text to display, may as well just stick this on
            //the nav stack now.
            call_user_func_array('private_addnav', $args);
        }
        else
        {
            if (! isset($navbysection[$navsection]))
            {
                $navbysection[$navsection] = [];
            }
            $t = $args[0];

            if (is_array($t))
            {
                $t = $t[0];
            }

            if (! array_key_exists($t, $navschema))
            {
                $navschema[$t] = $translation_namespace;
            }
            array_push($navbysection[$navsection], array_merge($args, ['translate' => false]));
        }
    }
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
    $block = \LotgdLocator::get(\Lotgd\Core\Nav\Blocked::class);

    return $block->isBlocked($link);
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
    if (is_array($session['allowednavs']) && count($session['allowednavs']) > 0)
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
    global $navbysection, $navschema, $session, $navnocollapse;

    $builtnavs = [];

    foreach ($navbysection as $key => $val)
    {
        $tkey = $key;
        $navbanner = '';

        if (count_viable_navs($key) > 0)
        {
            if ($key > '')
            {
                if (isset($session['loggedin']) && $session['loggedin'])
                {
                    tlschema($navschema[$key]);
                }

                if ('!array!' == substr($key, 0, 7))
                {
                    $key = unserialize(substr($key, 7));
                }

                $navbanner = private_addnav($key);

                if (isset($session['loggedin']) && $session['loggedin'])
                {
                    tlschema();
                }
            }

            $sublinks = [];

            foreach ($val as $v)
            {
                if (is_array($v) && count($v) > 0)
                {
                    $sublinks[] = call_user_func_array('private_addnav', $v);
                }
            }

            $builtnavs[$navbanner] = $sublinks;
        }//end if
    }//end foreach
    $navbysection = [];

    return $builtnavs;
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
    //don't call this directly please.  I'll break your thumbs if you do.
    global $nav, $session, $accesskeys, $REQUEST_URI, $quickkeys, $navschema, $notranslate;

    if (is_blocked($link))
    {
        return false;
    }

    $thisnav = '';
    $unschema = 0;
    $translate = true;

    if (isset($notranslate))
    {
        if (in_array([$text, $link], $notranslate))
        {
            $translate = false;
        }
    }

    if (is_array($text))
    {
        if ($text[0] && isset($session['loggedin']) && $session['loggedin'])
        {
            $schema = $text[0];
            if (false === $link)
            {
                $schema = '!array!'.serialize($text);
            }

            if ($translate)
            {
                tlschema($navschema[$schema]);
                $unschema = 1;
            }
        }

        if ('!!!addraw!!!' != $link)
        {
            if ($translate)
            {
                $text[0] = translate($text[0]);
            }
            $text = call_user_func_array('sprintf', $text);
        }
        else
        {
            $text = call_user_func_array('sprintf', $text);
        }
    }
    else
    {
        if ($text && isset($session['loggedin']) && $session['loggedin'] && $translate)
        {
            if (isset($navschema[$text]))
            {
                tlschema($navschema[$text]);
            }
            $unschema = 1;
        }

        if ('!!!addraw!!!' != $link && $text > '' && $translate)
        {
            $text = translate($text);
        } //leave the hack in here for now, use addnav_notl please
    }

    $extra = '';

    if (false === $link)
    {
        $text = holidayize($text, 'nav');
        $thisnav .= LotgdTheme::renderThemeTemplate('sidebar/navigation/head.twig', [
            'title' => appoencode($text, $priv),
            'tlbutton' => tlbutton_pop()
        ]);
    }
    elseif ('' === $link)
    {
        $text = holidayize($text, 'nav');
        $thisnav .= LotgdTheme::renderThemeTemplate('sidebar/navigation/help.twig', [
            'text' => appoencode($text, $priv),
            'tlbutton' => tlbutton_pop()
        ]);
    }
    elseif ('!!!addraw!!!' == $link)
    {
        $thisnav .= $text;
    }
    else
    {
        if ('' != $text)
        {
            $extra = "&c={$session['counter']}";
            if (false === strpos($link, '?'))
            {
                $extra = "?c={$session['counter']}";
            }

            $extra .= '-'.date('His');
            //hotkey for the link.
            $hotkey = add_accesskey($text, $link, $pop, $popsize, $extra);
            $key = $hotkey['key'];
            $keyrep = $hotkey['keyrep'];
            $text = $hotkey['text'];

            $thisnav .= LotgdTheme::renderThemeTemplate('sidebar/navigation/item.twig', [
                'text' => appoencode($text, $priv),
                'link' => htmlentities($link.(true != $pop ? $extra : ''), ENT_COMPAT, getsetting('charset', 'utf-8')),
                'accesskey' => $keyrep,
                'popup' => (true == $pop ? "target='_blank' rel='noopener noreferrer' " . ($popsize ? "onClick='Lotgd.embed(this)' data-force='true'": '') : ''),
                'tlbutton' => tlbutton_pop()
            ]);
        }

        $session['allowednavs'][$link.$extra] = true;
        $session['allowednavs'][str_replace(' ', '%20', $link).$extra] = true;
        $session['allowednavs'][str_replace(' ', '+', $link).$extra] = true;

        if (false !== ($pos = strpos($link, '#')))
        {
            $sublink = substr($link, 0, $pos);
            $session['allowednavs'][$sublink.$extra] = true;
        }
    }

    if ($unschema)
    {
        tlschema();
    }

    $nav .= $thisnav;

    return $thisnav;
}

/**
 * Determine how many navs are available.
 *
 * @return int The number of legal navs
 */
function navcount()
{
    //returns count of total navs added, be it they are pending addition or
    //actually added.
    global $session,$navbysection;
    $c = count($session['allowednavs']);

    if (! is_array($navbysection))
    {
        return $c;
    }

    foreach ($navbysection as $val)
    {
        if (is_array($val))
        {
            $c += count($val);
        }
    }

    return $c;
}

/**
 * Reset and wipe the navs.
 */
function clearnav()
{
    $session['allowednavs'] = [];
}

/**
 * Reset the output and wipe the navs.
 */
function clearoutput()
{
    global $output,$nestedtags,$header,$nav,$session;

    clearnav();
    $output = new LotgdOutputCollector();
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
                else
                {
                    if ('' != $ignoreuntil)
                    {
                        if ('<' == $char)
                        {
                            $ignoreuntil = '>';
                        }

                        if ('&' == $char)
                        {
                            $ignoreuntil = ';';
                        }

                        if ('`' == $char)
                        {
                            $ignoreuntil = $text[$i + 1];
                        }
                    }
                    else
                    {
                        if ($char == $key)
                        {
                            $found = true;
                            break;
                        }
                    }
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
