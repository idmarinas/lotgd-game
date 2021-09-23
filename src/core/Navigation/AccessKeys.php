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

namespace Lotgd\Core\Navigation;

use Laminas\Filter;

/**
 * Generate a unique access key for a nav.
 */
class AccessKeys
{
    /**
     * Array of accesskeys.
     *
     * @var array
     */
    protected $accesskeys = [];

    /**
     * Filter chain.
     *
     * @var Filter\FilterChain
     */
    protected $filterChain;

    /**
     * Create access key.
     */
    public function create(string $label, array &$attributes): string
    {
        $label = $this->filter($label);
        $key   = $this->checkAccessKey($label);

        $this->accesskeys[\strtolower($key)] = true;

        if ('' != $key || ' ' != $key)
        {
            $attributes['accesskey'] = $key;

            if (false === \strpos($label, (string) $key))
            {
                $label = '('.\strtoupper($key).') '.$label;
            }

            $pregKey = \preg_quote($key, '/');
            $label   = \preg_replace("/^{$pregKey}/", "`H{$key}´H", $label, 1);

            if (false === \strpos($label, '`H'))
            {
                $label = \preg_replace("/([^`´]){$pregKey}/", "\$1`H{$key}´H", $label, 1);
            }
        }

        return $label;
    }

    /**
     * Check for new access key.
     *
     * @param string $label
     *
     * @return string
     */
    protected function checkAccessKey(&$label): ?string
    {
        //-- Check explicit access Example: "G?The Graveyard"
        if ('?' == $label[1])
        {
            $char = \substr($label, 0, 1);

            if ( ! ($this->accesskeys[\strtolower($char)] ?? false))
            {
                $i = \strpos($label, $char, 2);

                $key = \substr($label, $i, 1);

                if (false !== $i)
                {
                    $key = \substr($label, $i, 1);
                }

                $label = \substr($label, 2);

                return $key;
            }

            //-- Delete "G?" from the label because the access key has been repeated
            $label = \substr($label, 2);
        }

        $strlen = \strlen($label);

        $ignoreuntil = '';

        for ($i = 0; $i < $strlen; ++$i)
        {
            $char = \substr($label, $i, 1);

            if ('&' == $char)
            {
                $ignoreuntil = ';';
            }
            elseif ('`' == $char || '´' == $char || "'" == $char)
            {
                $ignoreuntil = \substr($label, $i + 1, 1);
            }

            if ($ignoreuntil === $char)
            {
                $ignoreuntil = '';
            }
            elseif ( ! ($this->accesskeys[\strtolower($char)] ?? false) && (false !== \strpos('abcdefghijklmnopqrstuvwxyz0123456789', \strtolower($char))) && '' == $ignoreuntil)
            {
                break;
            }
        }

        return $char;
    }

    /**
     * Filter label.
     */
    private function filter(string $label): string
    {
        if ( ! $this->filterChain)
        {
            $this->filterChain = new Filter\FilterChain();
            $this->filterChain->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags())
                ->attach(new Filter\StripNewlines())
                // ->attach(new Filter\HtmlEntities())
            ;
        }

        return $this->filterChain->filter($label);
    }
}
