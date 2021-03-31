<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 4.0.0
 */

namespace Lotgd\Bundle\CoreBundle\Tool;

use Lotgd\Bundle\CoreBundle\Tool\Code;
use Lotgd\Bundle\CoreBundle\Tool\Color;

class Sanitize
{
    protected $color;
    protected $code;

    public function __construct(Color $color, Code $code)
    {
        $this->color = $color;
        $this->code  = $code;
    }

    /**
     * Remove all colors code from string.
     * ONLY colors, not include italic, bold... (`i `b).
     */
    public function noLotgdColor(string $string): string
    {
        $colors = \preg_quote(\implode('', \array_keys($this->color->getColors())));

        return \preg_replace("/[`´][0{$colors}]/u", '', $string);
    }

    /**
     * Remove all codes from string.
     * ONLY codes, not includes colors.
     */
    public function noLotgdCode(string $string): string
    {
        $codes = \preg_quote(\implode('', \array_keys($this->code->getCodes())));

        return \preg_replace("/[`´][{$codes}]/u", '', $string);
    }

    /**
     * Full sanitize a string, removed color and codes.
     * All LoTGD codes included (`i `b `c) and others.
     */
    public function fullSanitize(string $string): string
    {
        return \preg_replace('/[`´]./u', '', $string);
    }

    /**
     * Prevent for use LoTGD codes like colors and italic, bold...
     */
    public function preventLotgdFormat(string $string): string
    {
        return \str_replace(['`', '´'], ['&#96;', '&#180;'], $string);
    }

    /**
     * Sanitize name of player.
     *
     * @param int $spaceAllowed
     */
    public function nameSanitize($spaceAllowed, string $name): string
    {
        $expr = '/[^[:alpha:]]/';

        if ($spaceAllowed)
        {
            $expr = '/[^[:alpha:] _-]/';
        }

        return \preg_replace($expr, '', $name);
    }

    /**
     * Sanitize color name of player.
     * Handle spaces and color in character names.
     *
     * @param int  $spaceallowed
     * @param bool $admin
     */
    public function colorNameSanitize($spaceallowed, string $string, $admin = null): string
    {
        $colors = \preg_quote(\implode('', $this->color->getColors()));

        if ($admin && getsetting('allowoddadminrenames', 0))
        {
            return $string;
        }

        $expr = "/([^[:alpha:]`´0{$colors}])/u";

        if ($spaceallowed)
        {
            $expr = "/([^[:alpha:]`´0{$colors} _-])/u";
        }

        return \preg_replace($expr, '', $string);
    }

    /**
     * Remove new line code from string.
     */
    public function newLineSanitize(string $string): string
    {
        return \preg_replace('/[`´]n/u', '', $string);
    }

    /**
     * Sanitize a string to ve valid mb encoding.
     */
    public function mbSanitize(string $string): string
    {
        $encode = getsetting('charset', 'UTF-8');

        while ( ! \mb_check_encoding($string, $encode) && \strlen($string) > 0)
        {
            $string = \substr($string, 0, \strlen($string) - 1);
        }

        return $string;
    }

    /**
     * Sanitize description of server in LoGDNet.
     */
    public function logdnet(string $string): string
    {
        $colors = \preg_quote(\implode('', $this->color->getColors()));
        // to keep the regexp from boinging this, we need to make sure
        // that we're not replacing in with the ` mark.
        $string = \preg_replace("/[`´](?=[^0{$colors}bicn])/u", \chr(1).\chr(1), $string);

        return \str_replace(\chr(1).\chr(1), '`', $string);
    }
}
