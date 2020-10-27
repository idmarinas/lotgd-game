<?php

// translator ready
// addnews ready
// mail ready
/**
 * @deprecated 4.0.0
 */
function translator_setup()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in future version, use new translations system.',
        __METHOD__
    ), E_USER_DEPRECATED);
    //Determine what language to use
    if (\defined('TRANSLATOR_IS_SET_UP'))
    {
        return;
    }
    \define('TRANSLATOR_IS_SET_UP', true);
    global $language, $session;
    $language = '';

    if (isset($session['user']['prefs']['language']))
    {
        $language = $session['user']['prefs']['language'];
    }
    elseif (isset($_COOKIE['language']))
    {
        $language = $_COOKIE['language'];
    }

    if ('' == $language)
    {
        $language = getsetting('defaultlanguage', 'en');
    }
    \define('LANGUAGE', \preg_replace('/[^a-z]/i', '', $language));
}
$translation_table = [];
/**
 * @deprecated 4.0.0
 *
 * @param mixed $indata
 * @param mixed $namespace
 */
function translate($indata, $namespace = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in future version, use new translations system.',
        __METHOD__
    ), E_USER_DEPRECATED);

    if (false == getsetting('enabletranslation', true))
    {
        return $indata;
    }
    global $session, $translation_table, $translation_namespace;

    if ( ! $namespace)
    {
        $namespace = $translation_namespace;
    }
    $outdata = $indata;

    if ( ! isset($namespace) || '' == $namespace)
    {
        tlschema();
    }
    $foundtranslation = false;

    if ('notranslate' != $namespace)
    {
        if ( ! isset($translation_table[$namespace]) || ! \is_array($translation_table[$namespace]))
        {
            //build translation table for this page hit.
            $translation_table[$namespace] = translate_loadnamespace($namespace, ($session['tlanguage'] ?? false));
        }
    }

    if (\is_array($indata))
    {
        //recursive translation on arrays.
        $outdata = [];

        foreach ($indata as $key => $val)
        {
            $outdata[$key] = translate($val, $namespace);
        }
    }
    else
    {
        if ('notranslate' != $namespace)
        {
            if (isset($translation_table[$namespace][$indata]))
            {
                $outdata          = $translation_table[$namespace][$indata];
                $foundtranslation = true;
            // Remove this from the untranslated texts table if it is
                // in there and we are collecting texts
                // This delete is horrible on very heavily translated games.
                // It has been requested to be removed.
                /*
                if (getsetting("collecttexts", false)) {
                    $sql = "DELETE FROM " . DB::prefix("untranslated") .
                        " WHERE intext='" . addslashes($indata) .
                        "' AND language='" . LANGUAGE . "'";
                    DB::query($sql);
                }
                */
            }
            elseif (getsetting('collecttexts', false))
            {
                $sql = 'INSERT IGNORE INTO '.DB::prefix('untranslated')." (intext,language,namespace) VALUES ('".\addslashes($indata)."', '".LANGUAGE."', "."'{$namespace}')";
                DB::query($sql, false);
            }
            tlbutton_push($indata, ! $foundtranslation, $namespace);
        }
        else
        {
            $outdata = $indata;
        }
    }

    return $outdata;
}
/**
 * @deprecated 4.0.0
 */
function sprintf_translate()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in future version, use new translations system.',
        __METHOD__
    ), E_USER_DEPRECATED);
    $args      = \func_get_args();
    $setschema = false;
    // Handle if an array is passed in as the first arg
    if (\is_array($args[0]))
    {
        $args[0] = \call_user_func_array('sprintf_translate', $args[0]);
    }
    else
    {
        // array_shift returns the first element of an array and shortens this array by one...
        if (\is_bool($args[0]) && \array_shift($args))
        {
            tlschema(\array_shift($args));
            $setschema = true;
        }
        $args[0] = \str_replace('`%', '`%%', $args[0]);
        $args[0] = translate($args[0]);

        if ($setschema)
        {
            tlschema();
        }
    }
    \reset($args);
    \each($args); //skip the first entry which is the output text

    foreach ($args as $key => $val)
    {
        if (\is_array($val))
        {
            //When passed a sub-array this represents an independant
            //translation to happen then be inserted in the master string.
            $args[$key] = \call_user_func_array('sprintf_translate', $val);
        }
    }
    \ob_start();
    $return = \call_user_func_array('sprintf', $args);
    $err    = \ob_get_contents();
    \ob_end_clean();

    if ($err > '')
    {
        $args['error'] = $err;
        debug($err);
    }

    return $return;
}
/**
 * @deprecated 4.0.0
 *
 * @param mixed $in
 * @param mixed $namespace
 */
