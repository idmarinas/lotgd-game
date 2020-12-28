<?php

// addnews ready
// translator ready
// mail ready

$buffreplacements = [];
$debuggedbuffs    = [];
function calculate_buff_fields()
{
    global $session, $badguy, $buffreplacements, $debuggedbuffs;

    if ( ! isset($session['bufflist']) || ! $session['bufflist'])
    {
        return;
    }

    //run temp stats
    \reset($session['bufflist']);

    foreach ($session['bufflist'] as $buffname => $buff)
    {
        if ( ! isset($buff['tempstats_calculated']))
        {
            foreach ($buff as $property => $value)
            {
                if ('tempstat-' == \substr($property, 0, 9))
                {
                    apply_temp_stat(\substr($property, 9), $value);
                }
            }//end foreach
            $session['bufflist'][$buffname]['tempstats_calculated'] = true;
        }//end if
    }//end foreach

    //process calculated buff fields.
    \reset($session['bufflist']);

    if ( ! \is_array($buffreplacements))
    {
        $buffreplacements = [];
    }

    foreach ($session['bufflist'] as $buffname => $buff)
    {
        if ( ! isset($buff['fields_calculated']))
        {
            foreach ($buff as $property => $value)
            {
                //calculate dynamic buff fields
                $origstring = $value;
                //Simple <module|variable> replacements for get_module_pref('variable','module')
                $value = \preg_replace('/<([A-Za-z0-9]+)\\|([A-Za-z0-9]+)>/', "get_module_pref('\\2','\\1')", $value);
                //simple <variable> replacements for $session['user']['variable']
                $value = \preg_replace('/<([A-Za-z0-9]+)>/', "\$session['user']['\\1']", $value);

                if ( ! \defined('OLDSU'))
                {
                    \define('OLDSU', $session['user']['superuser']);
                }

                if ($value != $origstring)
                {
                    if ('debug:' == \strtolower(\substr($value, 0, 6)))
                    {
                        $errors     = '';
                        $origstring = \substr($origstring, 6);
                        $value      = \substr($value, 6);

                        if ( ! isset($debuggedbuffs[$buffname]))
                        {
                            $debuggedbuffs[$buffname] = [];
                        }

                        \ob_start();
                        $val    = eval("return {$value};");
                        $errors = \ob_get_contents();
                        \ob_end_clean();

                        if ( ! isset($debuggedbuffs[$buffname][$property]))
                        {
                            if ('' == $errors)
                            {
                                \LotgdResponse::pageDebug("Buffs[{$buffname}][{$property}] evaluates successfully to {$val}");
                            }
                            else
                            {
                                \LotgdResponse::pageDebug("Buffs[{$buffname}][{$property}] has an evaluation error<br>"
                                .\htmlentities($origstring, ENT_COMPAT, getsetting('charset', 'UTF-8')).' becomes <br>'
                                .\htmlentities($value, ENT_COMPAT, getsetting('charset', 'UTF-8')).'<br>'
                                .$errors);
                                $val = '';
                            }
                            $debuggedbuffs[$buffname][$property] = true;
                        }

                        $origstring = 'debug:'.$origstring;
                        $value      = 'debug'.$value;
                    }
                    else
                    {
                        $val = eval('return '.$value.';');
                    }
                }
                else
                {
                    $val = $value;
                }

                $session['user']['superuser'] = OLDSU;

                if (\is_numeric($val) && (\is_nan($val) || \is_infinite($val)))
                {
                    $val = $value;
                }

                $output = $output ?? '';

                if ('' == $output && (string) $val != (string) $origstring)
                {
                    $buffreplacements[$buffname][$property]    = $origstring;
                    $session['bufflist'][$buffname][$property] = $val;
                }//end if
                unset($val);
            }//end foreach
            $session['bufflist'][$buffname]['fields_calculated'] = true;
        }//end if
    }//end foreach
}//end function

