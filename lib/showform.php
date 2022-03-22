<?php

// translator ready
// addnews ready
// mail ready
require_once 'lib/dump_item.php';

/**
 * Construct FORM LOTGD style.
 *
 * @param array
 * @param array         $row
 * @param bool          $nosave
 * @param false|pattern $keypref
 * @param bool          $print
 * @param callable      $callback This can use for personalize the form used to show all inputs or for more process.
 *                                Can still use lotgd_show_form_field in your callable
 *                                Both functions get same parameters function($info, $row, $key, $keyout, $val, $extensions)
 * @param mixed         $layout
 *
 * @deprecated 4.1.0
 */
function lotgd_showform($layout, $row, $nosave = false, $keypref = false, $print = true, ?callable $callback = null)
{
    static $showform_id = 0;
    static $title_id    = 0;

    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 4.1.0; and delete in version future version, use new Symfony Form system.',
        __METHOD__
    ), E_USER_DEPRECATED);

    /**
     * If $layout is an string use new form system.
     */
    if (\is_string($layout))
    {
        return \LotgdLocator::get($layout);
    }

    ++$showform_id;
    $extensions = modulehook('showformextensions', []);

    $i          = false;
    $tabMenu    = [];
    $tabContent = [];

    foreach ($layout as $key => $val)
    {
        $keyout = (false !== $keypref) ? \sprintf($keypref, $key) : $key;

        if (\is_array($val))
        {
            $info    = \explode(',', $val[0]);
            $val[0]  = $info[0];
            $info[0] = $val;
        }
        else
        {
            $info = \explode(',', $val);
        }

        if (\is_array($info[0]))
        {
            $info[0] = \call_user_func_array('sprintf', $info[0]);
        }

        $info[1] = (isset($info[1])) ? \trim($info[1]) : '';

        if ('title' == $info[1])
        {
            ++$title_id;

            $tabMenu[] = \sprintf('<a class="w-full inline-block" data-tabs-target="tab" data-action="click->tabs#change" href="#">%s</a>', $info[0]);
        }
        elseif ('note' == $info[1])
        {
            $tabContent[$title_id][] = \sprintf('<div class="ui small info message">%s</div>', \LotgdSanitize::fullSanitize($info[0]));
        }
        else
        {
            $callback = $callback ?: 'lotgd_show_form_field';
            $result   = $callback($info, $row, $key, $keyout, $val, $extensions);

            $tabContent[$title_id][] = \sprintf(
                '<label class="md:flex md:items-center mb-6"><div class="md:w-1/3">%s</div><div class="md:w-2/3">%s</div></label>',
                \LotgdFormat::colorize($info[0]),
                $result
            );

            $i = ! $i;
        }
    }

    $content = '';

    foreach ($tabContent as $key => $value)
    {
        $text = \sprintf(
            '<div class="ui form">%s</div>',
            \implode('', $value)
        );

        if (0 < $key)
        {
            $text = \sprintf(
                '<div class="hidden border-b border-lotgd-200 pb-3 px-2" data-tabs-target="panel"><div class="italic bg-gradient-to-r from-transparent via-lotgd-800 text-center font-bold border-b border-lotgd-500 py-2 mb-2">%s</div></div>',
                $text
            );
        }

        $content .= $text;
    }

    unset($text);

    if ( ! empty($tabMenu))
    {
        $tabMenu = \array_chunk($tabMenu, \ceil(\count($tabMenu) / 4));

        $popupMenu = '';

        foreach ($tabMenu as $menu)
        {
            $popupMenu .= '<div class="column">'.\implode('', $menu).'</div>';
        }

        $menu = \sprintf(
            '<div class="grid grid-cols-4 divide-x divide-lotgd-200 border-b border-lotgd-200 py-3 px-2 text-center">%s</div>',
            $popupMenu
        );

        $content = $menu.$content;

        unset($popupMenu, $menu);
    }

    $content = '<div class="shadow overflow-hidden border border-lotgd-200 sm:rounded-lg" data-controller="tabs" data-tabs-active-tab="italic uppercase active">'.$content;
    $save = 'Save';

    if ( ! $nosave)
    {
        $content .= "<input class='ui button' type='submit' value='{$save}'>";
    }

    $content = $content.'</div>';

    if ($print)
    {
        \LotgdResponse::pageAddContent($content);
    }
    else
    {
        return $content;
    }
    unset($tabContent, $content, $tabMenu);
}