function translate_inline($in, $namespace = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in future version, use new translations system.',
        __METHOD__
    ), E_USER_DEPRECATED);
    $out = translate($in, $namespace);
    rawoutput(tlbutton_clear());

    return $out;
}
/**
 * @deprecated 4.0.0
 *
 * @param mixed $in
 * @param mixed $to
 */
function translate_mail($in, $to = 0)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in future version, use new translations system.',
        __METHOD__
    ), E_USER_DEPRECATED);
    global $session;
    tlschema('mail'); // should be same schema like systemmails!

    if ( ! \is_array($in))
    {
        $in = [$in];
    }
    //this is done by sprintf_translate.
    //$in[0] = str_replace("`%","`%%",$in[0]);
    if ($to > 0)
    {
        $language             = DB::fetch_assoc(DB::query('SELECT prefs FROM '.DB::prefix('accounts')." WHERE acctid={$to}"));
        $language['prefs']    = \unserialize($language['prefs']);
        $session['tlanguage'] = $language['prefs']['language'] ? $language['prefs']['language'] : getsetting('defaultlanguage', 'en');
    }
    \reset($in);
    // translation offered within translation tool here is in language
    // of sender!
    // translation of mails can't be done in language of recipient by
    // the sender via translation tool.
    $out = \call_user_func_array('sprintf_translate', $in);
    tlschema();
    unset($session['tlanguage']);

    return $out;
}
/**
 * @deprecated 4.0.0
 *
 * @param mixed $in
 */
function tl($in)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in future version, use new translations system.',
        __METHOD__
    ), E_USER_DEPRECATED);
    $out = translate($in);

    return tlbutton_clear().$out;
}
/**
 * @deprecated 4.0.0
 *
 * @param mixed $namespace
 * @param mixed $language
 */
function translate_loadnamespace($namespace, $language = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in future version, use new translations system.',
        __METHOD__
    ), E_USER_DEPRECATED);

    if (false === $language)
    {
        translator_setup();
        $language = LANGUAGE;
    }
    $page = translator_page($namespace);
    $uri  = translator_uri($namespace);

    if ($page == $uri)
    {
        $where = "uri = '{$page}'";
    }
    else
    {
        $where = "(uri='{$page}' OR uri='{$uri}')";
    }
    $sql = '
        SELECT intext,outtext
        FROM '.DB::prefix('translations')."
        WHERE language='{$language}'
            AND {$where}";
    /*	debug(nl2br(htmlentities($sql, ENT_COMPAT, getsetting("charset", "UTF-8")))); */
    if ( ! getsetting('cachetranslations', 0))
    {
        $result = DB::query($sql);
    }
    else
    {
        $result = DB::query($sql);
        //store it for 10 Minutes, normally you don't need to refresh this often
    }
    $out = [];

    while ($row = DB::fetch_assoc($result))
    {
        $out[$row['intext']] = $row['outtext'];
    }

    return $out;
}
$translatorbuttons = [];
$seentlbuttons     = [];
/**
 * @deprecated 4.0.0
 *
 * @param mixed $indata
 * @param mixed $hot
 * @param mixed $namespace
 */
function tlbutton_push($indata, $hot = false, $namespace = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in future version, use new translations system.',
        __METHOD__
    ), E_USER_DEPRECATED);
    global $translatorbuttons, $translation_is_enabled, $seentlbuttons, $session, $language;

    if ( ! $translation_is_enabled)
    {
        return;
    }

    if ( ! $namespace)
    {
        $namespace = 'unknown';
    }

    if (isset($session['user']['superuser']) && $session['user']['superuser'] & SU_IS_TRANSLATOR)
    {
        if ( ! \in_array($language, \explode(',', $session['user']['translatorlanguages'])))
        {
            return true;
        }

        if (\preg_replace("/[ 	\n\r]|`./", '', $indata) > '')
        {
            if (isset($seentlbuttons[$namespace][$indata]))
            {
                $link = '';
            }
            else
            {
                $seentlbuttons[$namespace][$indata] = true;
                $uri                                = \LotgdSanitize::cmdSanitize($namespace);
                $uri                                = comscroll_sanitize($uri);
                $link                               = 'translatortool.php?u='.\rawurlencode($uri).'&t='.\rawurlencode($indata);
                $link                               = \sprintf('<a href="%s" class="t%s" id="translator" data-force="true" onclick="Lotgd.embed(this)">T</a>',
                    $link,
                    ($hot ? 'hot' : '')
                );
            }
            \array_push($translatorbuttons, $link);
        }

        return true;
    }
    else
    {
        //when user is not a translator, return false.
        return false;
    }
}
/**
 * @deprecated 4.0.0
 */
