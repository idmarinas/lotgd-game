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

use Zend\Filter\FilterInterface;

/**
 * Convert to int value an array of BitField.
 */
class BitField implements FilterInterface
{
    /**
     * @inheritDoc
     *
     * @return int
     */
    public function filter($value)
    {
        if (! is_array($value))
        {
            return (int) $value;
        }

        return array_sum($value);
    }
}
