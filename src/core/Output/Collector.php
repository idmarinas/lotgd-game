<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Output;

\trigger_error(\sprintf(
    'Class %s is deprecated in 4.7.0 and deleted in future version, please use new clases and functions to replace this class.',
    Collector::class
), E_USER_DEPRECATED);

/**
 * @deprecated since 4.7.0; to be removed in future version, please use new clases and functions to replace this class..
 */
class Collector
{
    use \Lotgd\Core\Pattern\Container;
    use Pattern\Code;
    use Pattern\Color;

    protected $output           = ''; //!< the output to the template body
    protected $block_new_output = false; //!< is current output blocked? boolean
    protected $colormap_esc; //!< the letters of color codes only, escaped and not escaped
    protected $nestedtags = [
        'font' => false,
        'div'  => false,
        'em'   => false,
        'b'    => false,
        '<'    => false,
        '>'    => false,
        'B'    => false,
    ]; //!<open spans, or whatever...we need to make sure that we close them on output

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
     * Colormap for use with sanitize commands.
     *
     * @return string Returns only the codes with no spaces: $colorcode$colorcode...
     */
    public function get_colormap()
    {
        return \implode('', \array_keys($this->getColors()));
    }

    /**
     * Returns the Colormap like get_colormap() but escapes the dollar letter or the slash.
     *
     * @return string Returns only the codes with no spaces: $colorcode$colorcode...
     */
    public function get_colormap_escaped()
    {
        return \implode('', $this->get_colormap_escaped_array());
    }

    /**
     * Returns the Colormap like get_colormap() but escapes the dollar letter or the slash.
     *
     * @return array Returns only the codes as an array
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

        return \array_keys($cols);
    }
}