function lotgd_show_form_field($info, $row, $key, $keyout, $val, $extensions)
{
    $default = \explode('|', $info[1]);
    $title   = $default[0];
    $default = $default[1] ?? null;

    switch ($title) {
        case 'title':
        case 'note':
            break;
        case 'theme':
            // A generic way of allowing a theme to be selected.
            $skins  = [];
            $handle = @\opendir('templates');
            // Template directory open failed
            if ( ! $handle)
            {
                return 'None available';
            }

            while (false !== ($file = @\readdir($handle)))
            {
                if ('html' == \pathinfo($file, PATHINFO_EXTENSION))
                {
                    $skins[] = $file;
                }
            }
            // No templates installed!
            if (0 == \count($skins))
            {
                return 'None available';
            }
            \natcasesort($skins); //sort them in natural order
            $select = "<select class='ui lotgd dropdown' name='{$keyout}'>";

            foreach ($skins as $skin)
            {
                $name = \str_replace('-', ' ', \ucfirst(\substr($skin, 0, \strpos($skin, '.htm'))));
                $select .= "<option value='{$skin}' ".($skin == $row[$key] ? 'selected' : null).'>'.\htmlentities($name, ENT_COMPAT, LotgdSetting::getSetting('charset', 'UTF-8')).'</option>';
            }
            $select .= '</select>';

            return $select;

            break;
        case 'location':
            // A generic way of allowing the location to be specified for
            // things which only want to be in one place.  There are other
            // things which would be good to do as well of course, such
            // as making sure to handle village name changes in the module
            // that cares about this or what not, but this at least gives
            // some support.
            $location = '';

            if (isset($row[$key]))
            {
                $location = $row[$key];
            }

            $vloc         = [];
            $vname        = LotgdSetting::getSetting('villagename', LOCATION_FIELDS);
            $vloc[$vname] = 'village';
            $vloc['all']  = 1;
            $vloc         = modulehook('validlocation', $vloc);
            unset($vloc['all']);
            \reset($vloc);
            $select = "<select class='ui lotgd dropdown' name='{$keyout}'>";

            foreach ($vloc as $loc => $val)
            {
                $select .= "<option value='{$loc}' ".($loc == $location ? 'selected' : null).'>'.\htmlentities($loc, ENT_COMPAT, LotgdSetting::getSetting('charset', 'UTF-8')).'</option>';
            }
            $select .= '</select>';

            return $select;

            break;
        case 'checkpretrans':
            $pretrans = 1;
            // FALLTHROUGH
            // no break
        case 'checklist':
            \reset($info);
            list($k, $v) = \each($info);
            list($k, $v) = \each($info);
            $select      = '';

            while (list($k, $v) = \each($info))
            {
                $optval      = $v;
                list($k, $v) = \each($info);
                $optdis      = $v;

                if (\is_array($row[$key]))
                {
                    if ($row[$key][$optval])
                    {
                        $checked = true;
                    }
                    else
                    {
                        $checked = false;
                    }
                }
                else
                {
                    //any other ways to represent this?
                    \LotgdResponse::pageDebug('You must pass an array as the value when using a checklist.');
                    $checked = false;
                }
                $select .= "<div class='ui lotgd checkbox'><input type='checkbox' name='{$keyout}[{$optval}]' value='1'".($checked == $optval ? ' checked' : '').'><label>'.("{$optdis}").'</label></div>';
            }

            return $select;

            break;
        case 'radiopretrans':
            $pretrans = 1;
            // FALLTHROUGH
            // no break
        case 'radio':
            \reset($info);
            list($k, $v) = \each($info);
            list($k, $v) = \each($info);
            $select      = '';

            while (list($k, $v) = \each($info))
            {
                $optval      = $v;
                list($k, $v) = \each($info);
                $optdis      = $v;

                $select .= ("<div class='ui radio checkbox'><input type='radio' name='{$keyout}' value='{$optval}'".($row[$key] == $optval ? ' checked' : '').'><label>'.("{$optdis}").'</label></div>');
            }

            return $select;

            break;
        case 'dayrange':
            $start = \strtotime(\date('Y-m-d', \strtotime('now')));
            $end   = \strtotime($info[2]);
            $step  = $info[3];
            // we should really try to avoid an infinite loop here if
            // they define a time string which equates to 0 :/
            $cur    = $row[$key];
            $select = "<select class='ui lotgd dropdown' name='{$keyout}'>";

            if ($cur && $cur < \date('Y-m-d H:i:s', $start))
            {
                $select .= "<option value='{$cur}' selected>".\htmlentities($cur, ENT_COMPAT, LotgdSetting::getSetting('charset', 'UTF-8')).'</option>';
            }

            for ($j = $start; $j < $end; $j = \strtotime($step, $j))
            {
                $d = \date('Y-m-d H:i:s', $j);
                $select .= "<option value='{$d}'".($cur == $d ? ' selected' : '').'>'.\htmlentities("{$d}", ENT_COMPAT, LotgdSetting::getSetting('charset', 'UTF-8')).'</option>';
            }

            if ($cur && $cur > \date('Y-m-d H:i:s', $end))
            {
                $select .= "<option value='{$cur}' selected>".\htmlentities($cur, ENT_COMPAT, LotgdSetting::getSetting('charset', 'UTF-8')).'</option>';
            }
            $select .= '</select>';

            return $select;

            break;

        case 'range':
            $min  = (int) $info[2];
            $max  = (int) $info[3];
            $step = (int) ($info[4] ?? 0);

            if (0 == $step)
            {
                $step = 1;
            }
            $select = "<select class='ui lotgd dropdown' name='{$keyout}'>";

            if ($min < $max && ($max - $min) / $step > 300)
            {
                $step = \max(1, (int) (($max - $min) / 300));
            }

            for ($j = $min; $j <= $max; $j += $step)
            {
                $select .= "<option value='{$j}'".(isset($row[$key]) && $row[$key] == $j ? ' selected' : '').'>'.\htmlentities("{$j}", ENT_COMPAT, LotgdSetting::getSetting('charset', 'UTF-8')).'</option>';
            }
            $select .= '</select>';

            return $select;

            break;
        case 'floatrange':
            $min  = \round((float) $info[2], 2);
            $max  = \round((float) $info[3], 2);
            $step = \round((float) $info[4], 2);

            if (0 == $step)
            {
                $step = 1;
            }
            $select = "<select class='ui lotgd dropdown' name='{$keyout}'>";
            $val    = \round((float) ($row[$key] ?? 0), 2);

            for ($j = $min; $j <= $max; $j = \round($j + $step, 2))
            {
                $select .= "<option value='{$j}'".($val == $j ? ' selected' : '').'>'.\htmlentities("{$j}", ENT_COMPAT, LotgdSetting::getSetting('charset', 'UTF-8')).'</option>';
            }
            $select .= '</select>';

            return $select;

            break;
        case 'bitfieldpretrans':
            $pretrans = 1;
            // FALLTHROUGH
            // no break
        case 'bitfield':
            //format:
            //DisplayName,bitfield,disablemask,(highbit,display)+
            //1-26-03 added disablemask so this field type can be used
            // on bitfields other than superuser.
            \reset($info);
            \next($info);
            \next($info);
            list($k, $disablemask) = \each($info);
            $disablemask           = \trim($disablemask);
            $input                 = "<input type='hidden' name='{$keyout}"."[0]' value='1'>";

            while (list($k, $v) = \each($info))
            {
                if ('title' != $v)
                {
                    $input .= "<div class='ui toggle lotgd checkbox'><input type='checkbox' name='{$keyout}"."[{$v}]'"
                                    .(isset($row[$key]) && (int) $row[$key] & (int) $v ? ' checked' : '')
                                    .($disablemask & (int) $v ? '' : ' disabled')." value='1'> ";
                    list($k, $v) = \each($info);

                    $input .= \sprintf('<label>%s</label></div><br>', $v);
                }
                else
                {
                    list($k, $v) = \each($info);

                    $input .= \sprintf('%s<br>', $v);
                }
            }

            return '<div class="left floated">'.$input.'</div><br clear="all">';

        break;
        case 'datelength':
            // However, there was a bug with your translation code wiping
            // the key name for the actual form.  It's now fixed.
            // ok, I see that, but 24 hours and 1 day are the same
            // aren't they?
            $vals = [
                '1 hour', '2 hours', '3 hours', '4 hours',
                '5 hours', '6 hours', '8 hours', '10 hours',
                '12 hours', '16 hours', '18 hours', '24 hours',
                '1 day', '2 days', '3 days', '4 days', '5 days',
                '6 days', '7 days',
                '1 week', '2 weeks', '3 weeks', '4 weeks',
                '1 month', '2 months', '3 months', '4 months',
                '6 months', '9 months', '12 months',
                '1 year',
            ];

            foreach ($vals as $k => $v)
            {
                $vals[$k] = $v;
            }
            $select = "<select class='ui lotgd dropdown' name='{$keyout}'>";

            foreach ($vals as $k => $v)
            {
                $select .= '<option value="'.\htmlentities($v, ENT_COMPAT, LotgdSetting::getSetting('charset', 'UTF-8')).'"'.($row[$key] == $v ? ' selected' : '').'>'.\htmlentities($v, ENT_COMPAT, LotgdSetting::getSetting('charset', 'UTF-8')).'</option>';
            }
            $select .= '</select>';

            return $select;

            break;
        case 'enumpretrans':
            $pretrans = 1;
            // FALLTHROUGH
            // no break
        case 'enum':
            \reset($info);
            list($k, $v) = \each($info);
            list($k, $v) = \each($info);

            $select = "<select class='ui lotgd dropdown' name='{$keyout}'>";

            while (list($k, $v) = \each($info))
            {
                $optval      = $v;
                list($k, $v) = \each($info);
                $optdis      = $v;

                $selected = 0;

                if (isset($row[$key]) && $row[$key] == $optval)
                {
                    $selected = 1;
                }

                $select .= "<option value='{$optval}'".($selected ? ' selected' : '').'>'.\htmlentities("{$optdis}", ENT_COMPAT, LotgdSetting::getSetting('charset', 'UTF-8')).'</option>';
            }
            $select .= '</select>';

            return $select;

        break;
        case 'password':
            if (\array_key_exists($key, $row))
            {
                $out = $row[$key];
            }
            else
            {
                $out = '';
            }

            return "<input type='password' name='{$keyout}' value='".\htmlentities($out, ENT_COMPAT, LotgdSetting::getSetting('charset', 'UTF-8'))."'>";

        break;
        case 'bool':
            $value = $row[$key] ?? $default ?: 0;

            return '<div class="relative">
                <input type="hidden" name="'.$keyout.'" value="0" class="unstyle hidden">
                <input type="checkbox" value="1" name="'.$keyout.'" class="unstyle hidden" '.(1 == $value ? ' checked' : '').'>
                <!-- path -->
                <div class="toggle-path w-14 h-7 rounded-full shadow-inner"></div>
                <!-- circle -->
                <div class="toggle-circle absolute w-5 h-5 rounded-full shadow top-1 left-1"></div>
            </div>';

        case 'hidden':
            if (\array_key_exists($key, $row))
            {
                $val = $row[$key];
            }
            else
            {
                $val = '';
            }

            return "<input type='hidden' name='{$keyout}' value=\"".\htmlentities($val, ENT_COMPAT, LotgdSetting::getSetting('charset', 'UTF-8')).'">'.\htmlentities($val, ENT_COMPAT, LotgdSetting::getSetting('charset', 'UTF-8'));

        break;
        case 'viewonly':
            //don't unset it. it does not change, so nothing lost
            if (isset($row[$key]))
            {
                return '<span>'.dump_item($row[$key]).'</span>';
            }

            break;
        case 'viewhiddenonly':
            //don't unset it, transfer it, hide it. This is now used for legacy support of playernames that are empty and showform won't carry the name over to extract the real one
            if (isset($row[$key]))
            {
                $text = '<span>'.\LotgdFormat::colorize(dump_item($row[$key])).'</span>';
                $text .= "<input type='hidden' name='".\addslashes($key)."' value='".\addslashes($row[$key])."'>";

                return $text;
            }

            break;
        case 'readonly':
            if (isset($row[$key]))
            {
                return "<input type='text' readonly name='".\addslashes($key)."' value='".\addslashes($row[$key])."'>";
            }

        break;
        case 'rawtextarearesizeable':
        case 'textarearesizeable':
        case 'textarea':
            $text = $default ?: '';

            if (isset($row[$key]))
            {
                $text = $row[$key];
            }

            if ( ! isset($raw) || ! $raw)
            {
                $text = \str_replace('`n', "\n", $text);
            }

            $text = $text ?: '';

            return "<textarea class='input' name='{$keyout}'>".\htmlentities($text, ENT_COMPAT, LotgdSetting::getSetting('charset', 'UTF-8')).'</textarea>';

        case 'int':
            $out = $row[$key] ?? $default ?: 0;

            return "<input type='number' name='{$keyout}' value=\"".\htmlentities($out, ENT_COMPAT, LotgdSetting::getSetting('charset', 'UTF-8')).'">';

        case 'float':
            $text = $row[$key] ?? $default ?: 0;

            return "<input type='number' name='{$keyout}' value=\"".\htmlentities($text, ENT_COMPAT, LotgdSetting::getSetting('charset', 'UTF-8'))."\" step='any'>";

        case 'string':
            $len    = 50;
            $minlen = 50;

            if (isset($info[2]))
            {
                $len = (int) $info[2];
            }

            if ($len < $minlen)
            {
                $minlen = $len;
            }

            if ($len > $minlen)
            {
                $minlen = $len / 2;
            }

            if ($minlen > 70)
            {
                $minlen = 70;
            }

            if (\array_key_exists($key, $row))
            {
                $val = $row[$key];
            }
            else
            {
                $val = '';
            }

            return "<input type='text' size='{$minlen}' maxlength='{$len}' name='{$keyout}' value=\"".\htmlentities($val, ENT_COMPAT, LotgdSetting::getSetting('charset', 'UTF-8')).'">';

        default:
            if (\array_key_exists($info[1], $extensions))
            {
                $func = $extensions[$info[1]];

                if (\array_key_exists($key, $row))
                {
                    $val = $row[$key];
                }
                else
                {
                    $val = '';
                }
                \call_user_func($func, $keyout, $val, $info);
            }
            else
            {
                if (\array_key_exists($key, $row))
                {
                    $val = $row[$key];
                }
                else
                {
                    $val = '';
                }

                if ($val instanceof \DateTime)
                {
                    $val = $val->format(\DateTime::ISO8601);
                }

                return "<input type='text' name='{$keyout}' value=\"".\htmlentities($val, ENT_COMPAT, LotgdSetting::getSetting('charset', 'UTF-8')).'">';
            }

        break;
    }
}
