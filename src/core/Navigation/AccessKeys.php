<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Navigation;

/**
 * Generate a unique access key for a nav.
 */
class AccessKeys
{
    /**
     * Array of accesskeys
     *
     * @var array
     */
    protected $accesskeys = [];

    /**
     * Create access key.
     *
     * @param string $label
     * @param array  $attributes
     *
     * @return string
     */
    public function create(string $label, array &$attributes): string
    {
        $strlen = strlen($label);

        $ignoreuntil = '';
        for($i = 0; $i < $strlen; $i++)
        {
            $char = substr($label, $i, 1);

            if ($ignoreuntil == $char)
            {
                $ignoreuntil = '';

                continue;
            }
            elseif (1 != ($this->accesskeys[strtolower($char)] ?? 0) && (false !== strpos('abcdefghijklmnopqrstuvwxyz0123456789', strtolower($char))) && '' == $ignoreuntil)
            {
                break;
            }

            if ('`' == $char || '´' == $char)
            {
                $ignoreuntil = substr($label, $i + 1, 1);
            }
        }

        $i = $i ?? 0;

        $key = '';
        if ($i < strlen($label))
        {
            $key = substr($label, $i, 1);
            $this->accesskeys[strtolower($key)] = 1;
        }

        if ('' != $key || ' ' != $key)
        {
            $attributes['accesskey'] = $key;

            $pattern1 = '/^'.preg_quote($key, '/').'/';
            $pattern2 = '/([^`])'.preg_quote($key, '/').'/';
            $rep1 = "`H{$key}´H";
            $rep2 = "\$1`H{$key}´H";
            $label = preg_replace($pattern1, $rep1, $label, 1);

            if (false === strpos($label, '`H'))
            {
                $label = preg_replace($pattern2, $rep2, $label, 1);
            }
        }

        return $label;
    }
}
