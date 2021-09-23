<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.3.0
 */

namespace Lotgd\Core\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class BitFieldTransformer implements DataTransformerInterface
{
    public const MAX_BITFIELDS = 0x7FFFFFFF;

    public function transform($bits)
    {
        $valid   = [];
        $current = 1;

        while ($current & BitFieldTransformer::MAX_BITFIELDS)
        {
            if (($current & $bits) !== 0)
            {
                $valid[] = $current;
            }

            $current <<= 1;
        }

        return $valid;
    }

    public function reverseTransform($array)
    {
        return \array_sum($array);
    }
}
