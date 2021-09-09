<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Tool;

use Laminas\Filter;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Output\Code;
use Lotgd\Core\Output\Color;

class Sanitize
{
    protected $color;
    protected $code;
    private $settings;

    public function __construct(Color $color, Code $code, Settings $settings)
    {
        $this->color    = $color;
        $this->code     = $code;
        $this->settings = $settings;
    }

    /**
     * Remove all colors code from string.
     * ONLY colors, not include italic, bold... (`i `b).
     */
    public function unColorize(string $string): string
    {
        $colors = preg_quote(implode('', array_keys($this->color->getColors())));

        return preg_replace("/[`´][0{$colors}]/u", '', $string);
    }

    /**
     * Remove all codes that not are suported.
     */
    public function noLotgdCodes(string $string): string
    {
        $codes = array_merge($this->color->getColors(), $this->code->getCodes());
        $codes = preg_quote(implode('', array_keys($codes)));

        return preg_replace("/[`´][{$codes}]/u", '', $string);
    }

    /**
     * Full sanitize a string, removed color and codes.
     * All LoTGD codes included (`i `b `c) and others.
     */
    public function fullSanitize(string $string): string
    {
        return preg_replace('/[`´]./u', '', $string);
    }

    /**
     * Prevent for use LoTGD codes like colors and italic, bold...
     */
    public function preventLotgdCodes(string $string): string
    {
        return str_replace(['`', '´'], ['&#96;', '&#180;'], $string);
    }

    /**
     * Module file names can only contain alpha numeric characters and underscores.
     */
    public function moduleNameSanitize(string $string): string
    {
        return preg_replace('/[^[:alnum:]_]/', '', $string);
    }

    /**
     * Sanitize name of player.
     *
     * @param int $spaceAllowed
     */
    public function nameSanitize($spaceAllowed, string $name): string
    {
        $expr = '/[^[:alpha:]]/';

        if ($spaceAllowed !== 0)
        {
            $expr = '/[^[:alpha:] _-]/';
        }

        return preg_replace($expr, '', $name);
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
        $colors = preg_quote(implode('', $this->color->getColors()));

        if ($admin && $this->settings->getSetting('allowoddadminrenames', 0))
        {
            return $string;
        }

        $expr = "/([^[:alpha:]`´0{$colors}])/u";

        if ($spaceallowed !== 0)
        {
            $expr = "/([^[:alpha:]`´0{$colors} _-])/u";
        }

        return preg_replace($expr, '', $string);
    }

    /**
     * Sanitize html.
     */
    public function htmlSanitize(string $string): string
    {
        $filterChain = new Filter\FilterChain();
        $filterChain
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\StripTags())
            ->attach(new Filter\StripNewlines())
            ->attach(new Filter\Callback([new \HTMLPurifier(), 'purify']))
        ;

        return $filterChain->filter($string);
    }

    /**
     * Remove query param "c" from url.
     *
     * @param string $string Is a valid URL
     */
    public function cmdSanitize($string): string
    {
        $string = preg_replace('/[&?]c=[[:digit:]-]+/', '', $string);

        //-- Replace first & for ?
        if (false === strpos($string, '?') && false !== strpos($string, '&'))
        {
            $string = preg_replace('/[&]/', '?', $string, 1);
        }

        return $string;
    }

    /**
     * Remove new line code from string.
     */
    public function newLineSanitize(string $string): string
    {
        return preg_replace('/[`´]n/u', '', $string);
    }

    /**
     * Sanitize a string to ve valid mb encoding.
     */
    public function mbSanitize(string $string): string
    {
        $encode = $this->settings->getSetting('charset', 'UTF-8');

        while ( ! mb_check_encoding($string, $encode) && \strlen($string) > 0)
        {
            $string = substr($string, 0, \strlen($string) - 1);
        }

        return $string;
    }

    /**
     * Sanitize description of server in LoGDNet.
     *
     * @param string $in
     */
    public function logdnetSanitize(string $string): string
    {
        $colors = preg_quote(implode('', $this->color->getColors()));
        // to keep the regexp from boinging this, we need to make sure
        // that we're not replacing in with the ` mark.
        $string = preg_replace("/[`´](?=[^0{$colors}bicn])/u", \chr(1).\chr(1), $string);

        return str_replace(\chr(1).\chr(1), '`', $string);
    }

    /**
     * Converts a string into a slug.
     */
    public function slugify(string $string): string
    {
        $slugify = new \Cocur\Slugify\Slugify();

        return $slugify->slugify($string);
    }
}
