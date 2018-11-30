<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Output;

class Collector
{
    use \Lotgd\Core\Pattern\Container;

    protected $colors;
    protected $output = ''; //!< the output to the template body
    protected $block_new_output = false; //!< is current output blocked? boolean
    protected $color_map;
    protected $colormap_esc; //!< the letters of color codes only, escaped and not escaped
    protected $nestedtags = [
        'font' => false,
        'div' => false,
        'em' => false,
        'b' => false,
        '<' => false,
        '>' => false,
        'B' => false,
    ]; //!<open spans, or whatever...we need to make sure that we close them on output

    /**
     * Raw output (unprocessed) appended to the output buffer.
     *
     * @param $indata the raw material to be outputted
     */
    public function rawoutput($indata)
    {
        if ($this->block_new_output)
        {
            return;
        }

        if (is_string($indata) && ! is_null($indata))
        {
            $this->output .= $indata."\n";
        }
    }

    /**
     * Handles color and style encoding, and appends to the output buffer ($output).  It is usually called with output_notl($indata,...). If an array is passed then the format for sprintf is assumed otherwise a simple string is assumed.
     *
     * @see appoencode
     */
    public function output_notl()
    {
        if ($this->block_new_output)
        {
            return;
        }

        $args = func_get_args();
        $length = count($args);
        //get 'true' off the end if we have it
        $last = $args[$length - 1];

        if (true !== $last)
        {
            $priv = false;
        }
        else
        {
            unset($args[$length - 1]);
            $priv = true;
        }
        // $out = $indata;
        // $args[0]=&$out;
        //apply variables
        $out = &$args[0];
        $out = $this->sustitute($out);

        if (count($args) > 1)
        {
            //special case since we use `% as a color code so often.
            $out = str_replace('`%', '`%%', $out);
            $out = call_user_func_array('sprintf', $args);
        }
        //holiday text
        if (false == $priv)
        {
            $out = holidayize($out, 'output');
        }
        //`1`2 etc color & formatting
        $out = $this->appoencode($out, $priv);
        //apply to the page.
        $this->output .= tlbutton_pop().$out."\n";
    }

    /**
     * Outputs a translated, color/style encoded string to the browser.
     *
     * Argument in: What to output. If an array is passed then the format used by sprintf is assumed
     *
     * @see output_notl
     */
    public function output()
    {
        if ($this->block_new_output)
        {
            return;
        }
        $args = func_get_args();

        if (is_array($args[0]))
        {
            $args = $args[0];
        }

        if (is_bool($args[0]) && array_shift($args))
        {
            $schema = array_shift($args);
            $args[0] = translate($args[0], $schema);
        }
        else
        {
            $args[0] = translate($args[0]);
        }
        //in an object, call the function with an array pointing to object and then function, make sure we add this  to *our* object
        call_user_func_array([&$this, 'output_notl'], $args);
    }

    /**
     * Search and replace keywords.
     *
     * @param string $out
     *
     * @return string
     */
    public function sustitute(string $out): string
    {
        global $session;

        //-- This options are only available when user are signed in
        if (! ($session['loggedin'] ?? false))
        {
            return $out;
        }

        //-- Sustitute placeholders
        $sustitute = [
            '{playername}' => $session['user']['name'],
            '{playerweapon}' => $session['user']['weapon'],
            '{playerarmor}' => $session['user']['armor']
        ];

        return str_replace(array_keys($sustitute), array_values($sustitute), $out);
    }

    /**
     * Returns the formatted output.
     *
     * @return the complete output for the {content} tag
     */
    public function get_output()
    {
        $output = $this->output;
        //clean up unclosed output tags.
        foreach (array_keys($this->nestedtags) as $key => $val)
        {
            if ('font' == $key)
            {
                $key = 'span';
            }

            if (true === $val)
            {
                $output .= '</'.$key.'>';
            }
        }

        return $output;
    }

    /**
     * Returns the formatted output.
     *
     * @return the complete output WITHOUT closing open tags
     */
    public function get_rawoutput()
    {
        $output = $this->output;

        return $output;
    }

    /**
     * If you want to block new output, this is your function.
     *
     * @param $block boolean or 0,1 or similar
     */
    public function set_block_new_output($block)
    {
        $this->block_new_output = ($block ? true : false);
    }

    /**
     * Returns if new output is blocked or not.
     *
     * @return bool
     */
    public function get_block_new_output()
    {
        return $this->block_new_output;
    }

    /**
     * Lets you display debug output (specially formatted, optionally only visible to SU_DEBUG users).
     *
     * @param $text The input text or variable to debug, string
     * @param $force Default is false, if true it will always be outputted to ANY user. If false, only SU_DEBUG will see it.
     */
    public function debug($text, $force = false)
    {
        global $session;
        $temp = $this->get_block_new_output();
        $this->set_block_new_output(false);

        if ($force || isset($session['user']['superuser']) && $session['user']['superuser'] & SU_DEBUG_OUTPUT)
        {
            $this->rawoutput('<div class="debug">'.\Zend\Debug\Debug::dump($text, null, false).'</div>');
        }
        $this->set_block_new_output($temp);
    }