function tlbutton_pop()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in future version, use new translations system.',
        __METHOD__
    ), E_USER_DEPRECATED);
    global $translatorbuttons,$session;

    if (isset($session['user']['superuser']) && $session['user']['superuser'] & SU_IS_TRANSLATOR)
    {
        return \array_pop($translatorbuttons);
    }
    else
    {
        return;
    }
}
/**
 * @deprecated 4.0.0
 */
function tlbutton_clear()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in future version, use new translations system.',
        __METHOD__
    ), E_USER_DEPRECATED);
    global $translatorbuttons,$session;

    if (isset($session['user']['superuser']) && $session['user']['superuser'] & SU_IS_TRANSLATOR)
    {
        $return            = tlbutton_pop().\implode('', $translatorbuttons);
        $translatorbuttons = [];

        return $return;
    }
    else
    {
        return;
    }
}
$translation_is_enabled = true;
/**
 * @deprecated 4.0.0
 *
 * @param mixed $enable
 */
function enable_translation($enable = true)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in future version, use new translations system.',
        __METHOD__
    ), E_USER_DEPRECATED);
    global $translation_is_enabled;
    $translation_is_enabled = $enable;
}
$translation_namespace       = '';
$translation_namespace_stack = [];
/**
 * @deprecated 4.0.0
 *
 * @param mixed $schema
 */
function tlschema($schema = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in future version, use new translations system.',
        __METHOD__
    ), E_USER_DEPRECATED);
    global $translation_namespace,$translation_namespace_stack;

    if (false === $schema)
    {
        $translation_namespace = \array_pop($translation_namespace_stack);

        if ('' == $translation_namespace)
        {
            $translation_namespace = translator_uri(LotgdRequest::getServer('REQUEST_URI'));
        }
    }
    else
    {
        \array_push($translation_namespace_stack, $translation_namespace);
        $translation_namespace = $schema;
    }
}
/**
 * @deprecated 4.0.0
 */
function translator_check_collect_texts()
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in future version, use new translations system.',
        __METHOD__
    ), E_USER_DEPRECATED);
    $tlmax = getsetting('tl_maxallowed', 0);

    if (getsetting('permacollect', 0))
    {
        savesetting('collecttexts', 1);
    }
    elseif ($tlmax && getsetting('OnlineCount', 0) <= $tlmax)
    {
        savesetting('collecttexts', 1);
    }
    else
    {
        savesetting('collecttexts', 0);
    }
}

function translator_uri($in)
{
    \trigger_error(\sprintf(
        'The use of %s is obsolete since version 4.0.0; and remove it in version 4.1.0, has no replacement.',
        __METHOD__
    ), E_USER_DEPRECATED);

    $uri = comscroll_sanitize($in);
    $uri = \LotgdSanitize::cmdSanitize($uri);

    if ('?' == \substr($uri, -1))
    {
        $uri = \substr($uri, 0, -1);
    }

    return $uri;
}

function translator_page($in)
{
    \trigger_error(\sprintf(
        'The use of %s is obsolete since version 4.0.0; and remove it in version 4.1.0, has no replacement.',
        __METHOD__
    ), E_USER_DEPRECATED);

    $page = $in;

    if (false !== \strpos($page, '?'))
    {
        $page = \substr($page, 0, \strpos($page, '?'));
    }
    //if ($page=="runmodule.php" && 0){
    //	//we should handle this in runmodule.php now that we have tlschema.
    //	$matches = array();
    //	preg_match("/[&?](module=[^&]*)/i",$in,$matches);
    //	if (isset($matches[1])) $page.="?".$matches[1];
    //}
    return $page;
}

function comscroll_sanitize($in)
{
    \trigger_error(\sprintf(
        'The use of %s is obsolete since version 4.0.0; and remove it in version 4.1.0, has no replacement.',
        __METHOD__
    ), E_USER_DEPRECATED);

    $out = \preg_replace("'&c(omscroll)?=([[:digit:]]|-)*'", '', $in);
    $out = \preg_replace("'\\?c(omscroll)?=([[:digit:]]|-)*'", '?', $out);
    $out = \preg_replace("'&(refresh|comment)=1'", '', $out);

    return \preg_replace("'\\?(refresh|comment)=1'", '?', $out);
}
