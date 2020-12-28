<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.1.0
 */

namespace Lotgd\Core\Filter;

use Laminas\Filter\FilterInterface;

/**
 * Convert format:
 *      [value,value2]
 * into:
 *      'value,value2'.
 */
class ArrayToComaSeparator implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        if (\is_array($value))
        {
            return \implode(',', $value);
        }

        return $value;
    }
}
