<?php

// translator ready
// addnews ready
// mail ready
require_once 'lib/dump_item.php';

/**
 * Construct FORM LOTGD style.
 *
 * @var array
 * @var array         $row
 * @var bool          $nosave
 * @var false|pattern $keypref
 * @var bool          $print
 * @var callable      $callback This can use for personalize the form used to show all inputs or for more process.
 *                    Can still use lotgd_show_form_field in your callable
 *                    Both functions get same parameters function($info, $row, $key, $keyout, $val, $extensions)
 */
function lotgd_showform($layout, $row, $nosave = false, $keypref = false, $print = true, callable $callback = null)
{
    static $showform_id = 0;
    static $title_id = 0;

    $showform_id++;
    $extensions = modulehook('showformextensions', []);

    $i = false;
    $tabMenu = [];
    $tabContent = [];
    $tabActive = '';

    foreach ($layout as $key => $val)
    {
        if (false !== $keypref)
        {
            $keyout = sprintf($keypref, $key);
        }
        else
        {
            $keyout = $key;
        }

        if (is_array($val))
        {
            $info = explode(',', $val[0]);
            $val[0] = $info[0];
            $info[0] = $val;
        }
        else
        {
            $info = explode(',', $val);
        }

        if (is_array($info[0]))
        {
            $info[0] = call_user_func_array('sprintf_translate', $info[0]);
        }
        else
        {
            $info[0] = translate($info[0]);
        }

        if (isset($info[1]))
        {
            $info[1] = trim($info[1]);
        }
        else
        {
            $info[1] = '';
        }

        if ('title' == $info[1])
        {
            $title_id++;

            if (1 == $title_id)
            {
                $tabActive = $info[0];
                $tabMenu[] = sprintf('<a class="item active" data-tab="tab-%s">%s</a>', $title_id, $info[0]);
            }
            else
            {
                $tabMenu[] = sprintf('<a class="item" data-tab="tab-%s">%s</a>', $title_id, $info[0]);
            }
        }
        elseif ('note' == $info[1])
        {
            $tabContent[$title_id][] = sprintf('<div class="ui small info message">%s</div>', color_sanitize($info[0]));
        }
        else
        {
            if (! $callback)
            {
                $result = lotgd_show_form_field($info, $row, $key, $keyout, $val, $extensions);
            }
            else
            {
                $result = $callback($info, $row, $key, $keyout, $val, $extensions);
            }

            $tabContent[$title_id][] = sprintf('<div class="inline field"><label>%s</label>%s</div>',
                appoencode($info[0]),
                $result
            );

            $i = ! $i;
        }
    }

    $content = '';

    foreach ($tabContent as $key => $value)
    {
        $text = sprintf('<div class="ui form">%s</div>',
            implode('', $value)
        );

        if (0 < $key)
        {
            $text = sprintf('<div class="ui tab segment %s" data-tab="tab-%s">%s</div>',
            (1 == $key ? 'active' : null),
            $key,
            $text
        );
        }

        $content .= $text;
    }

    unset($text);

    if (! empty($tabMenu))
    {
        $tabMenu = array_chunk($tabMenu , ceil(count($tabMenu)/4));

        $popupMenu = '<div class="ui flowing popup transition hidden lotgd form">';
        $popupMenu .= '<div class="ui stackable equal width divided grid">';
        foreach($tabMenu as $menu)
        {
            $popupMenu .= '<div class="column"><div class="ui list">';
            $popupMenu .= implode('', $menu);
            $popupMenu .= '</div></div>';
        }
        $popupMenu .= '</div></div>';

        $menu = sprintf('<div class="ui menu lotgd form "><a class="browse item active">%s <i class="dropdown icon"></i></a>%s<div class="header item">%s</div></div>',
            translate_inline('Browse'),
            $popupMenu,
            $tabActive
        );

        $content = $menu . $content;

        unset($popupMenu, $menu);
    }

    if ($print)
    {
        rawoutput($content);
    }

    tlschema('showform');
    $save = translate_inline('Save');
    tlschema();

    if (! $nosave)
    {
        if ($print)
        {
            rawoutput("<input class='ui button' type='submit' value='$save'>");
        }
        else
        {
            $content .= "<input class='ui button' type='submit' value='$save'>";
        }
    }

    if (! $print)
    {
        return $content;
    }

    unset($tabContent, $content, $tabMenu);
}

