<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.8.0
 */

namespace Lotgd\Core\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

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
class UnTagifyTransformer implements DataTransformerInterface
{
    public function reverseTransform($value)
    {
        if ('[' == $value[0])
        {
            return \implode(',', \array_column(\json_decode($value, null, 512, JSON_THROW_ON_ERROR), 'value'));
        }

        return $value;
    }

    public function transform($value)
    {
        if ('[' == $value[0])
        {
            return \implode(',', \array_column(\json_decode($value, null, 512, JSON_THROW_ON_ERROR), 'value'));
        }

        return $value;
    }
}
