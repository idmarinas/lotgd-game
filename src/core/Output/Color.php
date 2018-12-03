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
     * Array of codes format code => css tag
     *
     * @var array
     */
    protected $colors = [
        '1' => 'colDkBlue',
        '2' => 'colDkGreen',
        '3' => 'colDkCyan',
        '4' => 'colDkRed',
        '5' => 'colDkMagenta',
        '6' => 'colDkYellow',
        '7' => 'colDkWhite',
        '8' => 'colLime',
        '!' => 'colLtBlue',
        '@' => 'colLtGreen',
        '#' => 'colLtCyan',
        '$' => 'colLtRed',
        '%' => 'colLtMagenta',
        '^' => 'colLtYellow',
        '&' => 'colLtWhite',
        'q' => 'colDkOrange',
        'Q' => 'colLtOrange',
        ')' => 'colLtBlack',
        'R' => 'colRose',
        'V' => 'colBlueViolet',
        'v' => 'coliceviolet',
        'g' => 'colXLtGreen',
        'G' => 'colXLtGreen',
        'T' => 'colDkBrown',
        't' => 'colLtBrown',
        '~' => 'colBlack',
        'e' => 'colDkRust',
        'E' => 'colLtRust',
        'j' => 'colMdGrey',
        'J' => 'colMdBlue',
        'l' => 'colDkLinkBlue',
        'L' => 'colLtLinkBlue',
        'x' => 'colburlywood',
        'X' => 'colbeige',
        'y' => 'colkhaki',
        'Y' => 'coldarkkhaki',
        'k' => 'colaquamarine',
        'K' => 'coldarkseagreen',
        'p' => 'collightsalmon',
        'P' => 'colsalmon',
        'm' => 'colwheat',
        'M' => 'coltan',
    ];

    /**
     * Remplace original game colors.
     *
     * @param array $colors
     *
     * @return $this
     */
    public function setColors(array $colors)
    {
        $this->colors = $colors;

        return $this;
    }

    /**
     * Add more colors to game
     *
     * @param array $colors
     *
     * @return $this
     */
    public function addColors(array $colors)
    {
        $this->colors = array_merge($this->colors, $colors);

        return $this;
    }

    /**
     * Get array of colors
     *
     * @return array
     */
    public function getColors(): array
    {
        return $this->colors;
    }
}