function lotgd_show_form_field($info, $row, $key, $keyout, $val, $extensions)
{
    switch ($info[1])
    {
        case 'title':
        case 'note':
            break;
        case 'theme':
            // A generic way of allowing a theme to be selected.
            $skins = [];
            $handle = @opendir('data/template');
            // Template directory open failed
            if (! $handle)
            {
                return 'None available';
            }

            while (false !== ($file = @readdir($handle)))
            {
                if ('html' == pathinfo($file, PATHINFO_EXTENSION))
                {
                    $skins[] = $file;
                }
            }
            // No templates installed!
            if (0 == count($skins))
            {
                return 'None available';
            }
            natcasesort($skins); //sort them in natural order
            $select = "<select class='ui dropdown' name='$keyout'>";

            foreach ($skins as $skin)
            {
                $name = str_replace('-', ' ', ucfirst(substr($skin, 0, strpos($skin, '.htm'))));
                $select .= "<option value='$skin' ".($skin == $row[$key] ? 'selected' : null).'>'.htmlentities($name, ENT_COMPAT, getsetting('charset', 'UTF-8')).'</option>';
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

            $vloc = [];
            $vname = getsetting('villagename', LOCATION_FIELDS);
            $vloc[$vname] = 'village';
            $vloc['all'] = 1;
            $vloc = modulehook('validlocation', $vloc);
            unset($vloc['all']);
            reset($vloc);
            $select = "<select class='ui dropdown' name='$keyout'>";

            foreach ($vloc as $loc => $val)
            {
                $select .= "<option value='$loc' ".($loc == $location ? 'selected' : null).'>'.htmlentities($loc, ENT_COMPAT, getsetting('charset', 'UTF-8')).'</option>';
            }
            $select .= '</select>';

            return $select;
            break;
        case 'checkpretrans':
            $pretrans = 1;
            // FALLTHROUGH
            // no break
        case 'checklist':
            reset($info);
            list($k, $v) = each($info);
            list($k, $v) = each($info);
            $select = '';

            while (list($k, $v) = each($info))
            {
                $optval = $v;
                list($k, $v) = each($info);
                $optdis = $v;

                if (! $pretrans)
                {
                    $optdis = translate_inline($optdis);
                }

                if (is_array($row[$key]))
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
                    debug('You must pass an array as the value when using a checklist.');
                    $checked = false;
                }
                $select .= "<div class='ui checkbox'><input type='checkbox' name='{$keyout}[{$optval}]' value='1'".($checked == $optval ? ' checked' : '').'><label>'.("$optdis").'</label></div>';
            }

            return $select;
            break;
        case 'radiopretrans':
            $pretrans = 1;
            // FALLTHROUGH
            // no break
        case 'radio':
            reset($info);
            list($k, $v) = each($info);
            list($k, $v) = each($info);
            $select = '';

            while (list($k, $v) = each($info))
            {
                $optval = $v;
                list($k, $v) = each($info);
                $optdis = $v;

                if (! $pretrans)
                {
                    $optdis = translate_inline($optdis);
                }
                $select .= ("<div class='ui radio checkbox'><input type='radio' name='$keyout' value='$optval'".($row[$key] == $optval ? ' checked' : '').'><label>'.("$optdis").'</label></div>');
            }

            return $select;

            break;
        case 'dayrange':
            $start = strtotime(date('Y-m-d', strtotime('now')));
            $end = strtotime($info[2]);
            $step = $info[3];
            // we should really try to avoid an infinite loop here if
            // they define a time string which equates to 0 :/
            $cur = $row[$key];
            $select = "<select class='ui dropdown' name='$keyout'>";

            if ($cur && $cur < date('Y-m-d H:i:s', $start))
            {
                $select .= "<option value='$cur' selected>".htmlentities($cur, ENT_COMPAT, getsetting('charset', 'UTF-8')).'</option>';
            }

            for ($j = $start; $j < $end; $j = strtotime($step, $j))
            {
                $d = date('Y-m-d H:i:s', $j);
                $select .= "<option value='$d'".($cur == $d ? ' selected' : '').'>'.htmlentities("$d", ENT_COMPAT, getsetting('charset', 'UTF-8')).'</option>';
            }

            if ($cur && $cur > date('Y-m-d H:i:s', $end))
            {
                $select .= "<option value='$cur' selected>".htmlentities($cur, ENT_COMPAT, getsetting('charset', 'UTF-8')).'</option>';
            }
            $select .= '</select>';

            return $select;
            break;

        case 'range':
            $min = (int) $info[2];
            $max = (int) $info[3];
            $step = (int) ($info[4] ?? 0);

            if (0 == $step)
            {
                $step = 1;
            }
            $select = "<select class='ui dropdown' name='$keyout'>";

            if ($min < $max && ($max - $min) / $step > 300)
            {
                $step = max(1, (int) (($max - $min) / 300));
            }

            for ($j = $min; $j <= $max; $j += $step)
            {
                $select .= "<option value='$j'".(isset($row[$key]) && $row[$key] == $j ? ' selected' : '').'>'.htmlentities("$j", ENT_COMPAT, getsetting('charset', 'UTF-8')).'</option>';
            }
            $select .= '</select>';

            return $select;
            break;
        case 'floatrange':
            $min = round((float) $info[2], 2);
            $max = round((float) $info[3], 2);
            $step = round((float) $info[4], 2);

            if (0 == $step)
            {
                $step = 1;
            }
            $select = "<select class='ui dropdown' name='$keyout'>";
            $val = round((float) ($row[$key] ?? 0), 2);

            for ($j = $min; $j <= $max; $j = round($j + $step, 2))
            {
                $select .= "<option value='$j'".($val == $j ? ' selected' : '').'>'.htmlentities("$j", ENT_COMPAT, getsetting('charset', 'UTF-8')).'</option>';
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
            reset($info);
            next($info);
            next($info);
            list($k, $disablemask) = each($info);
            $disablemask = trim($disablemask);
            $input = "<input type='hidden' name='$keyout"."[0]' value='1'>";

            while (list($k, $v) = each($info))
            {
                if ('title' != $v)
                {
                    $input .= "<div class='ui toggle checkbox'><input type='checkbox' name='$keyout"."[$v]'"
                                    .(isset($row[$key]) && (int) $row[$key] & (int) $v ? ' checked' : '')
                                    .($disablemask & (int) $v ? '' : ' disabled')." value='1'> ";
                    list($k, $v) = each($info);

                    if (! isset($pretrans) || ! $pretrans)
                    {
                        $v = translate_inline($v);
                    }
                    $input .= sprintf('<label>%s</label></div><br>', $v);
                }
                else
                {
                    list($k, $v) = each($info);

                    if (! isset($pretrans) || ! $pretrans)
                    {
                        $v = translate_inline($v);
                    }
                    $input .= sprintf('%s<br>', $v);
                }
            }

            return '<div class="right floated">'.$input.'</div>';
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
                '1 year'
            ];
            tlschema('showform');

            foreach ($vals as $k => $v)
            {
                $vals[$k] = translate($v);
                rawoutput(tlbutton_pop());
            }
            tlschema();
            $select = "<select class='ui dropdown' name='$keyout'>";

            foreach ($vals as $k => $v)
            {
                $select .= '<option value="'.htmlentities($v, ENT_COMPAT, getsetting('charset', 'UTF-8')).'"'.($row[$key] == $v ? ' selected' : '').'>'.htmlentities($v, ENT_COMPAT, getsetting('charset', 'UTF-8')).'</option>';
            }
            $select .= '</select>';

            return $select;
            break;
        case 'enumpretrans':
            $pretrans = 1;
            // FALLTHROUGH
            // no break
        case 'enum':
            reset($info);
            list($k, $v) = each($info);
            list($k, $v) = each($info);

            $select = "<select class='ui dropdown' name='$keyout'>";

            while (list($k, $v) = each($info))
            {
                $optval = $v;
                list($k, $v) = each($info);
                $optdis = $v;

                if (! isset($pretrans) || ! $pretrans)
                {
                    $optdis = translate_inline($optdis);
                }
                $selected = 0;

                if (isset($row[$key]) && $row[$key] == $optval)
                {
                    $selected = 1;
                }

                $select .= "<option value='$optval'".($selected ? ' selected' : '').'>'.htmlentities("$optdis", ENT_COMPAT, getsetting('charset', 'UTF-8')).'</option>';
            }
            $select .= '</select>';

            return $select;

        break;
        case 'password':
            if (array_key_exists($key, $row))
            {
                $out = $row[$key];
            }
            else
            {
                $out = '';
            }

            return "<input type='password' name='$keyout' value='".htmlentities($out, ENT_COMPAT, getsetting('charset', 'UTF-8'))."'>";
        break;
        case 'bool':
            tlschema('showform');
            $yes = translate_inline('Yes');
            $no = translate_inline('No');
            tlschema();

            $select = '<div class="ui toggle checkbox">';
            $select .= '<input type="hidden" name="'.$keyout.'" value="0">';
            $select .= '<input type="checkbox" value="1" name="'.$keyout.'" '.(isset($row[$key]) && 1 == $row[$key] ? ' checked' : '').'>';
            $select .= '</div>';

            return $select;

        break;
        case 'hidden':
            if (array_key_exists($key, $row))
            {
                $val = $row[$key];
            }
            else
            {
                $val = '';
            }

            return "<input type='hidden' name='$keyout' value=\"".htmlentities($val, ENT_COMPAT, getsetting('charset', 'UTF-8')).'">'.htmlentities($val, ENT_COMPAT, getsetting('charset', 'UTF-8'));

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
                $text = '<span>'.appoencode(dump_item($row[$key])).'</span>';
                $text .= "<input type='hidden' name='".addslashes($key)."' value='".addslashes($row[$key])."'>";

                return $text;
            }
            break;
        case 'readonly':
            if (isset($row[$key]))
            {
                return "<input type='text' readonly name='".addslashes($key)."' value='".addslashes($row[$key])."'>";
            }
        break;
        case 'rawtextarearesizeable':
        case 'textarearesizeable':
        case 'textarea':
            $text = '';

            if (isset($row[$key]))
            {
                $text = $row[$key];
            }

            if (! isset($raw) || ! $raw)
            {
                $text = str_replace('`n', "\n", $text);
            }

            return "<textarea class='input' name='$keyout'>".htmlentities($text, ENT_COMPAT, getsetting('charset', 'UTF-8')).'</textarea>';

        case 'int':
            $out = $row[$key] ?? 0;

            return "<input type='number' name='$keyout' value=\"".htmlentities($out, ENT_COMPAT, getsetting('charset', 'UTF-8')).'">';

        case 'float':
            $text = $row[$key] ?? '';

            return "<input type='number' name='$keyout' value=\"".htmlentities($text, ENT_COMPAT, getsetting('charset', 'UTF-8'))."\" step='any'>";

        case 'string':
            $len = 50;
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

            if (array_key_exists($key, $row))
            {
                $val = $row[$key];
            }
            else
            {
                $val = '';
            }

            return "<input size='$minlen' maxlength='$len' name='$keyout' value=\"".htmlentities($val, ENT_COMPAT, getsetting('charset', 'UTF-8')).'">';

        default:
            if (array_key_exists($info[1], $extensions))
            {
                $func = $extensions[$info[1]];

                if (array_key_exists($key, $row))
                {
                    $val = $row[$key];
                }
                else
                {
                    $val = '';
                }
                call_user_func($func, $keyout, $val, $info);
            }
            else
            {
                if (array_key_exists($key, $row))
                {
                    $val = $row[$key];
                }
                else
                {
                    $val = '';
                }

                return "<input type='text' name='$keyout' value=\"".htmlentities($val, ENT_COMPAT, getsetting('charset', 'UTF-8')).'">';
            }
        break;
    }
}
