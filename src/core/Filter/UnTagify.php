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
 * Convert format:
 *      [{"value":"tag_1"},{"value":"tag_2"}]"
 * into:
 *      tag_1,tag_2
 *
 * Them can use explode function to transform in array.
 *
 * Tagify can manage the tags separated by commas.
 */
class UnTagify implements FilterInterface
{
    /**
     * @inheritDoc
     */
    public function filter($value)
    {
        if (! $value)
        {
            return null;
        }

        return implode(',', array_column(json_decode($value), 'value'));
    }
}
