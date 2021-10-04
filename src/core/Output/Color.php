<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Output;

class Color
{
    /**
     * Array of codes format code => css tag.
     *
     * @var array
     */
    protected $colors = [
        '1' => 'text-col-dk-blue',
        '2' => 'text-col-dk-green',
        '3' => 'text-col-dk-cyan',
        '4' => 'text-col-dk-red',
        '5' => 'text-col-dk-magenta',
        '6' => 'text-col-dk-yellow',
        '7' => 'text-col-dk-white',
        '8' => 'text-col-lime',
        '!' => 'text-col-lt-blue',
        '@' => 'text-col-lt-green',
        '#' => 'text-col-lt-cyan',
        '$' => 'text-col-lt-red',
        '%' => 'text-col-lt-magenta',
        '^' => 'text-col-lt-yellow',
        '&' => 'text-col-lt-white',
        'q' => 'text-col-dk-orange',
        'Q' => 'text-col-lt-orange',
        ')' => 'text-col-lt-black',
        'R' => 'text-col-rose',
        'V' => 'text-col-blue-violet',
        'v' => 'text-col-iceviolet',
        'g' => 'text-col-x-lt-green',
        'G' => 'text-col-x-lt-green',
        'T' => 'text-col-dk-brown',
        't' => 'text-col-lt-brown',
        '~' => 'text-col-black',
        'e' => 'text-col-dk-rust',
        'E' => 'text-col-lt-rust',
        'j' => 'text-col-md-grey',
        'J' => 'text-col-md-blue',
        'l' => 'text-col-dk-link-blue',
        'L' => 'text-col-lt-link-blue',
        'x' => 'text-col-burlywood',
        'X' => 'text-col-beige',
        'y' => 'text-col-khaki',
        'Y' => 'text-col-darkkhaki',
        'k' => 'text-col-aquamarine',
        'K' => 'text-col-darkseagreen',
        'p' => 'text-col-lightsalmon',
        'P' => 'text-col-salmon',
        'm' => 'text-col-wheat',
        'M' => 'text-coltan',
    ];

    /**
     * Remplace original game colors.
     *
     * @return $this
     */
    public function setColors(array $colors)
    {
        $this->colors = $colors;

        return $this;
    }

    /**
     * Add more colors to game.
     *
     * @return $this
     */
    public function addColors(array $colors)
    {
        $this->colors = \array_merge($this->colors, $colors);

        return $this;
    }

    /**
     * Get array of colors.
     */
    public function getColors(): array
    {
        return $this->colors;
    }
}
