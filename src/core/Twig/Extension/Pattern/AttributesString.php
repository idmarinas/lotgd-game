<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Twig\Extension\Pattern;

use Laminas\Escaper\Exception\RuntimeException as EscaperException;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Tracy\Debugger;

/**
 * Create a string of attributes for a html tag.
 */
trait AttributesString
{
    /**
     * Create a string of all attribute/value pairs.
     *
     * Escapes all attribute keys and values
     *
     * @return string
     */
    public function createAttributesString(array $attributes)
    {
        $escape     = new EscapeHtml();
        $escapeAttr = new EscapeHtmlAttr();
        $strings    = [];

        foreach ($attributes as $key => $value)
        {
            $key = \strtolower($key);

            try
            {
                $escapedAttribute = $escapeAttr($value);
                $strings[]        = \sprintf('%s="%s"', $escape($key), $escapedAttribute);
            }
            catch (EscaperException $x)
            {
                Debugger::log($x);
                // If an escaper exception, escape only the key, and use a blank value.
                $strings[] = \sprintf('%s=""', $escape($key));
            }
        }

        return \implode(' ', $strings);
    }
}