function restore_buff_fields()
{
    global $session, $buffreplacements;

    if (\is_array($buffreplacements))
    {
        \reset($buffreplacements);

        foreach ($buffreplacements as $buffname => $val)
        {
            \reset($val);

            foreach ($val as $property => $value)
            {
                if (isset($session['bufflist'][$buffname]))
                {
                    $session['bufflist'][$buffname][$property] = $value;
                    unset($session['bufflist'][$buffname]['fields_calculated']);
                }//end if
            }//end foreach
            unset($buffreplacements[$buffname]);
        }//end foreach
    }//end if

    //restore temp stats
    if ( ! isset($session['bufflist']) || ! \is_array($session['bufflist']))
    {
        $session['bufflist'] = [];
    }
    \reset($session['bufflist']);

    foreach ($session['bufflist'] as $buffname => $buff)
    {
        if (\array_key_exists('tempstats_calculated', $buff) && $buff['tempstats_calculated'])
        {
            \reset($buff);

            foreach ($buff as $property => $value)
            {
                if ('tempstat-' == \substr($property, 0, 9))
                {
                    apply_temp_stat(\substr($property, 9), -$value);
                }
            }//end foreach
            unset($session['bufflist'][$buffname]['tempstats_calculated']);
        }//end if
    }//end foreach
}//end function

function apply_buff($name, $buff)
{
    global $session,$buffreplacements, $translation_namespace;

    if ( ! isset($buff['schema']) || '' == $buff['schema'])
    {
        $buff['schema'] = $translation_namespace;
    }

    if (isset($buffreplacements[$name]))
    {
        unset($buffreplacements[$name]);
    }

    if (isset($session['bufflist'][$name]))
    {
        //we'll need to unapply buff fields before applying this buff since
        //it's already set.
        restore_buff_fields();
    }

    $buff = ['name' => $name, 'buff' => $buff];
    \LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_CHARACTER_MODIFY_BUFF, null, $buff);
    $buff = modulehook('modify-buff', $buff);

    $session['bufflist'][$name] = $buff['buff'];
    calculate_buff_fields();
}

function apply_companion($name, $companion, $ignorelimit = false)
{
    global $session, $companions;

    $companionsallowed = getsetting('companionsallowed', 1);
    $args              = ['maxallowed' => $companionsallowed];
    \LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_CHARACTER_COMPANIONS_ALLOWED, null, $args);
    $args = modulehook('companionsallowed', $args);

    $companionsallowed = $args['maxallowed'];
    $current           = 0;

    if ( ! $ignorelimit)
    {
        foreach ($companions as $thisname => $thiscompanion)
        {
            if (( ! isset($thiscompanion['ignorelimit']) || ! $thiscompanion['ignorelimit'])
                && ($thisname != $name)
            ) {
                ++$current;
            }
        }
    }

    if ($current < $companionsallowed || $ignorelimit)
    {
        if (isset($companions[$name]))
        {
            unset($companions[$name]);
        }

        if ( ! isset($companion['ignorelimit']) && $ignorelimit)
        {
            $companion['ignorelimit'] = true;
        }
        $companions[$name]             = $companion;
        $session['user']['companions'] = $companions;

        return true; // success!
    }
    else
    {
        \LotgdResponse::pageDebug('Failed to add companion due to restrictions regarding the maximum amount of companions allowed.');

        return false;
    }
}

function strip_buff($name)
{
    global $session, $buffreplacements;
    restore_buff_fields();

    if (isset($session['bufflist'][$name]))
    {
        unset($session['bufflist'][$name]);
    }

    if (isset($buffreplacements[$name]))
    {
        unset($buffreplacements[$name]);
    }
    calculate_buff_fields();
}

function strip_all_buffs()
{
    global $session;

    foreach ($session['bufflist'] as $buffname => $buff)
    {
        strip_buff($buffname);
    }
}

function has_buff($name)
{
    global $session;

    return (bool) (isset($session['bufflist'][$name]))

     ;
}
