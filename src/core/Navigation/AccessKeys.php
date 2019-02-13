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
        $key = $this->checkAccessKey($label) ?: '';

        $this->accesskeys[strtolower($key)] = 1;

        if ('' != $key || ' ' != $key)
        {
            $attributes['accesskey'] = $key;

            if(false === \strpos($label, $key))
            {
                $label = '('.strtoupper($key).') '.$label;
            }

            $label = \preg_replace("/^$key/", "`H{$key}´H", $label, 1);

            if (false === strpos($label, '`H'))
            {
                $label = preg_replace("/([^`´])$key/", "\$1`H{$key}´H", $label, 1);
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
    protected function checkAccessKey(&$label): string
    {
        //-- Check explicit access Example: "G?The Graveyard"
        if ('?' == $label[1])
        {
            $char = substr($label, 0, 1);

            if (1 != ($this->accesskeys[strtolower($char)] ?? 0))
            {
                $i = \strpos($label, $char, 2);

                $key = substr($label, 0, 1);
                if (false !== $i)
                {
                    $key = substr($label, ($i - 2), 1);
                }

                $label = \substr($label, 2);

                return $key;
            }

            //-- Delete "G?" from the label because the access key has been repeated
            $label = \substr($label, 2);
        }

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

        return substr($label, $i, 1);
    }
}