    /**
     * This function puts the lotgd formatting `whatever into HTML tags. It will automatically close previous tags before opening new ones for the same class.
     *
     * @param string $data the logd formatted string
     * @param $priv If true, it uses no htmlentites before outputting to the browser, means it will parse HTML code through. Default is false
     */
    public function appoencode(string $data, $priv = false)
    {
        $start = 0;
        $out = '';
        $colors = $this->get_colors();

        if (false !== ($pos = strpos($data, '`')))
        {
            do
            {
                $pos++;

                if (false === $priv)
                {
                    $out .= htmlentities(substr($data, $start, $pos - $start - 1), ENT_COMPAT, getsetting('charset', 'UTF-8'));
                }
                else
                {
                    $out .= substr($data, $start, $pos - $start - 1);
                }
                $start = $pos + 1;

                if (isset($colors[$data[$pos]]))
                {
                    if ($this->nestedtags['font'])
                    {
                        $out .= '</span>';
                    }
                    else
                    {
                        $this->nestedtags['font'] = true;
                    }
                    $out .= "<span class='".$colors[$data[$pos]]."'>";
                }
                else
                {
                    switch ($data[$pos])
                    {
                        case 'n':
                            $out .= "<br>\n";
                            break;
                        case '0':
                            if (isset($this->nestedtags['font']) && $this->nestedtags['font'])
                            {
                                $out .= '</span>';
                            }
                            $this->nestedtags['font'] = false;
                            break;
                        case 'b':
                            if (isset($this->nestedtags['b']) && $this->nestedtags['b'])
                            {
                                $out .= '</b>';
                                $this->nestedtags['b'] = false;
                            }
                            else
                            {
                                $this->nestedtags['b'] = true;
                                $out .= '<b>';
                            }
                            break;
                        case 'i':
                            if (isset($this->nestedtags['em']) && $this->nestedtags['em'])
                            {
                                $out .= '</em>';
                                $this->nestedtags['em'] = false;
                            }
                            else
                            {
                                $this->nestedtags['em'] = true;
                                $out .= '<em>';
                            }
                            break;
                        case 'c':
                            if (isset($this->nestedtags['div']) && $this->nestedtags['div'])
                            {
                                $out .= '</div>';
                                $this->nestedtags['div'] = false;
                            }
                            else
                            {
                                $this->nestedtags['div'] = true;
                                $out .= "<div class='center aligned'>";
                            }
                            break;
                        case 'B':
                            if (isset($this->nestedtags['B']) && $this->nestedtags['B'])
                            {
                                $out .= '</em>';
                                $this->nestedtags['B'] = false;
                            }
                            else
                            {
                                $this->nestedtags['B'] = true;
                                $out .= '<em>';
                            }
                            break;
                        case '>':
                            if (isset($this->nestedtags['>']) && $this->nestedtags['>'])
                            {
                                $this->nestedtags['>'] = false;
                                $out .= '</div>';
                            }
                            else
                            {
                                $this->nestedtags['>'] = true;
                                $out .= "<div style='float: right; clear: right;'>";
                            }
                            break;
                        case '<':
                            if (isset($this->nestedtags['<']) && $this->nestedtags['<'])
                            {
                                $this->nestedtags['<'] = false;
                                $out .= '</div>';
                            }
                            else
                            {
                                $this->nestedtags['<'] = true;
                                $out .= "<div style='float: left; clear: left;'>";
                            }
                            break;
                        case 'H':
                            if (isset($this->nestedtags['span']) && $this->nestedtags['span'])
                            {
                                $out .= '</span>';
                                $this->nestedtags['span'] = false;
                            }
                            else
                            {
                                $this->nestedtags['span'] = true;
                                $out .= "<span class='navhi'>";
                            }
                            break;
                        case 'w':
                            global $session;

                            if (! isset($session['user']['weapon']))
                            {
                                $session['user']['weapon'] = '';
                            }
                            $out .= sanitize($session['user']['weapon']);
                            break;
                        case '`':
                            $out .= '`';
                            $pos++;
                            break;
                        default:
                            $out .= '`'.$data[$pos];
                    }
                }
            } while (false !== ($pos = strpos($data, '`', $pos)));
        }

        if (false === $priv)
        {
            $out .= htmlentities(substr($data, $start), ENT_COMPAT, getsetting('charset', 'UTF-8'));
        }
        else
        {
            $out .= substr($data, $start);
        }

        return $out;
    }

    /**
     * Returns the complete color array.
     *
     * @return an array with $colorcode=>$csstag format
     */
    public function get_colors()
    {
        if (! $this->colors)
        {
            $colors = $this->getContainer(\Lotgd\Core\Output\Color::class);
            $this->colors = $colors;
        }

        return $this->colors->getColors();
    }

    /**
     * Colormap for use with sanitize commands.
     *
     * @return Returns only the codes with no spaces: $colorcode$colorcode...
     */
    public function get_colormap()
    {
        return implode('', array_keys($this->get_colors()));
    }

    /**
     * Returns the Colormap like get_colormap() but escapes the dollar letter or the slash.
     *
     * @return Returns only the codes with no spaces: $colorcode$colorcode...
     */
    public function get_colormap_escaped()
    {
        return implode('', $this->get_colormap_escaped_array());
    }

    /**
     * Returns the Colormap like get_colormap() but escapes the dollar letter or the slash.
     *
     * @return Returns only the codes as an array
     */
    public function get_colormap_escaped_array()
    {
        $cols = $this->get_colors();
        //*cough* if you choose color codes like \ or whatnot... SENSITIVE codes like special programmer chars... then escape them. Sadly we have % (breaks sprintf i.e.) AND ) in it... (breaks regular expressions)
        $escape = [')', '$', '(', '[', ']', '{', '}'];

        foreach ($escape as $letter)
        {
            if (isset($cols[$letter]))
            {
                $cols['\\'.$letter] = $cols[$letter];
            }
            unset($cols[$letter]);
        }

        return array_keys($cols);
    }
}
