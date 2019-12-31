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
    use Pattern\Color;
    use Pattern\Code;

    protected $output = ''; //!< the output to the template body
    protected $block_new_output = false; //!< is current output blocked? boolean
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
        if (! ($session['user']['loggedin'] ?? false))
        {
            return $out;
        }

        //-- Sustitute placeholders
        $sustitute = [
            '{playername}' => $session['user']['playername'],
            '{charactername}' => $session['user']['name'],
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
        $text = $this->output;
        //clean up unclosed output tags.
        foreach (array_keys($this->nestedtags) as $key => $val)
        {
            if ('font' == $key)
            {
                $key = 'span';
            }

            if (true === $val)
            {
                $text .= '</'.$key.'>';
            }
        }

        return $text;
    }

    /**
     * Returns the formatted output.
     *
     * @return the complete output WITHOUT closing open tags
     */
    public function get_rawoutput()
    {
        return $this->output;
    }

    /**
     * If you want to block new output, this is your function.
     *
     * @param bool $block
     */
    public function set_block_new_output($block)
    {
        $this->block_new_output = (bool) $block;
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
     * This function puts the lotgd formatting `whatever into HTML tags.
     *
     * @param string $data the logd formatted string
     */
    public function appoencode(string $data)
    {
        $patternOpen = $this->getColorPatternOpen();
        $patternClose = $this->getColorPatternClose();
        $replacementOpen = $this->getColorReplacementOpen();

        $data = str_replace($patternOpen, $replacementOpen, $data);
        $data = str_replace($patternClose, '</span>', $data);

        //-- Replace codes of string
        $patternOpen = $this->getCodePatternOpen();
        $patternClose = $this->getCodePatternClose();
        $replacementOpen = $this->getCodeReplacementOpen();
        $replacementClose = $this->getCodeReplacementClose();

        $data = str_replace($patternOpen, $replacementOpen, $data);
        $data = str_replace($patternClose, $replacementClose, $data);

        //-- Special codes
        $patternOpen = $this->getCodeSpecialPatternOpen();
        $patternClose = $this->getCodeSpecialPatternClose();
        $replacementOpen = $this->getCodeSpecialReplacementOpen();
        $replacementClose = $this->getCodeSpecialReplacementClose();

        $data = str_replace($patternOpen, $replacementOpen, $data);

        return str_replace($patternClose, $replacementClose, $data);
    }

    /**
     * Colormap for use with sanitize commands.
     *
     * @return Returns only the codes with no spaces: $colorcode$colorcode...
     */
    public function get_colormap()
    {
        return implode('', array_keys($this->getColors()));
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
        $cols = $this->getColors();
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
